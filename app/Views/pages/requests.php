<?php

use App\Core\Csrf;

/** @var list<array> $incoming */
/** @var list<array> $outgoing */
?>
<div class="wrap-narrow page">

    <div class="page-head">
        <div class="stack-sm">
            <p class="eyebrow">Requests</p>
            <h1>Who wants to connect</h1>
        </div>
    </div>

    <div class="tabs" style="margin-bottom: var(--sp-5);" role="tablist">
        <button class="tab" role="tab" aria-selected="true" aria-controls="panel-incoming" data-tab="incoming">
            Received <?php if ($incoming !== []): ?><span class="count-dot"><?= count($incoming) ?></span><?php endif; ?>
        </button>
        <button class="tab" role="tab" aria-selected="false" aria-controls="panel-outgoing" data-tab="outgoing">
            Sent <?php if ($outgoing !== []): ?><span class="count-dot"><?= count($outgoing) ?></span><?php endif; ?>
        </button>
    </div>

    <!-- Received --------------------------------------------------------- -->
    <section id="panel-incoming" role="tabpanel" data-tab-panel="incoming">
        <?php if ($incoming === []): ?>
            <div class="empty">
                <?= icon('inbox', 40) ?>
                <h3>No requests waiting</h3>
                <p>When someone asks to connect, they will appear here for you to accept or decline.</p>
                <a class="btn btn-ghost btn-sm" href="<?= url('/browse') ?>">Browse profiles</a>
            </div>
        <?php else: ?>
            <ul class="grid" style="gap: var(--sp-3);">
                <?php foreach ($incoming as $i => $req): ?>
                    <?php $age = age_from($req['dob'] ?? null); ?>
                    <li class="card card-hover" data-reveal style="--reveal-delay: <?= min($i, 6) * 60 ?>ms">
                        <div class="row row-wrap" style="gap: var(--sp-4); align-items: flex-start;">
                            <img class="avatar avatar-lg" src="<?= e(photo_url($req['photo'] ?? null, full_name($req))) ?>" alt="">

                            <div class="grow stack-sm">
                                <div class="row-between">
                                    <h3 style="font-size: var(--step-1);">
                                        <a href="<?= url('/members/' . rawurlencode($req['user_id'])) ?>"
                                           style="color: inherit; text-decoration: none;"><?= e(full_name($req)) ?></a>
                                    </h3>
                                    <span class="small muted"><?= e(time_ago($req['created_at'])) ?></span>
                                </div>

                                <p class="small muted">
                                    <?= $age !== null ? (int) $age . ' · ' : '' ?>
                                    <?= e(humanise($req['profession'] ?? '')) ?>
                                    · <?= e($req['religion'] ?? '') ?>
                                </p>

                                <?php if (!empty($req['message'])): ?>
                                    <blockquote class="request-note">“<?= e($req['message']) ?>”</blockquote>
                                <?php endif; ?>

                                <div class="row row-wrap" style="margin-top: var(--sp-3);">
                                    <form method="post" action="<?= url('/requests/respond') ?>">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="request_id" value="<?= (int) $req['request_id'] ?>">
                                        <input type="hidden" name="action" value="accept">
                                        <input type="hidden" name="return_to" value="/requests">
                                        <button class="btn btn-success btn-sm" type="submit">
                                            <?= icon('check', 15) ?> Accept
                                        </button>
                                    </form>

                                    <form method="post" action="<?= url('/requests/respond') ?>">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="request_id" value="<?= (int) $req['request_id'] ?>">
                                        <input type="hidden" name="action" value="decline">
                                        <input type="hidden" name="return_to" value="/requests">
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <?= icon('x', 15) ?> Decline
                                        </button>
                                    </form>

                                    <a class="btn btn-ghost btn-sm" href="<?= url('/members/' . rawurlencode($req['user_id'])) ?>">
                                        View profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Sent ------------------------------------------------------------- -->
    <section id="panel-outgoing" role="tabpanel" data-tab-panel="outgoing" hidden>
        <?php if ($outgoing === []): ?>
            <div class="empty">
                <?= icon('send', 40) ?>
                <h3>You have not sent any requests</h3>
                <p>Find someone you would like to meet and send them a note.</p>
                <a class="btn btn-ghost btn-sm" href="<?= url('/browse') ?>">Browse profiles</a>
            </div>
        <?php else: ?>
            <ul class="grid" style="gap: var(--sp-3);">
                <?php foreach ($outgoing as $req): ?>
                    <li class="card">
                        <div class="row row-wrap" style="gap: var(--sp-4);">
                            <img class="avatar" src="<?= e(photo_url($req['photo'] ?? null, full_name($req))) ?>" alt="">

                            <div class="grow">
                                <h3 style="font-size: var(--step-0);">
                                    <a href="<?= url('/members/' . rawurlencode($req['user_id'])) ?>"
                                       style="color: inherit; text-decoration: none;"><?= e(full_name($req)) ?></a>
                                </h3>
                                <p class="small muted">
                                    Sent <?= e(time_ago($req['created_at'])) ?> · <?= e(humanise($req['profession'] ?? '')) ?>
                                </p>
                            </div>

                            <span class="badge badge-info"><?= icon('clock', 12) ?> Pending</span>

                            <form method="post" action="<?= url('/requests/cancel') ?>">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="request_id" value="<?= (int) $req['request_id'] ?>">
                                <input type="hidden" name="return_to" value="/requests">
                                <button class="btn btn-danger btn-sm" type="submit">Withdraw</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>
