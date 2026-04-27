<?php
// THEMORA SHOP — Contact Page
?>
<div style="background:var(--bg-2);border-bottom:1px solid var(--border);padding:3.5rem 0;text-align:center">
  <div class="container container-md">
    <span class="section-eyebrow">📬 Contact Us</span>
    <h1 style="margin:.5rem 0 .75rem">Get in Touch</h1>
    <p style="color:var(--text-muted)">We typically reply within 2–4 hours during business hours</p>
  </div>
</div>

<div class="container" style="padding-top:3rem;padding-bottom:5rem;max-width:1000px">
  <div style="display:grid;grid-template-columns:1fr 320px;gap:2rem;align-items:start">

    <!-- Contact Form -->
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:2rem">
      <?php if (!empty($success)): ?>
      <div style="text-align:center;padding:2rem">
        <div style="font-size:3.5rem;margin-bottom:1rem">✅</div>
        <h3 style="margin-bottom:.75rem">Message Sent!</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem">We'll get back to you within 2–4 hours. Check your email for confirmation.</p>
        <a href="<?= url('contact') ?>" class="btn btn-secondary">Send Another</a>
      </div>
      <?php else: ?>
      <?php if ($error): ?><div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= $error ?></div><?php endif; ?>
      <form method="POST" action="<?= url('contact') ?>">
        <?= csrf_field() ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Your Name *</label>
            <input type="text" name="name" class="form-control" value="<?= e(auth() ? auth()['name'] : '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control" value="<?= e(auth() ? auth()['email'] : '') ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Subject *</label>
          <input type="text" name="subject" class="form-control" placeholder="What's this about?" required>
        </div>
        <div class="form-group">
          <label class="form-label">Message *</label>
          <textarea name="message" class="form-control" rows="6" placeholder="Tell us everything…" required></textarea>
        </div>
        <div class="form-check" style="margin-bottom:1.25rem">
          <input type="checkbox" id="agree-contact" required>
          <label for="agree-contact" style="font-size:.85rem;color:var(--text-muted)">I agree this message may be stored to process my request</label>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-send"></i> Send Message</button>
      </form>
      <?php endif; ?>
    </div>

    <!-- Contact Info -->
    <div style="display:flex;flex-direction:column;gap:1rem">
      <?php $items = [
        ['bi-envelope', 'Email', setting('support_email', 'support@themorashop.com')],
        ['bi-clock', 'Response Time', '2–4 hours on weekdays'],
        ['bi-discord', 'Discord', 'Join our community server'],
      ]; ?>
      <?php foreach ($items as [$icon, $label, $val]): ?>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;display:flex;align-items:center;gap:1rem">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(99,102,241,.1);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0"><i class="bi <?= $icon ?>"></i></div>
        <div>
          <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-dim)"><?= $label ?></div>
          <div style="font-size:.9rem;color:var(--text);margin-top:.2rem"><?= e($val) ?></div>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- FAQ CTA -->
      <div style="background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(168,85,247,.08));border:1px solid rgba(99,102,241,.3);border-radius:var(--radius);padding:1.5rem;text-align:center">
        <div style="font-size:1.5rem;margin-bottom:.5rem">❓</div>
        <div style="font-weight:600;margin-bottom:.375rem">Check our FAQ first</div>
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:1rem">Most questions are already answered there</p>
        <a href="<?= url('faq') ?>" class="btn btn-secondary btn-sm">View FAQ</a>
      </div>
    </div>
  </div>
</div>
