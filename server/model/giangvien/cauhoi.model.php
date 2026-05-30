<?php
require_once __DIR__ . '/../Database.php';

class CauHoiModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function isBaiThiLocked($id_baithi)
    {
        $sql = "SELECT COUNT(*) as total FROM lanthi WHERE id_baithi = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_baithi);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return (int) ($result['total'] ?? 0) > 0;
    }

    public function checkDuplicate($id_baithi, $noidungcauhoi, $exclude_id = null)
    {
        $sql = "SELECT COUNT(*) as count FROM cauhoi WHERE id_baithi = ? AND noidungcauhoi = ?";
        $params = [$id_baithi, $noidungcauhoi];
        $types = "is";

        if ($exclude_id) {
            $sql .= " AND id_cauhoi != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int) ($row['count'] ?? 0) > 0;
    }

    public function create($id_baithi, $noidungcauhoi, $dokho, $loai_cauhoi, $dapan_list)
    {
        if ($this->isBaiThiLocked($id_baithi)) {
            return ['success' => false, 'message' => 'Bài thi này đã có thí sinh làm, không được phép thêm câu hỏi mới.'];
        }

        if ($this->checkDuplicate($id_baithi, $noidungcauhoi)) {
            return ['success' => false, 'message' => 'Câu hỏi này đã tồn tại trong bài thi!'];
        }

        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO cauhoi (id_baithi, noidungcauhoi, dokho, loai_cauhoi, ngaytao) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("issi", $id_baithi, $noidungcauhoi, $dokho, $loai_cauhoi);
            $stmt->execute();

            $id_cauhoi = $this->conn->insert_id;

            foreach ($dapan_list as $dapan) {
                $sql_d = "INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung, loigiai_chitiet) VALUES (?, ?, ?, ?)";
                $stmt_d = $this->conn->prepare($sql_d);
                $loigiai = $dapan['loigiai_chitiet'] ?? null;
                $stmt_d->bind_param("isis", $id_cauhoi, $dapan['noidung'], $dapan['dapandung'], $loigiai);
                $stmt_d->execute();
            }

            $this->conn->commit();
            return ['success' => true, 'id' => $id_cauhoi];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => "Lỗi: " . $e->getMessage()];
        }
    }

    public function update($id_cauhoi, $noidungcauhoi, $dokho, $loai_cauhoi, $dapan_list)
    {
        $cauhoi = $this->getById($id_cauhoi);
        if (!$cauhoi) {
            return ['success' => false, 'message' => 'Không tìm thấy câu hỏi'];
        }

        if ($this->isBaiThiLocked($cauhoi['id_baithi'])) {
            return ['success' => false, 'message' => 'Không thể sửa vì bài thi này đã có thí sinh làm.'];
        }

        if ($this->checkDuplicate($cauhoi['id_baithi'], $noidungcauhoi, $id_cauhoi)) {
            return ['success' => false, 'message' => 'Câu hỏi này đã tồn tại trong bài thi!'];
        }

        $this->conn->begin_transaction();
        try {
            $sql = "UPDATE cauhoi SET noidungcauhoi = ?, dokho = ?, loai_cauhoi = ? WHERE id_cauhoi = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssii", $noidungcauhoi, $dokho, $loai_cauhoi, $id_cauhoi);
            $stmt->execute();

            $stmt_del = $this->conn->prepare("DELETE FROM dapan WHERE id_cauhoi = ?");
            $stmt_del->bind_param("i", $id_cauhoi);
            $stmt_del->execute();

            foreach ($dapan_list as $dapan) {
                $stmt_ins = $this->conn->prepare("INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung, loigiai_chitiet) VALUES (?, ?, ?, ?)");
                $loigiai = $dapan['loigiai_chitiet'] ?? null;
                $stmt_ins->bind_param("isis", $id_cauhoi, $dapan['noidung'], $dapan['dapandung'], $loigiai);
                $stmt_ins->execute();
            }

            $this->conn->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id_cauhoi)
    {
        $cauhoi = $this->getById($id_cauhoi);
        if (!$cauhoi) {
            return ['success' => false, 'message' => 'Không tìm thấy câu hỏi'];
        }

        if ($this->isBaiThiLocked($cauhoi['id_baithi'])) {
            return ['success' => false, 'message' => 'Không thể xóa vì bài thi này đã có dữ liệu làm bài của thí sinh.'];
        }

        $this->conn->begin_transaction();
        try {
            $stmt1 = $this->conn->prepare("DELETE FROM dapan WHERE id_cauhoi = ?");
            $stmt1->bind_param("i", $id_cauhoi);
            $stmt1->execute();

            $stmt2 = $this->conn->prepare("DELETE FROM cauhoi WHERE id_cauhoi = ?");
            $stmt2->bind_param("i", $id_cauhoi);
            $stmt2->execute();

            $this->conn->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => "Lỗi hệ thống: " . $e->getMessage()];
        }
    }

    public function createMany($id_baithi, $questions)
    {
        if ($this->isBaiThiLocked($id_baithi)) {
            return ['success' => false, 'message' => 'Bài thi này đã có thí sinh làm, không được phép import câu hỏi mới.'];
        }

        $existingQuestions = $this->getByBaiThi($id_baithi);
        $existingCount = count($existingQuestions);
        $examInfo = $this->getBaiThiInfo($id_baithi);
        $maxQuestions = (int) ($examInfo['tongcauhoi'] ?? 0);
        $incomingCount = count($questions);

        if ($incomingCount === 0) {
            return ['success' => false, 'message' => 'Không có câu hỏi hợp lệ để import.'];
        }

        if ($existingCount >= $maxQuestions) {
            return ['success' => false, 'message' => "Bài thi đã đủ {$existingCount}/{$maxQuestions} câu. Không import thêm nữa."];
        }

        if ($existingCount + $incomingCount > $maxQuestions) {
            return ['success' => false, 'message' => "Không thể import {$incomingCount} câu vì bài thi hiện có {$existingCount}/{$maxQuestions} câu. Hệ thống chưa thêm câu nào từ file này."];
        }

        $normalizedExisting = [];
        foreach ($existingQuestions as $item) {
            $normalizedExisting[] = mb_strtolower(trim((string) $item['noidungcauhoi']), 'UTF-8');
        }

        $normalizedIncoming = [];
        $validQuestions = [];
        foreach ($questions as $item) {
            $normalized = mb_strtolower(trim((string) ($item['noidungcauhoi'] ?? '')), 'UTF-8');
            if ($normalized === '') {
                continue; // Skip empty questions
            }
            // If duplicate found in database or in the same word file, just skip it
            if (in_array($normalized, $normalizedExisting, true) || in_array($normalized, $normalizedIncoming, true)) {
                continue;
            }
            $normalizedIncoming[] = $normalized;
            $validQuestions[] = $item;
        }

        $this->conn->begin_transaction();
        try {
            foreach ($validQuestions as $question) {
                $stmt = $this->conn->prepare("INSERT INTO cauhoi (id_baithi, noidungcauhoi, dokho, loai_cauhoi, ngaytao) VALUES (?, ?, ?, ?, NOW())");
                $loai = !empty($question['loai_cauhoi']) ? (int)$question['loai_cauhoi'] : 1;
                $stmt->bind_param("issi", $id_baithi, $question['noidungcauhoi'], $question['dokho'], $loai);
                $stmt->execute();
                $id_cauhoi = $this->conn->insert_id;

                foreach ($question['dapan_list'] as $dapan) {
                    $stmtAnswer = $this->conn->prepare("INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung, loigiai_chitiet) VALUES (?, ?, ?, ?)");
                    $loigiai = $dapan['loigiai_chitiet'] ?? null;
                    $stmtAnswer->bind_param("isis", $id_cauhoi, $dapan['noidung'], $dapan['dapandung'], $loigiai);
                    $stmtAnswer->execute();
                }
            }

            $this->conn->commit();
            return ['success' => true, 'count' => count($validQuestions)];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Lỗi import: ' . $e->getMessage()];
        }
    }

    public function getByBaiThi($id_baithi)
    {
        $sql = "SELECT * FROM cauhoi WHERE id_baithi = ? ORDER BY id_cauhoi ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_baithi);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = [];
        while ($row = $result->fetch_assoc()) {
            $row['dapan'] = $this->getDapAnByCauHoi($row['id_cauhoi']);
            $list[] = $row;
        }
        return $list;
    }

    public function getDapAnByCauHoi($id_cauhoi)
    {
        $sql = "SELECT * FROM dapan WHERE id_cauhoi = ? ORDER BY id_dapan ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_cauhoi);
        $stmt->execute();
        $result = $stmt->get_result();
        $dapan = [];
        while ($row = $result->fetch_assoc()) {
            $dapan[] = $row;
        }
        return $dapan;
    }

    public function getById($id_cauhoi)
    {
        $sql = "SELECT * FROM cauhoi WHERE id_cauhoi = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_cauhoi);
        $stmt->execute();
        $cauhoi = $stmt->get_result()->fetch_assoc();
        if ($cauhoi) {
            $cauhoi['dapan'] = $this->getDapAnByCauHoi($id_cauhoi);
        }
        return $cauhoi;
    }

    public function getBaiThiInfo($id_baithi)
    {
        $sql = "SELECT bt.*, mh.tenmonhoc
                FROM baithi bt
                LEFT JOIN monhoc mh ON bt.id_monhoc = mh.id_monhoc
                WHERE bt.id_baithi = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_baithi);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function importFromBank($id_baithi, $id_nhch, $counts)
    {
        if ($this->isBaiThiLocked($id_baithi)) {
            return ['success' => false, 'message' => 'Bài thi này đã có thí sinh làm.'];
        }

        $examInfo = $this->getBaiThiInfo($id_baithi);
        $maxQuestions = (int) ($examInfo['tongcauhoi'] ?? 0);
        $currentQuestions = $this->getByBaiThi($id_baithi);
        $currentCount = count($currentQuestions);
        
        $totalToImport = array_sum($counts);
        if ($currentCount + $totalToImport > $maxQuestions) {
            return ['success' => false, 'message' => "Tổng số câu hỏi sẽ vượt quá giới hạn {$maxQuestions} câu của bài thi."];
        }

        $this->conn->begin_transaction();
        try {
            $importedCount = 0;
            foreach ($counts as $dokho => $count) {
                if ($count <= 0) continue;
                
                // Map friendly names to DB keys to search BOTH formats (e.g. 'Dễ' and 'de')
                $searchKey1 = $dokho;
                $searchKey2 = $dokho;
                if ($dokho === 'de' || $dokho === 'Dễ') { $searchKey1 = 'Dễ'; $searchKey2 = 'de'; }
                else if ($dokho === 'trungbinh' || $dokho === 'Trung bình') { $searchKey1 = 'Trung bình'; $searchKey2 = 'trungbinh'; }
                else if ($dokho === 'kho' || $dokho === 'Khó') { $searchKey1 = 'Khó'; $searchKey2 = 'kho'; }

                // Do not limit the query to $count initially because some might be duplicates.
                // Select all matching questions in random order, then take until we reach $count.
                $sql = "SELECT * FROM cauhoi WHERE id_nhch = ? AND id_baithi IS NULL AND (dokho = ? OR dokho = ?) AND trangthai = 'active' ORDER BY RAND()";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iss", $id_nhch, $searchKey1, $searchKey2);
                $stmt->execute();
                $res = $stmt->get_result();

                $addedForThisDifficulty = 0;
                while ($q = $res->fetch_assoc()) {
                    if ($addedForThisDifficulty >= $count) break;

                    // Check if question already exists in exam (to avoid duplicates if importing multiple times)
                    if ($this->checkDuplicate($id_baithi, $q['noidungcauhoi'])) continue;

                    // Copy question
                    $sql_iq = "INSERT INTO cauhoi (id_baithi, id_nhch, noidungcauhoi, dokho, loai_cauhoi, ngaytao) VALUES (?, ?, ?, ?, ?, NOW())";
                    $stmt_iq = $this->conn->prepare($sql_iq);
                    $stmt_iq->bind_param("iissi", $id_baithi, $id_nhch, $q['noidungcauhoi'], $q['dokho'], $q['loai_cauhoi']);
                    $stmt_iq->execute();
                    $id_new = $this->conn->insert_id;

                    // Copy answers
                    $sql_ans = "SELECT * FROM dapan WHERE id_cauhoi = ?";
                    $stmt_ans = $this->conn->prepare($sql_ans);
                    $stmt_ans->bind_param("i", $q['id_cauhoi']);
                    $stmt_ans->execute();
                    $res_ans = $stmt_ans->get_result();
                    while ($ans = $res_ans->fetch_assoc()) {
                        $sql_ia = "INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung, loigiai_chitiet) VALUES (?, ?, ?, ?)";
                        $stmt_ia = $this->conn->prepare($sql_ia);
                        $stmt_ia->bind_param("isis", $id_new, $ans['noidungdapan'], $ans['dapandung'], $ans['loigiai_chitiet']);
                        $stmt_ia->execute();
                    }
                    $importedCount++;
                    $addedForThisDifficulty++;
                }
            }

            $this->conn->commit();
            return ['success' => true, 'count' => $importedCount];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
