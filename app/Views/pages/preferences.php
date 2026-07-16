<?php

use App\Core\Csrf;

/** @var array $preferences */
/** @var array $errors */
/** @var array $genders, $religions, $ethnicities, $professionGroups, $maritalStatuses */
/** @var array $undergraduateDegrees, $postgraduateDegrees */

$err = static fn (string $f): ?string => field_error($errors, $f);

/** Prefer submitted-but-rejected input over the saved value when redisplaying. */
$value = static function (string $key) use ($preferences) {
    $old = old($key, null);

    return $old !== null && $old !== '' ? $old : ($preferences[$key] ?? '');
};

$educationOptions = $undergraduateDegrees + $postgraduateDegrees;
?>
<div class="wrap-narrow page">

    <div class="page-head">
        <div class="stack-sm">
            <p class="eyebrow">Preferences</p>
            <h1>Who are you hoping to meet?</h1>
            <p class="small muted">
                Fill in what matters. Leave anything blank and we will read that as “I don't mind” —
                blank fields are never counted against a profile.
            </p>
        </div>
    </div>

    <form method="post" action="<?= url('/preferences') ?>" class="stack-lg" novalidate>
        <?= Csrf::field() ?>

        <section class="panel">
            <div class="panel-head"><h2 style="font-size: var(--step-1);">The essentials</h2></div>
            <div class="panel-body">
                <div class="field-row">
                    <div class="field">
                        <label class="label" for="preferred_gender">Gender</label>
                        <select class="select" id="preferred_gender" name="preferred_gender">
                            <?= select_options($genders, $value('preferred_gender'), 'No preference') ?>
                        </select>
                        <span class="hint">This one also filters your browse results.</span>
                    </div>

                    <div class="field">
                        <label class="label" for="preferred_religion">Religion</label>
                        <select class="select" id="preferred_religion" name="preferred_religion">
                            <?= select_options($religions, $value('preferred_religion'), 'No preference') ?>
                        </select>
                    </div>
                </div>

                <div class="field <?= $err('min_age') ? 'field-error' : '' ?>">
                    <label class="label">Age range</label>
                    <div class="field-row">
                        <input class="input" type="number" name="min_age" min="18" max="100" placeholder="From"
                               value="<?= e($value('min_age')) ?>" aria-label="Minimum age">
                        <input class="input" type="number" name="max_age" min="18" max="100" placeholder="To"
                               value="<?= e($value('max_age')) ?>" aria-label="Maximum age">
                    </div>
                    <?php if ($err('min_age')): ?>
                        <span class="error-text"><?= e($err('min_age')) ?></span>
                    <?php elseif ($err('max_age')): ?>
                        <span class="error-text"><?= e($err('max_age')) ?></span>
                    <?php else: ?>
                        <span class="hint">Someone just outside this still scores partial credit.</span>
                    <?php endif; ?>
                </div>

                <div class="field <?= $err('min_height_cm') ? 'field-error' : '' ?>">
                    <label class="label">Height range (cm)</label>
                    <div class="field-row">
                        <input class="input" type="number" name="min_height_cm" min="100" max="250" placeholder="From"
                               value="<?= e($value('min_height_cm')) ?>" aria-label="Minimum height">
                        <input class="input" type="number" name="max_height_cm" min="100" max="250" placeholder="To"
                               value="<?= e($value('max_height_cm')) ?>" aria-label="Maximum height">
                    </div>
                    <?php if ($err('min_height_cm')): ?><span class="error-text"><?= e($err('min_height_cm')) ?></span><?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="preferred_marital_status">Marital status</label>
                    <select class="select" id="preferred_marital_status" name="preferred_marital_status">
                        <?= select_options($maritalStatuses, $value('preferred_marital_status'), 'No preference') ?>
                    </select>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head"><h2 style="font-size: var(--step-1);">Background</h2></div>
            <div class="panel-body">
                <div class="field-row">
                    <div class="field">
                        <label class="label" for="preferred_ethnicity">Ethnicity</label>
                        <select class="select" id="preferred_ethnicity" name="preferred_ethnicity">
                            <?= select_options($ethnicities, $value('preferred_ethnicity'), 'No preference') ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="label" for="preferred_education">Education</label>
                        <select class="select" id="preferred_education" name="preferred_education">
                            <?= select_options($educationOptions, $value('preferred_education'), 'No preference') ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="preferred_profession">Profession</label>
                    <select class="select" id="preferred_profession" name="preferred_profession">
                        <?= grouped_select_options($professionGroups, $value('preferred_profession'), 'No preference') ?>
                    </select>
                    <span class="hint">Someone in the same field, but a different job, still earns partial credit.</span>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head"><h2 style="font-size: var(--step-1);">Things you would like to share</h2></div>
            <div class="panel-body">
                <div class="field <?= $err('interests') ? 'field-error' : '' ?>">
                    <label class="label" for="interests">Interests</label>
                    <textarea class="textarea" id="interests" name="interests"
                              placeholder="Reading, travelling, food"><?= e($value('interests')) ?></textarea>
                    <?php if ($err('interests')): ?>
                        <span class="error-text"><?= e($err('interests')) ?></span>
                    <?php else: ?>
                        <span class="hint">Separate with commas. We match these against what others listed.</span>
                    <?php endif; ?>
                </div>

                <div class="field <?= $err('hobbies') ? 'field-error' : '' ?>">
                    <label class="label" for="hobbies">Hobbies</label>
                    <textarea class="textarea" id="hobbies" name="hobbies"
                              placeholder="Cooking, cricket, photography"><?= e($value('hobbies')) ?></textarea>
                    <?php if ($err('hobbies')): ?><span class="error-text"><?= e($err('hobbies')) ?></span><?php endif; ?>
                </div>
            </div>
        </section>

        <div class="row row-wrap">
            <button class="btn btn-lg" type="submit">Save and see matches <?= icon('arrow-right', 17) ?></button>
            <a class="btn btn-ghost btn-lg" href="<?= url('/dashboard') ?>">Back to dashboard</a>
        </div>
    </form>
</div>
