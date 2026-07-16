<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ConnectionRequest;
use App\Models\Message;
use App\Models\User;

final class ChatController
{
    /** Conversation list — the "Matches" page doubles as the inbox. */
    public function index(Request $request): string
    {
        $user = Auth::requireMember();
        User::touchLastSeen($user['user_id']);

        return View::render('pages/matches', [
            'title'   => 'Matches',
            'matches' => ConnectionRequest::matches($user['user_id']),
        ]);
    }

    public function show(Request $request, string $peerId): string
    {
        $user = Auth::requireMember();
        $peer = $this->authorisePeer($user['user_id'], $peerId);

        User::touchLastSeen($user['user_id']);
        Message::markRead($user['user_id'], $peerId);

        return View::render('pages/chat', [
            'title'    => 'Chat with ' . full_name($peer),
            'peer'     => $peer,
            'user'     => $user,
            'messages' => Message::thread($user['user_id'], $peerId),
            'matches'  => ConnectionRequest::matches($user['user_id']),
        ]);
    }

    /** Poll endpoint: returns only messages newer than the client's cursor. */
    public function fetch(Request $request, string $peerId): never
    {
        $user = Auth::requireMember();
        $this->authorisePeer($user['user_id'], $peerId);

        $since    = (int) $request->query('since', 0);
        $messages = Message::thread($user['user_id'], $peerId, $since);

        // Only mark read when we actually delivered something new, so the
        // UPDATE doesn't run on every single poll.
        if ($messages !== []) {
            Message::markRead($user['user_id'], $peerId);
        }

        User::touchLastSeen($user['user_id']);
        $peer = User::find($peerId);

        Response::json([
            'messages' => array_map(static fn (array $m) => [
                'id'      => (int) $m['message_id'],
                'mine'    => $m['sender_id'] === $user['user_id'],
                'body'    => $m['body'],
                'sent_at' => date('c', strtotime((string) $m['created_at'])),
                'time'    => date('g:i A', strtotime((string) $m['created_at'])),
            ], $messages),
            'peer_online' => User::isOnline($peer['last_seen_at'] ?? null),
        ]);
    }

    public function send(Request $request, string $peerId): never
    {
        $user = Auth::requireMember();
        $this->authorisePeer($user['user_id'], $peerId);

        $body = trim((string) $request->input('body', ''));

        if ($body === '') {
            Response::json(['error' => 'Message cannot be empty.'], 422);
        }

        if (mb_strlen($body) > 2000) {
            Response::json(['error' => 'Messages are limited to 2000 characters.'], 422);
        }

        $id = Message::send($user['user_id'], $peerId, $body);

        Response::json([
            'message' => [
                'id'   => $id,
                'mine' => true,
                'body' => $body,
                'time' => date('g:i A'),
            ],
        ], 201);
    }

    /**
     * You may only talk to someone you have matched with.
     *
     * The original checked nothing — chat.php and get-chat.php took a user id
     * from the URL and handed over the conversation, so any member could read
     * any two people's messages.
     *
     * @return array<string,mixed>
     */
    private function authorisePeer(string $userId, string $peerId): array
    {
        $peer = User::find($peerId);

        if ($peer === null || $peer['account_status'] !== 'Active') {
            if (Request::capture()->isAjax()) {
                Response::json(['error' => 'Conversation unavailable.'], 404);
            }

            Response::notFound('That member is not available.');
        }

        if (!ConnectionRequest::areConnected($userId, $peerId)) {
            if (Request::capture()->isAjax()) {
                Response::json(['error' => 'You are not connected with this member.'], 403);
            }

            Response::forbidden('You can only message members you have matched with.');
        }

        return $peer;
    }
}
