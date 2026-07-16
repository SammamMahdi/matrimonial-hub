<?php

use App\Core\Csrf;

/** @var array<string,list<string>> $errors */
?>
<div class="auth">
    <aside class="auth-aside">
        <div class="auth-quote stack">
            <p class="eyebrow" style="color: var(--gold-300);">Welcome back</p>
            <h2>Someone has probably been looking for you.</h2>
            <p>
                New matches are ranked against your preferences every time you sign in — and
                every score tells you exactly why it landed where it did.
            </p>
        </div>
    </aside>

    <div class="auth-main">
        <div class="auth-form stack-lg">
            <div class="stack-sm">
                <h1>Sign in</h1>
                <p class="muted small">
                    New here? <a href="<?= url('/register') ?>">Create a free profile</a>.
                </p>
            </div>

            <form method="post" action="<?= url('/login') ?>" class="stack" novalidate>
                <?= Csrf::field() ?>

                <div class="field <?= isset($errors['email']) ? 'field-error' : '' ?>">
                    <label class="label" for="email">Email address</label>
                    <input class="input" type="email" id="email" name="email"
                           value="<?= e(old('email')) ?>" autocomplete="email" required autofocus>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-text"><?= e($errors['email'][0]) ?></span>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="password">Password</label>
                    <input class="input" type="password" id="password" name="password"
                           autocomplete="current-password" required>
                </div>

                <button class="btn btn-block btn-lg" type="submit">
                    <?= icon('lock', 17) ?> Sign in
                </button>
            </form>

            <p class="small muted center">
                By signing in you agree to our <a href="<?= url('/terms') ?>">terms</a>
                and <a href="<?= url('/privacy') ?>">privacy policy</a>.
            </p>
        </div>
    </div>
</div>
