<?php
require_once __DIR__ . "/../../core/Api.php";
require_once __DIR__ . "/../../core/Response.php";
require_once __DIR__ . "/../../../server/model/giangvien/monhoc.model.php";

$user = Api::requireLogin();
$data = Api::jsonInput();
$id_monhoc = (int) ($data["id_monhoc"] ?? 0);

if (delete_monhoc($id_monhoc, $user["id"], $user["role"])) {
    Response::json(["success" => true]);
} else {
    Response::json(["error" => "Không thể xóa môn học hoặc bạn không có quyền"], 500);
}
