<?php
class Response
{
    public static function json($data, $status = 200)
    {
        if (ob_get_level() > 0) ob_clean();
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
