<?php
// THEMORA SHOP — Terms of Service View
?>
<div class="container" style="padding-top: 5rem; padding-bottom: 8rem; max-width: 800px;">
    <div class="reveal">
        <span class="section-eyebrow">Legal</span>
        <h1 style="font-size: 3rem; margin-bottom: 1.5rem;">Terms of <span class="text-gradient">Service</span></h1>
        <p style="color: var(--text-dim); margin-bottom: 3rem;">Effective Date: <?= date('M d, Y') ?></p>
        
        <div class="legal-content" style="line-height: 1.8; color: var(--text-muted); font-size: 1.05rem;">
            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">1. Acceptance of Terms</h3>
                <p>By accessing or using <?= setting('site_name') ?>, you agree to be bound by these terms. If you do not agree to any part of these terms, you may not access our services.</p>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">2. Digital Products</h3>
                <p>All products sold on this platform are digital assets. Upon successful payment, you are granted a non-exclusive license to use the downloaded content as per the specific license accompanying the product.</p>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">3. User Accounts</h3>
                <p>You are responsible for maintaining the confidentiality of your account credentials. <?= setting('site_name') ?> reserves the right to terminate accounts that violate our community standards or engage in fraudulent activity.</p>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">4. Prohibited Uses</h3>
                <p>You may not redistribute, resell, or sublicense our digital products unless explicitly permitted by an Extended License. Reverse engineering or attempting to bypass digital rights management is strictly prohibited.</p>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">5. Limitation of Liability</h3>
                <p>In no event shall <?= setting('site_name') ?> be liable for any indirect, incidental, or consequential damages arising out of the use or inability to use our digital assets.</p>
            </section>
        </div>
    </div>
</div>
