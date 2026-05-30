<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/baithi.model.php";

$user = Api::requireLogin();

$id_nguoidung = (int) ($user["id_nguoidung"] ?? 0);

$data = getAll_baithi($id_nguoidung);

foreach ($data as &$item) {
    $item['is_locked'] = isBaiThiLocked($item['id_baithi']) ? 1 : 0;
}
unset($item);

Api::json([
    "success" => true,
    "data" => $data,
    "subjects" => getAll_monhoc($id_nguoidung),
]);

