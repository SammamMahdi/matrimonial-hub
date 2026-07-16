<div class="wrap-narrow page">
    <div class="stack-lg">
        <div class="stack-sm">
            <p class="eyebrow">About us</p>
            <h1>Matchmaking without the theatre</h1>
        </div>

        <p class="lede">
            Matrimonial Hub started as a university database project and grew into something we
            actually wanted to use: a place where the matching is explainable and the profiles are real.
        </p>

        <section class="stack" data-reveal>
            <h2>Why we built it</h2>
            <p class="soft">
                Every matrimonial site shows you a compatibility percentage. Almost none of them will
                tell you where the number came from. We think a number you cannot check is worse than
                no number at all — so ours is computed from the preferences you saved, and every match
                lists the reasons behind its score.
            </p>
        </section>

        <section class="stack" data-reveal>
            <h2>How we think about your data</h2>
            <p class="soft">
                Your national ID is used to check you are a real person and is never shown on your
                profile. Your phone number is only visible to people you have matched with. Nobody can
                message you until you have accepted them, and you can withdraw or decline at any point.
            </p>
        </section>

        <section class="stack" data-reveal>
            <h2>What we will not do</h2>
            <ul class="stack-sm">
                <?php foreach ([
                    'Rank profiles by who paid the most.',
                    'Invent a match percentage to make a profile look better than it is.',
                    'Let a stranger into your inbox before you have agreed to it.',
                    'Sell your details to anyone.',
                ] as $point): ?>
                    <li class="row" style="align-items: flex-start; gap: var(--sp-3);">
                        <span style="color: var(--danger); flex: none; margin-top: 3px;"><?= icon('x-circle', 17) ?></span>
                        <span class="soft"><?= e($point) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="card card-sunk" data-reveal>
            <h2 style="font-size: var(--step-1);">The team</h2>
            <p class="soft small" style="margin-top: var(--sp-2);">
                Built by students at BRAC University as a CSE370 Database Systems project, and rebuilt
                since with a proper structure, real authorisation, and an interface we are happy to put
                our names on.
            </p>
        </section>

        <div class="cta-band" data-reveal>
            <div class="cta-glow" aria-hidden="true"></div>
            <div class="stack" style="position: relative;">
                <h2 style="color: #fff;">Have a look for yourself</h2>
                <p style="color: rgba(255,255,255,.78);">Creating a profile is free and takes two minutes.</p>
                <div><a class="btn btn-gold" href="<?= url('/register') ?>">Get started</a></div>
            </div>
        </div>
    </div>
</div>
