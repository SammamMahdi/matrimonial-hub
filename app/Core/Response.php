<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    private static string $basePath = '';

    public static function setBasePath(string $basePath): void
    {
        self::$basePath = $basePath;
    }

    /** Turns an app-relative path into a URL that works under any subdirectory. */
    public static function url(string $path = '/'): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return self::$basePath . '/' . ltrim($path, '/');
    }

    public static function redirect(string $path, int $status = 302): never
    {
        header('Location: ' . self::url($path), true, $status);
        exit;
    }

    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function notFound(string $message = 'Page not found'): never
    {
        http_response_code(404);
        echo View::render('errors/404', ['message' => $message], 'layouts/blank');
        exit;
    }

    public static function forbidden(string $message = 'You do not have access to that.'): never
    {
        http_response_code(403);
        echo View::render('errors/403', ['message' => $message], 'layouts/blank');
        exit;
    }
}
