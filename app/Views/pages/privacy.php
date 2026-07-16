<div class="wrap-narrow page">
    <div class="stack-lg">
        <div class="stack-sm">
            <p class="eyebrow">Privacy policy</p>
            <h1>What we hold, and who can see it</h1>
            <p class="small muted">Last updated <?= date('j F Y') ?></p>
        </div>

        <div class="card card-sunk">
            <p class="small soft">
                <strong>In one paragraph:</strong> we keep the details you type in, we show most of
                them to other members, we keep your national ID private, and we do not sell anything
                to anyone. Ask us to delete your account and we delete it.
            </p>
        </div>

        <div class="prose">
            <h2>What we collect</h2>
            <ul>
                <li><strong>Identity</strong> — name, date of birth, gender, religion, ethnicity, profession, national ID number.</li>
                <li><strong>Profile</strong> — education, address, phone number, height, weight, complexion, biography, family background, interests and hobbies.</li>
                <li><strong>Preferences</strong> — the criteria you set for the person you are hoping to meet.</li>
                <li><strong>Activity</strong> — sign-ins, requests sent and answered, and the times of them.</li>
                <li><strong>Messages</strong> — the content of chats with members you have matched with.</li>
            </ul>

            <h2>Who can see what</h2>
            <ul>
                <li><strong>Any signed-in member</strong> can see your name, photo, age, gender, religion, ethnicity, profession, marital status, physical details, biography, family background and interests.</li>
                <li><strong>Only members you have matched with</strong> can see your phone number and address, or message you.</li>
                <li><strong>Nobody</strong> sees your national ID or your password. The ID is stored for verification; the password is stored only as a bcrypt hash, which cannot be read back.</li>
                <li><strong>Administrators</strong> can see your account details and change your account status. They cannot read your messages through the admin console.</li>
            </ul>

            <h2>What we do not do</h2>
            <ul>
                <li>We do not sell or share your details with advertisers or data brokers.</li>
                <li>We do not email you anything you did not ask for.</li>
                <li>We do not rank profiles by payment. The order you see is your own preferences, applied.</li>
            </ul>

            <h2>How long we keep it</h2>
            <p>
                Until you ask us to delete it. Deleting your account removes your profile, preferences,
                photos, requests and messages. Entries in our activity log survive with the name
                removed, because we need a record that the site was used.
            </p>

            <h2>Your rights</h2>
            <p>
                You can see everything we hold about you on your profile page, correct any of it there,
                and have all of it deleted by asking. There is no waiting period and we do not ask why.
            </p>

            <h2>Security</h2>
            <p>
                Passwords are hashed with bcrypt. Every database query is parameterised. Sessions are
                regenerated whenever your privileges change, and forms carry anti-forgery tokens. None
                of that makes a system perfect — if you find a problem, please tell us via the
                <a href="<?= url('/help') ?>">help centre</a>.
            </p>

            <h2>Contact</h2>
            <p>
                Questions about any of this go to
                <a href="mailto:privacy@matrimonialhub.test">privacy@matrimonialhub.test</a>.
            </p>
        </div>
    </div>
</div>
