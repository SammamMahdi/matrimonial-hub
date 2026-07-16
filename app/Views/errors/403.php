<?php /** @var string $message */ ?>
<div class="wrap page" style="display: grid; place-items: center;">
    <div class="center stack" style="max-width: 34rem;">
        <span class="stat-icon" style="margin-inline: auto; width: 56px; height: 56px;">
            <?= icon('lock', 26) ?>
        </span>
        <h1>Not your door</h1>
        <p class="soft"><?= e($message ?? 'You do not have access to that.') ?></p>
        <div class="row" style="justify-content: center; margin-top: var(--sp-3);">
            <a class="btn" href="<?= url('/dashboard') ?>">Back to dashboard</a>
        </div>
    </div>
</div>
