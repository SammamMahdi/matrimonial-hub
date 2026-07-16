<?php

/**
 * Inline SVG icon set.
 *
 * The original pulled Font Awesome from a CDN for a handful of glyphs. These
 * are the icons the app actually uses, inlined — no extra request, no CDN
 * dependency, and they inherit currentColor.
 */
function icon(string $name, int $size = 20, string $class = ''): string
{
    $paths = [
        'heart'    => '<path d="M19.5 12.6 12 20l-7.5-7.4A5 5 0 0 1 12 6.3a5 5 0 0 1 7.5 6.3Z"/>',
        'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'users'    => '<path d="M16 19v-1.5a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4V19"/><circle cx="9" cy="7" r="3.5"/><path d="M22 19v-1.5a4 4 0 0 0-3-3.9M16 4.1a4 4 0 0 1 0 7.8"/>',
        'chat'     => '<path d="M20 14a2 2 0 0 1-2 2H8l-4 4V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2Z"/>',
        'send'     => '<path d="M21 3 10.5 13.5M21 3l-6.5 18-4-8-8-4Z"/>',
        'check'    => '<path d="m5 12 4.5 4.5L19 7"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
        'x'        => '<path d="M6 6 18 18M18 6 6 18"/>',
        'x-circle' => '<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/>',
        'alert'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5v5M12 16.2v.1"/>',
        'info'     => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5.5M12 7.6v.1"/>',
        'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/>',
        'sliders'  => '<path d="M4 6h9M17 6h3M4 12h3M11 12h9M4 18h9M17 18h3"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="15" cy="18" r="2"/>',
        'shield'   => '<path d="M12 3 5 6v5.5c0 4.3 2.9 8.2 7 9.5 4.1-1.3 7-5.2 7-9.5V6Z"/><path d="m9 12 2 2 4-4"/>',
        'sparkle'  => '<path d="M12 3.5 13.8 9 19 10.8 13.8 12.6 12 18l-1.8-5.4L5 10.8 10.2 9Z"/><path d="M18.5 4v3M20 5.5h-3"/>',
        'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/>',
        'lock'     => '<rect x="4.5" y="10" width="15" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'arrow-right' => '<path d="M4 12h16M14 6l6 6-6 6"/>',
        'arrow-left'  => '<path d="M20 12H4M10 18l-6-6 6-6"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'menu'     => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
        'moon'     => '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5Z"/>',
        'trash'    => '<path d="M4 7h16M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12"/>',
        'camera'   => '<path d="M4 8h3l1.5-2h7L17 8h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13" r="3.5"/>',
        'grid'     => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
        'inbox'    => '<path d="M4 13h4l1.5 3h5L16 13h4"/><path d="M4 13 6 5h12l2 8v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Z"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 2"/>',
        'logout'   => '<path d="M9 20H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h4"/><path d="M15 16.5 19.5 12 15 7.5M19.5 12H9"/>',
        'ring'     => '<circle cx="12" cy="14" r="6"/><path d="m9 6 3-3 3 3-3 2.5Z"/>',
    ];

    $path = $paths[$name] ?? $paths['info'];

    return sprintf(
        '<svg class="%s" width="%d" height="%d" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
        . 'stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
        e($class),
        $size,
        $size,
        $path
    );
}
