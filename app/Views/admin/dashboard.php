<?php

/** @var array $stats */
/** @var list<array> $activity */
?>
<div class="wrap page">

    <div class="page-head">
        <div class="stack-sm">
            <p class="eyebrow">Overview</p>
            <h1>How the site is doing</h1>
        </div>
        <a class="btn" href="<?= url('/admin/users') ?>"><?= icon('users', 17) ?> Manage members</a>
    </div>

    <div class="grid grid-4" style="margin-bottom: var(--sp-6);">
        <?php
        $cards = [
            ['icon' => 'users',  'value' => $stats['total'],    'label' => 'Total members'],
            ['icon' => 'check-circle', 'value' => $stats['active'], 'label' => 'Active'],
            ['icon' => 'heart',  'value' => $stats['accepted'], 'label' => 'Connections made'],
            ['icon' => 'chat',   'value' => $stats['messages'], 'label' => 'Messages sent'],
        ];

        foreach ($cards as $i => $card): ?>
            <div class="stat" data-reveal style="--reveal-delay: <?= $i * 70 ?>ms">
                <div class="stat-icon"><?= icon($card['icon'], 20) ?></div>
                <p class="stat-value" data-count-to="<?= (int) $card['value'] ?>">0</p>
                <p class="stat-label"><?= e($card['label']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-main">
        <section class="panel" data-reveal>
            <div class="panel-head"><h2>Recent activity</h2></div>

            <?php if ($activity === []): ?>
                <div class="panel-body">
                    <p class="small muted center">Nothing has happened yet.</p>
                </div>
            <?php else: ?>
                <ul>
                    <?php foreach ($activity as $entry): ?>
                        <li class="chat-list-item">
                            <img class="avatar avatar-sm"
                                 src="<?= e(photo_url($entry['photo'] ?? null, trim(($entry['first_name'] ?? '?') . ' ' . ($entry['last_name'] ?? '')))) ?>"
                                 alt="">
                            <div class="chat-list-body">
                                <p class="small">
                                    <strong>
                                        <?= $entry['user_id']
                                            ? e(trim(($entry['first_name'] ?? '') . ' ' . ($entry['last_name'] ?? '')))
                                            : '<em class="muted">Deleted member</em>' ?>
                                    </strong>
                                    — <?= e($entry['activity']) ?>
                                </p>
                                <p class="small muted"><?= e(time_ago($entry['created_at'])) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <div class="stack-lg">
            <section class="panel" data-reveal style="--reveal-delay: 100ms">
                <div class="panel-head"><h2 style="font-size: var(--step-1);">Accounts</h2></div>
                <div class="panel-body">
                    <dl class="detail-list">
                        <?php foreach ([
                            'Active'    => $stats['active'],
                            'Inactive'  => $stats['inactive'],
                            'Suspended' => $stats['suspended'],
                            'New in 30 days' => $stats['new30'],
                        ] as $label => $value): ?>
                            <div class="detail-row">
                                <dt class="small muted"><?= e($label) ?></dt>
                                <dd class="small"><?= number_format((int) $value) ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </div>
            </section>

            <section class="panel" data-reveal style="--reveal-delay: 160ms">
                <div class="panel-head"><h2 style="font-size: var(--step-1);">Requests</h2></div>
                <div class="panel-body">
                    <dl class="detail-list">
                        <?php foreach ([
                            'Total sent' => $stats['requests'],
                            'Accepted'   => $stats['accepted'],
                            'Pending'    => $stats['pending'],
                        ] as $label => $value): ?>
                            <div class="detail-row">
                                <dt class="small muted"><?= e($label) ?></dt>
                                <dd class="small"><?= number_format((int) $value) ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>

                    <?php if ($stats['requests'] > 0): ?>
                        <div style="margin-top: var(--sp-4);">
                            <div class="row-between small muted" style="margin-bottom: var(--sp-2);">
                                <span>Acceptance rate</span>
                                <strong class="soft"><?= (int) round(($stats['accepted'] / $stats['requests']) * 100) ?>%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar"
                                     data-progress="<?= (int) round(($stats['accepted'] / $stats['requests']) * 100) ?>"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>
