<?php
require_once __DIR__ . '/../Database.php';

function getAll_baithi($id_nguoidung)
{
    $conn = Database::connect();
    $sql = "SELECT bt.*, mh.tenmonhoc
            FROM baithi bt
            JOIN monhoc mh ON bt.id_monhoc = mh.id_monhoc
            WHERE mh.id_nguoidung = ?
            ORDER BY bt.id_baithi DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_nguoidung);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // $conn->close();
    return $data;
}

function getAll_monhoc($id_nguoidung)
{
    $conn = Database::connect();
    $sql = "SELECT id_monhoc, tenmonhoc FROM monhoc WHERE id_nguoidung = ? ORDER BY tenmonhoc ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_nguoidung);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // $conn->close();
    return $data;
}

function isBaiThiLocked($id_baithi)
{
    $conn = Database::connect();
    $sql = "SELECT COUNT(*) AS total FROM lanthi WHERE id_baithi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_baithi);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    // $conn->close();

    return isset($result['total']) && (int) $result['total'] > 0;
}

function save_baithi($data)
{
    $conn = Database::connect();

    $id_baithi = !empty($data['id_baithi']) ? (int) $data['id_baithi'] : 0;
    
    // Explicit check for toggles
    $only_toggle = !empty($data['only_toggle']);
    
    if ($only_toggle && $id_baithi > 0) {
        $updates = [];
        $params = [];
        $types = "";
        
        if (isset($data['xao_tron'])) {
            $updates[] = "xao_tron = ?";
            $params[] = $data['xao_tron'] ? 1 : 0;
            $types .= "i";
        }
        if (isset($data['hien_dapan'])) {
            $updates[] = "hien_dapan = ?";
            $params[] = $data['hien_dapan'] ? 1 : 0;
            $types .= "i";
        }
        
        if (empty($updates)) {
            // $conn->close();
            return true;
        }
        
        $sql = "UPDATE baithi SET " . implode(", ", $updates) . " WHERE id_baithi = ?";
        $params[] = $id_baithi;
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $res = $stmt->execute();
        // $conn->close();
        return $res;
    }

    $id_monhoc = isset($data['id_monhoc']) ? (int) $data['id_monhoc'] : 0;
    $xao_tron = isset($data['xao_tron']) ? ($data['xao_tron'] ? 1 : 0) : 0;
    $hien_dapan = isset($data['hien_dapan']) ? ($data['hien_dapan'] ? 1 : 0) : 0;

    $ten = trim($data['ten_baithi']);
    $ten = mb_strtolower($ten, 'UTF-8');
    $ten = mb_ucfirst($ten);

    $tongcauhoi = abs((int) ($data['tongcauhoi'] ?? 0));
    $thoigianlam = abs((int) ($data['thoigianlam'] ?? 0));
    $trangthai = !empty($data['trangthai']) ? $data['trangthai'] : "Đang mở";
    $mota = !empty($data['mota']) ? $data['mota'] : (!empty($data['mieuta']) ? $data['mieuta'] : null);

    $tg_ketthuc = !empty($data['thoigianketthuc']) ? $data['thoigianketthuc'] : null;
    $tg_batdau = !empty($data['thoigianbatdau']) ? $data['thoigianbatdau'] : null;

    if ($id_baithi > 0 && isBaiThiLocked($id_baithi)) {
        // Nếu đã khóa, chỉ cho phép cập nhật các trường không ảnh hưởng đến nội dung bài thi
        // Giả sử tên cột trong DB là mieuta (theo code gốc dòng 140)
        $sql = "UPDATE baithi 
                SET ten_baithi = ?, mieuta = ?, thoigianlam = ?, thoigianbatdau = ?, thoigianketthuc = ?, trangthai = ?, xao_tron = ?, hien_dapan = ?
                WHERE id_baithi = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssiii", $ten, $mota, $thoigianlam, $tg_batdau, $tg_ketthuc, $trangthai, $xao_tron, $hien_dapan, $id_baithi);
        $res = $stmt->execute();
        // $conn->close();
        return $res;
    }

    $sql_check = "SELECT id_baithi FROM baithi WHERE REPLACE(ten_baithi, ' ', '') = REPLACE(?, ' ', '') AND id_baithi != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $ten, $id_baithi);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Bài thi '$ten' đã tồn tại (hoặc tương tự)!";
        // $conn->close();
        return false;
    }

    if ($id_baithi === 0) {
        $sql = "INSERT INTO baithi (id_monhoc, ten_baithi, mieuta, tongcauhoi, thoigianlam, thoigianbatdau, thoigianketthuc, trangthai, xao_tron, hien_dapan, ngaytao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issiisssii", $id_monhoc, $ten, $mota, $tongcauhoi, $thoigianlam, $tg_batdau, $tg_ketthuc, $trangthai, $xao_tron, $hien_dapan);
    } else {
        $sql = "UPDATE baithi
                SET id_monhoc = ?, ten_baithi = ?, mieuta = ?, tongcauhoi = ?, thoigianlam = ?, thoigianbatdau = ?, thoigianketthuc = ?, trangthai = ?, xao_tron = ?, hien_dapan = ?
                WHERE id_baithi = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issiisssiii", $id_monhoc, $ten, $mota, $tongcauhoi, $thoigianlam, $tg_batdau, $tg_ketthuc, $trangthai, $xao_tron, $hien_dapan, $id_baithi);
    }

    $res = $stmt->execute();
    if (!$res) {
        $_SESSION['error'] = "Lỗi Database: " . $conn->error;
    }

    // $conn->close();
    return $res;
}

function delete_baithi($id)
{
    $conn = Database::connect();
    $conn->begin_transaction();

    try {
        $questionIds = [];
        $stmtQuestions = $conn->prepare("SELECT id_cauhoi FROM cauhoi WHERE id_baithi = ?");
        $stmtQuestions->bind_param("i", $id);
        $stmtQuestions->execute();
        $resultQuestions = $stmtQuestions->get_result();
        while ($row = $resultQuestions->fetch_assoc()) {
            $questionIds[] = (int) $row['id_cauhoi'];
        }

        $lanthiIds = [];
        $stmtLanthi = $conn->prepare("SELECT id_lanthi FROM lanthi WHERE id_baithi = ?");
        $stmtLanthi->bind_param("i", $id);
        $stmtLanthi->execute();
        $resultLanthi = $stmtLanthi->get_result();
        while ($row = $resultLanthi->fetch_assoc()) {
            $lanthiIds[] = (int) $row['id_lanthi'];
        }

        foreach ($lanthiIds as $id_lanthi) {
            $stmt = $conn->prepare("DELETE FROM traloithisinh WHERE id_lanthi = ?");
            $stmt->bind_param("i", $id_lanthi);
            $stmt->execute();
        }

        foreach ($questionIds as $id_cauhoi) {
            $stmt = $conn->prepare("DELETE FROM traloithisinh WHERE id_cauhoi = ?");
            $stmt->bind_param("i", $id_cauhoi);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM dapan WHERE id_cauhoi = ?");
            $stmt->bind_param("i", $id_cauhoi);
            $stmt->execute();
        }

        $stmtDeleteQuestions = $conn->prepare("DELETE FROM cauhoi WHERE id_baithi = ?");
        $stmtDeleteQuestions->bind_param("i", $id);
        $stmtDeleteQuestions->execute();

        $stmtDeleteLanthi = $conn->prepare("DELETE FROM lanthi WHERE id_baithi = ?");
        $stmtDeleteLanthi->bind_param("i", $id);
        $stmtDeleteLanthi->execute();

        $stmtDeleteExam = $conn->prepare("DELETE FROM baithi WHERE id_baithi = ?");
        $stmtDeleteExam->bind_param("i", $id);
        $res = $stmtDeleteExam->execute();

        $conn->commit();
        // $conn->close();
        return $res;
    } catch (Throwable $e) {
        $conn->rollback();
        $_SESSION['error'] = "Lỗi Database: " . $e->getMessage();
        // $conn->close();
        return false;
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($str, $encoding = "UTF-8")
    {
        $firstChar = mb_substr($str, 0, 1, $encoding);
        $then = mb_substr($str, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }
}
