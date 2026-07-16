<?php

use App\Core\Csrf;

/** @var array $user */
/** @var array $stats */
/** @var list<array> $suggestions */
/** @var bool $hasPreferences */
/** @var list<array> $incoming */
/** @var list<array> $activity */

$firstName = $user['first_name'] ?? 'there';
$hour      = (int) date('G');
$greeting  = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
?>
<div class="wrap page">

    <div class="page-head">
        <div class="stack-sm">
            <p class="eyebrow"><?= e($greeting) ?></p>
            <h1><?= e($firstName) ?>, here is where you stand</h1>
        </div>
        <a class="btn" href="<?= url('/browse') ?>"><?= icon('search', 17) ?> Browse matches</a>
    </div>

    <!-- Stats -------------------------------------------------------- -->
    <div class="grid grid-4" style="margin-bottom: var(--sp-6);">
        <?php
        $cards = [
            ['icon' => 'heart',  'value' => $stats['matches'],  'label' => 'Matches',           'href' => '/matches'],
            ['icon' => 'inbox',  'value' => $stats['requests'], 'label' => 'Pending requests',  'href' => '/requests'],
            ['icon' => 'chat',   'value' => $stats['unread'],   'label' => 'Unread messages',   'href' => '/matches'],
        ];

        foreach ($cards as $i => $card): ?>
            <a class="stat stat-link" href="<?= url($card['href']) ?>"
               data-reveal style="--reveal-delay: <?= $i * 70 ?>ms">
                <div class="stat-icon"><?= icon($card['icon'], 20) ?></div>
                <p class="stat-value" data-count-to="<?= (int) $card['value'] ?>">0</p>
                <p class="stat-label"><?= e($card['label']) ?></p>
            </a>
        <?php endforeach; ?>

        <div class="stat" data-reveal style="--reveal-delay: 210ms">
            <div class="stat-icon"><?= icon('user', 20) ?></div>
            <p class="stat-value"><span data-count-to="<?= (int) $stats['complete'] ?>">0</span>%</p>
            <p class="stat-label">Profile complete</p>
            <div class="progress" style="margin-top: var(--sp-3);">
                <div class="progress-bar" data-progress="<?= (int) $stats['complete'] ?>"></div>
            </div>
        </div>
    </div>

    <!-- Nudges ------------------------------------------------------- -->
    <?php if (!$hasPreferences): ?>
        <div class="card" style="border-color: var(--gold-400); background: color-mix(in srgb, var(--gold-500) 7%, var(--surface)); margin-bottom: var(--sp-6);">
            <div class="row row-wrap" style="gap: var(--sp-4);">
                <span class="stat-icon" style="background: color-mix(in srgb, var(--gold-500) 20%, transparent); color: var(--gold-600);">
                    <?= icon('sliders', 20) ?>
                </span>
                <div class="grow">
                    <h3 style="font-size: var(--step-1);">Tell us who you are looking for</h3>
                    <p class="small soft">Until you do, we can rank profiles by nothing but how recently they joined.</p>
                </div>
                <a class="btn btn-gold" href="<?= url('/preferences') ?>">Set preferences</a>
            </div>
        </div>
    <?php elseif ($stats['complete'] < 60): ?>
        <div class="card" style="margin-bottom: var(--sp-6);">
            <div class="row row-wrap" style="gap: var(--sp-4);">
                <span class="stat-icon"><?= icon('user', 20) ?></span>
                <div class="grow">
                    <h3 style="font-size: var(--step-1);">Your profile is <?= (int) $stats['complete'] ?>% complete</h3>
                    <p class="small soft">Profiles with a biography and interests get noticeably more requests.</p>
                </div>
                <a class="btn btn-ghost" href="<?= url('/profile') ?>">Finish profile</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-main">

        <!-- Suggestions ---------------------------------------------- -->
        <section class="panel" data-reveal>
            <div class="panel-head">
                <h2>Picked for you</h2>
                <a class="small" href="<?= url('/browse') ?>">See all</a>
            </div>

            <?php if ($suggestions === []): ?>
                <div class="panel-body">
                    <div class="empty">
                        <?= icon('search', 34) ?>
                        <h3>Nothing to show yet</h3>
                        <p>
                            <?= $hasPreferences
                                ? 'No profiles match your preferences right now. Try widening them a little.'
                                : 'Set your preferences and we will start ranking people for you.' ?>
                        </p>
                        <a class="btn btn-ghost btn-sm" href="<?= url($hasPreferences ? '/browse' : '/preferences') ?>">
                            <?= $hasPreferences ? 'Browse everyone' : 'Set preferences' ?>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <ul>
                    <?php foreach ($suggestions as $person): ?>
                        <?php $age = age_from($person['dob'] ?? null); ?>
                        <li class="chat-list-item" style="border-bottom: 1px solid var(--border);">
                            <div class="avatar-wrap">
                                <img class="avatar" src="<?= e(photo_url($person['photo'] ?? null, full_name($person))) ?>" alt="">
                            </div>

                            <div class="chat-list-body">
                                <p class="chat-list-name">
                                    <a href="<?= url('/members/' . rawurlencode($person['user_id'])) ?>"
                                       style="color: inherit; text-decoration: none;">
                                        <?= e(full_name($person)) ?>
                                    </a>
                                </p>
                                <p class="small muted">
                                    <?= $age !== null ? (int) $age . ' · ' : '' ?><?= e(humanise($person['profession'] ?? '')) ?>
                                </p>
                                <?php if (!empty($person['match_reasons'])): ?>
                                    <p class="small" style="color: var(--success); margin-top: 2px;">
                                        <?= icon('check', 12) ?> <?= e($person['match_reasons'][0]) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="row" style="gap: var(--sp-2);">
                                <span class="badge badge-gold"><?= (int) ($person['match_score'] ?? 0) ?>%</span>
                                <a class="btn btn-ghost btn-sm" href="<?= url('/members/' . rawurlencode($person['user_id'])) ?>">View</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <!-- Side column ---------------------------------------------- -->
        <div class="stack-lg">
            <section class="panel" data-reveal style="--reveal-delay: 100ms">
                <div class="panel-head">
                    <h2 style="font-size: var(--step-1);">Requests</h2>
                    <?php if ($stats['requests'] > 0): ?>
                        <span class="count-dot"><?= (int) $stats['requests'] ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($incoming === []): ?>
                    <div class="panel-body">
                        <p class="small muted center">No pending requests.</p>
                    </div>
                <?php else: ?>
                    <ul>
                        <?php foreach ($incoming as $req): ?>
                            <li class="chat-list-item">
                                <img class="avatar avatar-sm" src="<?= e(photo_url($req['photo'] ?? null, full_name($req))) ?>" alt="">
                                <div class="chat-list-body">
                                    <p class="small" style="font-weight: 600;"><?= e(full_name($req)) ?></p>
                                    <p class="small muted"><?= e(time_ago($req['created_at'])) ?></p>
                                </div>
                                <form method="post" action="<?= url('/requests/respond') ?>">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="request_id" value="<?= (int) $req['request_id'] ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <input type="hidden" name="return_to" value="/dashboard">
                                    <button class="btn btn-success btn-sm" type="submit" title="Accept">
                                        <?= icon('check', 15) ?>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="panel-body" style="padding-top: var(--sp-3);">
                        <a class="btn btn-ghost btn-sm btn-block" href="<?= url('/requests') ?>">See all requests</a>
                    </div>
                <?php endif; ?>
            </section>

            <?php if ($activity !== []): ?>
                <section class="panel" data-reveal style="--reveal-delay: 160ms">
                    <div class="panel-head"><h2 style="font-size: var(--step-1);">Recent activity</h2></div>
                    <div class="panel-body">
                        <ul class="stack-sm">
                            <?php foreach ($activity as $entry): ?>
                                <li class="row" style="gap: var(--sp-3); align-items: flex-start;">
                                    <span class="muted" style="flex: none; margin-top: 3px;"><?= icon('clock', 13) ?></span>
                                    <span class="small">
                                        <?= e($entry['activity']) ?>
                                        <span class="muted"> · <?= e(time_ago($entry['created_at'])) ?></span>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </div>
</div>
