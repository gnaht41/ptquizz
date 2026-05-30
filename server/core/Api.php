<?php
require_once __DIR__ . "/../model/Database.php";

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

if (!class_exists('Api')) {
    class Api
    {
        public static function boot()
        {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }

            // --- SECURITY HEADERS ---
            header("X-Content-Type-Options: nosniff");
            header("X-Frame-Options: DENY");
            header("X-XSS-Protection: 1; mode=block");
            header("Referrer-Policy: strict-origin-when-cross-origin");
            
            // --- CORS CONFIG ---
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                http_response_code(204);
                exit;
            }

            if (ob_get_level() > 0) ob_clean();
            else ob_start();

            header("Content-Type: application/json; charset=UTF-8");
        }

        public static function json($data, $status = 200)
        {
            if (ob_get_level() > 0) ob_clean();
            http_response_code($status);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            exit;
        }

        public static function requireMethod($method)
        {
            if (($_SERVER["REQUEST_METHOD"] ?? "GET") !== strtoupper($method)) {
                self::json(["error" => "Method Not Allowed"], 405);
            }
        }

        public static function requireMethods(array $methods)
        {
            $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"] ?? "GET");
            $allowed = array_map("strtoupper", $methods);

            if (!in_array($requestMethod, $allowed, true)) {
                self::json([
                    "error" => "Method Not Allowed",
                    "allowed_methods" => $allowed,
                ], 405);
            }
        }

        public static function requireLogin()
        {
            require_once __DIR__ . "/Jwt.php";
            
            $user = null;

            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
                $decoded = Jwt::decode($token);
                if ($decoded) {
                    $user = $decoded;
                } else {
                    self::json(["error" => "Token không hợp lệ hoặc đã hết hạn"], 401);
                }
            } 
            elseif (isset($_SESSION["user"])) {
                $user = $_SESSION["user"];
            }

            if (!$user) {
                self::json(["error" => "Unauthorized - Cần đăng nhập hoặc Token"], 401);
            }

            $id = $user["id_nguoidung"] ?? $user["id"] ?? 0;
            $conn = Database::connect();
            $stmt = $conn->prepare("SELECT trangthai FROM nguoidung WHERE id_nguoidung = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $userStatus = $result->fetch_assoc();
            // $conn->close();

            if (!$userStatus || $userStatus['trangthai'] !== 'active') {
                if (isset($_SESSION['user'])) session_destroy();
                self::json(["error" => "Tài khoản của bạn đã bị khóa hoặc không tồn tại."], 403);
            }

            // Normalize user data to ensure both key styles exist
            $user['id_nguoidung'] = $id;
            $user['id'] = $id;
            $user['vaitro'] = $user['vaitro'] ?? $user['role'] ?? '';
            $user['role'] = $user['vaitro'];

            return $user;
        }

        public static function requireRole(array $roles)
        {
            $user = self::requireLogin();
            $role = $user["vaitro"] ?? $user["role"] ?? "";

            if (!in_array($role, $roles, true)) {
                self::json(["error" => "Forbidden"], 403);
            }

            return $user;
        }

        public static function detectRoute()
        {
            if (!empty($_GET["route"])) {
                return trim((string) $_GET["route"], "/");
            }

            $uriPath = parse_url($_SERVER["REQUEST_URI"] ?? "", PHP_URL_PATH) ?? "";
            $scriptName = str_replace("\\", "/", $_SERVER["SCRIPT_NAME"] ?? "");
            $baseDir = rtrim(str_replace("\\", "/", dirname($scriptName)), "/");

           if ($baseDir !== "" && strpos($uriPath, $baseDir) === 0) {
                $uriPath = substr($uriPath, strlen($baseDir));
            }

            return trim($uriPath, "/");
        }

        public static function jsonInput()
        {
            $raw = file_get_contents("php://input");
            $data = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                self::json(["error" => "Invalid JSON payload"], 400);
            }

            return is_array($data) ? $data : [];
        }
    }
}