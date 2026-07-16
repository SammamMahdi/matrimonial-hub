<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;
use App\Models\ActivityLog;
use App\Models\ConnectionRequest;
use App\Models\Message;
use App\Models\Preference;
use App\Models\Profile;
use App\Models\User;
use App\Support\Matcher;

final class DashboardController
{
    public function index(Request $request): string
    {
        $user = Auth::requireMember();
        User::touchLastSeen($user['user_id']);

        $full        = User::findWithProfile($user['user_id']) ?? $user;
        $preferences = Preference::find($user['user_id']);

        // Every figure here is a real query. The original showed hard-coded
        // constants: $profile_views = 102, $matches_found = 15.
        $stats = [
            'matches'   => ConnectionRequest::countMatches($user['user_id']),
            'requests'  => ConnectionRequest::countIncoming($user['user_id']),
            'unread'    => Message::unreadCount($user['user_id']),
            'complete'  => Profile::completeness($full),
        ];

        $suggestions = [];

        if (Preference::isConfigured($preferences)) {
            $candidates  = User::candidatesFor($user['user_id'], [
                'gender' => $preferences['preferred_gender'] ?? null,
            ], 60);
            $suggestions = array_slice(Matcher::rank($preferences, $candidates), 0, 3);
        }

        return View::render('pages/dashboard', [
            'title'          => 'Dashboard',
            'user'           => $full,
            'stats'          => $stats,
            'suggestions'    => $suggestions,
            'hasPreferences' => Preference::isConfigured($preferences),
            'incoming'       => array_slice(ConnectionRequest::incoming($user['user_id']), 0, 3),
            'activity'       => ActivityLog::forUser($user['user_id'], 5),
        ]);
    }
}
