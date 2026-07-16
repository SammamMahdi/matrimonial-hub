<?php

/**
 * The original site presented six testimonials — "Rahul & Sneha", "Arjun &
 * Priya" — attributed to photographs of real, identifiable couples pulled off
 * the web. Those were not our members and never said those words.
 *
 * This site is new and has no real success stories yet. Rather than invent
 * them, the page says so and explains what a story will look like when there
 * is one to tell. Illustrative content below is labelled as such.
 */
?>
<div class="wrap-narrow page">
    <div class="stack-lg">
        <div class="stack-sm">
            <p class="eyebrow">Client stories</p>
            <h1>No stories to tell yet — and we will not make them up</h1>
        </div>

        <p class="lede">
            Matrimonial Hub is new. When couples who met here are happy to share how it went, their
            words and their photographs will appear on this page, with their permission and under
            their own names.
        </p>

        <div class="card" data-reveal
             style="border-color: var(--gold-400); background: color-mix(in srgb, var(--gold-500) 7%, var(--surface));">
            <div class="row" style="gap: var(--sp-4); align-items: flex-start;">
                <span class="stat-icon" style="background: color-mix(in srgb, var(--gold-500) 20%, transparent); color: var(--gold-600); flex: none;">
                    <?= icon('info', 20) ?>
                </span>
                <div>
                    <h2 style="font-size: var(--step-1);">Why this page is empty</h2>
                    <p class="small soft" style="margin-top: var(--sp-2);">
                        A testimonial is a claim about a real person's life. Stock photographs with
                        invented names underneath are not a claim — they are decoration pretending to
                        be evidence. We would rather show you an empty page than a convincing one.
                    </p>
                </div>
            </div>
        </div>

        <section class="stack" data-reveal>
            <h2>What a story will look like</h2>
            <p class="soft small">The example below is illustrative — it is not a real couple.</p>

            <div class="quote" style="opacity: .75;">
                <p>
                    We matched on a Tuesday and spent the next three weeks arguing about whether
                    Old Dhaka biryani beats Dhanmondi. The site got the important parts right —
                    same faith, same idea of family, both of us hopeless about mornings.
                </p>
                <footer>
                    <img class="avatar avatar-sm" src="<?= avatar_data_uri('Sample Couple') ?>" alt="">
                    <div>
                        <p class="small" style="font-weight: 600;">Illustrative example</p>
                        <p class="small muted">Not a real member</p>
                    </div>
                </footer>
            </div>
        </section>

        <section class="stack" data-reveal>
            <h2>Met someone here?</h2>
            <p class="soft">
                We would genuinely love to hear about it — and only with your explicit say-so would
                anything of yours appear on this page. Write to us from the
                <a href="<?= url('/help') ?>">help centre</a>.
            </p>
        </section>

        <div class="cta-band" data-reveal>
            <div class="cta-glow" aria-hidden="true"></div>
            <div class="stack" style="position: relative;">
                <h2 style="color: #fff;">Be the first story</h2>
                <p style="color: rgba(255,255,255,.78);">Create a profile and see who is out there.</p>
                <div><a class="btn btn-gold" href="<?= url('/register') ?>">Join Matrimonial Hub</a></div>
            </div>
        </div>
    </div>
</div>
