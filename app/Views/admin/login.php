<?php use App\Core\Csrf; ?>

<div class="auth-main" style="min-height: 100vh;">
    <div class="auth-form stack-lg">
        <div class="center stack-sm">
            <span class="brand-mark" style="width: 46px; height: 46px; margin-inline: auto;">
                <?= icon('shield', 24) ?>
            </span>
            <h1 style="font-size: var(--step-2);">Administrator sign-in</h1>
            <p class="small muted">Staff access only.</p>
        </div>

        <form method="post" action="<?= url('/admin/login') ?>" class="card stack" novalidate>
            <?= Csrf::field() ?>

            <div class="field">
                <label class="label" for="username">Username</label>
                <input class="input" id="username" name="username" autocomplete="username" required autofocus>
            </div>

            <div class="field">
                <label class="label" for="password">Password</label>
                <input class="input" type="password" id="password" name="password"
                       autocomplete="current-password" required>
            </div>

            <button class="btn btn-block" type="submit"><?= icon('lock', 16) ?> Sign in</button>
        </form>

        <p class="center small muted">
            <a href="<?= url('/') ?>">Back to Matrimonial Hub</a>
        </p>
    </div>
</div>
