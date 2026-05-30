<?php
require_once __DIR__ . "/../Database.php";

function getAll_monhoc_with_user($id_nguoidung, $vaitro)
{
    $conn = Database::connect();
    $list = [];

    $whereClause = ($vaitro === 'admin') ? "1=1" : "m.id_nguoidung = ?";
    
    $sql = "SELECT 
                m.*,
                (SELECT COUNT(*) FROM baithi b WHERE b.id_monhoc = m.id_monhoc) AS so_bai_thi,
                (SELECT COUNT(DISTINCT ch.id_cauhoi) 
                 FROM baithi b 
                 JOIN cauhoi ch ON b.id_baithi = ch.id_baithi 
                 WHERE b.id_monhoc = m.id_monhoc) AS so_cau_hoi,
                (SELECT COUNT(DISTINCT d.id_dapan)
                 FROM baithi b
                 JOIN cauhoi ch ON b.id_baithi = ch.id_baithi
                 JOIN dapan d ON ch.id_cauhoi = d.id_cauhoi
                 WHERE b.id_monhoc = m.id_monhoc) AS so_dap_an,
                (SELECT GROUP_CONCAT(b.ten_baithi SEPARATOR '||') 
                 FROM baithi b 
                 WHERE b.id_monhoc = m.id_monhoc) AS ds_baithi
            FROM monhoc m
            WHERE $whereClause
            ORDER BY m.id_monhoc DESC";

    $stmt = $conn->prepare($sql);
    if ($vaitro !== 'admin') {
        $stmt->bind_param("i", $id_nguoidung);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $list[] = $row;
    }

    $stmt->close();
    // $conn->close();
    return $list;
}

function isDuplicateMonHoc($tenmonhoc, $exclude_id = 0)
{
    $conn = Database::connect();

    $normalize = function ($str) {
        $str = mb_strtolower($str, 'UTF-8');
        return preg_replace('/[^a-z0-9]/', '', $str);
    };

    $search = $normalize($tenmonhoc);

    $sql = "SELECT id_monhoc, tenmonhoc FROM monhoc WHERE id_monhoc != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exclude_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($search === $normalize($row['tenmonhoc'])) {
                $stmt->close();
                // $conn->close();
                return true;
            }
        }
    }

    $stmt->close();
    // $conn->close();
    return false;
}

function insert_monhoc($tenmonhoc, $id_nguoidung, $mieuta = null)
{
    $conn = Database::connect();
    $tenmonhoc = trim($tenmonhoc);
    $mieuta = !empty($mieuta) ? trim($mieuta) : null;

    $sql = "INSERT INTO monhoc (tenmonhoc, id_nguoidung, mieuta, ngaythem) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $tenmonhoc, $id_nguoidung, $mieuta);

    $result = $stmt->execute();
    $stmt->close();
    // $conn->close();
    return $result;
}

function update_monhoc($id, $tenmonhoc, $mieuta = null)
{
    $conn = Database::connect();
    $tenmonhoc = trim($tenmonhoc);
    $mieuta = !empty($mieuta) ? trim($mieuta) : null;

    $sql = "UPDATE monhoc SET tenmonhoc = ?, mieuta = ? WHERE id_monhoc = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $tenmonhoc, $mieuta, $id);

    $result = $stmt->execute();
    $stmt->close();
    // $conn->close();
    return $result;
}

function update_monhoc_by_owner($id_monhoc, $tenmonhoc, $mieuta, $id_nguoidung, $vaitro)
{
    $conn = Database::connect();
    $tenmonhoc = trim($tenmonhoc);
    $mieuta = !empty($mieuta) ? trim($mieuta) : null;

    $sql = "UPDATE monhoc SET tenmonhoc = ?, mieuta = ? WHERE id_monhoc = ? AND id_nguoidung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $tenmonhoc, $mieuta, $id_monhoc, $id_nguoidung);

    $result = $stmt->execute();
    $stmt->close();
    // $conn->close();
    return $result;
}

function delete_monhoc($id_monhoc, $id_nguoidung, $vaitro)
{
    $conn = Database::connect();

    $sql = "DELETE FROM monhoc WHERE id_monhoc = ? AND id_nguoidung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_monhoc, $id_nguoidung);

    $result = $stmt->execute();
    if (!$result) {
        $stmt->close();
        // $conn->close();
        return false;
    }

    $deleted = $stmt->affected_rows > 0;
    $stmt->close();
    // $conn->close();
    return $deleted;
}

function count_baithi_by_monhoc($id_monhoc)
{
    $conn = Database::connect();
    $sql = "SELECT COUNT(*) AS total FROM baithi WHERE id_monhoc = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_monhoc);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    // $conn->close();

    return (int) ($result['total'] ?? 0);
}

function getOne_monhoc($id)
{
    $conn = Database::connect();
    $sql = "SELECT * FROM monhoc WHERE id_monhoc = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    // $conn->close();
    return $result;
}
