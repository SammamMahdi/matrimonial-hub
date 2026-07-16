<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Profile
{
    /** @return array<string,mixed>|null */
    public static function find(string $userId): ?array
    {
        return Database::instance()->first(
            'SELECT * FROM profiles WHERE user_id = :id',
            ['id' => $userId]
        );
    }

    /**
     * Upsert that only touches the columns passed in.
     *
     * The original used REPLACE INTO with a partial column list, which is a
     * DELETE plus INSERT — so every profile save silently wiped the user's
     * address and phone number. ON DUPLICATE KEY UPDATE leaves untouched
     * columns alone.
     *
     * @param array<string,mixed> $data
     */
    public static function save(string $userId, array $data): void
    {
        $allowed = [
            'phone', 'road_number', 'street_number', 'building_number',
            'secondary_education', 'higher_secondary', 'undergraduate', 'postgraduate',
            'marital_status', 'height_cm', 'weight_kg', 'complexion',
            'interests', 'hobbies', 'biography', 'family_background',
        ];

        $columns = array_values(array_intersect($allowed, array_keys($data)));

        if ($columns === []) {
            return;
        }

        $params = ['user_id' => $userId];

        foreach ($columns as $column) {
            $value = $data[$column];
            $params[$column] = ($value === '' ? null : $value);
        }

        $insertColumns = implode(', ', array_merge(['user_id'], $columns));
        $placeholders  = implode(', ', array_map(static fn ($c) => ':' . $c, array_merge(['user_id'], $columns)));
        $updates       = implode(', ', array_map(static fn ($c) => "{$c} = VALUES({$c})", $columns));

        Database::instance()->execute(
            "INSERT INTO profiles ({$insertColumns}) VALUES ({$placeholders})
             ON DUPLICATE KEY UPDATE {$updates}",
            $params
        );
    }

    /** How complete a profile is, as a percentage — drives the dashboard nudge. */
    public static function completeness(array $userWithProfile): int
    {
        $fields = [
            'photo', 'phone', 'secondary_education', 'higher_secondary', 'undergraduate',
            'marital_status', 'height_cm', 'weight_kg', 'complexion',
            'interests', 'hobbies', 'biography', 'family_background',
        ];

        $filled = 0;

        foreach ($fields as $field) {
            $value = $userWithProfile[$field] ?? null;

            if ($value !== null && $value !== '' && $value !== '0.00') {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }
}
