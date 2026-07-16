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

/**
 * Profile photo URL with a generated fallback when the user has no photo.
 *
 * @param 'square'|'wide' $shape 'wide' suits the 4:3 media area on a card.
 */
function photo_url(?string $photo, string $seedName = '?', string $shape = 'square'): string
{
    if (is_string($photo) && $photo !== '' && is_file(BASE_PATH . '/public/uploads/' . $photo)) {
        return Response::url('uploads/' . rawurlencode($photo));
    }

    return avatar_data_uri($seedName, $shape);
}

/**
 * Inline SVG avatar built from a name's initials.
 *
 * Removes the whole class of "default-profile.png 404s" the original had — it
 * referenced three different default paths, at least two of which did not
 * exist — with no binary asset and no request.
 *
 * @param 'square'|'wide' $shape
 */
function avatar_data_uri(string $name, string $shape = 'square'): string
{
    $initials = '';

    foreach (preg_split('/\s+/', trim($name)) ?: [] as $part) {
        if ($part !== '' && mb_strlen($initials) < 2) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }
    }

    $initials = $initials === '' ? '?' : $initials;

    // Deterministic, so a given name always gets the same colour — but confined
    // to a 60-degree warm arc (pink → red → terracotta) so a wall of generated
    // avatars still reads as one palette rather than a bag of highlighters.
    // The arc stops short of yellow, which fights the wine brand.
    $hue = (330 + (crc32($name) % 60)) % 360;

    [$w, $h, $fontSize] = $shape === 'wide' ? [160, 120, 42] : [96, 96, 38];

    // The gradient darkens rather than rotating hue: a hue shift on the second
    // stop drags anything near orange into olive, which reads as a different
    // colour family entirely.
    $svg = sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %2$d">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="hsl(%3$d 40%% 47%%)"/>'
        . '<stop offset="1" stop-color="hsl(%4$d 46%% 29%%)"/>'
        . '</linearGradient></defs>'
        . '<rect width="%1$d" height="%2$d" fill="url(#g)"/>'
        . '<circle cx="%5$s" cy="%6$s" r="%7$s" fill="none" stroke="rgba(255,255,255,.16)" stroke-width="1"/>'
        . '<text x="%5$s" y="%6$s" text-anchor="middle" dominant-baseline="central" '
        . 'font-family="Georgia,serif" font-size="%8$d" fill="rgba(255,255,255,.92)">%9$s</text></svg>',
        $w,
        $h,
        $hue,
        $hue,
        $w / 2,
        $h / 2,
        min($w, $h) * 0.36,
        $fontSize,
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

/**
 * "software-engineer" -> "Software Engineer"
 *
 * Acronyms are restored afterwards, otherwise ucwords() turns "ux-ui-designer"
 * into "Ux Ui Designer" and "hr-manager" into "Hr Manager".
 */
function humanise(?string $slug): string
{
    if (!is_string($slug) || $slug === '') {
        return '—';
    }

    $label = ucwords(str_replace(['-', '_'], ' ', $slug));

    $acronyms = [
        'Ux Ui' => 'UX/UI',
        'It '   => 'IT ',
        'Hr '   => 'HR ',
        'Ai '   => 'AI ',
        'Hvac'  => 'HVAC',
        'Ux'    => 'UX',
        'Ui'    => 'UI',
    ];

    return strtr($label, $acronyms);
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

/**
 * Renders <option> tags, marking the current value selected.
 *
 * @param array<string,string> $options slug => label
 */
function select_options(array $options, mixed $selected = null, ?string $placeholder = null): string
{
    $html = '';

    if ($placeholder !== null) {
        $html .= '<option value=""' . ($selected === null || $selected === '' ? ' selected' : '') . '>'
            . e($placeholder) . '</option>';
    }

    foreach ($options as $value => $label) {
        $html .= sprintf(
            '<option value="%s"%s>%s</option>',
            e($value),
            ((string) $value === (string) $selected) ? ' selected' : '',
            e($label)
        );
    }

    return $html;
}

/**
 * Renders grouped <optgroup>/<option> tags.
 *
 * @param array<string, array<string,string>> $groups
 */
function grouped_select_options(array $groups, mixed $selected = null, ?string $placeholder = null): string
{
    $html = '';

    if ($placeholder !== null) {
        $html .= '<option value=""' . ($selected === null || $selected === '' ? ' selected' : '') . '>'
            . e($placeholder) . '</option>';
    }

    foreach ($groups as $group => $options) {
        $html .= '<optgroup label="' . e($group) . '">';
        $html .= select_options($options, $selected);
        $html .= '</optgroup>';
    }

    return $html;
}

/** First validation error for a field, or null. */
function field_error(array $errors, string $field): ?string
{
    return $errors[$field][0] ?? null;
}
