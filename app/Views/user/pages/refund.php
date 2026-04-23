<?php
// THEMORA SHOP — Refund Policy View
?>
<div class="container" style="padding-top: 5rem; padding-bottom: 8rem; max-width: 800px;">
    <div class="reveal">
        <span class="section-eyebrow">Legal</span>
        <h1 style="font-size: 3rem; margin-bottom: 1.5rem;">Refund <span class="text-gradient">Policy</span></h1>
        <p style="color: var(--text-dim); margin-bottom: 3rem;">Clear & Transparent Policy</p>
        
        <div class="legal-content" style="line-height: 1.8; color: var(--text-muted); font-size: 1.05rem;">
            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">1. Nature of Digital Goods</h3>
                <p>Because our products are digital assets delivered instantly via download, we generally do not offer refunds once a file has been accessed. However, your satisfaction is our priority.</p>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">2. Eligibility for Refund</h3>
                <p>We will consider refund requests under the following conditions within 14 days of purchase:</p>
                <ul style="margin-left: 1.5rem; margin-top: 1rem;">
                    <li>The product file is corrupted or technically defective.</li>
                    <li>The product is significantly different from its description or preview.</li>
                    <li>You were double-charged for the same item due to a technical error.</li>
                </ul>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">3. Non-Refundable Cases</h3>
                <p>We cannot issue refunds for:</p>
                <ul style="margin-left: 1.5rem; margin-top: 1rem;">
                    <li>"Change of mind" after the product has been downloaded.</li>
                    <li>Incompatibility with software not listed in the product requirements.</li>
                    <li>Lack of skills required to use the digital asset.</li>
                </ul>
            </section>

            <section style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">4. Processing Refunds</h3>
                <p>Approved refunds are processed to the original payment method within 5-7 business days. Once a refund is issued, your license to use the product is immediately revoked.</p>
            </section>

            <div class="card" style="padding: 2.5rem; border: 1px dashed var(--accent); background: rgba(var(--accent-rgb), 0.05); text-align: center; margin-top: 4rem;">
                <i class="bi bi-patch-check" style="font-size: 2.5rem; color: var(--accent); margin-bottom: 1rem; display: block;"></i>
                <h4 style="margin-bottom: 0.5rem;">Need Help?</h4>
                <p style="margin-bottom: 1.5rem;">Our goal is 100% satisfaction. If you're having trouble with a product, please let us know so we can fix it!</p>
                <a href="<?= url('dashboard/tickets/new') ?>" class="btn btn-primary">Open a Support Ticket</a>
            </div>
        </div>
    </div>
</div>
