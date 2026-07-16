<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Admin;
use App\Models\User;

/**
 * Authentication and authorisation.
 *
 * Members and admins are stored under *separate* session keys. The original
 * app put both under $_SESSION['user_id'], which meant any logged-in member
 * could open the admin dashboard. Keeping them apart is what prevents that.
 */
final class Auth
{
    private const USER_KEY  = 'auth_user_id';
    private const ADMIN_KEY = 'auth_admin_id';

    private static ?array $cachedUser = null;

    // ---------------------------------------------------------------- members

    public static function attempt(string $email, string $password): ?array
    {
        $user = User::findByEmail($email);

        if ($user === null) {
            // Hash anyway so a missing account costs the same time as a wrong
            // password; otherwise response timing reveals which emails exist.
            password_verify($password, '$2y$12$usesomesillystringfoeindexingofthepasswordhash');

            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            User::updatePassword($user['user_id'], $password);
        }

        return $user;
    }

    public static function login(string $userId): void
    {
        Session::regenerate();
        Session::put(self::USER_KEY, $userId);
        self::$cachedUser = null;
    }

    public static function check(): bool
    {
        return Session::has(self::USER_KEY);
    }

    public static function id(): ?string
    {
        $id = Session::get(self::USER_KEY);

        return is_string($id) ? $id : null;
    }

    /** @return array<string,mixed>|null */
    public static function user(): ?array
    {
        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $id = self::id();

        if ($id === null) {
            return null;
        }

        $user = User::find($id);

        // Session points at a deleted account — clear it rather than 500.
        if ($user === null) {
            Session::forget(self::USER_KEY);

            return null;
        }

        return self::$cachedUser = $user;
    }

    // ----------------------------------------------------------------- admins

    public static function attemptAdmin(string $username, string $password): ?array
    {
        $admin = Admin::findByUsername($username);

        if ($admin === null || !password_verify($password, $admin['password_hash'])) {
            return null;
        }

        return $admin;
    }

    public static function loginAdmin(int $adminId): void
    {
        Session::regenerate();
        Session::put(self::ADMIN_KEY, $adminId);
    }

    public static function isAdmin(): bool
    {
        return Session::has(self::ADMIN_KEY);
    }

    public static function adminId(): ?int
    {
        $id = Session::get(self::ADMIN_KEY);

        return is_int($id) ? $id : null;
    }

    /** @return array<string,mixed>|null */
    public static function admin(): ?array
    {
        $id = self::adminId();

        return $id === null ? null : Admin::find($id);
    }

    // ------------------------------------------------------------------ gates

    public static function requireMember(): array
    {
        $user = self::user();

        if ($user === null) {
            Flash::add('error', 'Please sign in to continue.');
            Response::redirect('/login');
        }

        if ($user['account_status'] !== 'Active') {
            self::logout();
            Flash::add('error', 'Your account is ' . strtolower($user['account_status']) . '. Contact support for help.');
            Response::redirect('/login');
        }

        return $user;
    }

    public static function requireAdmin(): array
    {
        $admin = self::admin();

        if ($admin === null) {
            Flash::add('error', 'Administrator sign-in required.');
            Response::redirect('/admin/login');
        }

        return $admin;
    }

    public static function logout(): void
    {
        Session::destroy();
        self::$cachedUser = null;
    }
}
