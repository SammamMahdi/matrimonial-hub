<?php

use App\Core\Csrf;
use App\Core\Request;

/** @var array|null $currentUser */
/** @var int $pendingCount */
/** @var int $unreadCount */

$path = Request::capture()->path();

/** Marks the current section so the nav can show where you are. */
$isActive = static function (string $prefix) use ($path): bool {
    return $prefix === '/' ? $path === '/' : str_starts_with($path, $prefix);
};
?>
<header class="header" data-header>
    <div class="wrap header-inner">
        <a class="brand" href="<?= url('/') ?>">
            <span class="brand-mark"><?= icon('ring', 18) ?></span>
            Matrimonial<span class="muted">Hub</span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle>
            <span class="sr-only">Menu</span>
            <?= icon('menu', 22) ?>
        </button>

        <nav class="nav" id="primary-nav" data-nav aria-label="Primary">
            <?php if ($currentUser !== null): ?>
                <a class="nav-link <?= $isActive('/dashboard') ? 'is-active' : '' ?>" href="<?= url('/dashboard') ?>">Dashboard</a>
                <a class="nav-link <?= $isActive('/browse') ? 'is-active' : '' ?>" href="<?= url('/browse') ?>">Browse</a>

                <a class="nav-link <?= $isActive('/requests') ? 'is-active' : '' ?>" href="<?= url('/requests') ?>">
                    Requests
                    <?php if ($pendingCount > 0): ?>
                        <span class="count-dot"><?= (int) $pendingCount ?></span>
                    <?php endif; ?>
                </a>

                <a class="nav-link <?= $isActive('/matches') || $isActive('/chat') ? 'is-active' : '' ?>" href="<?= url('/matches') ?>">
                    Matches
                    <?php if ($unreadCount > 0): ?>
                        <span class="count-dot"><?= (int) $unreadCount ?></span>
                    <?php endif; ?>
                </a>

                <a class="nav-link <?= $isActive('/profile') ? 'is-active' : '' ?>" href="<?= url('/profile') ?>">Profile</a>

                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle dark mode">
                    <?= icon('sun', 17, 'icon-sun') ?><?= icon('moon', 17, 'icon-moon') ?>
                </button>

                <form method="post" action="<?= url('/logout') ?>">
                    <?= Csrf::field() ?>
                    <button class="btn btn-ghost btn-sm" type="submit"><?= icon('logout', 16) ?> Sign out</button>
                </form>
            <?php else: ?>
                <a class="nav-link <?= $isActive('/about') ? 'is-active' : '' ?>" href="<?= url('/about') ?>">About</a>
                <a class="nav-link <?= $isActive('/stories') ? 'is-active' : '' ?>" href="<?= url('/stories') ?>">Stories</a>
                <a class="nav-link <?= $isActive('/help') ? 'is-active' : '' ?>" href="<?= url('/help') ?>">Help</a>

                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle dark mode">
                    <?= icon('sun', 17, 'icon-sun') ?><?= icon('moon', 17, 'icon-moon') ?>
                </button>

                <a class="nav-link" href="<?= url('/login') ?>">Sign in</a>
                <a class="btn btn-sm" href="<?= url('/register') ?>">Join free</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
