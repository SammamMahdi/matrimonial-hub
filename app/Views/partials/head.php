<?php /** @var string|null $title */ ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Matrimonial Hub') ?><?= ($title ?? '') !== 'Matrimonial Hub — Find someone who fits your life' ? ' · Matrimonial Hub' : '' ?></title>
<meta name="description" content="Matrimonial Hub helps you find someone who fits your life — build a profile, set what matters to you, and get matches ranked on real compatibility.">
<meta name="theme-color" content="#8f1f45">

<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#128150;</text></svg>">

<?php
// Set the theme before first paint so a dark-mode user never sees a white
// flash. This has to be inline and blocking — a deferred script is too late.
?>
<script>
    (function () {
        document.documentElement.classList.add('js');
        try {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || t === 'light') {
                document.documentElement.dataset.theme = t;
            }
        } catch (e) { /* private mode — fall back to the media query */ }
    })();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
