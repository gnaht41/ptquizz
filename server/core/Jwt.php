<?php

class Jwt
{
    private static $secret = "pt_quiz_security_research_2026_secret_key"; // Trong thực tế nên để ở file config/env

    /**
     * Encode payload thành JWT
     */
    public static function encode(array $payload)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Decode và verify JWT
     */
    public static function decode($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        list($header, $payload, $signature) = $parts;

        // Verify Signature
        $validSignature = hash_hmac('sha256', $header . "." . $payload, self::$secret, true);
        if (self::base64UrlEncode($validSignature) !== $signature) {
            return false;
        }

        $payloadData = json_decode(self::base64UrlDecode($payload), true);

        // Check Expiration (nếu có)
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false;
        }

        return $payloadData;
    }

    private static function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    private static function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
