<?php

use App\Models\User;

/** @var list<array> $matches */
?>
<div class="wrap-narrow page">

    <div class="page-head">
        <div class="stack-sm">
            <p class="eyebrow">Matches</p>
            <h1><?= count($matches) ?> <?= count($matches) === 1 ? 'person' : 'people' ?> you can talk to</h1>
        </div>
        <a class="btn btn-ghost" href="<?= url('/browse') ?>"><?= icon('search', 17) ?> Find more</a>
    </div>

    <?php if ($matches === []): ?>
        <div class="empty">
            <?= icon('heart', 40) ?>
            <h3>No matches yet</h3>
            <p>
                Once you and another member have both accepted, they will appear here and your
                chat will open. Nobody can message you before that.
            </p>
            <a class="btn btn-sm" href="<?= url('/browse') ?>">Browse profiles</a>
        </div>
    <?php else: ?>
        <ul class="panel">
            <?php foreach ($matches as $match): ?>
                <?php
                $name   = full_name($match);
                $unread = (int) ($match['unread_count'] ?? 0);
                $online = User::isOnline($match['last_seen_at'] ?? null);
                ?>
                <li>
                    <a class="chat-list-item" href="<?= url('/chat/' . rawurlencode($match['user_id'])) ?>">
                        <div class="avatar-wrap">
                            <img class="avatar" src="<?= e(photo_url($match['photo'] ?? null, $name)) ?>" alt="">
                            <span class="presence <?= $online ? 'presence-on' : '' ?>"></span>
                        </div>

                        <div class="chat-list-body">
                            <div class="row-between" style="gap: var(--sp-2);">
                                <p class="chat-list-name"><?= e($name) ?></p>
                                <span class="small muted nowrap">
                                    <?= $match['last_message_at'] ? e(time_ago($match['last_message_at'])) : e(time_ago($match['matched_at'] ?? '')) ?>
                                </span>
                            </div>

                            <p class="chat-list-preview">
                                <?php if (!empty($match['last_message'])): ?>
                                    <?= e($match['last_message']) ?>
                                <?php else: ?>
                                    <span class="muted">You matched — say hello.</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <?php if ($unread > 0): ?>
                            <span class="count-dot"><?= $unread ?></span>
                        <?php else: ?>
                            <span class="muted"><?= icon('chevron-down', 16, 'rotate-left') ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
