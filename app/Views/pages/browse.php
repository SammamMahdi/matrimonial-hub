<?php

use App\Core\Csrf;

/** @var list<array> $results */
/** @var int $total, $page, $pages */
/** @var array $filters */
/** @var bool $hasPreferences */
/** @var array $genders, $religions, $ethnicities, $professionGroups, $maritalStatuses, $complexions */

/** Rebuilds the query string while changing one key — used by the pager. */
$queryWith = static function (array $overrides) use ($filters): string {
    $params = array_filter(
        array_merge($filters, $overrides),
        static fn ($v) => $v !== null && $v !== ''
    );

    return $params === [] ? '' : '?' . http_build_query($params);
};

$activeFilters = array_filter($filters, static fn ($v) => $v !== null && $v !== '');
?>
<div class="wrap page">

    <div class="page-head">
        <div class="stack-sm">
            <p class="eyebrow">Browse</p>
            <h1><?= number_format($total) ?> <?= $total === 1 ? 'profile' : 'profiles' ?> for you</h1>
            <?php if ($hasPreferences): ?>
                <p class="small muted">Ranked by how well each one matches your saved preferences.</p>
            <?php else: ?>
                <p class="small muted">
                    <a href="<?= url('/preferences') ?>">Set your preferences</a> to rank these by compatibility.
                </p>
            <?php endif; ?>
        </div>
        <a class="btn btn-ghost" href="<?= url('/preferences') ?>"><?= icon('sliders', 17) ?> Preferences</a>
    </div>

    <div class="sidebar-layout">

        <!-- Filters ------------------------------------------------------ -->
        <aside class="filters">
            <form class="panel" method="get" action="<?= url('/browse') ?>">
                <div class="panel-head">
                    <h2 style="font-size: var(--step-0);"><?= icon('sliders', 16) ?> Filters</h2>
                    <?php if ($activeFilters !== []): ?>
                        <a class="small" href="<?= url('/browse') ?>">Clear</a>
                    <?php endif; ?>
                </div>

                <div class="panel-body filters-body">
                    <div class="field">
                        <label class="label" for="q">Search</label>
                        <input class="input" id="q" name="q" value="<?= e($filters['q'] ?? '') ?>"
                               placeholder="Name or biography">
                    </div>

                    <div class="field">
                        <label class="label" for="f-gender">Gender</label>
                        <select class="select" id="f-gender" name="gender">
                            <?= select_options($genders, $filters['gender'] ?? null, 'Any') ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="label" for="f-religion">Religion</label>
                        <select class="select" id="f-religion" name="religion">
                            <?= select_options($religions, $filters['religion'] ?? null, 'Any') ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="label">Age range</label>
                        <div class="field-row">
                            <input class="input" type="number" name="min_age" min="18" max="100"
                                   value="<?= e($filters['min_age'] ?? '') ?>" placeholder="From" aria-label="Minimum age">
                            <input class="input" type="number" name="max_age" min="18" max="100"
                                   value="<?= e($filters['max_age'] ?? '') ?>" placeholder="To" aria-label="Maximum age">
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Height (cm)</label>
                        <div class="field-row">
                            <input class="input" type="number" name="min_height" min="100" max="250"
                                   value="<?= e($filters['min_height'] ?? '') ?>" placeholder="From" aria-label="Minimum height">
                            <input class="input" type="number" name="max_height" min="100" max="250"
                                   value="<?= e($filters['max_height'] ?? '') ?>" placeholder="To" aria-label="Maximum height">
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="f-marital">Marital status</label>
                        <select class="select" id="f-marital" name="marital_status">
                            <?= select_options($maritalStatuses, $filters['marital_status'] ?? null, 'Any') ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="label" for="f-ethnicity">Ethnicity</label>
                        <select class="select" id="f-ethnicity" name="ethnicity">
                            <?= select_options($ethnicities, $filters['ethnicity'] ?? null, 'Any') ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="label" for="f-profession">Profession</label>
                        <select class="select" id="f-profession" name="profession">
                            <?= grouped_select_options($professionGroups, $filters['profession'] ?? null, 'Any') ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="label" for="f-complexion">Complexion</label>
                        <select class="select" id="f-complexion" name="complexion">
                            <?= select_options($complexions, $filters['complexion'] ?? null, 'Any') ?>
                        </select>
                    </div>

                    <button class="btn btn-block" type="submit"><?= icon('search', 16) ?> Apply filters</button>
                </div>
            </form>
        </aside>

        <!-- Results ------------------------------------------------------ -->
        <div>
            <?php if ($results === []): ?>
                <div class="empty">
                    <?= icon('search', 40) ?>
                    <h3>No profiles match that</h3>
                    <p>
                        <?= $activeFilters !== []
                            ? 'Try removing a filter or two — every filter is an exact match.'
                            : 'There is nobody else here yet. Once more members join, they will show up here.' ?>
                    </p>
                    <?php if ($activeFilters !== []): ?>
                        <a class="btn btn-ghost btn-sm" href="<?= url('/browse') ?>">Clear filters</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="grid grid-3">
                    <?php foreach ($results as $i => $person): ?>
                        <?php
                        $age    = age_from($person['dob'] ?? null);
                        $name   = full_name($person);
                        $status = $person['request_status'] ?? null;
                        $link   = url('/members/' . rawurlencode($person['user_id']));
                        ?>
                        <article class="profile-card" data-reveal style="--reveal-delay: <?= min($i, 6) * 60 ?>ms">
                            <a class="profile-card-media" href="<?= $link ?>" aria-label="View <?= e($name) ?>'s profile">
                                <img src="<?= e(photo_url($person['photo'] ?? null, $name, 'wide')) ?>" alt="" loading="lazy">
                                <?php if ($hasPreferences && ($person['match_score'] ?? 0) > 0): ?>
                                    <span class="profile-card-score" title="Compatibility score">
                                        <?= (int) $person['match_score'] ?>%
                                    </span>
                                <?php endif; ?>
                            </a>

                            <div class="profile-card-body">
                                <div>
                                    <h3 class="profile-card-name"><a href="<?= $link ?>"><?= e($name) ?></a></h3>
                                    <p class="small muted">
                                        <?= $age !== null ? (int) $age . ' years' : 'Age not shared' ?>
                                        · <?= e(humanise($person['profession'] ?? '')) ?>
                                    </p>
                                </div>

                                <div class="profile-card-meta">
                                    <span class="badge"><?= e($person['religion'] ?? '—') ?></span>
                                    <?php if (!empty($person['marital_status'])): ?>
                                        <span class="badge"><?= e($person['marital_status']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($person['height_cm']) && (float) $person['height_cm'] > 0): ?>
                                        <span class="badge"><?= (int) $person['height_cm'] ?> cm</span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($person['match_reasons'])): ?>
                                    <ul class="profile-card-why">
                                        <?php foreach (array_slice($person['match_reasons'], 0, 2) as $reason): ?>
                                            <li><?= icon('check', 13) ?> <span><?= e($reason) ?></span></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <div class="profile-card-actions">
                                    <?php if ($status === 'Pending' && ($person['request_direction'] ?? '') === 'outgoing'): ?>
                                        <span class="btn btn-ghost btn-sm grow" aria-disabled="true">
                                            <?= icon('clock', 15) ?> Request sent
                                        </span>
                                    <?php elseif ($status === 'Pending'): ?>
                                        <form method="post" action="<?= url('/requests/send') ?>" class="grow">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="receiver_id" value="<?= e($person['user_id']) ?>">
                                            <input type="hidden" name="return_to" value="/browse">
                                            <button class="btn btn-success btn-sm btn-block" type="submit">
                                                <?= icon('heart', 15) ?> Accept them
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post" action="<?= url('/requests/send') ?>" class="grow">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="receiver_id" value="<?= e($person['user_id']) ?>">
                                            <input type="hidden" name="return_to" value="/browse">
                                            <button class="btn btn-sm btn-block" type="submit">
                                                <?= icon('heart', 15) ?> Connect
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <a class="btn btn-ghost btn-sm" href="<?= $link ?>">View</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($pages > 1): ?>
                    <nav class="pagination" style="margin-top: var(--sp-6);" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                            <a class="page-link" href="<?= url('/browse') . $queryWith(['page' => $page - 1]) ?>" rel="prev">
                                <?= icon('arrow-left', 15) ?>
                            </a>
                        <?php endif; ?>

                        <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
                            <a class="page-link" href="<?= url('/browse') . $queryWith(['page' => $p]) ?>"
                               <?= $p === $page ? 'aria-current="page"' : '' ?>><?= $p ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $pages): ?>
                            <a class="page-link" href="<?= url('/browse') . $queryWith(['page' => $page + 1]) ?>" rel="next">
                                <?= icon('arrow-right', 15) ?>
                            </a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
