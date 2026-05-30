<?php
if (!class_exists('Database')) {
    class Database
    {
        private static $instance = null;

        public static function connect()
        {
            $needsConnect = false;
            if (self::$instance === null) {
                $needsConnect = true;
            } else {
                try {
                    if (!@self::$instance->ping()) {
                        $needsConnect = true;
                    }
                } catch (Exception $e) {
                    $needsConnect = true;
                }
            }

            if ($needsConnect) {
                $host = getenv('DB_HOST') ?: "localhost";
                $username = getenv('DB_USER') ?: "root";
                $password = getenv('DB_PASS') ?: "";
                $dbname = getenv('DB_NAME') ?: "phutha4_tracnghiem";
                $port = getenv('DB_PORT') ?: 3306;
                $ssl_mode = getenv('DB_SSL_MODE') ?: "";

                self::$instance = mysqli_init();

                if ($ssl_mode === 'REQUIRED') {
                    // Vô hiệu hóa xác minh CA cert để dễ dàng kết nối với Aiven
                    self::$instance->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
                }

                $flags = 0;
                if ($ssl_mode === 'REQUIRED') {
                    $flags = MYSQLI_CLIENT_SSL;
                }

                $success = @self::$instance->real_connect(
                    $host,
                    $username,
                    $password,
                    $dbname,
                    $port,
                    null,
                    $flags
                );

                if (!$success || self::$instance->connect_error) {
                    http_response_code(500);
                    echo json_encode(["error" => "Lỗi kết nối database: " . self::$instance->connect_error]);
                    exit;
                }
                self::$instance->set_charset("utf8mb4");
            }

            return self::$instance;
        }
    }
}