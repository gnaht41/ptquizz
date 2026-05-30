<?php
class GoiPremiumModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll($status = 'active') {
        if ($status === 'all') {
            $sql = "SELECT * FROM goi_premium ORDER BY trangthai ASC, gia ASC";
            $result = $this->db->query($sql);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM goi_premium WHERE trangthai = ? ORDER BY gia ASC");
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        if (isset($stmt)) {
            $stmt->close();
        }
        return $data;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM goi_premium WHERE id_goi = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function save($data) {
        if (isset($data['id_goi']) && $data['id_goi'] > 0) {
            // Update
            $stmt = $this->db->prepare("UPDATE goi_premium SET ten_goi = ?, gia = ?, thoihan_ngay = ?, mieuta = ? WHERE id_goi = ?");
            $stmt->bind_param("sdisi", $data['ten_goi'], $data['gia'], $data['thoihan_ngay'], $data['mieuta'], $data['id_goi']);
        } else {
            // Insert
            $stmt = $this->db->prepare("INSERT INTO goi_premium (ten_goi, gia, thoihan_ngay, mieuta, trangthai) VALUES (?, ?, ?, ?, 'active')");
            $stmt->bind_param("sdis", $data['ten_goi'], $data['gia'], $data['thoihan_ngay'], $data['mieuta']);
        }
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE goi_premium SET trangthai = 'inactive' WHERE id_goi = ? AND trangthai = 'active'");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $success && $affected > 0;
    }

    public function restore($id) {
        // Lấy thông tin gói hiện tại (đang ở trạng thái inactive)
        $goi = $this->getById($id);
        if (!$goi) return false;

        // Kiểm tra xem tên gói có bị trùng với gói nào đang 'active' không
        if (!$this->checkUniqueName($goi['ten_goi'])) {
            // Nếu trùng, không cho khôi phục
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION["error"] = "Không thể khôi phục vì tên gói '" . $goi['ten_goi'] . "' đã tồn tại ở danh sách đang bán.";
            return false;
        }

        $stmt = $this->db->prepare("UPDATE goi_premium SET trangthai = 'active' WHERE id_goi = ? AND trangthai = 'inactive'");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $success && $affected > 0;
    }

    public function copy($id) {
        $goi = $this->getById($id);
        if (!$goi) return false;

        $new_name = $goi['ten_goi'] . " (Copy)";
        
        // Kiểm tra trùng tên cho bản copy mới
        if (!$this->checkUniqueName($new_name)) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION["error"] = "Bản sao '" . $new_name . "' đã tồn tại trong danh sách đang bán.";
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO goi_premium (ten_goi, gia, thoihan_ngay, mieuta, trangthai) VALUES (?, ?, ?, ?, 'active')");
        $stmt->bind_param("sdis", $new_name, $goi['gia'], $goi['thoihan_ngay'], $goi['mieuta']);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function checkUniqueName($name, $excludeId = 0) {
        $sql = "SELECT id_goi FROM goi_premium WHERE ten_goi = ? AND id_goi != ? AND trangthai = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $name, $excludeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows === 0;
    }
}
?>
