<?php
require_once __DIR__ . "/../core/Database.php";
$conn = Database::connect();

// 1. Add column thoigianconlai if missing
$result = $conn->query("SHOW COLUMNS FROM lanthi LIKE 'thoigianconlai'");
if ($result && $result->num_rows === 0) {
    $conn->query("ALTER TABLE lanthi ADD COLUMN thoigianconlai INT NULL AFTER diem");
}

// 2. Add column cautraloi_tam if missing
$result = $conn->query("SHOW COLUMNS FROM lanthi LIKE 'cautraloi_tam'");
if ($result && $result->num_rows === 0) {
    $conn->query("ALTER TABLE lanthi ADD COLUMN cautraloi_tam TEXT NULL AFTER thoigianconlai");
}

// 3. Add column xao_tron to baithi if missing
$result = $conn->query("SHOW COLUMNS FROM baithi LIKE 'xao_tron'");
if ($result && $result->num_rows === 0) {
    $conn->query("ALTER TABLE baithi ADD COLUMN xao_tron TINYINT(1) NOT NULL DEFAULT 0 AFTER trangthai");
}

// 4. Ensure id_baithi is AUTO_INCREMENT
$result = $conn->query("SHOW COLUMNS FROM baithi LIKE 'id_baithi'");
if ($result && ($column = $result->fetch_assoc())) {
    $extra = strtolower((string) ($column['Extra'] ?? ''));
    if (strpos($extra, 'auto_increment') === false) {
        $conn->query("ALTER TABLE baithi MODIFY COLUMN id_baithi INT NOT NULL AUTO_INCREMENT");
    }
}

// 5. Create password_resets table
$conn->query("CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

echo "Done";
?>
