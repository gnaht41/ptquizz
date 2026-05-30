<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";


$conn = Database::connect();
$res = $conn->query("SELECT * FROM monhoc");

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

Response::json(["success" => true, "data" => $data]);

