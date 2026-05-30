<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../../server/model/giangvien/ketqua.model.php";
require_once __DIR__ . "/../core/Response.php";

// Standard session start or token check would happen here
// For now, assuming session/cookie is handled by APIHelper

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "giangvien") {
    Response::json(["error" => "Unauthorized"], 401);
}

$id_giangvien = $_SESSION["user"]["id"];

$stats = get_lecturer_dashboard_stats($id_giangvien);
$chartData = get_lecturer_chart_data($id_giangvien);

Response::json([
    "success" => true,
    "stats" => $stats,
    "chartData" => $chartData
]);
