<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    /** Columns safe to expose on a public profile card. */
    private const PUBLIC_COLUMNS = 'u.user_id, u.first_name, u.middle_name, u.last_name, u.dob,
        u.gender, u.religion, u.ethnicity, u.profession, u.photo, u.account_status, u.last_seen_at';

    /** @return array<string,mixed>|null */
    public static function find(string $userId): ?array
    {
        return Database::instance()->first(
            'SELECT * FROM users WHERE user_id = :id',
            ['id' => $userId]
        );
    }

    /** @return array<string,mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        return Database::instance()->first(
            'SELECT * FROM users WHERE email = :email',
            ['email' => $email]
        );
    }

    public static function emailExists(string $email, ?string $exceptUserId = null): bool
    {
        $sql    = 'SELECT COUNT(*) FROM users WHERE email = :email';
        $params = ['email' => $email];

        if ($exceptUserId !== null) {
            $sql .= ' AND user_id <> :id';
            $params['id'] = $exceptUserId;
        }

        return (int) Database::instance()->value($sql, $params) > 0;
    }

    /** Full user + profile row, as the profile page and matcher want it. */
    public static function findWithProfile(string $userId): ?array
    {
        return Database::instance()->first(
            'SELECT u.*, p.phone, p.road_number, p.street_number, p.building_number,
                    p.secondary_education, p.higher_secondary, p.undergraduate, p.postgraduate,
                    p.marital_status, p.height_cm, p.weight_kg, p.complexion,
                    p.interests, p.hobbies, p.biography, p.family_background
             FROM users u
             LEFT JOIN profiles p ON p.user_id = u.user_id
             WHERE u.user_id = :id',
            ['id' => $userId]
        );
    }

    /**
     * Creates the user plus their profile and preference rows in one
     * transaction, so a half-registered account can never exist.
     *
     * @param  array<string,mixed> $data
     * @return string the new user_id
     */
    public static function create(array $data): string
    {
        $db = Database::instance();

        return $db->transaction(static function (Database $db) use ($data): string {
            $userId = self::generateId();

            $db->execute(
                'INSERT INTO users
                    (user_id, email, password_hash, first_name, middle_name, last_name, dob,
                     gender, religion, ethnicity, profession, nid, photo, account_status)
                 VALUES
                    (:user_id, :email, :password_hash, :first_name, :middle_name, :last_name, :dob,
                     :gender, :religion, :ethnicity, :profession, :nid, :photo, :status)',
                [
                    'user_id'       => $userId,
                    'email'         => $data['email'],
                    'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'first_name'    => $data['first_name'],
                    'middle_name'   => $data['middle_name'] !== '' ? $data['middle_name'] : null,
                    'last_name'     => $data['last_name'],
                    'dob'           => $data['dob'],
                    'gender'        => $data['gender'],
                    'religion'      => $data['religion'],
                    'ethnicity'     => $data['ethnicity'],
                    'profession'    => $data['profession'],
                    'nid'           => $data['nid'],
                    'photo'         => $data['photo'] ?? null,
                    'status'        => 'Active',
                ]
            );

            // Guarantee the child rows exist. The original's profile UPDATE used
            // an inner JOIN, so a user without a profile row saved nothing while
            // still reporting success.
            $db->execute('INSERT INTO profiles (user_id) VALUES (:id)', ['id' => $userId]);
            $db->execute('INSERT INTO preferences (user_id) VALUES (:id)', ['id' => $userId]);

            ActivityLog::record($userId, 'Account created');

            return $userId;
        });
    }

    public static function updatePassword(string $userId, string $plainPassword): void
    {
        Database::instance()->execute(
            'UPDATE users SET password_hash = :hash WHERE user_id = :id',
            ['hash' => password_hash($plainPassword, PASSWORD_DEFAULT), 'id' => $userId]
        );
    }

    /** @param array<string,mixed> $data */
    public static function updateAccount(string $userId, array $data): void
    {
        Database::instance()->execute(
            'UPDATE users SET
                first_name = :first_name, middle_name = :middle_name, last_name = :last_name,
                dob = :dob, gender = :gender, religion = :religion, ethnicity = :ethnicity,
                profession = :profession, email = :email
             WHERE user_id = :id',
            [
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] !== '' ? $data['middle_name'] : null,
                'last_name'  => $data['last_name'],
                'dob'        => $data['dob'],
                'gender'     => $data['gender'],
                'religion'   => $data['religion'],
                'ethnicity'  => $data['ethnicity'],
                'profession' => $data['profession'],
                'email'      => $data['email'],
                'id'         => $userId,
            ]
        );
    }

    public static function updatePhoto(string $userId, string $filename): void
    {
        Database::instance()->execute(
            'UPDATE users SET photo = :photo WHERE user_id = :id',
            ['photo' => $filename, 'id' => $userId]
        );
    }

    public static function touchLastSeen(string $userId): void
    {
        Database::instance()->execute(
            'UPDATE users SET last_seen_at = NOW() WHERE user_id = :id',
            ['id' => $userId]
        );
    }

    /** Online = seen in the last two minutes. */
    public static function isOnline(?string $lastSeenAt): bool
    {
        if (!is_string($lastSeenAt) || $lastSeenAt === '') {
            return false;
        }

        return (time() - strtotime($lastSeenAt)) < 120;
    }

    /**
     * Candidates for the browse page. Hard filters run in SQL; ranking happens
     * in PHP via Matcher so the score can explain itself.
     *
     * @param  array<string,mixed> $filters
     * @return list<array<string,mixed>>
     */
    public static function candidatesFor(string $viewerId, array $filters = [], int $limit = 200): array
    {
        $sql = 'SELECT ' . self::PUBLIC_COLUMNS . ',
                       p.marital_status, p.height_cm, p.complexion, p.interests, p.hobbies,
                       p.biography, p.undergraduate, p.postgraduate
                FROM users u
                LEFT JOIN profiles p ON p.user_id = u.user_id
                WHERE u.user_id <> :viewer
                  AND u.account_status = :active
                  AND NOT EXISTS (
                        SELECT 1 FROM connection_requests r
                        WHERE r.status = :accepted
                          AND ((r.sender_id = :viewer2 AND r.receiver_id = u.user_id)
                            OR (r.receiver_id = :viewer3 AND r.sender_id = u.user_id))
                  )';

        $params = [
            'viewer'   => $viewerId,
            'viewer2'  => $viewerId,
            'viewer3'  => $viewerId,
            'active'   => 'Active',
            'accepted' => 'Accepted',
        ];

        // Each filter is an exact match on a whitelisted column, bound as a
        // parameter — never concatenated the way the original built its WHERE.
        $exactFilters = [
            'gender'         => 'u.gender',
            'religion'       => 'u.religion',
            'ethnicity'      => 'u.ethnicity',
            'profession'     => 'u.profession',
            'marital_status' => 'p.marital_status',
            'complexion'     => 'p.complexion',
            'undergraduate'  => 'p.undergraduate',
            'postgraduate'   => 'p.postgraduate',
        ];

        foreach ($exactFilters as $key => $column) {
            if (!empty($filters[$key])) {
                $sql            .= " AND {$column} = :{$key}";
                $params[$key]    = $filters[$key];
            }
        }

        if (!empty($filters['min_age'])) {
            // Born early enough to be at least min_age today.
            $sql .= ' AND u.dob <= DATE_SUB(CURDATE(), INTERVAL :min_age YEAR)';
            $params['min_age'] = (int) $filters['min_age'];
        }

        if (!empty($filters['max_age'])) {
            $sql .= ' AND u.dob >= DATE_SUB(CURDATE(), INTERVAL :max_age YEAR)';
            $params['max_age'] = (int) $filters['max_age'];
        }

        if (!empty($filters['min_height'])) {
            $sql .= ' AND p.height_cm >= :min_height';
            $params['min_height'] = (float) $filters['min_height'];
        }

        if (!empty($filters['max_height'])) {
            $sql .= ' AND p.height_cm <= :max_height';
            $params['max_height'] = (float) $filters['max_height'];
        }

        if (!empty($filters['q'])) {
            $sql .= ' AND (u.first_name LIKE :q OR u.last_name LIKE :q2 OR p.biography LIKE :q3)';
            // Escape LIKE wildcards so a literal % does not match everything.
            $term = '%' . addcslashes((string) $filters['q'], '%_\\') . '%';
            $params['q']  = $term;
            $params['q2'] = $term;
            $params['q3'] = $term;
        }

        $sql .= ' ORDER BY u.created_at DESC LIMIT ' . (int) $limit;

        return Database::instance()->all($sql, $params);
    }

    // ------------------------------------------------------------ admin views

    /** @return list<array<string,mixed>> */
    public static function search(string $term = '', string $status = '', int $limit = 100): array
    {
        $sql    = 'SELECT * FROM users WHERE 1 = 1';
        $params = [];

        if ($term !== '') {
            $sql .= ' AND (first_name LIKE :t1 OR last_name LIKE :t2 OR email LIKE :t3)';
            $like = '%' . addcslashes($term, '%_\\') . '%';
            $params += ['t1' => $like, 't2' => $like, 't3' => $like];
        }

        if ($status !== '') {
            $sql             .= ' AND account_status = :status';
            $params['status'] = $status;
        }

        $sql .= ' ORDER BY created_at DESC LIMIT ' . (int) $limit;

        return Database::instance()->all($sql, $params);
    }

    public static function setStatus(string $userId, string $status): void
    {
        Database::instance()->execute(
            'UPDATE users SET account_status = :status WHERE user_id = :id',
            ['status' => $status, 'id' => $userId]
        );
    }

    public static function delete(string $userId): void
    {
        Database::instance()->execute('DELETE FROM users WHERE user_id = :id', ['id' => $userId]);
    }

    public static function countAll(): int
    {
        return (int) Database::instance()->value('SELECT COUNT(*) FROM users');
    }

    public static function countByStatus(string $status): int
    {
        return (int) Database::instance()->value(
            'SELECT COUNT(*) FROM users WHERE account_status = :s',
            ['s' => $status]
        );
    }

    public static function countRegisteredSince(string $date): int
    {
        return (int) Database::instance()->value(
            'SELECT COUNT(*) FROM users WHERE created_at >= :d',
            ['d' => $date]
        );
    }

    /**
     * Cryptographically random, collision-checked id.
     * The original used rand() — predictable, and its login endpoint let you
     * sign in as any user_id you could guess.
     */
    private static function generateId(): string
    {
        $alphabet = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $max      = strlen($alphabet) - 1;

        for ($attempt = 0; $attempt < 8; $attempt++) {
            $id = '';

            for ($i = 0; $i < 12; $i++) {
                $id .= $alphabet[random_int(0, $max)];
            }

            $taken = (int) Database::instance()->value(
                'SELECT COUNT(*) FROM users WHERE user_id = :id',
                ['id' => $id]
            );

            if ($taken === 0) {
                return $id;
            }
        }

        throw new \RuntimeException('Could not allocate a unique user id.');
    }
}
