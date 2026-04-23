<?php // THEMORA SHOP — Forgot Password page ?>
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:3rem 1.25rem">
  <div style="max-width:420px;width:100%">
    <div style="text-align:center;margin-bottom:2rem">
      <a href="<?= url('/') ?>" class="site-logo" style="font-size:1.5rem;display:inline-block;margin-bottom:1.5rem"><i class="bi bi-lightning-charge-fill"></i> <?= e(setting('site_name')) ?></a>
      <h1 style="font-size:1.75rem;margin-bottom:.5rem">Reset Password</h1>
      <p style="color:var(--text-muted);font-size:.9rem">Enter your email and we'll send you a reset link</p>
    </div>

    <?php if ($sent): ?>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2.5rem;text-align:center">
      <div style="font-size:3rem;margin-bottom:1rem">📧</div>
      <h3 style="margin-bottom:.75rem">Check Your Inbox</h3>
      <p style="color:var(--text-muted);margin-bottom:1.5rem">We've sent a password reset link to your email. It expires in 1 hour.</p>
      <a href="<?= url('login') ?>" class="btn btn-secondary btn-full"><i class="bi bi-arrow-left"></i> Back to Login</a>
    </div>
    <?php else: ?>
    <div class="card">
      <div class="card-body" style="padding:2rem">
        <?php if ($error): ?><div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= url('forgot-password') ?>">
          <?= csrf_field() ?>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" required autocomplete="email">
          </div>
          <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:.5rem">
            <i class="bi bi-envelope"></i> Send Reset Link
          </button>
        </form>
      </div>
      <div class="card-footer" style="text-align:center;font-size:.875rem;color:var(--text-muted)">
        Remember your password? <a href="<?= url('login') ?>" style="color:var(--accent-light);font-weight:600">Sign in</a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
