<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Synchroniser-token CSRF protection. Every state-changing form embeds
 * Csrf::field(); Router rejects any POST whose token does not verify.
 */
final class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        $token = Session::get(self::KEY);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::put(self::KEY, $token);
        }

        return $token;
    }

    public static function field(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            self::KEY,
            htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8')
        );
    }

    public static function verify(?string $token): bool
    {
        $expected = Session::get(self::KEY);

        if (!is_string($expected) || $expected === '' || !is_string($token)) {
            return false;
        }

        return hash_equals($expected, $token);
    }

    public static function check(array $input): bool
    {
        return self::verify($input[self::KEY] ?? null);
    }
}
