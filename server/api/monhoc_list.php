<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/monhoc.model.php";

$user = Api::requireLogin();

$list = getAll_monhoc_with_user($user["id_nguoidung"] ?? 0, $user["vaitro"] ?? "");

Api::json([
    "success" => true,
    "data" => $list,
    "debug_id" => $user["id_nguoidung"] ?? 0,
    "debug_role" => $user["vaitro"] ?? ""
]);

