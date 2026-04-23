<?php
// THEMORA SHOP — Privacy Policy View
?>
<div class="container" style="padding-top: 5rem; padding-bottom: 8rem; max-width: 800px;">
    <div class="reveal">
        <span class="section-eyebrow">Legal</span>
        <h1 style="font-size: 3rem; margin-bottom: 1.5rem;">Privacy <span class="text-gradient">Policy</span></h1>
        <p style="color: var(--text-dim); margin-bottom: 3rem;">Last Updated: <?= date('M d, Y') ?></p>
        
        <div class="legal-content" style="line-height: 1.8; color: var(--text-muted); font-size: 1.05rem;">
            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">1. Introduction</h3>
                <p>Welcome to <?= setting('site_name') ?>. We value your privacy and are committed to protecting your personal data. This policy explains how we collect and use your information when you use our digital shop.</p>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">2. Data Collection</h3>
                <p>We collect essential information to process your orders, including:</p>
                <ul style="margin-left: 1.5rem; margin-top: 1rem;">
                    <li>Name and Email Address</li>
                    <li>Billing Information (handled securely via payment gateways)</li>
                    <li>IP Address and browser metadata for security and fraud prevention</li>
                </ul>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">3. How We Use Data</h3>
                <p>Your data is used to deliver your digital purchases, provide customer support, and send occasional updates if you've subscribed to our newsletter.</p>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">4. Cookies</h3>
                <p>We use cookies to maintain your shopping cart, login session, and analyze site traffic to improve our store experience.</p>
            </section>

            <div class="card" style="padding: 2rem; background: rgba(var(--bg-rgb), 0.3); margin-top: 4rem;">
                <h4 style="margin-bottom: 0.5rem;">Questions?</h4>
                <p style="font-size: 0.9rem; margin-bottom: 1.5rem;">If you have any questions about this policy, please reach out to our support team.</p>
                <a href="<?= url('contact') ?>" class="btn btn-primary">Contact Support</a>
            </div>
        </div>
    </div>
</div>
