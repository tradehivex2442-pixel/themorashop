<?php
// THEMORA SHOP — Login Page
?>
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:3rem 1.25rem;position:relative">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 50% 0%,rgba(99,102,241,.12),transparent 70%);pointer-events:none"></div>
  <div style="max-width:440px;width:100%;z-index:1">
    <div style="text-align:center;margin-bottom:2rem">
      <a href="<?= url('/') ?>" class="site-logo" style="font-size:1.5rem;display:inline-block;margin-bottom:1.5rem">
        <i class="bi bi-lightning-charge-fill"></i> <?= e(setting('site_name')) ?>
      </a>
      <h1 style="font-size:1.75rem;margin-bottom:.5rem">Welcome back</h1>
      <p style="color:var(--text-muted);font-size:.9rem">Sign in to your account to continue</p>
    </div>

    <div class="card">
      <div class="card-body" style="padding:2rem">
        <?php if ($error): ?>
        <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= $success ?></div>
        <?php endif; ?>
        <?php if ($info): ?>
        <div class="alert alert-info"><i class="bi bi-info-circle-fill"></i> <?= $info ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>" id="login-form">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" value="<?= old('email') ?>" required autocomplete="email">
          </div>

          <div class="form-group">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem">
              <label class="form-label" for="password" style="margin-bottom:0">Password</label>
              <a href="<?= url('forgot-password') ?>" style="font-size:.8rem;color:var(--accent-light)">Forgot password?</a>
            </div>
            <div style="position:relative">
              <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
              <button type="button" onclick="togglePwd('password','pwd-eye')" style="position:absolute;right:.875rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-dim);cursor:pointer;font-size:1rem">
                <i class="bi bi-eye" id="pwd-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:.75rem">
            <i class="bi bi-box-arrow-in-right"></i> Sign In
          </button>
        </form>

        <!-- Divider -->
        <div style="display:flex;align-items:center;gap:1rem;margin:1.5rem 0">
          <div style="flex:1;height:1px;background:var(--border)"></div>
          <span style="font-size:.8rem;color:var(--text-dim)">or continue with</span>
          <div style="flex:1;height:1px;background:var(--border)"></div>
        </div>

        <!-- OAuth Buttons -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <a href="<?= url('auth/google') ?>" class="btn btn-secondary" style="justify-content:center">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" style="width:18px;height:18px"> Google
          </a>
          <a href="<?= url('auth/github') ?>" class="btn btn-secondary" style="justify-content:center">
            <i class="bi bi-github"></i> GitHub
          </a>
        </div>
      </div>

      <div class="card-footer" style="text-align:center;font-size:.875rem;color:var(--text-muted)">
        <div>
          Don't have an account? <a href="<?= url('signup') ?>" style="color:var(--accent-light);font-weight:600">Sign up free</a>
        </div>
      </div>
    </div>

    <!-- Trust badges -->
    <div style="display:flex;justify-content:center;gap:1.5rem;margin-top:1.5rem">
      <div style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;color:var(--text-dim)">
        <i class="bi bi-shield-lock" style="color:var(--success)"></i> SSL Secured
      </div>
      <div style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;color:var(--text-dim)">
        <i class="bi bi-lock" style="color:var(--success)"></i> 2FA Available
      </div>
    </div>
  </div>
</div>

<script>
function togglePwd(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon  = document.getElementById(iconId);
  if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
  else { input.type = 'password'; icon.className = 'bi bi-eye'; }
}
</script>
