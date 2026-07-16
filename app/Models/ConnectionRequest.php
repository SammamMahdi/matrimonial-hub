<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ConnectionRequest
{
    /**
     * Sends (or re-sends) a request.
     *
     * $senderId always comes from the session at the call site — never from the
     * request body, which is how the original let anyone forge a request from
     * any user to any user.
     *
     * @return array{ok:bool, message:string}
     */
    public static function send(string $senderId, string $receiverId, ?string $note = null): array
    {
        if ($senderId === $receiverId) {
            return ['ok' => false, 'message' => 'You cannot send yourself a request.'];
        }

        $receiver = User::find($receiverId);

        if ($receiver === null || $receiver['account_status'] !== 'Active') {
            return ['ok' => false, 'message' => 'That member is no longer available.'];
        }

        $existing = self::between($senderId, $receiverId);

        if ($existing !== null) {
            if ($existing['status'] === 'Accepted') {
                return ['ok' => false, 'message' => 'You are already connected.'];
            }

            if ($existing['status'] === 'Pending') {
                // They already asked us — treat this as an accept.
                if ($existing['receiver_id'] === $senderId) {
                    self::respond((int) $existing['request_id'], $senderId, 'accept');

                    return ['ok' => true, 'message' => 'You matched! They had already sent you a request.'];
                }

                return ['ok' => false, 'message' => 'Your request is already pending.'];
            }
        }

        // A previously declined/cancelled row is revived rather than duplicated,
        // which is what the UNIQUE(sender_id, receiver_id) key expects.
        Database::instance()->execute(
            'INSERT INTO connection_requests (sender_id, receiver_id, status, message)
             VALUES (:sender, :receiver, :pending, :note)
             ON DUPLICATE KEY UPDATE
                status = :pending2, message = :note2, created_at = NOW(), responded_at = NULL',
            [
                'sender'   => $senderId,
                'receiver' => $receiverId,
                'pending'  => 'Pending',
                'pending2' => 'Pending',
                'note'     => $note !== '' ? $note : null,
                'note2'    => $note !== '' ? $note : null,
            ]
        );

        ActivityLog::record($senderId, 'Sent a connection request');

        return ['ok' => true, 'message' => 'Request sent.'];
    }

    /**
     * Accept or decline. Only the receiver can act, and only on a Pending row —
     * the original let any logged-in user accept any request by id.
     *
     * @param 'accept'|'decline' $action
     */
    public static function respond(int $requestId, string $receiverId, string $action): array
    {
        $status = $action === 'accept' ? 'Accepted' : 'Declined';

        $affected = Database::instance()->execute(
            'UPDATE connection_requests
             SET status = :status, responded_at = NOW()
             WHERE request_id = :id AND receiver_id = :receiver AND status = :pending',
            [
                'status'   => $status,
                'id'       => $requestId,
                'receiver' => $receiverId,
                'pending'  => 'Pending',
            ]
        );

        if ($affected === 0) {
            return ['ok' => false, 'message' => 'That request is no longer pending.'];
        }

        ActivityLog::record($receiverId, $status === 'Accepted' ? 'Accepted a connection' : 'Declined a connection');

        return [
            'ok'      => true,
            'message' => $status === 'Accepted' ? 'You are now connected — say hello!' : 'Request declined.',
        ];
    }

    /** Sender withdraws their own pending request. */
    public static function cancel(int $requestId, string $senderId): array
    {
        $affected = Database::instance()->execute(
            'UPDATE connection_requests
             SET status = :cancelled, responded_at = NOW()
             WHERE request_id = :id AND sender_id = :sender AND status = :pending',
            ['cancelled' => 'Cancelled', 'id' => $requestId, 'sender' => $senderId, 'pending' => 'Pending']
        );

        return $affected > 0
            ? ['ok' => true, 'message' => 'Request withdrawn.']
            : ['ok' => false, 'message' => 'That request cannot be withdrawn.'];
    }

    /** @return array<string,mixed>|null */
    public static function between(string $a, string $b): ?array
    {
        return Database::instance()->first(
            'SELECT * FROM connection_requests
             WHERE (sender_id = :a AND receiver_id = :b)
                OR (sender_id = :b2 AND receiver_id = :a2)
             ORDER BY created_at DESC
             LIMIT 1',
            ['a' => $a, 'b' => $b, 'a2' => $a, 'b2' => $b]
        );
    }

    /**
     * The authorisation check the chat depends on. The original never had one,
     * so any member could open any conversation by changing the URL.
     */
    public static function areConnected(string $a, string $b): bool
    {
        return (int) Database::instance()->value(
            'SELECT COUNT(*) FROM connection_requests
             WHERE status = :accepted
               AND ((sender_id = :a AND receiver_id = :b) OR (sender_id = :b2 AND receiver_id = :a2))',
            ['accepted' => 'Accepted', 'a' => $a, 'b' => $b, 'a2' => $a, 'b2' => $b]
        ) > 0;
    }

    /** Incoming pending requests. @return list<array<string,mixed>> */
    public static function incoming(string $userId): array
    {
        return Database::instance()->all(
            'SELECT r.request_id, r.message, r.created_at,
                    u.user_id, u.first_name, u.middle_name, u.last_name, u.photo,
                    u.dob, u.profession, u.religion
             FROM connection_requests r
             JOIN users u ON u.user_id = r.sender_id
             WHERE r.receiver_id = :id AND r.status = :pending AND u.account_status = :active
             ORDER BY r.created_at DESC',
            ['id' => $userId, 'pending' => 'Pending', 'active' => 'Active']
        );
    }

    /** Outgoing pending requests. @return list<array<string,mixed>> */
    public static function outgoing(string $userId): array
    {
        return Database::instance()->all(
            'SELECT r.request_id, r.created_at,
                    u.user_id, u.first_name, u.middle_name, u.last_name, u.photo,
                    u.dob, u.profession
             FROM connection_requests r
             JOIN users u ON u.user_id = r.receiver_id
             WHERE r.sender_id = :id AND r.status = :pending AND u.account_status = :active
             ORDER BY r.created_at DESC',
            ['id' => $userId, 'pending' => 'Pending', 'active' => 'Active']
        );
    }

    /**
     * Everyone the user has matched with, newest first, with the last message
     * and unread count for the conversation list.
     *
     * @return list<array<string,mixed>>
     */
    public static function matches(string $userId): array
    {
        return Database::instance()->all(
            'SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.photo, u.dob,
                    u.profession, u.last_seen_at,
                    r.responded_at AS matched_at,
                    (SELECT m.body FROM messages m
                      WHERE (m.sender_id = :me1 AND m.receiver_id = u.user_id)
                         OR (m.sender_id = u.user_id AND m.receiver_id = :me2)
                      ORDER BY m.message_id DESC LIMIT 1) AS last_message,
                    (SELECT m.created_at FROM messages m
                      WHERE (m.sender_id = :me3 AND m.receiver_id = u.user_id)
                         OR (m.sender_id = u.user_id AND m.receiver_id = :me4)
                      ORDER BY m.message_id DESC LIMIT 1) AS last_message_at,
                    (SELECT COUNT(*) FROM messages m
                      WHERE m.sender_id = u.user_id AND m.receiver_id = :me5 AND m.read_at IS NULL
                    ) AS unread_count
             FROM connection_requests r
             JOIN users u
               ON u.user_id = IF(r.sender_id = :me6, r.receiver_id, r.sender_id)
             WHERE r.status = :accepted
               AND (r.sender_id = :me7 OR r.receiver_id = :me8)
               AND u.account_status = :active
             ORDER BY last_message_at IS NULL, last_message_at DESC, r.responded_at DESC',
            [
                'me1' => $userId, 'me2' => $userId, 'me3' => $userId, 'me4' => $userId,
                'me5' => $userId, 'me6' => $userId, 'me7' => $userId, 'me8' => $userId,
                'accepted' => 'Accepted', 'active' => 'Active',
            ]
        );
    }

    public static function countIncoming(string $userId): int
    {
        return (int) Database::instance()->value(
            'SELECT COUNT(*) FROM connection_requests WHERE receiver_id = :id AND status = :pending',
            ['id' => $userId, 'pending' => 'Pending']
        );
    }

    public static function countMatches(string $userId): int
    {
        return (int) Database::instance()->value(
            'SELECT COUNT(*) FROM connection_requests
             WHERE status = :accepted AND (sender_id = :a OR receiver_id = :b)',
            ['accepted' => 'Accepted', 'a' => $userId, 'b' => $userId]
        );
    }

    public static function countAll(): int
    {
        return (int) Database::instance()->value('SELECT COUNT(*) FROM connection_requests');
    }

    public static function countByStatus(string $status): int
    {
        return (int) Database::instance()->value(
            'SELECT COUNT(*) FROM connection_requests WHERE status = :s',
            ['s' => $status]
        );
    }
}
