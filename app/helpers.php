<?php

declare(strict_types=1);

use App\Core\Response;

/** Stores and reads the app config without a global. */
function app_config(?array $set = null): array
{
    static $config = [];

    if ($set !== null) {
        $config = $set;
    }

    return $config;
}

function config(string $key, mixed $default = null): mixed
{
    $value = app_config();

    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

/** Escape for HTML output. Used in every template — never on input. */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '/'): string
{
    return Response::url($path);
}

/** Cache-busted asset URL so CSS changes show up without a hard refresh. */
function asset(string $path): string
{
    $relative = 'public/' . ltrim($path, '/');
    $file     = BASE_PATH . '/' . $relative;
    $version  = is_file($file) ? substr((string) filemtime($file), -6) : '1';

    return Response::url(ltrim($path, '/')) . '?v=' . $version;
}

/** Profile photo URL with a generated fallback when the user has no photo. */
function photo_url(?string $photo, string $seedName = '?'): string
{
    if (is_string($photo) && $photo !== '' && is_file(BASE_PATH . '/public/uploads/' . $photo)) {
        return Response::url('uploads/' . rawurlencode($photo));
    }

    return avatar_data_uri($seedName);
}

/**
 * Inline SVG avatar built from a name's initials. Removes the whole class of
 * "default-profile.png 404s" bugs the original had, with no binary asset.
 */
function avatar_data_uri(string $name): string
{
    $initials = '';

    foreach (preg_split('/\s+/', trim($name)) ?: [] as $part) {
        if ($part !== '' && mb_strlen($initials) < 2) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }
    }

    $initials = $initials === '' ? '?' : $initials;

    // Deterministic hue so a given name always gets the same colour.
    $hue = crc32($name) % 360;

    $svg = sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="hsl(%d 62%% 62%%)"/>'
        . '<stop offset="1" stop-color="hsl(%d 58%% 44%%)"/>'
        . '</linearGradient></defs>'
        . '<rect width="96" height="96" fill="url(#g)"/>'
        . '<text x="48" y="49" text-anchor="middle" dominant-baseline="central" '
        . 'font-family="Georgia,serif" font-size="38" fill="rgba(255,255,255,.95)">%s</text></svg>',
        $hue,
        ($hue + 28) % 360,
        htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
    );

    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function age_from(?string $dob): ?int
{
    if (!is_string($dob) || $dob === '') {
        return null;
    }

    try {
        return (new DateTimeImmutable($dob))->diff(new DateTimeImmutable('today'))->y;
    } catch (Exception) {
        return null;
    }
}

/** "software-engineer" -> "Software Engineer" */
function humanise(?string $slug): string
{
    if (!is_string($slug) || $slug === '') {
        return '—';
    }

    return ucwords(str_replace(['-', '_'], ' ', $slug));
}

function full_name(array $user): string
{
    $parts = array_filter([
        $user['first_name'] ?? '',
        $user['middle_name'] ?? '',
        $user['last_name'] ?? '',
    ], static fn ($p) => is_string($p) && trim($p) !== '');

    return implode(' ', $parts);
}

/** "3 minutes ago" style timestamps. */
function time_ago(?string $timestamp): string
{
    if (!is_string($timestamp) || $timestamp === '') {
        return '';
    }

    try {
        $then = new DateTimeImmutable($timestamp);
    } catch (Exception) {
        return '';
    }

    $seconds = time() - $then->getTimestamp();

    if ($seconds < 60) {
        return 'just now';
    }

    $units = [
        31536000 => 'year',
        2592000  => 'month',
        604800   => 'week',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute',
    ];

    foreach ($units as $secondsPerUnit => $label) {
        if ($seconds >= $secondsPerUnit) {
            $count = (int) floor($seconds / $secondsPerUnit);

            return $count . ' ' . $label . ($count > 1 ? 's' : '') . ' ago';
        }
    }

    return 'just now';
}

function old(string $key, mixed $default = ''): mixed
{
    static $old = null;

    if ($old === null) {
        $old = App\Core\Flash::oldInput();
    }

    return $old[$key] ?? $default;
}
