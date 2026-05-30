<?php
require 'client/core/Database.php';
$c = Database::connect();
$r = $c->query('SELECT id_nguoidung, ten, avatar FROM nguoidung');
while($row = $r->fetch_assoc()) { print_r($row); }
