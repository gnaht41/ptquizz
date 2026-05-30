<?php

class TokenManager
{
    private static $secret_key = "pt_quiz_security_research_secret_key";

    /**
     * Generate a simple JWT-like token
     */
    public static function generateToken($payload)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = time() + (60 * 60 * 24); // 24 hours expiry
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Validate and decode token
     */
    public static function validateToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        
        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];
        
        $validSignature = self::base64UrlEncode(hash_hmac('sha256', $header . "." . $payload, self::$secret_key, true));
        
        if (!hash_equals($validSignature, $signature)) return false;
        
        $data = json_decode(self::base64UrlDecode($payload), true);
        if ($data['exp'] < time()) return false;
        
        return $data;
    }

    private static function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    private static function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
