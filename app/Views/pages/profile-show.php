<?php

use App\Core\Csrf;
use App\Models\User;

/** @var array $person */
/** @var array $viewer */
/** @var array|null $relationship */
/** @var bool $connected */

$name   = full_name($person);
$age    = age_from($person['dob'] ?? null);
$status = $relationship['status'] ?? null;
$isOutgoing = $relationship !== null && ($relationship['sender_id'] ?? '') === $viewer['user_id'];
$online = User::isOnline($person['last_seen_at'] ?? null);

/** Only render a detail row when there is something in it. */
$rows = array_filter([
    'Profession'      => humanise($person['profession'] ?? ''),
    'Religion'        => $person['religion'] ?? '',
    'Ethnicity'       => $person['ethnicity'] ?? '',
    'Marital status'  => $person['marital_status'] ?? '',
    'Height'          => !empty($person['height_cm']) && (float) $person['height_cm'] > 0 ? (int) $person['height_cm'] . ' cm' : '',
    'Weight'          => !empty($person['weight_kg']) && (float) $person['weight_kg'] > 0 ? (int) $person['weight_kg'] . ' kg' : '',
    'Complexion'      => $person['complexion'] ?? '',
    'Undergraduate'   => $person['undergraduate'] ?? '',
    'Postgraduate'    => $person['postgraduate'] ?? '',
    'Secondary'       => $person['secondary_education'] ?? '',
    'Higher secondary' => $person['higher_secondary'] ?? '',
], static fn ($v) => $v !== null && $v !== '' && $v !== '—');
?>
<div class="wrap page">

    <a class="btn btn-ghost btn-sm" href="<?= url('/browse') ?>" style="margin-bottom: var(--sp-5);">
        <?= icon('arrow-left', 15) ?> Back to browse
    </a>

    <div class="grid grid-main">
        <div class="stack-lg">

            <!-- Header ------------------------------------------------- -->
            <section class="card">
                <div class="row row-wrap" style="gap: var(--sp-5); align-items: flex-start;">
                    <div class="avatar-wrap">
                        <img class="avatar avatar-xl" src="<?= e(photo_url($person['photo'] ?? null, $name)) ?>"
                             alt="<?= e($name) ?>">
                        <span class="presence <?= $online ? 'presence-on' : '' ?>"
                              title="<?= $online ? 'Online now' : 'Offline' ?>"></span>
                    </div>

                    <div class="grow stack-sm">
                        <h1 style="font-size: var(--step-2);"><?= e($name) ?></h1>
                        <p class="soft">
                            <?= $age !== null ? (int) $age . ' years old' : 'Age not shared' ?>
                            · <?= e(humanise($person['profession'] ?? '')) ?>
                        </p>

                        <div class="row row-wrap" style="gap: var(--sp-2); margin-top: var(--sp-2);">
                            <span class="badge"><?= icon('user', 12) ?> <?= e($person['gender'] ?? '') ?></span>
                            <span class="badge"><?= e($person['religion'] ?? '') ?></span>
                            <?php if (!empty($person['marital_status'])): ?>
                                <span class="badge"><?= e($person['marital_status']) ?></span>
                            <?php endif; ?>
                            <?php if ($connected): ?>
                                <span class="badge badge-success"><?= icon('check', 12) ?> Matched</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Action ------------------------------------------- -->
                <div class="row row-wrap" style="margin-top: var(--sp-5); padding-top: var(--sp-4); border-top: 1px solid var(--border);">
                    <?php if ($connected): ?>
                        <a class="btn" href="<?= url('/chat/' . rawurlencode($person['user_id'])) ?>">
                            <?= icon('chat', 17) ?> Open chat
                        </a>
                    <?php elseif ($status === 'Pending' && $isOutgoing): ?>
                        <span class="btn btn-ghost" aria-disabled="true"><?= icon('clock', 16) ?> Request pending</span>
                        <form method="post" action="<?= url('/requests/cancel') ?>">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="request_id" value="<?= (int) $relationship['request_id'] ?>">
                            <input type="hidden" name="return_to" value="/members/<?= e($person['user_id']) ?>">
                            <button class="btn btn-danger btn-sm" type="submit">Withdraw</button>
                        </form>
                    <?php elseif ($status === 'Pending'): ?>
                        <form method="post" action="<?= url('/requests/respond') ?>">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="request_id" value="<?= (int) $relationship['request_id'] ?>">
                            <input type="hidden" name="action" value="accept">
                            <input type="hidden" name="return_to" value="/members/<?= e($person['user_id']) ?>">
                            <button class="btn btn-success" type="submit">
                                <?= icon('heart', 16) ?> Accept their request
                            </button>
                        </form>
                        <form method="post" action="<?= url('/requests/respond') ?>">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="request_id" value="<?= (int) $relationship['request_id'] ?>">
                            <input type="hidden" name="action" value="decline">
                            <input type="hidden" name="return_to" value="/browse">
                            <button class="btn btn-danger" type="submit">Decline</button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="<?= url('/requests/send') ?>" class="stack" style="width: 100%;">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="receiver_id" value="<?= e($person['user_id']) ?>">
                            <input type="hidden" name="return_to" value="/members/<?= e($person['user_id']) ?>">
                            <div class="field">
                                <label class="label" for="message">Say something (optional)</label>
                                <textarea class="textarea" id="message" name="message" maxlength="500"
                                          style="min-height: 5rem;"
                                          placeholder="A short note travels further than a blank request."></textarea>
                            </div>
                            <button class="btn" type="submit"><?= icon('heart', 16) ?> Send connection request</button>
                        </form>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Biography ---------------------------------------------- -->
            <?php if (!empty($person['biography'])): ?>
                <section class="panel">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">About <?= e($person['first_name']) ?></h2></div>
                    <div class="panel-body">
                        <p class="soft" style="white-space: pre-line;"><?= e($person['biography']) ?></p>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Interests ---------------------------------------------- -->
            <?php
            $tags = [];
            foreach (['interests', 'hobbies'] as $field) {
                foreach (preg_split('/[,;\/\n]+/', (string) ($person[$field] ?? '')) ?: [] as $tag) {
                    $tag = trim($tag);
                    if ($tag !== '') {
                        $tags[] = $tag;
                    }
                }
            }
            $tags = array_slice(array_unique($tags), 0, 16);
            ?>
            <?php if ($tags !== []): ?>
                <section class="panel">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">Interests &amp; hobbies</h2></div>
                    <div class="panel-body">
                        <div class="row row-wrap" style="gap: var(--sp-2);">
                            <?php foreach ($tags as $tag): ?>
                                <span class="chip"><?= e($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($person['family_background'])): ?>
                <section class="panel">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">Family background</h2></div>
                    <div class="panel-body">
                        <p class="soft" style="white-space: pre-line;"><?= e($person['family_background']) ?></p>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <!-- Details rail --------------------------------------------- -->
        <aside class="stack-lg">
            <section class="panel">
                <div class="panel-head"><h2 style="font-size: var(--step-1);">Details</h2></div>
                <div class="panel-body">
                    <dl class="detail-list">
                        <?php foreach ($rows as $label => $val): ?>
                            <div class="detail-row">
                                <dt class="small muted"><?= e($label) ?></dt>
                                <dd class="small"><?= e($val) ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </div>
            </section>

            <?php if ($connected && !empty($person['phone'])): ?>
                <section class="panel">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">Contact</h2></div>
                    <div class="panel-body stack-sm">
                        <p class="small"><?= icon('user', 13) ?> <?= e($person['phone']) ?></p>
                        <p class="small muted">Shared because you have matched.</p>
                    </div>
                </section>
            <?php elseif (!$connected): ?>
                <section class="card card-sunk">
                    <p class="small muted">
                        <?= icon('lock', 14) ?>
                        Contact details and chat unlock once you have both accepted.
                    </p>
                </section>
            <?php endif; ?>
        </aside>
    </div>
</div>
