<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";


$data = Api::jsonInput();

if (!isset($data["id_lanthi"])) {
    Response::json(["error" => "Invalid payload"], 400);
}

$id_lanthi = (int) $data["id_lanthi"];
$thoigianconlai = isset($data["thoigianconlai"]) ? (int) $data["thoigianconlai"] : null;
$answers_data = isset($data["answers"]) ? $data["answers"] : [];
$flags_data = isset($data["flags"]) ? $data["flags"] : (isset($data["flagged_questions"]) ? $data["flagged_questions"] : []);

$draft_payload = [
    "answers" => $answers_data,
    "flags" => $flags_data
];
$answers = json_encode($draft_payload, JSON_UNESCAPED_UNICODE);

$conn = Database::connect();

// Support authentication via session (Web) or id_lanthi (App)
$user_id = isset($_SESSION["user"]["id"]) ? $_SESSION["user"]["id"] : null;

$stmt = $conn->prepare("SELECT id_lanthi, id_nguoidung, session_token, last_active FROM lanthi WHERE id_lanthi = ?");
$stmt->bind_param("i", $id_lanthi);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    Response::json(["error" => "Forbidden"], 403);
}

// Security check: if not logged in (App), ensure we only operate on the provided id_lanthi
if ($user_id !== null && $row["id_nguoidung"] != $user_id) {
    Response::json(["error" => "Forbidden"], 403);
}

// LOCK CHECK in sync-draft
$session_token = $data["token"] ?? "unknown";
$stored_token = $row["session_token"];
$last_active = $row["last_active"];

if ($last_active) {
    $last_time = strtotime($last_active);
    if ((time() - $last_time) < 45 && $stored_token !== $session_token && !empty($stored_token) && $stored_token !== "unknown") {
        Response::json([
            "error" => "dual_session", 
            "message" => "Thiết bị khác đang giữ khóa. Vui lòng chờ hoặc thoát thiết bị kia.",
            "debug" => ["stored" => $stored_token, "sent" => $session_token]
        ], 403);
    }
}

$release = isset($data["release"]) && $data["release"] === true;

if ($release) {
    $stmt = $conn->prepare("UPDATE lanthi SET thoigianconlai = ?, cautraloi_tam = ?, last_active = NULL, session_token = NULL WHERE id_lanthi = ?");
    $stmt->bind_param("isi", $thoigianconlai, $answers, $id_lanthi);
} else {
    $stmt = $conn->prepare("UPDATE lanthi SET thoigianconlai = ?, cautraloi_tam = ?, last_active = NOW(), session_token = ? WHERE id_lanthi = ?");
    $stmt->bind_param("issi", $thoigianconlai, $answers, $session_token, $id_lanthi);
}

if ($stmt->execute()) {
    Response::json(["success" => true]);
}

Response::json(["error" => "Failed to sync"], 500);

