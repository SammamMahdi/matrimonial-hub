<?php /** @var string $message */ ?>
<div class="wrap page" style="display: grid; place-items: center;">
    <div class="center stack" style="max-width: 34rem;">
        <p class="display" style="font-size: var(--step-4); color: var(--wine-300);">404</p>
        <h1>We cannot find that page</h1>
        <p class="soft"><?= e($message ?? 'The page you were looking for has moved or never existed.') ?></p>
        <div class="row" style="justify-content: center; margin-top: var(--sp-3);">
            <a class="btn" href="<?= url('/') ?>">Go home</a>
            <a class="btn btn-ghost" href="<?= url('/browse') ?>">Browse profiles</a>
        </div>
    </div>
</div>
