<?php
require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/admin/dashboard.model.php";

Api::requireRole(["admin"]);

Api::json([
    "success" => true,
    "stats" => get_admin_dashboard_stats(),
    "recent_critical_logs" => get_recent_critical_logs()
]);
