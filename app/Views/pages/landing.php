<?php /** @var array $stats */ ?>

<section class="hero">
    <div class="hero-bg hero-art" aria-hidden="true">
        <?php require __DIR__ . '/../partials/hero-art.php'; ?>
    </div>

    <div class="petals" aria-hidden="true">
        <?php
        // Deterministic drift so the hearts don't reshuffle on every render.
        $petals = [
            ['left' => 8,  'size' => 14, 'dur' => 19, 'delay' => 0],
            ['left' => 21, 'size' => 9,  'dur' => 25, 'delay' => 5],
            ['left' => 34, 'size' => 18, 'dur' => 22, 'delay' => 11],
            ['left' => 48, 'size' => 11, 'dur' => 28, 'delay' => 3],
            ['left' => 62, 'size' => 16, 'dur' => 21, 'delay' => 8],
            ['left' => 76, 'size' => 10, 'dur' => 26, 'delay' => 14],
            ['left' => 89, 'size' => 15, 'dur' => 23, 'delay' => 2],
        ];
        foreach ($petals as $p): ?>
            <span class="petal" style="left: <?= $p['left'] ?>%; animation-duration: <?= $p['dur'] ?>s; animation-delay: -<?= $p['delay'] ?>s;">
                <?= icon('heart', $p['size']) ?>
            </span>
        <?php endforeach; ?>
    </div>

    <div class="wrap hero-inner stack-lg">
        <p class="eyebrow" data-reveal>Matrimonial Hub</p>

        <h1 class="display" data-reveal style="--reveal-delay: 80ms">
            Find someone who <em>fits your life</em>
        </h1>

        <p class="lede" data-reveal style="--reveal-delay: 160ms">
            Tell us what actually matters to you — faith, family, ambition, the small things —
            and we rank every profile against it. No guesswork, no invented percentages.
        </p>

        <div class="hero-actions" data-reveal style="--reveal-delay: 240ms">
            <a class="btn btn-lg" href="<?= url('/register') ?>">
                Create your profile <?= icon('arrow-right', 18) ?>
            </a>
            <a class="btn btn-lg btn-ghost hero-ghost" href="<?= url('/login') ?>">I already have one</a>
        </div>

        <?php if ($stats['members'] > 0): ?>
            <p class="small hero-note" data-reveal style="--reveal-delay: 320ms">
                <?= icon('users', 14) ?>
                <strong><?= number_format($stats['members']) ?></strong>
                active <?= $stats['members'] === 1 ? 'member' : 'members' ?>
                <?php if ($stats['joined'] > 0): ?>
                    · <strong><?= number_format($stats['joined']) ?></strong> joined this month
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <a class="scroll-cue" href="#how" aria-label="See how it works"><?= icon('chevron-down', 26) ?></a>
</section>

<!-- How it works ------------------------------------------------------- -->
<section class="section" id="how">
    <div class="wrap">
        <div class="center stack section-intro">
            <p class="eyebrow eyebrow-center" data-reveal>How it works</p>
            <h2 data-reveal style="--reveal-delay: 60ms">Three steps, and no games</h2>
            <p class="lede center-lede" data-reveal style="--reveal-delay: 120ms">
                Most of the work happens once. After that, the matching does the looking for you.
            </p>
        </div>

        <div class="grid grid-3">
            <?php
            $features = [
                ['icon' => 'user', 'title' => 'Build a profile worth reading',
                 'text' => 'Education, profession, family background, the things you do on a Friday. A completeness meter nudges you until it is a real picture of you.'],
                ['icon' => 'sliders', 'title' => 'Say what you are looking for',
                 'text' => 'Faith, age, height, profession, education, marital status — set what matters and leave the rest blank. Blank means you do not mind.'],
                ['icon' => 'sparkle', 'title' => 'Get matches that explain themselves',
                 'text' => 'Every profile is scored against your preferences, and every score shows its reasons. You will never wonder why someone appeared.'],
            ];

            foreach ($features as $i => $f): ?>
                <article class="feature" data-reveal style="--reveal-delay: <?= $i * 90 ?>ms">
                    <div class="feature-icon"><?= icon($f['icon'], 22) ?></div>
                    <h3><?= e($f['title']) ?></h3>
                    <p class="soft small" style="margin-top: var(--sp-2);"><?= e($f['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- The score ---------------------------------------------------------- -->
<section class="section" style="background: var(--bg-tint);">
    <div class="wrap grid grid-2" style="align-items: center; gap: var(--sp-7);">
        <div class="stack" data-reveal>
            <p class="eyebrow">Honest matching</p>
            <h2>A percentage that means something</h2>
            <p class="soft">
                A match score should be a claim you can check. Ours is built from the preferences you
                saved, weighted by how much each one tends to matter, and it always shows its working.
            </p>

            <ul class="stack-sm" style="margin-top: var(--sp-3);">
                <?php foreach ([
                    'Only the preferences you filled in are counted — you are never penalised for leaving something blank.',
                    'Near misses earn partial credit. Someone one year outside your range is not a stranger.',
                    'Every card lists the reasons behind its number.',
                ] as $point): ?>
                    <li class="row" style="align-items: flex-start; gap: var(--sp-3);">
                        <span style="color: var(--success); flex: none; margin-top: 2px;"><?= icon('check-circle', 18) ?></span>
                        <span class="small soft"><?= e($point) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- A worked example, not a screenshot -->
        <div class="card" data-reveal style="--reveal-delay: 120ms">
            <div class="row" style="gap: var(--sp-4);">
                <img class="avatar avatar-lg" src="<?= avatar_data_uri('Ayesha Rahman') ?>" alt="">
                <div class="grow">
                    <h3 style="font-size: var(--step-1);">Ayesha R.</h3>
                    <p class="small muted">28 · Architect · Dhaka</p>
                </div>
                <?php
                $demoScore = 92;
                $r = 26;
                $circumference = 2 * M_PI * $r;
                ?>
                <div class="ring" style="--pct: <?= $demoScore ?>">
                    <svg width="64" height="64" viewBox="0 0 64 64">
                        <defs>
                            <linearGradient id="ringGradient" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="var(--wine-500)"/>
                                <stop offset="1" stop-color="var(--gold-500)"/>
                            </linearGradient>
                        </defs>
                        <circle class="ring-track" cx="32" cy="32" r="<?= $r ?>" fill="none" stroke-width="5"/>
                        <circle class="ring-fill" cx="32" cy="32" r="<?= $r ?>" fill="none" stroke-width="5"
                                stroke-dasharray="<?= round($circumference, 2) ?>"
                                stroke-dashoffset="<?= round($circumference * (1 - $demoScore / 100), 2) ?>"/>
                    </svg>
                    <span class="ring-label small"><?= $demoScore ?>%</span>
                </div>
            </div>

            <ul class="profile-card-why" style="margin-top: var(--sp-4);">
                <?php foreach ([
                    'Age 28 is inside your 26–33 range',
                    'Both Muslim',
                    'Shared interest in reading, travelling',
                    'Also works in Engineering & Architecture',
                ] as $reason): ?>
                    <li><?= icon('check', 13) ?> <span><?= e($reason) ?></span></li>
                <?php endforeach; ?>
            </ul>

            <p class="small muted" style="margin-top: var(--sp-4);">
                An illustration of the scoring — not a real member.
            </p>
        </div>
    </div>
</section>

<!-- Steps + trust ------------------------------------------------------ -->
<section class="section">
    <div class="wrap grid grid-2" style="gap: var(--sp-7); align-items: start;">
        <div class="stack" data-reveal>
            <p class="eyebrow">From sign-up to hello</p>
            <h2>What the first week looks like</h2>

            <ol class="steps" style="margin-top: var(--sp-5);">
                <?php foreach ([
                    ['Create your profile', 'Name, date of birth, profession, faith. Two minutes. A photo is optional — we generate one until you are ready.'],
                    ['Set your preferences', 'The person you are hoping to meet. This is what your ranking is built from.'],
                    ['Browse and request', 'Send a connection request with a note. If they already sent you one, you match on the spot.'],
                    ['Talk, once you both agree', 'Chat opens only after both sides accept. Nobody can message you out of nowhere.'],
                ] as [$title, $text]): ?>
                    <li class="step">
                        <h3 style="font-size: var(--step-0);"><?= e($title) ?></h3>
                        <p class="small soft"><?= e($text) ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>

        <div class="stack" data-reveal style="--reveal-delay: 120ms">
            <p class="eyebrow">Built to be safe</p>
            <h2>Your details, handled properly</h2>

            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--sp-3); margin-top: var(--sp-4);">
                <?php foreach ([
                    ['lock', 'Passwords hashed', 'bcrypt, never stored or logged in the clear.'],
                    ['shield', 'Messages stay private', 'Chat opens only between members who matched.'],
                    ['users', 'You control contact', 'Decline or withdraw a request at any time.'],
                    ['check-circle', 'Real profiles', 'Every account is verified against a national ID at sign-up.'],
                ] as $item): ?>
                    <div class="card card-sunk">
                        <div class="stat-icon" style="width:34px;height:34px;"><?= icon($item[0], 17) ?></div>
                        <h3 style="font-size: var(--step-0);"><?= e($item[1]) ?></h3>
                        <p class="small muted" style="margin-top: var(--sp-1);"><?= e($item[2]) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- CTA ---------------------------------------------------------------- -->
<section class="section-sm">
    <div class="wrap">
        <div class="cta-band" data-reveal>
            <div class="cta-glow" aria-hidden="true"></div>
            <div class="stack" style="position: relative;">
                <h2 style="color: #fff;">Ready when you are</h2>
                <p style="color: rgba(255,255,255,.78); max-width: 42ch;">
                    Creating a profile is free, takes two minutes, and you can delete it whenever you like.
                </p>
                <div class="row row-wrap" style="margin-top: var(--sp-2);">
                    <a class="btn btn-gold btn-lg" href="<?= url('/register') ?>">Join Matrimonial Hub</a>
                    <a class="btn btn-lg btn-ghost hero-ghost" href="<?= url('/about') ?>">Read about us</a>
                </div>
            </div>
        </div>
    </div>
</section>
