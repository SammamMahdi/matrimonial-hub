<?php

use App\Core\Csrf;
use App\Models\User;

/** @var array $peer */
/** @var array $user */
/** @var list<array> $messages */
/** @var list<array> $matches */

$peerName = full_name($peer);
$online   = User::isOnline($peer['last_seen_at'] ?? null);
$lastId   = $messages === [] ? 0 : (int) end($messages)['message_id'];
?>
<div class="wrap page">
    <div class="chat-shell">

        <!-- Conversation list ------------------------------------------ -->
        <aside class="chat-aside">
            <div class="chat-aside-head">
                <a class="btn btn-ghost btn-sm btn-block" href="<?= url('/matches') ?>">
                    <?= icon('arrow-left', 15) ?> All matches
                </a>
            </div>

            <ul class="chat-list">
                <?php foreach ($matches as $match): ?>
                    <?php
                    $name     = full_name($match);
                    $isActive = $match['user_id'] === $peer['user_id'];
                    $unread   = (int) ($match['unread_count'] ?? 0);
                    ?>
                    <li>
                        <a class="chat-list-item <?= $isActive ? 'is-active' : '' ?>"
                           href="<?= url('/chat/' . rawurlencode($match['user_id'])) ?>">
                            <div class="avatar-wrap">
                                <img class="avatar avatar-sm" src="<?= e(photo_url($match['photo'] ?? null, $name)) ?>" alt="">
                                <span class="presence <?= User::isOnline($match['last_seen_at'] ?? null) ? 'presence-on' : '' ?>"></span>
                            </div>
                            <div class="chat-list-body">
                                <p class="chat-list-name small"><?= e($name) ?></p>
                                <p class="chat-list-preview">
                                    <?= !empty($match['last_message']) ? e($match['last_message']) : 'Say hello' ?>
                                </p>
                            </div>
                            <?php if ($unread > 0 && !$isActive): ?>
                                <span class="count-dot"><?= $unread ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- Thread ------------------------------------------------------ -->
        <div class="chat-main"
             data-chat
             data-fetch-url="<?= e(url('/chat/' . rawurlencode($peer['user_id']) . '/messages')) ?>"
             data-send-url="<?= e(url('/chat/' . rawurlencode($peer['user_id']) . '/messages')) ?>"
             data-last-id="<?= $lastId ?>">

            <header class="chat-head">
                <a class="btn-icon btn-ghost chat-back" href="<?= url('/matches') ?>" aria-label="Back to matches">
                    <?= icon('arrow-left', 16) ?>
                </a>

                <div class="avatar-wrap">
                    <img class="avatar avatar-sm" src="<?= e(photo_url($peer['photo'] ?? null, $peerName)) ?>" alt="">
                    <span class="presence <?= $online ? 'presence-on' : '' ?>" data-presence></span>
                </div>

                <div class="grow">
                    <p style="font-weight: 600;"><?= e($peerName) ?></p>
                    <p class="small muted" data-presence-label>
                        <?= $online ? 'Online now' : 'Last seen ' . e(time_ago($peer['last_seen_at'] ?? '')) ?>
                    </p>
                </div>

                <a class="btn btn-ghost btn-sm" href="<?= url('/members/' . rawurlencode($peer['user_id'])) ?>">
                    View profile
                </a>
            </header>

            <div class="chat-log" data-chat-log role="log" aria-live="polite" aria-label="Conversation">
                <?php if ($messages === []): ?>
                    <div class="empty" style="margin: auto; background: transparent; border: 0;">
                        <?= icon('chat', 34) ?>
                        <h3 style="font-size: var(--step-0);">You matched with <?= e($peer['first_name']) ?></h3>
                        <p class="small">Someone has to go first. It may as well be you.</p>
                    </div>
                <?php else: ?>
                    <?php $lastDay = null; ?>
                    <?php foreach ($messages as $message): ?>
                        <?php
                        $day  = date('Y-m-d', strtotime((string) $message['created_at']));
                        $mine = $message['sender_id'] === $user['user_id'];
                        ?>
                        <?php if ($day !== $lastDay): ?>
                            <?php $lastDay = $day; ?>
                            <span class="chat-day"><?= e(date('j M Y', strtotime($day))) ?></span>
                        <?php endif; ?>

                        <div class="bubble <?= $mine ? 'bubble-out' : 'bubble-in' ?>">
                            <?= nl2br(e($message['body'])) ?>
                            <span class="bubble-time"><?= e(date('g:i A', strtotime((string) $message['created_at']))) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form class="chat-form" data-chat-form>
                <?= Csrf::field() ?>
                <label class="sr-only" for="chat-body">Message</label>
                <textarea class="chat-input" id="chat-body" name="body" rows="1" maxlength="2000"
                          placeholder="Write a message… (Enter to send, Shift+Enter for a new line)"
                          data-chat-input></textarea>
                <button class="chat-send" type="submit" aria-label="Send" data-chat-send disabled>
                    <?= icon('send', 18) ?>
                </button>
            </form>
        </div>
    </div>
</div>
