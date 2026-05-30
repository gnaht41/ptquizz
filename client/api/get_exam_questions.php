<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";


require_once __DIR__ . "/../core/TokenManager.php";

$user = null;
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (strpos($authHeader, 'Bearer ') === 0) {
    $token = substr($authHeader, 7);
    $user = TokenManager::validateToken($token);
} elseif (isset($_SESSION["user"])) {
    $user = $_SESSION["user"];
}

if (!$user) {
    Response::json(["error" => "Unauthorized - Cần đăng nhập"], 401);
}

$user_id = $user["id"] ?? $user["id_nguoidung"] ?? 0;
$id_baithi = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id_baithi === 0) {
    Response::json(["error" => "Thiếu ID bài thi"], 400);
}

$conn = Database::connect();

// --- PREMIUM CHECK: Limit 30 attempts per day for free users ---
$stmt_status = $conn->prepare("SELECT premium_status FROM nguoidung WHERE id_nguoidung = ?");
$stmt_status->bind_param("i", $user_id);
$stmt_status->execute();
$userData = $stmt_status->get_result()->fetch_assoc();
$is_premium = ($userData['premium_status'] ?? 0) == 1;

if (!$is_premium) {
    // Count attempts TODAY
    $stmt_count = $conn->prepare("SELECT COUNT(*) as count FROM lanthi WHERE id_nguoidung = ? AND DATE(thoigianbatdau) = CURDATE()");
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $attempts = $stmt_count->get_result()->fetch_assoc()['count'];
    
    // Check if user is ALREADY in an ongoing attempt of THIS exam (allow resume)
    $stmt_check = $conn->prepare("SELECT id_lanthi FROM lanthi WHERE id_nguoidung = ? AND id_baithi = ? AND trangthai = 'ongoing'");
    $stmt_check->bind_param("ii", $user_id, $id_baithi);
    $stmt_check->execute();
    $hasOngoing = $stmt_check->get_result()->num_rows > 0;

    if ($attempts >= 30 && !$hasOngoing) {
        Response::json([
            "error" => "limit_reached",
            "message" => "Bạn đã hết lượt làm bài miễn phí trong hôm nay (30 lượt). Vui lòng nâng cấp Premium để làm bài không giới hạn!"
        ], 403);
    }
}

$stmt = $conn->prepare("SELECT ten_baithi, thoigianlam, IFNULL(xao_tron, 0) AS xao_tron FROM baithi WHERE id_baithi = ?");
$stmt->bind_param("i", $id_baithi);
$stmt->execute();
$baithi = $stmt->get_result()->fetch_assoc();

if (!$baithi) {
    Response::json(["error" => "Bai thi khong ton tai"], 404);
}

$ten_baithi = $baithi["ten_baithi"];
$thoigianlam = (int) $baithi["thoigianlam"];
$xao_tron = (int) ($baithi["xao_tron"] ?? 0);

// AUTO-REPAIR DATABASE: Ensure columns exist (wrapped in try-catch for Aiven compatibility)
try { $conn->query("ALTER TABLE lanthi ADD COLUMN IF NOT EXISTS last_active DATETIME NULL"); } catch (Exception $e) {}
try { $conn->query("ALTER TABLE lanthi ADD COLUMN IF NOT EXISTS session_token VARCHAR(255) NULL"); } catch (Exception $e) {}

$session_token = $_GET["token"] ?? "unknown";

// USER-WIDE LOCKING LOGIC: Check if this user is active on ANY exam on another device
$stmt_lock = $conn->prepare("
    SELECT id_baithi, last_active, session_token 
    FROM lanthi 
    WHERE id_nguoidung = ? AND trangthai = 'ongoing' 
      AND last_active > DATE_SUB(NOW(), INTERVAL 45 SECOND)
    ORDER BY last_active DESC LIMIT 1
");
$stmt_lock->bind_param("i", $user_id);
$stmt_lock->execute();
$lock_res = $stmt_lock->get_result()->fetch_assoc();

if ($lock_res) {
    $last_active = $lock_res["last_active"];
    $stored_token = $lock_res["session_token"];
    
    if ($last_active) {
        $last_time = strtotime($last_active);
        // If active in last 45 seconds AND tokens don't match -> CHECK PLATFORM
        if ((time() - $last_time) < 45 && !empty($stored_token) && $stored_token !== "unknown" && $stored_token !== $session_token) {
            
            // Allow override if same platform (e.g. JAVA overrides JAVA, WEB overrides WEB)
            $old_platform = substr($stored_token, 0, 4);
            $new_platform = substr($session_token, 0, 4);
            
            if ($old_platform !== $new_platform) {
                Response::json([
                    "error" => "dual_session", 
                    "message" => "Tài khoản của bạn đang hoạt động ở một thiết bị hoặc trình duyệt khác. Vui lòng thoát ở thiết bị kia trước khi bắt đầu hoặc tiếp tục bài thi này."
                ], 403);
            }
        }
    }
}

$stmt = $conn->prepare("
    SELECT id_lanthi, thoigianconlai, cautraloi_tam, last_active, session_token,
           TIMESTAMPDIFF(SECOND, thoigianbatdau, NOW()) as elapsed_seconds
    FROM lanthi
    WHERE id_nguoidung = ? AND id_baithi = ? AND trangthai = 'ongoing'
    LIMIT 1
");
$stmt->bind_param("ii", $user_id, $id_baithi);
$stmt->execute();
$res = $stmt->get_result();

$elapsed_seconds = 0;
$thoigianconlai = null;
$cautraloi_tam = null;

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $id_lanthi = $row["id_lanthi"];
    
    // Update session token and activity for the current device
    $stmt_update = $conn->prepare("UPDATE lanthi SET session_token = ?, last_active = NOW() WHERE id_lanthi = ?");
    $stmt_update->bind_param("si", $session_token, $id_lanthi);
    $stmt_update->execute();

    $elapsed_seconds = (int) $row["elapsed_seconds"];
    
    // Prioritize saved time from draft
    if ($row["thoigianconlai"] !== null) {
        $thoigianconlai = (int) $row["thoigianconlai"];
    } else {
        $thoigianconlai = ($thoigianlam * 60) - $elapsed_seconds;
    }
    
    if ($thoigianconlai < 0) $thoigianconlai = 0;
    $cautraloi_tam = $row["cautraloi_tam"];
} else {
    $stmt = $conn->prepare("
        INSERT INTO lanthi (id_nguoidung, id_baithi, diem, thoigianbatdau, trangthai)
        VALUES (?, ?, 0, NOW(), 'ongoing')
    ");
    $stmt->bind_param("ii", $user_id, $id_baithi);
    $stmt->execute();
    $id_lanthi = $conn->insert_id;
    $thoigianconlai = $thoigianlam * 60;

    // Increment free attempt count if not premium
    $stmt_status = $conn->prepare("SELECT premium_status FROM nguoidung WHERE id_nguoidung = ?");
    $stmt_status->bind_param("i", $user_id);
    $stmt_status->execute();
    $status = $stmt_status->get_result()->fetch_assoc()["premium_status"] ?? 0;
    
    if ($status == 0) {
        // Count is derived live from lanthi table - no need to update a separate column
    }
}

$cautraloi_tam_str = "";
$flagged_questions_str = "";
if (!empty($cautraloi_tam)) {
    $arr = json_decode($cautraloi_tam, true);
    if (is_array($arr)) {
        $answers_map = [];
        $flags_list = [];
        if (isset($arr["answers"]) && is_array($arr["answers"])) {
            $answers_map = $arr["answers"];
            $flags_list = isset($arr["flags"]) ? $arr["flags"] : [];
        } else {
            // Backward compatibility
            $answers_map = $arr;
        }

        $pairs = [];
        foreach ($answers_map as $k => $v) {
            if (!is_array($v)) {
                $pairs[] = $k . ":" . $v;
            }
        }
        $cautraloi_tam_str = implode("|", $pairs);
        $flagged_questions_str = implode(",", $flags_list);
    }
}

$sql = "
    SELECT
        c.id_cauhoi, c.noidungcauhoi, c.loai_cauhoi,
        d.id_dapan, d.noidungdapan
    FROM cauhoi c
    JOIN dapan d ON c.id_cauhoi = d.id_cauhoi
    WHERE c.id_baithi = ?
    ORDER BY c.id_cauhoi, d.id_dapan
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_baithi);
$stmt->execute();
$res = $stmt->get_result();

$cauhoi_map = [];
while ($row = $res->fetch_assoc()) {
    $id = $row["id_cauhoi"];
    if (!isset($cauhoi_map[$id])) {
        $cauhoi_map[$id] = [
            "id_cauhoi" => $id,
            "noidung" => htmlspecialchars($row["noidungcauhoi"]),
            "loai_cauhoi" => (int)($row["loai_cauhoi"] ?? 1),
            "dapan" => [],
        ];
    }

    $cauhoi_map[$id]["dapan"][] = [
        "id_dapan" => $row["id_dapan"],
        "noidungdapan" => htmlspecialchars($row["noidungdapan"]),
    ];
}

$cauhoi = array_values($cauhoi_map);
if ($xao_tron) {
    // Deterministic shuffle using ID attempt and ID question
    usort($cauhoi, function($a, $b) use ($id_lanthi) {
        return strcmp(md5($a['id_cauhoi'] . $id_lanthi), md5($b['id_cauhoi'] . $id_lanthi));
    });
    foreach ($cauhoi as &$item) {
        usort($item["dapan"], function($a, $b) use ($id_lanthi) {
            return strcmp(md5($a['id_dapan'] . $id_lanthi), md5($b['id_dapan'] . $id_lanthi));
        });
    }
    unset($item);
}

Response::json([
    "success" => true,
    "id_lanthi" => $id_lanthi,
    "id_baithi" => $id_baithi,
    "ten_baithi" => $ten_baithi,
    "thoigianlam" => $thoigianlam,
    "xao_tron" => $xao_tron,
    "elapsed_seconds" => $elapsed_seconds,
    "thoigianconlai" => $thoigianconlai,
    "cautraloi_tam" => $cautraloi_tam_str,
    "flagged_questions" => $flagged_questions_str,
    "cauhoi" => $cauhoi,
]);