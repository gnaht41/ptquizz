<?php
class Auth
{
    public static function createToken($userId)
    {
        $payload = [
            "user_id" => $userId,
            "exp" => time() + 3600
        ];

        return base64_encode(json_encode($payload));
    }

    public static function verifyToken($token)
    {
        $payload = json_decode(base64_decode($token), true);

        if (!$payload || $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }
}
