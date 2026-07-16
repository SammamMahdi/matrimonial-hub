<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Admin
{
    /** @return array<string,mixed>|null */
    public static function find(int $adminId): ?array
    {
        return Database::instance()->first(
            'SELECT admin_id, username, created_at FROM admins WHERE admin_id = :id',
            ['id' => $adminId]
        );
    }

    /** @return array<string,mixed>|null */
    public static function findByUsername(string $username): ?array
    {
        return Database::instance()->first(
            'SELECT * FROM admins WHERE username = :u',
            ['u' => $username]
        );
    }
}
