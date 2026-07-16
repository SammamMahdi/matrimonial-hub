<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function __construct(
        private array $query = [],
        private array $body = [],
        private array $files = [],
        private array $server = []
    ) {
    }

    public static function capture(): self
    {
        return new self($_GET, $_POST, $_FILES, $_SERVER);
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * The path the router matches on, with any subdirectory prefix and query
     * string stripped. Works whether the app sits at the domain root or under
     * something like /Matrimonial-Project-370/public.
     */
    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $uri = explode('?', $uri, 2)[0];
        $uri = rawurldecode($uri);

        $base = $this->basePath();

        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    /** Directory the front controller lives in, e.g. '' or '/project/public'. */
    public function basePath(): string
    {
        $script = $this->server['SCRIPT_NAME'] ?? '';
        $dir    = rtrim(str_replace('\\', '/', dirname($script)), '/');

        return $dir === '.' ? '' : $dir;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $value = $this->body[$key] ?? $default;

        return is_string($value) ? trim($value) : $value;
    }

    public function has(string $key): bool
    {
        return isset($this->body[$key]);
    }

    public function all(): array
    {
        return $this->body;
    }

    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $file;
    }

    public function isAjax(): bool
    {
        return strtolower($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }
}
