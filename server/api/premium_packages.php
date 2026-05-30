<?php
require_once __DIR__ . '/../core/Api.php';
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/admin/goipremium.model.php';

Api::boot();
Api::requireMethod("GET");

// Ai đăng nhập rồi cũng có thể xem danh sách gói đang bán
Api::requireLogin();

$db = Database::connect();
$model = new GoiPremiumModel($db);

// Chỉ lấy các gói đang 'active'
$packages = $model->getAll('active');

Api::json([
    "success" => true,
    "data" => $packages
]);
?>
