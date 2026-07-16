<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;
use App\Models\ConnectionRequest;
use App\Models\Preference;
use App\Models\User;
use App\Support\Matcher;
use App\Support\Vocabulary;

final class BrowseController
{
    private const PER_PAGE = 12;

    public function index(Request $request): string
    {
        $user        = Auth::requireMember();
        $preferences = Preference::find($user['user_id']);

        // Filters come from the query string so a search stays shareable and
        // survives paging. Each value is validated against the vocabulary
        // before it reaches SQL.
        $filters = [
            'q'              => (string) $request->query('q', ''),
            'gender'         => $this->pick($request->query('gender'), Vocabulary::genders()),
            'religion'       => $this->pick($request->query('religion'), Vocabulary::religions()),
            'ethnicity'      => $this->pick($request->query('ethnicity'), Vocabulary::ethnicities()),
            'profession'     => $this->pick($request->query('profession'), Vocabulary::professions()),
            'marital_status' => $this->pick($request->query('marital_status'), Vocabulary::maritalStatuses()),
            'complexion'     => $this->pick($request->query('complexion'), Vocabulary::complexions()),
            'min_age'        => $this->number($request->query('min_age'), 18, 100),
            'max_age'        => $this->number($request->query('max_age'), 18, 100),
            'min_height'     => $this->number($request->query('min_height'), 100, 250),
            'max_height'     => $this->number($request->query('max_height'), 100, 250),
        ];

        // With no explicit gender filter, fall back to what they said they want.
        if ($filters['gender'] === null && !empty($preferences['preferred_gender'])) {
            $filters['gender'] = $preferences['preferred_gender'];
        }

        $candidates = User::candidatesFor($user['user_id'], array_filter($filters, static fn ($v) => $v !== null && $v !== ''));
        $ranked     = Matcher::rank($preferences, $candidates);

        // Annotate each card with any request already in flight, so the button
        // can say "Pending" instead of letting them send a duplicate.
        foreach ($ranked as $i => $candidate) {
            $existing = ConnectionRequest::between($user['user_id'], $candidate['user_id']);
            $ranked[$i]['request_status']    = $existing['status'] ?? null;
            $ranked[$i]['request_direction'] = $existing === null
                ? null
                : ($existing['sender_id'] === $user['user_id'] ? 'outgoing' : 'incoming');
        }

        $page  = max(1, (int) $request->query('page', 1));
        $total = count($ranked);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page  = min($page, $pages);
        $slice = array_slice($ranked, ($page - 1) * self::PER_PAGE, self::PER_PAGE);

        return View::render('pages/browse', [
            'title'            => 'Browse matches',
            'user'             => $user,
            'results'          => $slice,
            'total'            => $total,
            'page'             => $page,
            'pages'            => $pages,
            'filters'          => $filters,
            'hasPreferences'   => Preference::isConfigured($preferences),
            'genders'          => Vocabulary::genders(),
            'religions'        => Vocabulary::religions(),
            'ethnicities'      => Vocabulary::ethnicities(),
            'professionGroups' => Vocabulary::professionGroups(),
            'maritalStatuses'  => Vocabulary::maritalStatuses(),
            'complexions'      => Vocabulary::complexions(),
        ]);
    }

    /** @param array<string,string> $allowed */
    private function pick(mixed $value, array $allowed): ?string
    {
        return is_string($value) && isset($allowed[$value]) ? $value : null;
    }

    private function number(mixed $value, int $min, int $max): ?int
    {
        if (!is_numeric($value)) {
            return null;
        }

        $number = (int) $value;

        return ($number >= $min && $number <= $max) ? $number : null;
    }
}
