<?php /** @var string $content */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body>
<?php require __DIR__ . '/../partials/flash.php'; ?>

<main id="main">
    <?= $content ?>
</main>

<script src="<?= asset('assets/js/app.js') ?>" defer></script>
</body>
</html>
