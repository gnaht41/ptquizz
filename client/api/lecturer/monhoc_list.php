<?php
require_once __DIR__ . "/../../core/Api.php";
require_once __DIR__ . "/../../core/Response.php";
require_once __DIR__ . "/../../../server/model/giangvien/monhoc.model.php";

$user = Api::requireLogin();
// Thử lấy ID từ tất cả các nguồn có thể
$userId = $user["id"] ?? $user["id_nguoidung"] ?? $user["userId"] ?? 0;

$list = getAll_monhoc_with_user($userId, $user["role"] ?? "");

Response::json([
    "success" => true,
    "debug_id" => $userId,
    "data" => $list,
]);
