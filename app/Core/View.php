<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Plain-PHP templates. Data is escaped at render time by e() in the templates
 * themselves — never on the way into the database, which is what corrupted
 * names like "O'Brien" in the original app.
 */
final class View
{
    private static string $path = '';

    public static function setPath(string $path): void
    {
        self::$path = rtrim($path, '/\\');
    }

    public static function render(string $template, array $data = [], ?string $layout = 'layouts/app'): string
    {
        $content = self::capture($template, $data);

        if ($layout === null) {
            return $content;
        }

        return self::capture($layout, $data + ['content' => $content]);
    }

    private static function capture(string $template, array $data): string
    {
        $file = self::$path . '/' . str_replace('.', '/', $template) . '.php';

        if (!is_file($file)) {
            throw new RuntimeException("View not found: {$template} (looked in {$file})");
        }

        extract($data, EXTR_SKIP);
        ob_start();

        try {
            require $file;
        } catch (\Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return (string) ob_get_clean();
    }
}
