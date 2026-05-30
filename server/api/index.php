<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/SecurityLogger.php";

$routes = require __DIR__ . "/../routes/api.php";
$route = Api::detectRoute();

Api::boot();

// Ghi log hành động API
$user = $_SESSION['user'] ?? null;
SecurityLogger::logRequest($user['id_nguoidung'] ?? null, http_response_code());

if ($route === "") {
    Api::json([
        "success" => true,
        "message" => "PT QUIZ Server API router",
        "usage" => "api/monhoc/list",
        "routes" => array_keys($routes),
    ]);
}

if (!isset($routes[$route])) {
    Api::json([
        "error" => "API route not found",
        "route" => $route,
    ], 404);
}

$config = $routes[$route];

if (!empty($config["methods"])) {
    Api::requireMethods($config["methods"]);
}

if (!empty($config["auth"])) {
    Api::requireLogin();
}

if (!empty($config["roles"])) {
    Api::requireRole($config["roles"]);
}

require $config["handler"];
