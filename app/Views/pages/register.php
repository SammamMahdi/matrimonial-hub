<?php

use App\Core\Csrf;

/** @var array $errors */
/** @var array $genders */
/** @var array $religions */
/** @var array $ethnicities */
/** @var array $professionGroups */

$err = static fn (string $field): ?string => field_error($errors, $field);
?>
<div class="auth">
    <aside class="auth-aside">
        <div class="auth-quote stack">
            <p class="eyebrow" style="color: var(--gold-300);">Two minutes</p>
            <h2>Start with who you are.</h2>
            <p>
                This is the part everyone else will read. You can fill in the rest — education,
                family, the things you love — right after, and a photo whenever you are ready.
            </p>

            <ul class="stack-sm" style="margin-top: var(--sp-4);">
                <?php foreach ([
                    'Free, and deletable at any time',
                    'Nobody can message you until you accept them',
                    'Your national ID is never shown on your profile',
                ] as $point): ?>
                    <li class="row" style="gap: var(--sp-3); align-items: flex-start;">
                        <span style="color: var(--gold-300); flex: none;"><?= icon('check', 16) ?></span>
                        <span class="small" style="color: rgba(255,255,255,.78);"><?= e($point) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <div class="auth-main">
        <div class="auth-form stack-lg" style="width: min(100%, 34rem);">
            <div class="stack-sm">
                <h1>Create your profile</h1>
                <p class="muted small">
                    Already a member? <a href="<?= url('/login') ?>">Sign in</a>.
                </p>
            </div>

            <form method="post" action="<?= url('/register') ?>" enctype="multipart/form-data" class="stack-lg" novalidate>
                <?= Csrf::field() ?>

                <fieldset>
                    <legend class="fieldset-title">Your name</legend>

                    <div class="field-row">
                        <div class="field <?= $err('first_name') ? 'field-error' : '' ?>">
                            <label class="label" for="first_name">First name <span class="req">*</span></label>
                            <input class="input" id="first_name" name="first_name" value="<?= e(old('first_name')) ?>" required>
                            <?php if ($err('first_name')): ?><span class="error-text"><?= e($err('first_name')) ?></span><?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="label" for="middle_name">Middle name</label>
                            <input class="input" id="middle_name" name="middle_name" value="<?= e(old('middle_name')) ?>">
                        </div>
                    </div>

                    <div class="field <?= $err('last_name') ? 'field-error' : '' ?>">
                        <label class="label" for="last_name">Last name <span class="req">*</span></label>
                        <input class="input" id="last_name" name="last_name" value="<?= e(old('last_name')) ?>" required>
                        <?php if ($err('last_name')): ?><span class="error-text"><?= e($err('last_name')) ?></span><?php endif; ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="fieldset-title">About you</legend>

                    <div class="field-row">
                        <div class="field <?= $err('dob') ? 'field-error' : '' ?>">
                            <label class="label" for="dob">Date of birth <span class="req">*</span></label>
                            <input class="input" type="date" id="dob" name="dob" value="<?= e(old('dob')) ?>"
                                   max="<?= date('Y-m-d', strtotime('-18 years')) ?>" required>
                            <?php if ($err('dob')): ?>
                                <span class="error-text"><?= e($err('dob')) ?></span>
                            <?php else: ?>
                                <span class="hint">You must be 18 or older.</span>
                            <?php endif; ?>
                        </div>

                        <div class="field <?= $err('gender') ? 'field-error' : '' ?>">
                            <label class="label" for="gender">Gender <span class="req">*</span></label>
                            <select class="select" id="gender" name="gender" required>
                                <?= select_options($genders, old('gender'), 'Select…') ?>
                            </select>
                            <?php if ($err('gender')): ?><span class="error-text"><?= e($err('gender')) ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field <?= $err('religion') ? 'field-error' : '' ?>">
                            <label class="label" for="religion">Religion <span class="req">*</span></label>
                            <select class="select" id="religion" name="religion" required>
                                <?= select_options($religions, old('religion'), 'Select…') ?>
                            </select>
                            <?php if ($err('religion')): ?><span class="error-text"><?= e($err('religion')) ?></span><?php endif; ?>
                        </div>

                        <div class="field <?= $err('ethnicity') ? 'field-error' : '' ?>">
                            <label class="label" for="ethnicity">Ethnicity <span class="req">*</span></label>
                            <select class="select" id="ethnicity" name="ethnicity" required>
                                <?= select_options($ethnicities, old('ethnicity'), 'Select…') ?>
                            </select>
                            <?php if ($err('ethnicity')): ?><span class="error-text"><?= e($err('ethnicity')) ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="field <?= $err('profession') ? 'field-error' : '' ?>">
                        <label class="label" for="profession">Profession <span class="req">*</span></label>
                        <select class="select" id="profession" name="profession" required>
                            <?= grouped_select_options($professionGroups, old('profession'), 'Select…') ?>
                        </select>
                        <?php if ($err('profession')): ?><span class="error-text"><?= e($err('profession')) ?></span><?php endif; ?>
                    </div>

                    <div class="field <?= $err('photo') ? 'field-error' : '' ?>">
                        <label class="label" for="photo">Profile photo</label>
                        <input class="input" type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp"
                               data-photo-input>
                        <?php if ($err('photo')): ?>
                            <span class="error-text"><?= e($err('photo')) ?></span>
                        <?php else: ?>
                            <span class="hint">Optional — JPEG, PNG or WebP, up to 4 MB. We will generate one until you add yours.</span>
                        <?php endif; ?>
                        <img class="avatar avatar-lg" data-photo-preview hidden alt="" style="margin-top: var(--sp-3);">
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="fieldset-title">Sign-in details</legend>

                    <div class="field <?= $err('email') ? 'field-error' : '' ?>">
                        <label class="label" for="email">Email address <span class="req">*</span></label>
                        <input class="input" type="email" id="email" name="email" value="<?= e(old('email')) ?>"
                               autocomplete="email" required>
                        <?php if ($err('email')): ?><span class="error-text"><?= e($err('email')) ?></span><?php endif; ?>
                    </div>

                    <div class="field <?= $err('nid') ? 'field-error' : '' ?>">
                        <label class="label" for="nid">National ID number <span class="req">*</span></label>
                        <input class="input" id="nid" name="nid" value="<?= e(old('nid')) ?>" required>
                        <?php if ($err('nid')): ?>
                            <span class="error-text"><?= e($err('nid')) ?></span>
                        <?php else: ?>
                            <span class="hint">Used to verify you are real. It is never shown on your profile.</span>
                        <?php endif; ?>
                    </div>

                    <div class="field-row">
                        <div class="field <?= $err('password') ? 'field-error' : '' ?>">
                            <label class="label" for="password">Password <span class="req">*</span></label>
                            <input class="input" type="password" id="password" name="password"
                                   autocomplete="new-password" minlength="8" required>
                            <?php if ($err('password')): ?>
                                <span class="error-text"><?= e($err('password')) ?></span>
                            <?php else: ?>
                                <span class="hint">At least 8 characters.</span>
                            <?php endif; ?>
                        </div>

                        <div class="field <?= $err('password_confirmation') ? 'field-error' : '' ?>">
                            <label class="label" for="password_confirmation">Confirm password <span class="req">*</span></label>
                            <input class="input" type="password" id="password_confirmation" name="password_confirmation"
                                   autocomplete="new-password" required>
                            <?php if ($err('password_confirmation')): ?>
                                <span class="error-text"><?= e($err('password_confirmation')) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </fieldset>

                <button class="btn btn-block btn-lg" type="submit">
                    Create my profile <?= icon('arrow-right', 18) ?>
                </button>

                <p class="small muted center">
                    By joining you agree to our <a href="<?= url('/terms') ?>">terms</a>
                    and <a href="<?= url('/privacy') ?>">privacy policy</a>.
                </p>
            </form>
        </div>
    </div>
</div>
