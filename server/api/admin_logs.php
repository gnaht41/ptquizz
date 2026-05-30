<?php
require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/admin/logs.model.php";

Api::requireRole(["admin"]);

$filters = [
    "method" => $_GET["method"] ?? "",
    "date" => $_GET["date"] ?? "",
    "keyword" => $_GET["keyword"] ?? "",
];

$logs = get_api_logs($filters);

// Bổ sung thêm trường thoigian để tương thích với các phiên bản code Java
foreach ($logs as &$log) {
    $log['thoigian'] = $log['created_at'];
}

Api::json([
    "success" => true,
    "data" => $logs
]);
