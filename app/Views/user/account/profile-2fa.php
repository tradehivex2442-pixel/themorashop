<?php // THEMORA SHOP — 2FA Setup Flow ?>
<div class="container" style="padding-top:3rem;padding-bottom:6rem;max-width:800px">
  <div style="margin-bottom:2rem;display:flex;align-items:center;gap:1rem">
    <a href="<?= url('dashboard/profile') ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)"><i class="bi bi-arrow-left"></i></a>
    <h1 style="font-size:1.75rem">Two-Factor Authentication</h1>
  </div>

  <div class="card reveal visible shadow-2xl" style="padding:2rem">
    <?php if ($error): ?><div class="alert alert-error mb-6"><i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?></div><?php endif; ?>
    
    <div style="display:grid;grid-template-columns:1fr 280px;gap:3rem;align-items:start">
      <div>
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">1. Install Authenticator App</h3>
        <p class="text-dim mb-6">Download and install a TOTP app like <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong>, or <strong>Authy</strong> on your mobile device.</p>

        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">2. Scan QR Code or Enter Secret</h3>
        <p class="text-dim mb-4">Open your authenticator app and scan the QR code, or manually enter the secret key shown below.</p>
        
        <div style="background:var(--bg-3);padding:1rem;border-radius:12px;border:1px solid var(--border);margin-bottom:2rem">
            <div style="font-size:.75rem;text-transform:uppercase;color:var(--text-dim);margin-bottom:.5rem;letter-spacing:.05em">Your Secret Key</div>
            <code style="font-size:1.25rem;font-weight:800;color:var(--accent-light);letter-spacing:.1em"><?= e($user['totp_secret']) ?></code>
        </div>

        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">3. Verify 6-Digit Code</h3>
        <p class="text-dim mb-4">Enter the 6-digit code from your app to confirm everything is set up correctly.</p>
        
        <form method="POST" action="<?= url('dashboard/profile/2fa-enable') ?>">
            <?= csrf_field() ?>
            <div style="display:flex;gap:.75rem;margin-bottom:1.5rem">
                <input type="text" name="code" class="form-control" maxlength="6" placeholder="000000" style="font-size:1.5rem;letter-spacing:.5em;text-align:center;font-weight:800" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Enable 2FA Protection</button>
        </form>
      </div>

      <div style="text-align:center">
        <!-- SIMULATED QR CODE -->
        <div style="background:white;padding:1.5rem;border-radius:16px;margin-bottom:1.5rem;box-shadow:0 10px 30px rgba(0,0,0,.1)">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=otpauth://totp/ThemoraShop:<?= e($user['email']) ?>?secret=<?= e($user['totp_secret']) ?>&issuer=ThemoraShop" alt="QR Code" style="width:100%">
        </div>
        <p style="font-size:.78rem;color:var(--text-dim)"><i class="bi bi-info-circle"></i> Scan this image with your phone's camera inside the app.</p>
      </div>
    </div>
  </div>
</div>
