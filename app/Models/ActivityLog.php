<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ActivityLog
{
    public static function record(string $userId, string $activity, ?string $ip = null): void
    {
        Database::instance()->execute(
            'INSERT INTO activity_log (user_id, activity, ip_address) VALUES (:id, :activity, :ip)',
            [
                'id'       => $userId,
                'activity' => $activity,
                'ip'       => $ip ?? ($_SERVER['REMOTE_ADDR'] ?? null),
            ]
        );
    }

    /** @return list<array<string,mixed>> */
    public static function recent(int $limit = 20): array
    {
        return Database::instance()->all(
            'SELECT l.log_id, l.activity, l.created_at, l.user_id,
                    u.first_name, u.last_name, u.photo
             FROM activity_log l
             LEFT JOIN users u ON u.user_id = l.user_id
             ORDER BY l.created_at DESC, l.log_id DESC
             LIMIT ' . (int) $limit
        );
    }

    /** @return list<array<string,mixed>> */
    public static function forUser(string $userId, int $limit = 10): array
    {
        return Database::instance()->all(
            'SELECT activity, created_at FROM activity_log
             WHERE user_id = :id ORDER BY created_at DESC LIMIT ' . (int) $limit,
            ['id' => $userId]
        );
    }
}
