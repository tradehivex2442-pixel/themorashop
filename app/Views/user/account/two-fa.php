<?php // THEMORA SHOP — 2FA Verification ?>
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:3rem 1.25rem">
  <div style="max-width:400px;width:100%">
    <div style="text-align:center;margin-bottom:2rem">
      <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--accent-dark),var(--accent));display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:2rem">🔐</div>
      <h1 style="font-size:1.5rem;margin-bottom:.5rem">Two-Factor Verification</h1>
      <p style="color:var(--text-muted);font-size:.875rem">Enter the 6-digit code from your authenticator app</p>
    </div>

    <div class="card">
      <div class="card-body" style="padding:2rem">
        <?php if ($error): ?><div class="alert alert-error"><i class="bi bi-shield-x-fill"></i> <?= e($error) ?></div><?php endif; ?>
        <form method="POST" action="<?= url('2fa') ?>" id="twofa-form">
          <?= csrf_field() ?>
          <!-- Six digit OTP inputs -->
          <div style="display:flex;gap:.625rem;justify-content:center;margin-bottom:1.75rem" id="otp-wrap">
            <?php for ($i = 0; $i < 6; $i++): ?>
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
              class="otp-input form-control"
              style="width:48px;height:56px;text-align:center;font-size:1.375rem;font-weight:800;padding:0;border:2px solid var(--border);border-radius:10px"
              autocomplete="<?= $i === 0 ? 'one-time-code' : 'off' ?>">
            <?php endfor; ?>
          </div>
          <input type="hidden" name="code" id="otp-code">
          <button type="submit" class="btn btn-primary btn-full btn-lg">Verify Code</button>
        </form>

        <div style="text-align:center;margin-top:1.25rem;font-size:.85rem;color:var(--text-muted)">
          <a href="<?= url('logout') ?>" style="color:var(--text-dim)"><i class="bi bi-arrow-left"></i> Back to login</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  const inputs = document.querySelectorAll('.otp-input');
  inputs.forEach((inp, idx) => {
    inp.addEventListener('input', e => {
      if (e.target.value.length === 1 && idx < inputs.length - 1) inputs[idx+1].focus();
      collectCode();
    });
    inp.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !e.target.value && idx > 0) inputs[idx-1].focus();
    });
    inp.addEventListener('paste', e => {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
      paste.split('').forEach((c, i) => { if (inputs[i]) inputs[i].value = c; });
      if (inputs[paste.length - 1]) inputs[paste.length - 1].focus();
      collectCode();
    });
  });
  function collectCode() {
    document.getElementById('otp-code').value = Array.from(inputs).map(i=>i.value).join('');
  }
  document.getElementById('twofa-form').addEventListener('submit', e => {
    const code = document.getElementById('otp-code').value;
    if (code.length !== 6) { e.preventDefault(); }
    inputs.forEach(i => { i.style.borderColor = code.length === 6 ? 'var(--accent)' : 'var(--danger)'; });
  });
})();
</script>
