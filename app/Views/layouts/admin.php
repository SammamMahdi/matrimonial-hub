<?php

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;

/** @var string $content */
/** @var string|null $title */

$admin = Auth::admin();
$path  = Request::capture()->path();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<header class="header is-stuck">
    <div class="wrap header-inner">
        <a class="brand" href="<?= url('/admin') ?>">
            <span class="brand-mark"><?= icon('shield', 18) ?></span>
            Admin<span class="muted">Console</span>
        </a>

        <nav class="nav" aria-label="Admin">
            <a class="nav-link <?= $path === '/admin' ? 'is-active' : '' ?>" href="<?= url('/admin') ?>">Overview</a>
            <a class="nav-link <?= str_starts_with($path, '/admin/users') ? 'is-active' : '' ?>" href="<?= url('/admin/users') ?>">Members</a>
            <a class="nav-link" href="<?= url('/') ?>">View site</a>

            <button class="theme-toggle" type="button" data-theme-toggle aria-label="Toggle dark mode">
                <?= icon('sun', 17, 'icon-sun') ?><?= icon('moon', 17, 'icon-moon') ?>
            </button>

            <?php if ($admin !== null): ?>
                <span class="badge"><?= icon('user', 13) ?> <?= e($admin['username']) ?></span>
                <form method="post" action="<?= url('/admin/logout') ?>">
                    <?= Csrf::field() ?>
                    <button class="btn btn-ghost btn-sm" type="submit"><?= icon('logout', 16) ?> Sign out</button>
                </form>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php require __DIR__ . '/../partials/flash.php'; ?>

<main id="main">
    <?= $content ?>
</main>

<script src="<?= asset('assets/js/app.js') ?>" defer></script>
</body>
</html>
