<?php

$faqs = [
    ['How is the match percentage calculated?',
     'From the preferences you saved. Each one carries a weight — age and religion count for more than education — and a criterion only counts when you expressed that preference and the other member published that field. The score is what you achieved over what applied, so leaving a preference blank never counts against anyone. Every match card lists the reasons behind its number.'],

    ['Why can I not message someone straight away?',
     'Chat only opens once both of you have accepted. You send a connection request, they accept, and the conversation appears under Matches. This is deliberate: nobody can arrive in your inbox uninvited.'],

    ['What happens if we both send each other a request?',
     'You match immediately. If you send a request to someone whose request is already sitting in your inbox, we read that as an acceptance and open the chat.'],

    ['Who can see my phone number and address?',
     'Only members you have matched with. Your national ID is never shown to anyone — it is used at sign-up to check you are a real person.'],

    ['Can I withdraw a request?',
     'Yes, at any time before it is answered. Open Requests, switch to the Sent tab and choose Withdraw. Declining somebody is silent — they are not told.'],

    ['Do I have to upload a photo?',
     'No. We generate an avatar from your initials until you add one. Profiles with a real photo and a biography do get noticeably more requests, though.'],

    ['Why do I have no matches?',
     'Usually because your preferences are narrow — every filter on the browse page is an exact match. Try widening the age range or clearing a filter. Remember that a blank preference means "I do not mind", which is often what you want.'],

    ['How do I delete my account?',
     'Write to us from this page and we will remove it, along with your profile, photos, requests and messages. There is no soft-delete or waiting period.'],
];
?>
<div class="wrap-narrow page">
    <div class="stack-lg">
        <div class="stack-sm">
            <p class="eyebrow">Help centre</p>
            <h1>Questions people actually ask</h1>
        </div>

        <div class="stack-sm">
            <?php foreach ($faqs as $i => [$question, $answer]): ?>
                <details class="faq" data-reveal style="--reveal-delay: <?= min($i, 6) * 50 ?>ms">
                    <summary>
                        <span><?= e($question) ?></span>
                        <?= icon('chevron-down', 18, 'faq-chevron') ?>
                    </summary>
                    <p class="soft small"><?= e($answer) ?></p>
                </details>
            <?php endforeach; ?>
        </div>

        <section class="card card-sunk" data-reveal>
            <div class="row row-wrap" style="gap: var(--sp-4);">
                <span class="stat-icon"><?= icon('mail', 20) ?></span>
                <div class="grow">
                    <h2 style="font-size: var(--step-1);">Still stuck?</h2>
                    <p class="small soft" style="margin-top: var(--sp-1);">
                        Email <a href="mailto:support@matrimonialhub.test">support@matrimonialhub.test</a>
                        and a real person will answer.
                    </p>
                </div>
            </div>
        </section>

        <section class="card" data-reveal style="border-color: var(--danger);">
            <div class="row row-wrap" style="gap: var(--sp-4);">
                <span class="stat-icon" style="background: var(--danger-bg); color: var(--danger);">
                    <?= icon('shield', 20) ?>
                </span>
                <div class="grow">
                    <h2 style="font-size: var(--step-1);">Reporting someone</h2>
                    <p class="small soft" style="margin-top: var(--sp-1);">
                        If a member is harassing you or their profile looks fake, email us with their
                        name and we will look at the account the same day. Suspending an account takes
                        one click on our side.
                    </p>
                </div>
            </div>
        </section>
    </div>
</div>
