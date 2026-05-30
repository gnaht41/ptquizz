<?php

require_once __DIR__ . "/../Database.php";

function getAccessibleBankSubjects($id_nguoidung, $vaitro)
{
    $conn = Database::connect();
    $list = [];

    if ($vaitro === 'admin') {
        $sql = "SELECT m.id_monhoc, m.tenmonhoc, m.id_nguoidung, n.ten
                FROM monhoc m
                LEFT JOIN nguoidung n ON m.id_nguoidung = n.id_nguoidung
                ORDER BY m.tenmonhoc ASC";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT m.id_monhoc, m.tenmonhoc, m.id_nguoidung, n.ten
                FROM monhoc m
                LEFT JOIN nguoidung n ON m.id_nguoidung = n.id_nguoidung
                WHERE m.id_nguoidung = ?
                ORDER BY m.tenmonhoc ASC";
        $stmt = $conn->prepare($sql);
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

function getQuestionBanks($id_nguoidung, $vaitro, $id_monhoc = 0, $onlyActive = false)
{
    $conn = Database::connect();
    $list = [];

    // Use aliases to maintain compatibility with the old frontend JS
    $sql = "SELECT nh.id_nhch AS id_nganhang, 
                   nh.ten_nganhang, 
                   nh.mota AS mieuta, 
                   nh.id_giangvien AS id_nguoidung, 
                   CASE WHEN nh.trangthai = 1 THEN 'active' ELSE 'inactive' END AS trangthai,
                   nh.ngaytao,
                   u.ten AS ten_nguoidung,
                   1 AS so_monhoc,
                   (SELECT COUNT(*) FROM cauhoi ch WHERE ch.id_nhch = nh.id_nhch AND ch.id_baithi IS NULL) AS so_cauhoi,
                   m.tenmonhoc AS ds_monhoc,
                   m.tenmonhoc
            FROM nganhang_cauhoi nh
            LEFT JOIN nguoidung u ON nh.id_giangvien = u.id_nguoidung
            LEFT JOIN monhoc m ON nh.id_mon = m.id_monhoc
            WHERE 1=1";

    $params = [];
    $types = "";

    if ($vaitro !== 'admin') {
        $sql .= " AND nh.id_giangvien = ?";
        $params[] = $id_nguoidung;
        $types .= "i";
    }

    if ($id_monhoc > 0) {
        $sql .= " AND nh.id_mon = ?";
        $params[] = $id_monhoc;
        $types .= "i";
    }

    if ($onlyActive) {
        $sql .= " AND nh.trangthai = 1";
    }

    $sql .= " ORDER BY nh.id_nhch DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
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

function getQuestionBankById($id_nganhang, $id_nguoidung, $vaitro)
{
    $conn = Database::connect();
    $sql = "SELECT nh.id_nhch AS id_nganhang, 
                   nh.ten_nganhang, 
                   nh.mota AS mieuta, 
                   nh.id_giangvien AS id_nguoidung, 
                   CASE WHEN nh.trangthai = 1 THEN 'active' ELSE 'inactive' END AS trangthai,
                   u.ten AS ten_nguoidung
            FROM nganhang_cauhoi nh
            LEFT JOIN nguoidung u ON nh.id_giangvien = u.id_nguoidung
            WHERE nh.id_nhch = ?";

    if ($vaitro !== 'admin') {
        $sql .= " AND nh.id_giangvien = ?";
    }

    $sql .= " LIMIT 1";

    $stmt = $conn->prepare($sql);
    if ($vaitro !== 'admin') {
        $stmt->bind_param("ii", $id_nganhang, $id_nguoidung);
    } else {
        $stmt->bind_param("i", $id_nganhang);
    }
    $stmt->execute();
    $bank = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$bank) {
        // $conn->close();
        return null;
    }

    // New schema only has one subject per bank
    $stmtSubjects = $conn->prepare("SELECT m.id_monhoc, m.tenmonhoc
                                    FROM nganhang_cauhoi nh
                                    JOIN monhoc m ON nh.id_mon = m.id_monhoc
                                    WHERE nh.id_nhch = ?");
    $stmtSubjects->bind_param("i", $id_nganhang);
    $stmtSubjects->execute();
    $resSubjects = $stmtSubjects->get_result();
    $bank['subjects'] = [];
    while ($row = $resSubjects->fetch_assoc()) {
        $bank['subjects'][] = $row;
    }
    $stmtSubjects->close();

    // $conn->close();
    return $bank;
}

function validateBankSubjects($subjectIds, $id_nguoidung, $vaitro)
{
    $subjectIds = array_values(array_unique(array_map('intval', $subjectIds)));
    $subjectIds = array_values(array_filter($subjectIds, fn($id) => $id > 0));

    if (empty($subjectIds)) {
        return [];
    }

    $conn = Database::connect();
    $placeholders = implode(',', array_fill(0, count($subjectIds), '?'));

    if ($vaitro === 'admin') {
        $sql = "SELECT id_monhoc FROM monhoc WHERE id_monhoc IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('i', count($subjectIds));
        $stmt->bind_param($types, ...$subjectIds);
    } else {
        // Only allow subjects belonging to the lecturer
        $sql = "SELECT id_monhoc FROM monhoc WHERE id_monhoc IN ($placeholders) AND id_nguoidung = ?";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('i', count($subjectIds)) . 'i';
        $params = array_merge($subjectIds, [$id_nguoidung]);
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $validIds = [];
    while ($row = $result->fetch_assoc()) {
        $validIds[] = (int) $row['id_monhoc'];
    }

    $stmt->close();
    // $conn->close();
    
    // Return the list of valid IDs found in the DB
    return $validIds;
}

function createQuestionBank($ten_nganhang, $mieuta, $subjectIds, $id_nguoidung)
{
    $conn = Database::connect();
    $id_mon = !empty($subjectIds) ? (int) $subjectIds[0] : 0;

    try {
        $stmt = $conn->prepare("INSERT INTO nganhang_cauhoi (ten_nganhang, mota, id_mon, id_giangvien, trangthai, ngaytao)
                                VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->bind_param("ssii", $ten_nganhang, $mieuta, $id_mon, $id_nguoidung);
        $stmt->execute();
        $id_nganhang = (int) $conn->insert_id;
        $stmt->close();
        // $conn->close();
        return $id_nganhang;
    } catch (Exception $e) {
        // $conn->close();
        return 0;
    }
}

function updateQuestionBank($id_nganhang, $ten_nganhang, $mieuta, $trangthai, $subjectIds)
{
    $conn = Database::connect();
    $id_mon = !empty($subjectIds) ? (int) $subjectIds[0] : 0;
    $statusNum = ($trangthai === 'active') ? 1 : 0;

    try {
        $stmt = $conn->prepare("UPDATE nganhang_cauhoi
                                SET ten_nganhang = ?, mota = ?, id_mon = ?, trangthai = ?
                                WHERE id_nhch = ?");
        $stmt->bind_param("ssiii", $ten_nganhang, $mieuta, $id_mon, $statusNum, $id_nganhang);
        $stmt->execute();
        $stmt->close();
        // $conn->close();
        return true;
    } catch (Exception $e) {
        // $conn->close();
        return false;
    }
}

function deleteQuestionBank($id_nganhang)
{
    $conn = Database::connect();
    $conn->begin_transaction();

    try {
        // 1. Get all question IDs in this bank (where id_baithi is NULL)
        $qIds = [];
        $res = $conn->query("SELECT id_cauhoi FROM cauhoi WHERE id_nhch = $id_nganhang AND id_baithi IS NULL");
        while($r = $res->fetch_assoc()) $qIds[] = $r['id_cauhoi'];

        if (!empty($qIds)) {
            $idsStr = implode(',', $qIds);
            // 2. Delete answers
            $conn->query("DELETE FROM dapan WHERE id_cauhoi IN ($idsStr)");
            // 3. Delete questions
            $conn->query("DELETE FROM cauhoi WHERE id_cauhoi IN ($idsStr)");
        }

        // 4. Delete the bank itself
        $stmt = $conn->prepare("DELETE FROM nganhang_cauhoi WHERE id_nhch = ?");
        $stmt->bind_param("i", $id_nganhang);
        $stmt->execute();

        $conn->commit();
        // $conn->close();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        // $conn->close();
        return false;
    }
}

function getQuestionBankQuestions($id_nganhang, $id_monhoc, $id_nguoidung, $vaitro)
{
    $conn = Database::connect();
    $questions = [];

    // Use cauhoi table for bank questions (id_baithi IS NULL)
    $sql = "SELECT ch.id_cauhoi AS id_cauhoi_nganhang, 
                   ch.noidungcauhoi, 
                   ch.dokho, 
                   ch.loai_cauhoi, 
                   ch.trangthai,
                   ch.id_nhch AS id_nganhang
            FROM cauhoi ch
            JOIN nganhang_cauhoi nh ON ch.id_nhch = nh.id_nhch
            WHERE ch.id_nhch = ? AND ch.id_baithi IS NULL";
    
    if ($vaitro !== 'admin') {
        $sql .= " AND nh.id_giangvien = ?";
    }
    $sql .= " ORDER BY ch.id_cauhoi DESC";

    $stmt = $conn->prepare($sql);
    if ($vaitro !== 'admin') {
        $stmt->bind_param("ii", $id_nganhang, $id_nguoidung);
    } else {
        $stmt->bind_param("i", $id_nganhang);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $row['dapan'] = getBankAnswersByQuestionIdWithConnection($conn, (int) $row['id_cauhoi_nganhang']);
        $questions[] = $row;
    }

    $stmt->close();
    // $conn->close();
    return $questions;
}

function getBankAnswersByQuestionIdWithConnection($conn, $id_cauhoi_nganhang)
{
    // Use the base dapan table
    $stmt = $conn->prepare("SELECT id_dapan AS id_dapan_nganhang, noidungdapan, dapandung, loigiai_chitiet 
                            FROM dapan 
                            WHERE id_cauhoi = ? 
                            ORDER BY id_dapan ASC");
    $stmt->bind_param("i", $id_cauhoi_nganhang);
    $stmt->execute();
    $result = $stmt->get_result();
    $answers = [];
    while ($row = $result->fetch_assoc()) {
        $answers[] = $row;
    }
    $stmt->close();
    return $answers;
}

function getQuestionBankQuestionById($id_cauhoi_nganhang, $id_nguoidung, $vaitro)
{
    $conn = Database::connect();
    $sql = "SELECT ch.id_cauhoi AS id_cauhoi_nganhang, 
                    ch.noidungcauhoi, 
                    ch.dokho, 
                    ch.loai_cauhoi, 
                    ch.trangthai,
                    ch.id_nhch AS id_nganhang
            FROM cauhoi ch
            JOIN nganhang_cauhoi nh ON ch.id_nhch = nh.id_nhch
            WHERE ch.id_cauhoi = ? AND ch.id_baithi IS NULL";
    
    if ($vaitro !== 'admin') {
        $sql .= " AND nh.id_giangvien = ?";
    }
    $sql .= " LIMIT 1";

    $stmt = $conn->prepare($sql);
    if ($vaitro !== 'admin') {
        $stmt->bind_param("ii", $id_cauhoi_nganhang, $id_nguoidung);
    } else {
        $stmt->bind_param("i", $id_cauhoi_nganhang);
    }
    $stmt->execute();
    $question = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($question) {
        $question['dapan'] = getBankAnswersByQuestionIdWithConnection($conn, $id_cauhoi_nganhang);
    }

    // $conn->close();
    return $question ?: null;
}

function createQuestionBankQuestion($id_nganhang, $id_monhoc, $noidungcauhoi, $dokho, $loai_cauhoi, $trangthai, $answers)
{
    $conn = Database::connect();
    $conn->begin_transaction();

    try {
        // Check for duplicate
        $stmtCheck = $conn->prepare("SELECT id_cauhoi FROM cauhoi WHERE id_nhch = ? AND noidungcauhoi = ? AND id_baithi IS NULL");
        $stmtCheck->bind_param("is", $id_nganhang, $noidungcauhoi);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        if ($resCheck->num_rows > 0) {
            $stmtCheck->close();
            $conn->rollback();
            // $conn->close();
            return 0; // Duplicate exists
        }
        $stmtCheck->close();

        // id_baithi is NULL for bank questions
        $stmt = $conn->prepare("INSERT INTO cauhoi (id_baithi, id_nhch, noidungcauhoi, dokho, loai_cauhoi, trangthai, ngaytao)
                                VALUES (NULL, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issis", $id_nganhang, $noidungcauhoi, $dokho, $loai_cauhoi, $trangthai);
        $stmt->execute();
        $id_cauhoi = (int) $conn->insert_id;
        $stmt->close();

        foreach ($answers as $answer) {
            $stmtAnswer = $conn->prepare("INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung, loigiai_chitiet)
                                          VALUES (?, ?, ?, ?)");
            $loigiai = !empty($answer['loigiai_chitiet']) ? $answer['loigiai_chitiet'] : null;
            $stmtAnswer->bind_param("isis", $id_cauhoi, $answer['noidung'], $answer['dapandung'], $loigiai);
            $stmtAnswer->execute();
            $stmtAnswer->close();
        }

        $conn->commit();
        // $conn->close();
        return $id_cauhoi;
    } catch (Exception $e) {
        $conn->rollback();
        // $conn->close();
        return 0;
    }
}

function updateQuestionBankQuestion($id_cauhoi_nganhang, $id_monhoc, $noidungcauhoi, $dokho, $loai_cauhoi, $trangthai, $answers)
{
    $conn = Database::connect();
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE cauhoi
                                SET noidungcauhoi = ?, dokho = ?, loai_cauhoi = ?, trangthai = ?
                                WHERE id_cauhoi = ?");
        $stmt->bind_param("ssisi", $noidungcauhoi, $dokho, $loai_cauhoi, $trangthai, $id_cauhoi_nganhang);
        $stmt->execute();
        $stmt->close();

        $stmtDelete = $conn->prepare("DELETE FROM dapan WHERE id_cauhoi = ?");
        $stmtDelete->bind_param("i", $id_cauhoi_nganhang);
        $stmtDelete->execute();
        $stmtDelete->close();

        foreach ($answers as $answer) {
            $stmtAnswer = $conn->prepare("INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung, loigiai_chitiet)
                                          VALUES (?, ?, ?, ?)");
            $loigiai = !empty($answer['loigiai_chitiet']) ? $answer['loigiai_chitiet'] : null;
            $stmtAnswer->bind_param("isis", $id_cauhoi_nganhang, $answer['noidung'], $answer['dapandung'], $loigiai);
            $stmtAnswer->execute();
            $stmtAnswer->close();
        }

        $conn->commit();
        // $conn->close();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        // $conn->close();
        return false;
    }
}

function deleteQuestionBankQuestion($id_cauhoi_nganhang)
{
    $conn = Database::connect();
    $conn->begin_transaction();

    try {
        // Only delete if it's truly a bank question (id_baithi IS NULL)
        // 1. Delete answers
        $stmt1 = $conn->prepare("DELETE FROM dapan WHERE id_cauhoi = ? AND EXISTS (SELECT 1 FROM cauhoi WHERE id_cauhoi = ? AND id_baithi IS NULL)");
        $stmt1->bind_param("ii", $id_cauhoi_nganhang, $id_cauhoi_nganhang);
        $stmt1->execute();

        // 2. Delete question
        $stmt2 = $conn->prepare("DELETE FROM cauhoi WHERE id_cauhoi = ? AND id_baithi IS NULL");
        $stmt2->bind_param("i", $id_cauhoi_nganhang);
        $stmt2->execute();

        $conn->commit();
        // $conn->close();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        // $conn->close();
        return false;
    }
}

function createManyInBank($id_nhch, $questions)
{
    $conn = Database::connect();
    $conn->begin_transaction();

    try {
        $count = 0;
        foreach ($questions as $q) {
            // Check for duplicate
            $stmtCheck = $conn->prepare("SELECT id_cauhoi FROM cauhoi WHERE id_nhch = ? AND noidungcauhoi = ? AND id_baithi IS NULL");
            $stmtCheck->bind_param("is", $id_nhch, $q['noidungcauhoi']);
            $stmtCheck->execute();
            $resCheck = $stmtCheck->get_result();
            if ($resCheck->num_rows > 0) {
                $stmtCheck->close();
                continue; // Skip duplicate
            }
            $stmtCheck->close();

            $stmt = $conn->prepare("INSERT INTO cauhoi (id_baithi, id_nhch, noidungcauhoi, dokho, loai_cauhoi, ngaytao)
                                    VALUES (NULL, ?, ?, ?, ?, NOW())");
            $loai = !empty($q['loai_cauhoi']) ? (int)$q['loai_cauhoi'] : 1;
            $stmt->bind_param("issi", $id_nhch, $q['noidungcauhoi'], $q['dokho'], $loai);
            $stmt->execute();
            $id_cauhoi = (int) $conn->insert_id;
            $stmt->close();

            foreach ($q['dapan_list'] as $dapan) {
                $stmtAnswer = $conn->prepare("INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung, loigiai_chitiet)
                                              VALUES (?, ?, ?, ?)");
                $loigiai = !empty($dapan['loigiai_chitiet']) ? $dapan['loigiai_chitiet'] : null;
                $stmtAnswer->bind_param("isis", $id_cauhoi, $dapan['noidung'], $dapan['dapandung'], $loigiai);
                $stmtAnswer->execute();
                $stmtAnswer->close();
            }
            $count++;
        }

        $conn->commit();
        // $conn->close();
        return ["success" => true, "count" => $count];
    } catch (Exception $e) {
        $conn->rollback();
        // $conn->close();
        return ["success" => false, "message" => $e->getMessage()];
    }
}

