<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ConnectionRequest;

final class RequestController
{
    /** Incoming and outgoing requests in one place. */
    public function index(Request $request): string
    {
        $user = Auth::requireMember();

        return View::render('pages/requests', [
            'title'    => 'Requests',
            'incoming' => ConnectionRequest::incoming($user['user_id']),
            'outgoing' => ConnectionRequest::outgoing($user['user_id']),
        ]);
    }

    public function send(Request $request): void
    {
        $user       = Auth::requireMember();
        $receiverId = (string) $request->input('receiver_id', '');
        $note       = (string) $request->input('message', '');

        if (mb_strlen($note) > 500) {
            $note = mb_substr($note, 0, 500);
        }

        // The sender is the session user, full stop. The original read it from
        // a hidden form field, so anyone could send requests as anyone else.
        $result = ConnectionRequest::send($user['user_id'], $receiverId, $note);

        Flash::add($result['ok'] ? 'success' : 'error', $result['message']);
        Response::redirect($this->backTo($request));
    }

    public function respond(Request $request): void
    {
        $user      = Auth::requireMember();
        $requestId = (int) $request->input('request_id', 0);
        $action    = (string) $request->input('action', '');

        if (!in_array($action, ['accept', 'decline'], true)) {
            Flash::add('error', 'Unknown action.');
            Response::redirect('/requests');
        }

        // respond() scopes its UPDATE to receiver_id = this user, so one member
        // cannot accept a request that was sent to somebody else.
        $result = ConnectionRequest::respond($requestId, $user['user_id'], $action);

        Flash::add($result['ok'] ? 'success' : 'error', $result['message']);
        Response::redirect($this->backTo($request, '/requests'));
    }

    public function cancel(Request $request): void
    {
        $user      = Auth::requireMember();
        $requestId = (int) $request->input('request_id', 0);

        $result = ConnectionRequest::cancel($requestId, $user['user_id']);

        Flash::add($result['ok'] ? 'success' : 'error', $result['message']);
        Response::redirect($this->backTo($request, '/requests'));
    }

    /**
     * Returns the user to the page they acted from, but only if it is a local
     * path — an open redirect would let a phisher bounce users off this site.
     */
    private function backTo(Request $request, string $default = '/browse'): string
    {
        $return = (string) $request->input('return_to', '');

        if ($return !== '' && str_starts_with($return, '/') && !str_starts_with($return, '//')) {
            return $return;
        }

        return $default;
    }
}
