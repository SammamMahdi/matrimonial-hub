<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Message
{
    /**
     * Messages in a conversation, optionally only those after $sinceId.
     *
     * The cursor is what lets the client append new messages instead of
     * re-downloading and re-rendering the whole transcript twice a second,
     * which is what the original did.
     *
     * @return list<array<string,mixed>>
     */
    public static function thread(string $userId, string $peerId, int $sinceId = 0): array
    {
        return Database::instance()->all(
            'SELECT message_id, sender_id, receiver_id, body, read_at, created_at
             FROM messages
             WHERE ((sender_id = :me AND receiver_id = :peer)
                 OR (sender_id = :peer2 AND receiver_id = :me2))
               AND message_id > :since
             ORDER BY message_id ASC
             LIMIT 500',
            ['me' => $userId, 'peer' => $peerId, 'me2' => $userId, 'peer2' => $peerId, 'since' => $sinceId]
        );
    }

    public static function send(string $senderId, string $receiverId, string $body): int
    {
        $db = Database::instance();

        $db->execute(
            'INSERT INTO messages (sender_id, receiver_id, body) VALUES (:sender, :receiver, :body)',
            ['sender' => $senderId, 'receiver' => $receiverId, 'body' => $body]
        );

        return (int) $db->lastInsertId();
    }

    /** Marks everything the peer sent us as read. */
    public static function markRead(string $userId, string $peerId): void
    {
        Database::instance()->execute(
            'UPDATE messages SET read_at = NOW()
             WHERE receiver_id = :me AND sender_id = :peer AND read_at IS NULL',
            ['me' => $userId, 'peer' => $peerId]
        );
    }

    public static function unreadCount(string $userId): int
    {
        return (int) Database::instance()->value(
            'SELECT COUNT(*) FROM messages WHERE receiver_id = :id AND read_at IS NULL',
            ['id' => $userId]
        );
    }

    public static function countAll(): int
    {
        return (int) Database::instance()->value('SELECT COUNT(*) FROM messages');
    }
}
