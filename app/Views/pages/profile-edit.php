<?php

use App\Core\Csrf;

/** @var array $user */
/** @var int $completeness */
/** @var array $errors */
/** @var array $genders, $religions, $ethnicities, $professionGroups */
/** @var array $maritalStatuses, $complexions, $undergraduateDegrees, $postgraduateDegrees */

$err = static fn (string $f): ?string => field_error($errors, $f);

$value = static function (string $key) use ($user) {
    $old = old($key, null);

    return $old !== null && $old !== '' ? $old : ($user[$key] ?? '');
};
?>
<div class="wrap page">

    <div class="page-head">
        <div class="stack-sm">
            <p class="eyebrow">My profile</p>
            <h1>How you appear to others</h1>
        </div>
        <a class="btn btn-ghost" href="<?= url('/preferences') ?>"><?= icon('sliders', 17) ?> Preferences</a>
    </div>

    <div class="grid grid-main">
        <div class="stack-lg">

            <form method="post" action="<?= url('/profile') ?>" class="stack-lg" novalidate>
                <?= Csrf::field() ?>

                <!-- Identity ------------------------------------------- -->
                <section class="panel">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">About you</h2></div>
                    <div class="panel-body">
                        <div class="field-row">
                            <div class="field <?= $err('first_name') ? 'field-error' : '' ?>">
                                <label class="label" for="first_name">First name <span class="req">*</span></label>
                                <input class="input" id="first_name" name="first_name" value="<?= e($value('first_name')) ?>" required>
                                <?php if ($err('first_name')): ?><span class="error-text"><?= e($err('first_name')) ?></span><?php endif; ?>
                            </div>
                            <div class="field">
                                <label class="label" for="middle_name">Middle name</label>
                                <input class="input" id="middle_name" name="middle_name" value="<?= e($value('middle_name')) ?>">
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field <?= $err('last_name') ? 'field-error' : '' ?>">
                                <label class="label" for="last_name">Last name <span class="req">*</span></label>
                                <input class="input" id="last_name" name="last_name" value="<?= e($value('last_name')) ?>" required>
                                <?php if ($err('last_name')): ?><span class="error-text"><?= e($err('last_name')) ?></span><?php endif; ?>
                            </div>
                            <div class="field <?= $err('dob') ? 'field-error' : '' ?>">
                                <label class="label" for="dob">Date of birth <span class="req">*</span></label>
                                <input class="input" type="date" id="dob" name="dob" value="<?= e($value('dob')) ?>"
                                       max="<?= date('Y-m-d', strtotime('-18 years')) ?>" required>
                                <?php if ($err('dob')): ?><span class="error-text"><?= e($err('dob')) ?></span><?php endif; ?>
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field">
                                <label class="label" for="gender">Gender</label>
                                <select class="select" id="gender" name="gender">
                                    <?= select_options($genders, $value('gender')) ?>
                                </select>
                            </div>
                            <div class="field">
                                <label class="label" for="religion">Religion</label>
                                <select class="select" id="religion" name="religion">
                                    <?= select_options($religions, $value('religion')) ?>
                                </select>
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field">
                                <label class="label" for="ethnicity">Ethnicity</label>
                                <select class="select" id="ethnicity" name="ethnicity">
                                    <?= select_options($ethnicities, $value('ethnicity')) ?>
                                </select>
                            </div>
                            <div class="field">
                                <label class="label" for="profession">Profession</label>
                                <select class="select" id="profession" name="profession">
                                    <?= grouped_select_options($professionGroups, $value('profession')) ?>
                                </select>
                            </div>
                        </div>

                        <div class="field <?= $err('email') ? 'field-error' : '' ?>">
                            <label class="label" for="email">Email address <span class="req">*</span></label>
                            <input class="input" type="email" id="email" name="email" value="<?= e($value('email')) ?>" required>
                            <?php if ($err('email')): ?><span class="error-text"><?= e($err('email')) ?></span><?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Contact -------------------------------------------- -->
                <section class="panel">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">Contact &amp; address</h2></div>
                    <div class="panel-body">
                        <div class="field <?= $err('phone') ? 'field-error' : '' ?>">
                            <label class="label" for="phone">Phone number</label>
                            <input class="input" id="phone" name="phone" value="<?= e($value('phone')) ?>"
                                   placeholder="01XXX-XXXXXX">
                            <?php if ($err('phone')): ?>
                                <span class="error-text"><?= e($err('phone')) ?></span>
                            <?php else: ?>
                                <span class="hint">Only shown to members you have matched with.</span>
                            <?php endif; ?>
                        </div>

                        <div class="field-trio">
                            <div class="field">
                                <label class="label" for="road_number">Road</label>
                                <input class="input" id="road_number" name="road_number" value="<?= e($value('road_number')) ?>">
                            </div>
                            <div class="field">
                                <label class="label" for="street_number">Street</label>
                                <input class="input" id="street_number" name="street_number" value="<?= e($value('street_number')) ?>">
                            </div>
                            <div class="field">
                                <label class="label" for="building_number">Building</label>
                                <input class="input" id="building_number" name="building_number" value="<?= e($value('building_number')) ?>">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Education ------------------------------------------ -->
                <section class="panel">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">Education</h2></div>
                    <div class="panel-body">
                        <div class="field-row">
                            <div class="field">
                                <label class="label" for="secondary_education">Secondary (SSC)</label>
                                <input class="input" id="secondary_education" name="secondary_education"
                                       value="<?= e($value('secondary_education')) ?>" placeholder="School name">
                            </div>
                            <div class="field">
                                <label class="label" for="higher_secondary">Higher secondary (HSC)</label>
                                <input class="input" id="higher_secondary" name="higher_secondary"
                                       value="<?= e($value('higher_secondary')) ?>" placeholder="College name">
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field">
                                <label class="label" for="undergraduate">Undergraduate</label>
                                <select class="select" id="undergraduate" name="undergraduate">
                                    <?= select_options($undergraduateDegrees, $value('undergraduate'), 'Not applicable') ?>
                                </select>
                            </div>
                            <div class="field">
                                <label class="label" for="postgraduate">Postgraduate</label>
                                <select class="select" id="postgraduate" name="postgraduate">
                                    <?= select_options($postgraduateDegrees, $value('postgraduate'), 'Not applicable') ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Personal ------------------------------------------- -->
                <section class="panel">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">Personal details</h2></div>
                    <div class="panel-body">
                        <div class="field-trio">
                            <div class="field <?= $err('height_cm') ? 'field-error' : '' ?>">
                                <label class="label" for="height_cm">Height (cm)</label>
                                <input class="input" type="number" step="0.01" id="height_cm" name="height_cm"
                                       value="<?= e($value('height_cm')) ?>">
                                <?php if ($err('height_cm')): ?><span class="error-text"><?= e($err('height_cm')) ?></span><?php endif; ?>
                            </div>
                            <div class="field <?= $err('weight_kg') ? 'field-error' : '' ?>">
                                <label class="label" for="weight_kg">Weight (kg)</label>
                                <input class="input" type="number" step="0.01" id="weight_kg" name="weight_kg"
                                       value="<?= e($value('weight_kg')) ?>">
                                <?php if ($err('weight_kg')): ?><span class="error-text"><?= e($err('weight_kg')) ?></span><?php endif; ?>
                            </div>
                            <div class="field">
                                <label class="label" for="complexion">Complexion</label>
                                <select class="select" id="complexion" name="complexion">
                                    <?= select_options($complexions, $value('complexion'), 'Prefer not to say') ?>
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label" for="marital_status">Marital status</label>
                            <select class="select" id="marital_status" name="marital_status">
                                <?= select_options($maritalStatuses, $value('marital_status')) ?>
                            </select>
                        </div>

                        <div class="field">
                            <label class="label" for="interests">Interests</label>
                            <textarea class="textarea" id="interests" name="interests"
                                      placeholder="Reading, travelling, food"><?= e($value('interests')) ?></textarea>
                            <span class="hint">Separate with commas — these are matched against other members' interests.</span>
                        </div>

                        <div class="field">
                            <label class="label" for="hobbies">Hobbies</label>
                            <textarea class="textarea" id="hobbies" name="hobbies"
                                      placeholder="Cooking, cricket, photography"><?= e($value('hobbies')) ?></textarea>
                        </div>

                        <div class="field <?= $err('biography') ? 'field-error' : '' ?>">
                            <label class="label" for="biography">Biography</label>
                            <textarea class="textarea" id="biography" name="biography" style="min-height: 9rem;"
                                      placeholder="Tell people who you are."><?= e($value('biography')) ?></textarea>
                            <?php if ($err('biography')): ?><span class="error-text"><?= e($err('biography')) ?></span><?php endif; ?>
                        </div>

                        <div class="field <?= $err('family_background') ? 'field-error' : '' ?>">
                            <label class="label" for="family_background">Family background</label>
                            <textarea class="textarea" id="family_background" name="family_background"><?= e($value('family_background')) ?></textarea>
                            <?php if ($err('family_background')): ?><span class="error-text"><?= e($err('family_background')) ?></span><?php endif; ?>
                        </div>
                    </div>
                </section>

                <button class="btn btn-lg" type="submit"><?= icon('check', 17) ?> Save changes</button>
            </form>
        </div>

        <!-- Photo + completeness ----------------------------------------- -->
        <div class="stack-lg">
            <section class="panel">
                <div class="panel-head"><h2 style="font-size: var(--step-1);">Photo</h2></div>
                <div class="panel-body center stack">
                    <img class="avatar avatar-xl" style="margin-inline: auto;" data-photo-preview-target
                         src="<?= e(photo_url($user['photo'] ?? null, full_name($user))) ?>" alt="Your profile photo">

                    <?php if (empty($user['photo'])): ?>
                        <p class="small muted">This is generated from your initials until you upload a photo.</p>
                    <?php endif; ?>

                    <form method="post" action="<?= url('/profile/photo') ?>" enctype="multipart/form-data" class="stack">
                        <?= Csrf::field() ?>
                        <input class="input" type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                               data-photo-input aria-label="Choose a photo" required>
                        <button class="btn btn-ghost btn-block btn-sm" type="submit">
                            <?= icon('camera', 15) ?> Upload photo
                        </button>
                    </form>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h2 style="font-size: var(--step-1);">Completeness</h2></div>
                <div class="panel-body stack">
                    <div class="row-between">
                        <span class="small muted">Your profile</span>
                        <strong><?= (int) $completeness ?>%</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" data-progress="<?= (int) $completeness ?>"></div>
                    </div>
                    <p class="small muted">
                        <?= $completeness >= 90
                            ? 'This is a complete picture of you — nothing left to add.'
                            : 'Members with a biography and interests receive noticeably more requests.' ?>
                    </p>
                </div>
            </section>
        </div>
    </div>
</div>
