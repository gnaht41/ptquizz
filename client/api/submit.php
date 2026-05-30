<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";


$data = Api::jsonInput();

$id_lanthi = (int) ($data["id_lanthi"] ?? 0);
$id_baithi = (int) ($data["id_baithi"] ?? 0);
$answers = $data["answers"] ?? [];

if ($id_lanthi === 0 || $id_baithi === 0 || !is_array($answers)) {
    Response::json(["error" => "Dữ liệu không hợp lệ"], 400);
}

$conn = Database::connect();

// 1. Get all questions and correct answers (multiple answers allowed per question for Cloze)
$sql = "
    SELECT c.id_cauhoi, c.loai_cauhoi, d.id_dapan, d.noidungdapan
    FROM cauhoi c
    JOIN dapan d ON c.id_cauhoi = d.id_cauhoi
    WHERE c.id_baithi = ? AND d.dapandung = 1
    ORDER BY c.id_cauhoi, d.id_dapan
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_baithi);
$stmt->execute();
$res = $stmt->get_result();
$correctMap = [];
while ($row = $res->fetch_assoc()) {
    $qid = $row["id_cauhoi"];
    if (!isset($correctMap[$qid])) {
        $correctMap[$qid] = [
            "loai_cauhoi" => (int)$row["loai_cauhoi"],
            "answers" => []
        ];
    }
    $correctMap[$qid]["answers"][] = $row;
}

// 2. Clear old answers
$stmt = $conn->prepare("DELETE FROM traloithisinh WHERE id_lanthi = ?");
$stmt->bind_param("i", $id_lanthi);
$stmt->execute();

// 3. Insert new answers and calculate score
$stmtIns = $conn->prepare("
    INSERT INTO traloithisinh (id_cauhoi, id_lanthi, cautraloichon, noidungtraloi)
    VALUES (?, ?, ?, ?)
");

$socaudung = 0;
foreach ($answers as $id_cauhoi => $value) {
    if (!$value && !is_array($value)) continue;
    $id_cauhoi = (int) $id_cauhoi;
    if (!isset($correctMap[$id_cauhoi])) continue;
    
    $qInfo = $correctMap[$id_cauhoi];
    $type = $qInfo["loai_cauhoi"];
    $correctAnswers = $qInfo["answers"];
    
    $isCorrect = false;

    if ($type === 2) {
        $submittedArr = is_array($value) ? $value : [$value];
        // For Cloze: ALL blanks must be correct
        $allMatch = true;
        
        // Store as JSON in noidungtraloi for the first record or just handle separately
        // Actually, let's just store the plain string or first element for DB compatibility
        $noidungtraloi = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string)$value;
        
        if (count($submittedArr) < count($correctAnswers)) {
            $allMatch = false;
        } else {
            foreach ($correctAnswers as $idx => $correctRow) {
                $sub = trim((string)($submittedArr[$idx] ?? ""));
                $cor = trim((string)$correctRow["noidungdapan"]);
                if (mb_strtolower($sub, 'UTF-8') !== mb_strtolower($cor, 'UTF-8')) {
                    $allMatch = false;
                    break;
                }
            }
        }
        $isCorrect = $allMatch;
        $stmtIns->bind_param("iiis", $id_cauhoi, $id_lanthi, $nullVal, $noidungtraloi);
        $nullVal = null;
    } else {
        $cautraloichon = (int)$value;
        if ($cautraloichon === (int)($correctAnswers[0]["id_dapan"] ?? 0)) {
            $isCorrect = true;
        }
        $stmtIns->bind_param("iiis", $id_cauhoi, $id_lanthi, $cautraloichon, $nullVal);
        $nullVal = null;
    }

    if ($isCorrect) $socaudung++;
    $stmtIns->execute();
}

$stmt = $conn->prepare("SELECT tongcauhoi FROM baithi WHERE id_baithi = ?");
$stmt->bind_param("i", $id_baithi);
$stmt->execute();
$tong = $stmt->get_result()->fetch_assoc()["tongcauhoi"] ?? 0;

$diem = $tong > 0 ? round(($socaudung / $tong) * 10, 2) : 0;

$stmt = $conn->prepare("
    UPDATE lanthi
    SET diem = ?,
        socaudung = ?,
        tongdiem_toida = 10,
        thoigiannop = NOW(),
        trangthai = 'done'
    WHERE id_lanthi = ?
");
$stmt->bind_param("dii", $diem, $socaudung, $id_lanthi);
$stmt->execute();

Response::json([
    "success" => true,
    "diem" => $diem,
    "socaudung" => $socaudung,
    "id_lanthi" => $id_lanthi,
]);

