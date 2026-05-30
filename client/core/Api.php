<?php

require_once __DIR__ . "/Response.php";
require_once __DIR__ . "/Database.php";

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


if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

if (!class_exists('Api')) {
    class Api
    {
        public static function json($data, $status = 200)
        {
            Response::json($data, $status);
        }

        public static function boot($useSession = true)
        {
            if ($useSession && session_status() === PHP_SESSION_NONE) {
                @session_start();
            }

            if (ob_get_level() > 0) ob_clean();
            else ob_start();

            header("Content-Type: application/json");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Pragma: no-cache");
        }

        public static function requireMethod($method)
        {
            if (($_SERVER["REQUEST_METHOD"] ?? "GET") !== strtoupper($method)) {
                Response::json(["error" => "Method Not Allowed"], 405);
            }
        }

        public static function requireMethods(array $methods)
        {
            $requestMethod = strtoupper($_SERVER["REQUEST_METHOD"] ?? "GET");
            $allowed = array_map("strtoupper", $methods);

            if (!in_array($requestMethod, $allowed, true)) {
                Response::json([
                    "error" => "Method Not Allowed",
                    "allowed_methods" => $allowed,
                ], 405);
            }
        }

        public static function requireLogin()
        {
            $user = null;

            if (isset($_SESSION["user"])) {
                $user = $_SESSION["user"];
            } else {
                $headers = getallheaders();
                $auth = $_SERVER["HTTP_AUTHORIZATION"] ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"] ?? $headers["Authorization"] ?? $headers["authorization"] ?? "";
                
                if (strpos($auth, "Bearer ") === 0) {
                    $token = substr($auth, 7);
                    require_once __DIR__ . "/TokenManager.php";
                    $decoded = TokenManager::validateToken($token);
                    if ($decoded) {
                        $user = $decoded;
                        // Synchronize state to PHP Session for subsequent stateless handlers
                        $_SESSION["user"] = [
                            "id" => $user["id"] ?? $user["id_nguoidung"] ?? 0,
                            "email" => $user["email"] ?? "",
                            "name" => $user["name"] ?? $user["hoten"] ?? "",
                            "role" => $user["role"] ?? $user["vaitro"] ?? "",
                        ];
                    }
                }
            }

            if (!$user) {
                Response::json(["error" => "Unauthorized - Vui lòng đăng nhập lại"], 401);
            }

            $conn = Database::connect();
            $stmt = $conn->prepare("SELECT trangthai FROM nguoidung WHERE id_nguoidung = ?");
            $userId = $user["id"] ?? $user["id_nguoidung"] ?? 0;
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $userData = $stmt->get_result()->fetch_assoc();
            // $conn->close();

            if (!$userData || $userData['trangthai'] !== 'active') {
                @session_destroy();
                Response::json(["error" => "Account locked or not found"], 403);
            }

            // Normalize user data to ensure both key styles exist
            $user['id_nguoidung'] = $userId;
            $user['id'] = $userId;
            $user['vaitro'] = $user['vaitro'] ?? $user['role'] ?? '';
            $user['role'] = $user['vaitro'];

            return $user;
        }

        public static function requireRole(array $roles)
        {
            $user = self::requireLogin();
            $role = $user["role"] ?? $user["vaitro"] ?? "";

            if (!in_array($role, $roles, true)) {
                Response::json(["error" => "Forbidden"], 403);
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
                Response::json(["error" => "Invalid JSON payload"], 400);
            }

            return is_array($data) ? $data : [];
        }
    }
}