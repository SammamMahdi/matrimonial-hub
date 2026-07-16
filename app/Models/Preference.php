<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Preference
{
    /** Always returns a row — a missing one is created empty on registration. */
    public static function find(string $userId): array
    {
        $row = Database::instance()->first(
            'SELECT * FROM preferences WHERE user_id = :id',
            ['id' => $userId]
        );

        return $row ?? ['user_id' => $userId];
    }

    /** @param array<string,mixed> $data */
    public static function save(string $userId, array $data): void
    {
        $allowed = [
            'preferred_gender', 'preferred_religion', 'preferred_ethnicity',
            'preferred_profession', 'preferred_marital_status', 'preferred_education',
            'min_age', 'max_age', 'min_height_cm', 'max_height_cm', 'interests', 'hobbies',
        ];

        $columns = array_values(array_intersect($allowed, array_keys($data)));

        if ($columns === []) {
            return;
        }

        $params = ['user_id' => $userId];

        foreach ($columns as $column) {
            $value           = $data[$column];
            $params[$column] = ($value === '' ? null : $value);
        }

        $insertColumns = implode(', ', array_merge(['user_id'], $columns));
        $placeholders  = implode(', ', array_map(static fn ($c) => ':' . $c, array_merge(['user_id'], $columns)));
        $updates       = implode(', ', array_map(static fn ($c) => "{$c} = VALUES({$c})", $columns));

        Database::instance()->execute(
            "INSERT INTO preferences ({$insertColumns}) VALUES ({$placeholders})
             ON DUPLICATE KEY UPDATE {$updates}",
            $params
        );
    }

    /** True once the member has expressed at least one preference. */
    public static function isConfigured(array $preferences): bool
    {
        foreach ($preferences as $key => $value) {
            if ($key === 'user_id' || $key === 'updated_at') {
                continue;
            }

            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }
}
