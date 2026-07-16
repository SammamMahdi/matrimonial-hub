<?php

declare(strict_types=1);

namespace App\Core;

/** One-shot messages that survive exactly one redirect. */
final class Flash
{
    private const KEY = '_flash';

    /** @param 'success'|'error'|'info' $type */
    public static function add(string $type, string $message): void
    {
        $messages   = Session::get(self::KEY, []);
        $messages[] = ['type' => $type, 'message' => $message];
        Session::put(self::KEY, $messages);
    }

    /** @return list<array{type:string,message:string}> */
    public static function drain(): array
    {
        $messages = Session::pull(self::KEY, []);

        return is_array($messages) ? $messages : [];
    }

    /** Keeps form input across a failed validation redirect. */
    public static function withInput(array $input): void
    {
        unset($input['_csrf_token'], $input['password'], $input['password_confirmation']);
        Session::put('_old_input', $input);
    }

    public static function oldInput(): array
    {
        $old = Session::pull('_old_input', []);

        return is_array($old) ? $old : [];
    }

    public static function withErrors(array $errors): void
    {
        Session::put('_errors', $errors);
    }

    public static function errors(): array
    {
        $errors = Session::pull('_errors', []);

        return is_array($errors) ? $errors : [];
    }
}
