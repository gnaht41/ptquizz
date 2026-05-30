<?php

require_once __DIR__ . "/../Database.php";

class NganHangModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function createBank($data)
    {
        $sql = "INSERT INTO nganhang_cauhoi (ten_nganhang, id_mon, id_giangvien, mota, trangthai) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siisi", $data['ten_nganhang'], $data['id_mon'], $data['id_giangvien'], $data['mota'], $data['trangthai']);
        $res = $stmt->execute();
        $id = $this->conn->insert_id;
        $stmt->close();
        return $res ? $id : false;
    }

    public function getAllBanks($id_giangvien)
    {
        $sql = "SELECT nh.*, mh.tenmonhoc 
                FROM nganhang_cauhoi nh
                JOIN monhoc mh ON nh.id_mon = mh.id_monhoc
                WHERE nh.id_giangvien = ? 
                ORDER BY nh.id_nhch DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_giangvien);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    public function getBankById($id_nhch, $id_giangvien)
    {
        $sql = "SELECT * FROM nganhang_cauhoi WHERE id_nhch = ? AND id_giangvien = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id_nhch, $id_giangvien);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    public function addQuestionToBank($id_nhch, $data)
    {
        $this->conn->begin_transaction();
        try {
            // id_baithi is NULL for bank questions
            $sql = "INSERT INTO cauhoi (id_baithi, id_nhch, noidungcauhoi, dokho, ngaytao) VALUES (NULL, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iss", $id_nhch, $data['noidungcauhoi'], $data['dokho']);
            $stmt->execute();
            $id_cauhoi = $this->conn->insert_id;
            $stmt->close();

            foreach ($data['dapan_list'] as $dapan) {
                $sql_d = "INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung) VALUES (?, ?, ?)";
                $stmt_d = $this->conn->prepare($sql_d);
                $stmt_d->bind_param("isi", $id_cauhoi, $dapan['noidungdapan'], $dapan['dapandung']);
                $stmt_d->execute();
                $stmt_d->close();
            }

            $this->conn->commit();
            return $id_cauhoi;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getQuestionsByBank($id_nhch)
    {
        $sql = "SELECT * FROM cauhoi WHERE id_nhch = ? AND id_baithi IS NULL ORDER BY id_cauhoi DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_nhch);
        $stmt->execute();
        $result = $stmt->get_result();
        $questions = [];
        while ($q = $result->fetch_assoc()) {
            // Get answers
            $id_q = $q['id_cauhoi'];
            $sql_a = "SELECT * FROM dapan WHERE id_cauhoi = ?";
            $stmt_a = $this->conn->prepare($sql_a);
            $stmt_a->bind_param("i", $id_q);
            $stmt_a->execute();
            $res_a = $stmt_a->get_result();
            $q['dapan'] = [];
            while ($a = $res_a->fetch_assoc()) {
                $q['dapan'][] = $a;
            }
            $stmt_a->close();
            $questions[] = $q;
        }
        $stmt->close();
        return $questions;
    }

    public function copyQuestionsToExam($id_baithi, $id_nhch, $id_cauhoi_list)
    {
        $this->conn->begin_transaction();
        try {
            foreach ($id_cauhoi_list as $id_old) {
                // Get old question
                $sql = "SELECT * FROM cauhoi WHERE id_cauhoi = ? AND id_nhch = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ii", $id_old, $id_nhch);
                $stmt->execute();
                $q = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$q) continue;

                // Insert new question linked to baithi
                $sql_in = "INSERT INTO cauhoi (id_baithi, id_nhch, noidungcauhoi, dokho, ngaytao) VALUES (?, ?, ?, ?, NOW())";
                $stmt_in = $this->conn->prepare($sql_in);
                $stmt_in->bind_param("iiss", $id_baithi, $id_nhch, $q['noidungcauhoi'], $q['dokho']);
                $stmt_in->execute();
                $id_new = $this->conn->insert_id;
                $stmt_in->close();

                // Clone answers
                $sql_ans = "SELECT * FROM dapan WHERE id_cauhoi = ?";
                $stmt_ans = $this->conn->prepare($sql_ans);
                $stmt_ans->bind_param("i", $id_old);
                $stmt_ans->execute();
                $res_ans = $stmt_ans->get_result();
                while ($ans = $res_ans->fetch_assoc()) {
                    $sql_ain = "INSERT INTO dapan (id_cauhoi, noidungdapan, dapandung) VALUES (?, ?, ?)";
                    $stmt_ain = $this->conn->prepare($sql_ain);
                    $stmt_ain->bind_param("isi", $id_new, $ans['noidungdapan'], $ans['dapandung']);
                    $stmt_ain->execute();
                    $stmt_ain->close();
                }
                $stmt_ans->close();
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
