<?php
/**
 * API Security Validator
 * Centralized validation & sanitization for API inputs
 * Best practices:
 * - Input validation & sanitization
 * - Type checking
 * - SQL injection prevention (via prepared statements)
 * - XSS prevention (via htmlspecialchars in views)
 * - CSRF protection (token validation in production)
 */

class ApiSecurityValidator
{
    /**
     * Validate and sanitize string input
     */
    public static function validateString($value, $fieldName, $minLen = 0, $maxLen = 255)
    {
        if (!is_string($value)) {
            throw new Exception("$fieldName phải là chuỗi");
        }
        
        $value = trim($value);
        
        if (strlen($value) < $minLen) {
            throw new Exception("$fieldName phải có ít nhất $minLen ký tự");
        }
        
        if (strlen($value) > $maxLen) {
            throw new Exception("$fieldName không được quá $maxLen ký tự");
        }
        
        return $value;
    }

    /**
     * Validate numeric input (integer)
     */
    public static function validateInt($value, $fieldName, $min = 0, $max = PHP_INT_MAX)
    {
        if (!is_numeric($value) && !is_int($value)) {
            throw new Exception("$fieldName phải là số");
        }
        
        $value = (int)$value;
        
        if ($value < $min || $value > $max) {
            throw new Exception("$fieldName phải trong khoảng $min đến $max");
        }
        
        return $value;
    }

    /**
     * Validate email format
     */
    public static function validateEmail($value, $fieldName)
    {
        $value = self::validateString($value, $fieldName, 5, 255);
        
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("$fieldName không hợp lệ");
        }
        
        return strtolower($value);
    }

    /**
     * Validate datetime format
     */
    public static function validateDateTime($value, $fieldName, $nullable = false)
    {
        if (empty($value)) {
            if ($nullable) return null;
            throw new Exception("$fieldName không được trống");
        }

        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $value);
        if (!$dateTime) {
            throw new Exception("$fieldName không hợp lệ (định dạng: YYYY-MM-DDTHH:mm)");
        }
        
        return $value;
    }

    /**
     * Validate boolean
     */
    public static function validateBool($value, $fieldName)
    {
        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
        }
        return (bool)$value;
    }

    /**
     * Validate array of specific enum values
     */
    public static function validateEnum($value, $fieldName, $allowedValues)
    {
        $value = self::validateString($value, $fieldName);
        
        if (!in_array($value, $allowedValues, true)) {
            throw new Exception("$fieldName phải là một trong: " . implode(", ", $allowedValues));
        }
        
        return $value;
    }

    /**
     * Validate request method
     */
    public static function validateMethod($required = ['GET', 'POST'])
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!in_array($method, $required)) {
            throw new Exception("Phương thức $method không được phép");
        }
        return $method;
    }

    /**
     * Validate authorization header exists
     */
    public static function checkAuthenticationRequired($user)
    {
        if (!$user || empty($user['id_nguoidung'])) {
            throw new Exception("Yêu cầu đăng nhập");
        }
        return $user;
    }

    /**
     * Validate role-based access
     */
    public static function checkRole($user, $allowedRoles = [])
    {
        if (!isset($user['role'])) {
            throw new Exception("Không tìm thấy role người dùng");
        }

        if (!in_array($user['role'], $allowedRoles)) {
            throw new Exception("Bạn không có quyền truy cập tài nguyên này", 403);
        }

        return $user;
    }

    /**
     * Prevent rate limiting abuse (basic check)
     * In production: use Redis or cache system
     */
    public static function checkRateLimit($userId, $action, $limit = 50, $window = 3600)
    {
        $cacheKey = "ratelimit_{$userId}_{$action}";
        $sessionKey = "ratelimit_{$userId}_{$action}";
        
        // Simple session-based rate limiting
        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = ['count' => 0, 'time' => time()];
        }

        $data = $_SESSION[$sessionKey];
        
        if (time() - $data['time'] > $window) {
            $_SESSION[$sessionKey] = ['count' => 1, 'time' => time()];
            return true;
        }

        $data['count']++;
        
        if ($data['count'] > $limit) {
            throw new Exception("Quá nhiều yêu cầu. Vui lòng thử lại sau.", 429);
        }

        $_SESSION[$sessionKey] = $data;
        return true;
    }

    /**
     * Generate CSRF token (implement in production)
     */
    public static function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken($token)
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            throw new Exception("CSRF token không hợp lệ");
        }

        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            throw new Exception("CSRF token không khớp");
        }

        return true;
    }
}
