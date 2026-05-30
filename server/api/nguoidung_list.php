<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/admin/nguoidung.model.php";

Api::requireRole(["admin"]);

$filters = [
    "vaitro" => $_GET["vaitro"] ?? "",
    "trangthai" => $_GET["trangthai"] ?? "",
    "keyword" => $_GET["keyword"] ?? "",
];

Api::json([
    "success" => true,
    "data" => getManagedUsers($filters),
]);
