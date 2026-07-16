<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal route table. Patterns support {placeholders}, which arrive at the
 * handler as ordered arguments.
 *
 * CSRF is enforced here for every POST, so no controller can forget it.
 */
final class Router
{
    /** @var array<string, array<string, callable|array>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $pattern, callable|array $handler): void
    {
        $this->routes['GET'][$pattern] = $handler;
    }

    public function post(string $pattern, callable|array $handler): void
    {
        $this->routes['POST'][$pattern] = $handler;
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $path   = $request->path();

        if ($method === 'POST' && !Csrf::check($request->all())) {
            if ($request->isAjax()) {
                Response::json(['error' => 'Your session expired. Refresh the page and try again.'], 419);
            }

            Flash::add('error', 'Your session expired. Please try that again.');
            Response::redirect($path);
        }

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $params = $this->match($pattern, $path);

            if ($params !== null) {
                $this->call($handler, $request, $params);

                return;
            }
        }

        Response::notFound();
    }

    /** @return list<string>|null */
    private function match(string $pattern, string $path): ?array
    {
        if ($pattern === $path) {
            return [];
        }

        if (!str_contains($pattern, '{')) {
            return null;
        }

        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $path, $matches) === 1) {
            array_shift($matches);

            return $matches;
        }

        return null;
    }

    private function call(callable|array $handler, Request $request, array $params): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $handler = [new $class(), $method];
        }

        $output = $handler($request, ...$params);

        if (is_string($output)) {
            echo $output;
        }
    }
}
