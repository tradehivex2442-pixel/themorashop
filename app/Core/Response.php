<?php
// ============================================================
// THEMORA SHOP — Response
// ============================================================

namespace App\Core;

class Response
{
    public static function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function redirect(string $url, int $code = 302): never
    {
        http_response_code($code);
        header("Location: {$url}");
        exit;
    }

    public static function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        self::redirect($referer);
    }

    public static function notFound(): never
    {
        http_response_code(404);
        $view = new View();
        $view->render('errors/404');
        exit;
    }

    public static function forbidden(): never
    {
        http_response_code(403);
        $view = new View();
        $view->render('errors/403');
        exit;
    }

    public static function download(string $filePath, string $filename): never
    {
        if (!file_exists($filePath)) {
            self::notFound();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public static function success(string $message, mixed $data = null): never
    {
        self::json(['success' => true, 'message' => $message, 'data' => $data]);
    }

    public static function error(string $message, int $code = 400, mixed $errors = null): never
    {
        self::json(['success' => false, 'message' => $message, 'errors' => $errors], $code);
    }
}
