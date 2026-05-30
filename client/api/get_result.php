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
    Response::json(["success" => false, "error" => "Unauthorized"], 401);
}

$user_id = $user["id"] ?? $user["id_nguoidung"] ?? 0;
$conn = Database::connect();
$id_lanthi = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id_lanthi === 0) {
    Response::json(["error" => "Thiếu ID lần thi"], 400);
}

$stmt = $conn->prepare("
    SELECT l.*, b.ten_baithi, b.hien_dapan
    FROM lanthi l
    JOIN baithi b ON l.id_baithi = b.id_baithi
    WHERE l.id_lanthi = ? AND l.id_nguoidung = ?
");
$stmt->bind_param("ii", $id_lanthi, $user_id);
$stmt->execute();
$lanthi = $stmt->get_result()->fetch_assoc();

if (!$lanthi) {
    Response::json(["error" => "Khong tim thay ket qua"], 404);
}

// Premium check
$stmt = $conn->prepare("SELECT premium_status FROM nguoidung WHERE id_nguoidung = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_premium = $stmt->get_result()->fetch_assoc()['premium_status'] ?? 0;

$id_baithi = (int) $lanthi["id_baithi"];

$sql = "
    SELECT
        c.id_cauhoi,
        c.noidungcauhoi,
        c.loai_cauhoi,
        d.id_dapan,
        d.noidungdapan,
        d.dapandung,
        d.loigiai_chitiet,
        ts.cautraloichon,
        ts.noidungtraloi
    FROM cauhoi c
    JOIN dapan d ON c.id_cauhoi = d.id_cauhoi
    LEFT JOIN traloithisinh ts
        ON ts.id_cauhoi = c.id_cauhoi
        AND ts.id_lanthi = ?
    WHERE c.id_baithi = ?
    ORDER BY c.id_cauhoi, d.id_dapan
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_lanthi, $id_baithi);
$stmt->execute();
$res = $stmt->get_result();

$questions = [];
while ($row = $res->fetch_assoc()) {
    $qid = (int) $row["id_cauhoi"];

    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            "id_cauhoi" => $qid,
            "noidungcauhoi" => htmlspecialchars($row["noidungcauhoi"]),
            "loai_cauhoi" => (int)$row["loai_cauhoi"],
            "user_text_ans" => $row["noidungtraloi"],
            "loigiai_chitiet" => $row["loigiai_chitiet"],
            "answers" => [],
        ];
    } else if (empty($questions[$qid]["loigiai_chitiet"]) && !empty($row["loigiai_chitiet"])) {
        $questions[$qid]["loigiai_chitiet"] = $row["loigiai_chitiet"];
    }

    $can_see_answers = ($user_premium == 1) || ((int) ($lanthi["hien_dapan"] ?? 0) === 1);
    $selected = false;
    if ($questions[$qid]["loai_cauhoi"] === 1) {
        $selected = ((string) $row["cautraloichon"] !== "" && (int) $row["cautraloichon"] === (int) $row["id_dapan"]);
    }

    // Luôn lấy trạng thái đáp án đúng để chấm điểm hiển thị, nhưng chỉ trả về cho client nếu được phép
    $is_true_correct = (int) $row["dapandung"] === 1;

    $questions[$qid]["answers"][] = [
        "id_dapan" => (int) $row["id_dapan"],
        "noidungdapan" => htmlspecialchars($row["noidungdapan"]),
        "dapandung" => $can_see_answers ? $is_true_correct : null,
        "is_true_correct" => $is_true_correct, // Dùng nội bộ để tính status bên dưới
        "selected" => $selected,
    ];
}
// Update $lanthi["hien_dapan"] if user is premium to affect frontend logic
if ($user_premium == 1) {
    $lanthi["hien_dapan"] = 1;
}

$questions_arr = [];
$correct = 0;
$wrong = 0;
$empty = 0;

foreach ($questions as $qid => $q) {
    $isCorrect = false;
    $isEmpty = false;

    if ($q["loai_cauhoi"] === 2) {
        $userVal = $q["user_text_ans"];
        if (!$userVal) {
            $isEmpty = true;
        } else {
            $submitted = json_decode($userVal, true) ?: [$userVal];
            $correctAnswers = array_filter($q["answers"], fn($a) => $a["is_true_correct"]);
            $correctAnswers = array_values($correctAnswers);

            $allMatch = true;
            if (count($submitted) < count($correctAnswers)) {
                $allMatch = false;
            } else {
                foreach ($correctAnswers as $idx => $corRow) {
                    $sub = trim(mb_strtolower((string)($submitted[$idx] ?? ""), 'UTF-8'));
                    $cor = trim(mb_strtolower((string)($corRow["noidungdapan"] ?? ""), 'UTF-8'));
                    if ($sub !== $cor) {
                        $allMatch = false;
                        break;
                    }
                }
            }
            $isCorrect = $allMatch;
        }
        
        // Nếu không được xem đáp án, xóa sạch nội dung đáp án đúng khỏi payload trả về
        if (!$can_see_answers) {
            foreach($q["answers"] as &$a) {
                $a["noidungdapan"] = "???";
            }
        }
    } else {
        $selected_ans = null;
        foreach ($q["answers"] as $ans) {
            if ($ans["selected"]) {
                $selected_ans = $ans;
                break;
            }
        }
        if (!$selected_ans) {
            $isEmpty = true;
        } else if ($selected_ans["is_true_correct"]) {
            $isCorrect = true;
        }
        
        // Xóa dấu hiệu đáp án đúng nếu bị ẩn
        foreach($q["answers"] as &$a) {
            unset($a["is_true_correct"]);
        }
    }

    if ($isCorrect) {
        $correct++;
        $q["status"] = "correct";
    } else if ($isEmpty) {
        $empty++;
        $q["status"] = "empty";
    } else {
        $wrong++;
        $q["status"] = "wrong";
    }

    if ($user_premium == 0) {
        $q["loigiai_chitiet"] = "Tính năng này chỉ dành cho tài khoản Premium. Vui lòng nâng cấp để xem lời giải chi tiết!";
    }
    
    $questions_arr[] = $q;
}

Response::json([
    "success" => true,
    "lanthi" => $lanthi,
    "can_see_answers" => $can_see_answers, // Gửi thêm flag này cho chắc
    "stats" => [
        "correct" => $correct,
        "wrong" => $wrong,
        "empty" => $empty
    ],
    "questions" => $questions_arr,
]);