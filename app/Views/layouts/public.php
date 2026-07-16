<?php

use App\Core\Auth;

/** @var string $content */
/** @var string|null $title */

// Public pages render for signed-out visitors, so the badge counts the header
// wants are always zero here.
$currentUser  = Auth::user();
$pendingCount = 0;
$unreadCount  = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<main id="main">
    <?= $content ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

<script src="<?= asset('assets/js/app.js') ?>" defer></script>
</body>
</html>
