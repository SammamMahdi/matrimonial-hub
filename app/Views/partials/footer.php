<footer class="footer">
    <div class="wrap">
        <div class="footer-grid">
            <div>
                <a class="brand" href="<?= url('/') ?>">
                    <span class="brand-mark"><?= icon('ring', 18) ?></span>
                    Matrimonial<span class="muted">Hub</span>
                </a>
                <p class="small muted" style="margin-top: var(--sp-3); max-width: 34ch;">
                    Matches ranked on what you actually told us matters — not on who paid to be seen.
                </p>
            </div>

            <div>
                <h4>Explore</h4>
                <ul class="footer-links">
                    <li><a href="<?= url('/about') ?>">About us</a></li>
                    <li><a href="<?= url('/stories') ?>">Client stories</a></li>
                    <li><a href="<?= url('/register') ?>">Create a profile</a></li>
                </ul>
            </div>

            <div>
                <h4>Support</h4>
                <ul class="footer-links">
                    <li><a href="<?= url('/help') ?>">Help centre</a></li>
                    <li><a href="<?= url('/privacy') ?>">Privacy policy</a></li>
                    <li><a href="<?= url('/terms') ?>">Terms &amp; conditions</a></li>
                </ul>
            </div>

            <div>
                <h4>Admin</h4>
                <ul class="footer-links">
                    <li><a href="<?= url('/admin/login') ?>">Staff sign-in</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Matrimonial Hub. A CSE370 project, rebuilt.</p>
            <p>Made with care in Dhaka.</p>
        </div>
    </div>
</footer>
