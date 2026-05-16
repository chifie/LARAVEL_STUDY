<?php
declare(strict_types=1);

class Response
{
    public static function redirect($to)
    {
        header('Location: ' . $to, true, 302);
        exit;
    }

    public static function json(array $data, $statusCode = 200)
    {
        http_response_code((int) $statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data);
        exit;
    }

    public static function forbidden($message = 'Forbidden')
    {
        http_response_code(403);
        echo '<h1>403 Forbidden</h1><p>' . e($message) . '</p>';
        exit;
    }
}