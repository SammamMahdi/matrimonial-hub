<?php

use App\Core\Auth;
use App\Models\ConnectionRequest;
use App\Models\Message;

/** @var string $content */
/** @var string|null $title */

$currentUser = Auth::user();
$pendingCount = $currentUser ? ConnectionRequest::countIncoming($currentUser['user_id']) : 0;
$unreadCount  = $currentUser ? Message::unreadCount($currentUser['user_id']) : 0;
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
