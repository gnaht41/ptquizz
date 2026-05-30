<?php
require_once __DIR__ . "/../../core/Api.php";
require_once __DIR__ . "/../../core/Response.php";
require_once __DIR__ . "/../../../server/model/giangvien/monhoc.model.php";

$user = Api::requireLogin();
$data = Api::jsonInput();

$id_monhoc = (int) ($data["id_monhoc"] ?? 0);
$tenmonhoc = trim($data["tenmonhoc"] ?? "");
$mieuta = trim($data["mieuta"] ?? "");

if ($tenmonhoc === "") {
    Response::json(["error" => "Tên môn học không được để trống"], 400);
}

if (isDuplicateMonHoc($tenmonhoc, $id_monhoc)) {
    Response::json(["error" => "Tên môn học này đã tồn tại"], 409);
}

if ($id_monhoc > 0) {
    $ok = update_monhoc_by_owner($id_monhoc, $tenmonhoc, $mieuta, $user["id"], $user["role"]);
} else {
    $ok = insert_monhoc($tenmonhoc, $user["id"], $mieuta);
}

if (!$ok) {
    Response::json(["error" => "Không thể lưu môn học"], 500);
}

Response::json(["success" => true, "message" => "Đã lưu môn học"]);
