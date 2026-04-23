<?php // THEMORA SHOP — Password Reset page ?>
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:3rem 1.25rem">
  <div style="max-width:420px;width:100%">
    <div style="text-align:center;margin-bottom:2rem">
      <a href="<?= url('/') ?>" class="site-logo" style="font-size:1.5rem;display:inline-block;margin-bottom:1.5rem">
        <i class="bi bi-lightning-charge-fill"></i> <?= e(setting('site_name', 'Themora Shop')) ?>
      </a>
      <h1 style="font-size:1.75rem;margin-bottom:.5rem">Set new password</h1>
      <p style="color:var(--text-muted);font-size:.9rem">Enter a strong password for your account</p>
    </div>

    <div class="card">
      <div class="card-body" style="padding:2rem">
        <?php if ($error): ?><div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= e($error) ?></div><?php endif; ?>
        <?php if ($expired): ?>
        <div class="alert alert-error"><i class="bi bi-clock"></i> This reset link has expired or already been used.</div>
        <a href="<?= url('forgot-password') ?>" class="btn btn-primary btn-full">Request a new link</a>
        <?php else: ?>
        <form method="POST" action="<?= url('reset-password') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="token" value="<?= e($token) ?>">
          <div class="form-group">
            <label class="form-label">New Password</label>
            <div style="position:relative">
              <input type="password" name="password" id="pw-field" class="form-control" placeholder="Min 8 characters" required minlength="8">
              <button type="button" onclick="togglePw('pw-field','pw-eye')" class="icon-btn" style="position:absolute;right:0;top:0;height:44px;width:44px"><i id="pw-eye" class="bi bi-eye"></i></button>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirm" class="form-control" placeholder="Repeat your password" required minlength="8">
          </div>
          <!-- Password strength indicator -->
          <div style="height:4px;border-radius:99px;background:var(--bg-3);margin-bottom:1rem;overflow:hidden">
            <div id="pw-strength-bar" style="height:100%;width:0;border-radius:99px;transition:width .3s,background .3s"></div>
          </div>
          <button type="submit" class="btn btn-primary btn-full btn-lg"><i class="bi bi-shield-lock-fill"></i> Reset Password</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
function togglePw(fieldId, eyeId) {
  const f = document.getElementById(fieldId);
  const e = document.getElementById(eyeId);
  f.type = f.type === 'password' ? 'text' : 'password';
  e.className = f.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}
document.getElementById('pw-field')?.addEventListener('input', function() {
  const v = this.value; let s = 0;
  if (v.length >= 8)    s++;
  if (/[A-Z]/.test(v)) s++;
  if (/[0-9]/.test(v)) s++;
  if (/[^A-Za-z0-9]/.test(v)) s++;
  const bar = document.getElementById('pw-strength-bar');
  const cols = ['','#ef4444','#f59e0b','#22c55e','#6366f1'];
  bar.style.width = (s * 25) + '%';
  bar.style.background = cols[s] || '';
});
</script>
