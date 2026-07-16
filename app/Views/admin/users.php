<?php

use App\Core\Csrf;

/** @var list<array> $users */
/** @var string $term */
/** @var string $status */
/** @var list<string> $statuses */

$badgeFor = ['Active' => 'badge-success', 'Suspended' => 'badge-danger', 'Inactive' => 'badge'];
?>
<div class="wrap page">

    <div class="page-head">
        <div class="stack-sm">
            <p class="eyebrow">Members</p>
            <h1><?= count($users) ?> <?= count($users) === 1 ? 'member' : 'members' ?></h1>
        </div>
        <a class="btn btn-ghost" href="<?= url('/admin') ?>"><?= icon('arrow-left', 16) ?> Overview</a>
    </div>

    <form class="card" method="get" action="<?= url('/admin/users') ?>" style="margin-bottom: var(--sp-5);">
        <div class="row row-wrap" style="gap: var(--sp-3);">
            <div class="field grow" style="min-width: 200px;">
                <label class="sr-only" for="q">Search members</label>
                <input class="input" id="q" name="q" value="<?= e($term) ?>" placeholder="Search by name or email">
            </div>

            <div class="field" style="min-width: 160px;">
                <label class="sr-only" for="status">Status</label>
                <select class="select" id="status" name="status">
                    <option value="">Any status</option>
                    <?php foreach ($statuses as $option): ?>
                        <option value="<?= e($option) ?>" <?= $status === $option ? 'selected' : '' ?>>
                            <?= e($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn" type="submit"><?= icon('search', 16) ?> Search</button>

            <?php if ($term !== '' || $status !== ''): ?>
                <a class="btn btn-ghost" href="<?= url('/admin/users') ?>">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($users === []): ?>
        <div class="empty">
            <?= icon('users', 40) ?>
            <h3>No members found</h3>
            <p><?= $term !== '' || $status !== '' ? 'Nothing matches that search.' : 'Nobody has registered yet.' ?></p>
        </div>
    <?php else: ?>
        <div class="panel">
            <div class="table-scroll">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Member</th>
                        <th scope="col">Age</th>
                        <th scope="col">Religion</th>
                        <th scope="col">Profession</th>
                        <th scope="col">Joined</th>
                        <th scope="col">Status</th>
                        <th scope="col"><span class="sr-only">Actions</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <?php
                        $name = full_name($user);
                        $age  = age_from($user['dob'] ?? null);
                        ?>
                        <tr>
                            <td>
                                <div class="row" style="gap: var(--sp-3);">
                                    <img class="avatar avatar-sm" src="<?= e(photo_url($user['photo'] ?? null, $name)) ?>" alt="">
                                    <div>
                                        <p style="font-weight: 600;"><?= e($name) ?></p>
                                        <p class="muted" style="font-size: 11px;"><?= e($user['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td><?= $age !== null ? (int) $age : '—' ?></td>
                            <td><?= e($user['religion'] ?? '—') ?></td>
                            <td><?= e(humanise($user['profession'] ?? '')) ?></td>
                            <td class="muted"><?= e(date('j M Y', strtotime((string) $user['created_at']))) ?></td>
                            <td>
                                <span class="badge <?= $badgeFor[$user['account_status']] ?? 'badge' ?>">
                                    <?= e($user['account_status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="row" style="gap: var(--sp-2); justify-content: flex-end;">
                                    <form method="post" action="<?= url('/admin/users/status') ?>" class="row" style="gap: var(--sp-2);">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="user_id" value="<?= e($user['user_id']) ?>">
                                        <label class="sr-only" for="status-<?= e($user['user_id']) ?>">Change status</label>
                                        <select class="select" id="status-<?= e($user['user_id']) ?>" name="status"
                                                style="padding: 0.35rem 2rem 0.35rem 0.6rem; font-size: var(--step--1);">
                                            <?php foreach ($statuses as $option): ?>
                                                <option value="<?= e($option) ?>" <?= $user['account_status'] === $option ? 'selected' : '' ?>>
                                                    <?= e($option) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-ghost btn-sm" type="submit">Save</button>
                                    </form>

                                    <form method="post" action="<?= url('/admin/users/delete') ?>"
                                          data-confirm="Delete <?= e($name) ?> permanently? Their profile, requests and messages all go with them.">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="user_id" value="<?= e($user['user_id']) ?>">
                                        <button class="btn btn-danger btn-sm" type="submit" aria-label="Delete <?= e($name) ?>">
                                            <?= icon('trash', 14) ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
