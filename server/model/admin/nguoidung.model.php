<?php

require_once __DIR__ . "/../Database.php";

function userManageAllowedRoles()
{
    return ["thisinh", "giangvien"];
}

function normalizeManagedRole($role)
{
    $role = trim((string) $role);
    return in_array($role, userManageAllowedRoles(), true) ? $role : null;
}

function normalizeUserStatus($status)
{
    $status = trim((string) $status);
    return in_array($status, ["active", "inactive"], true) ? $status : null;
}

function getManagedUsers($filters = [])
{
    $conn = Database::connect();
    $list = [];

    $sql = "SELECT id_nguoidung, email, ten, vaitro, trangthai, ngaytao
            FROM nguoidung
            WHERE vaitro IN ('thisinh', 'giangvien')";

    $params = [];
    $types = "";

    $role = normalizeManagedRole($filters["vaitro"] ?? "");
    if ($role !== null) {
        $sql .= " AND vaitro = ?";
        $types .= "s";
        $params[] = $role;
    }

    $status = normalizeUserStatus($filters["trangthai"] ?? "");
    if ($status !== null) {
        $sql .= " AND trangthai = ?";
        $types .= "s";
        $params[] = $status;
    }

    $keyword = trim((string) ($filters["keyword"] ?? ""));
    if ($keyword !== "") {
        $like = "%" . $keyword . "%";
        $sql .= " AND (ten LIKE ? OR email LIKE ?)";
        $types .= "ss";
        $params[] = $like;
        $params[] = $like;
    }

    $sql .= " ORDER BY id_nguoidung DESC";

    $stmt = $conn->prepare($sql);
    if ($types !== "") {
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

function getManagedUserById($id_nguoidung)
{
    $conn = Database::connect();
    $sql = "SELECT id_nguoidung, email, ten, vaitro, trangthai, ngaytao
            FROM nguoidung
            WHERE id_nguoidung = ? AND vaitro IN ('thisinh', 'giangvien')
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_nguoidung);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    // $conn->close();

    return $user ?: null;
}

function isUserEmailExists($email, $excludeId = 0)
{
    $conn = Database::connect();
    $sql = "SELECT id_nguoidung FROM nguoidung WHERE email = ? AND id_nguoidung != ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $excludeId);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_assoc();
    $stmt->close();
    // $conn->close();

    return $exists;
}

function createManagedUser($email, $matkhau, $ten, $vaitro, $trangthai = "active")
{
    $conn = Database::connect();
    $hashedPassword = password_hash($matkhau, PASSWORD_DEFAULT);
    $sql = "INSERT INTO nguoidung (email, matkhau, ten, vaitro, trangthai, ngaytao, avatar)
            VALUES (?, ?, ?, ?, ?, NOW(), 'default.jpg')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $email, $hashedPassword, $ten, $vaitro, $trangthai);
    $ok = $stmt->execute();
    $newId = $conn->insert_id;
    $stmt->close();
    // $conn->close();

    return $ok ? $newId : 0;
}

function updateManagedUser($id_nguoidung, $email, $ten, $vaitro, $trangthai, $matkhau = "")
{
    $conn = Database::connect();
    $matkhau = trim((string) $matkhau);

    if ($matkhau !== "") {
        $hashedPassword = password_hash($matkhau, PASSWORD_DEFAULT);
        $sql = "UPDATE nguoidung
                SET email = ?, ten = ?, vaitro = ?, trangthai = ?, matkhau = ?
                WHERE id_nguoidung = ? AND vaitro IN ('thisinh', 'giangvien')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $email, $ten, $vaitro, $trangthai, $hashedPassword, $id_nguoidung);
    } else {
        $sql = "UPDATE nguoidung
                SET email = ?, ten = ?, vaitro = ?, trangthai = ?
                WHERE id_nguoidung = ? AND vaitro IN ('thisinh', 'giangvien')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $email, $ten, $vaitro, $trangthai, $id_nguoidung);
    }

    $ok = $stmt->execute();
    $affected = $stmt->affected_rows >= 0;
    $stmt->close();
    // $conn->close();

    return $ok && $affected;
}

function softDeleteManagedUser($id_nguoidung)
{
    $conn = Database::connect();
    $sql = "UPDATE nguoidung
            SET trangthai = 'inactive'
            WHERE id_nguoidung = ? AND vaitro IN ('thisinh', 'giangvien')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_nguoidung);
    $ok = $stmt->execute();
    $affected = $stmt->affected_rows >= 0;
    $stmt->close();
    // $conn->close();

    return $ok && $affected;
}
