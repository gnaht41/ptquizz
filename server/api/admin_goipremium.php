<?php
require_once __DIR__ . '/../core/Api.php';
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/admin/goipremium.model.php';

if (ob_get_level() > 0) ob_clean();

$db = Database::connect();
$model = new GoiPremiumModel($db);
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id > 0) {
            Api::json(['success' => true, 'data' => $model->getById($id)]);
        }

        $status = $_GET['status'] ?? 'active';
        if (!in_array($status, ['active', 'inactive', 'all'], true)) {
            $status = 'active';
        }

        Api::json(['success' => true, 'data' => $model->getAll($status)]);
    }

    if ($method === 'POST') {
        $input = Api::jsonInput();

        if (isset($input['copy_id']) && (int)$input['copy_id'] > 0) {
            $success = $model->copy((int)$input['copy_id']);
            $message = $success ? 'Sao chép gói thành công' : ($_SESSION['error'] ?? 'Không thể sao chép gói');
            unset($_SESSION['error']);
            Api::json([
                'success' => $success,
                'message' => $message
            ], $success ? 200 : 409);
        }

        if (empty($input['ten_goi']) || empty($input['gia']) || empty($input['thoihan_ngay']) || empty($input['mieuta'])) {
            Api::json(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin gói premium'], 400);
        }
        if ((float)$input['gia'] < 10000) {
            Api::json(['success' => false, 'message' => 'Giá gói phải lớn hơn hoặc bằng 10.000 VNĐ'], 400);
        }
        if ((int)$input['thoihan_ngay'] <= 1) {
            Api::json(['success' => false, 'message' => 'Thời hạn phải lớn hơn 1 ngày'], 400);
        }
        if (!$model->checkUniqueName($input['ten_goi'])) {
            Api::json(['success' => false, 'message' => 'Tên gói này đã tồn tại, vui lòng chọn tên khác'], 409);
        }

        $success = $model->save($input);
        Api::json([
            'success' => $success,
            'message' => $success ? 'Lưu gói thành công' : 'Không thể lưu gói'
        ]);
    }

    if ($method === 'PATCH') {
        $data = Api::jsonInput();

        if (($data['action'] ?? '') === 'restore') {
            $id = isset($data['id_goi']) ? (int)$data['id_goi'] : 0;
            if ($id <= 0) {
                Api::json(['success' => false, 'message' => 'Mã gói không hợp lệ'], 400);
            }

            $success = $model->restore($id);
            $message = $success ? 'Khôi phục gói premium thành công' : ($_SESSION['error'] ?? 'Không thể khôi phục gói này');
            unset($_SESSION['error']);
            Api::json([
                'success' => $success,
                'message' => $message
            ], $success ? 200 : 409);
        }

        if (empty($data['ten_goi']) || empty($data['gia']) || empty($data['thoihan_ngay']) || empty($data['mieuta'])) {
            Api::json(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin gói premium'], 400);
        }
        if ((float)$data['gia'] < 10000) {
            Api::json(['success' => false, 'message' => 'Giá gói phải lớn hơn hoặc bằng 10.000 VNĐ'], 400);
        }
        if ((int)$data['thoihan_ngay'] <= 1) {
            Api::json(['success' => false, 'message' => 'Thời hạn phải lớn hơn 1 ngày'], 400);
        }

        $id = isset($data['id_goi']) ? (int)$data['id_goi'] : 0;
        if (!$model->checkUniqueName($data['ten_goi'], $id)) {
            Api::json(['success' => false, 'message' => 'Tên gói này đã tồn tại, vui lòng chọn tên khác'], 409);
        }

        $success = $model->save($data);
        Api::json([
            'success' => $success,
            'message' => $success ? 'Cập nhật thành công' : 'Không thể cập nhật'
        ]);
    }

    if ($method === 'DELETE') {
        $input = Api::jsonInput();
        $id = isset($input['id_goi']) ? (int)$input['id_goi'] : 0;
        if ($id <= 0) {
            Api::json(['success' => false, 'message' => 'Mã gói không hợp lệ'], 400);
        }

        $success = $model->delete($id);
        Api::json([
            'success' => $success,
            'message' => $success ? 'Đã chuyển gói premium vào thùng rác' : 'Không thể xóa gói này hoặc gói đã nằm trong thùng rác'
        ]);
    }

    Api::json(['success' => false, 'message' => 'Phương thức không được hỗ trợ'], 405);
} catch (Exception $e) {
    Api::json(['success' => false, 'message' => 'Không thể xử lý yêu cầu. Vui lòng thử lại sau.'], 500);
}
?>
