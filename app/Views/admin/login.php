<?php
// THEMORA SHOP — Admin Login Page (no layout)
$siteName = setting('site_name', 'Themora Shop');
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — <?= e($siteName) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
  <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');</script>
</head>
<body>
<div class="admin-login-page">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 50% 0%,rgba(99,102,241,.15),transparent 70%);pointer-events:none"></div>
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(99,102,241,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,.04) 1px,transparent 1px);background-size:50px 50px;pointer-events:none"></div>

  <div class="admin-login-card">
    <div style="text-align:center;margin-bottom:2rem">
      <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--accent-dark),var(--accent));border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;color:white;box-shadow:0 8px 28px rgba(99,102,241,.4)">
        <i class="bi bi-shield-lock-fill"></i>
      </div>
      <h1 style="font-size:1.5rem;margin-bottom:.375rem">Admin Access</h1>
      <p style="color:var(--text-muted);font-size:.875rem"><?= e($siteName) ?> Control Panel</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url('admin/login') ?>">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label" for="email">Admin Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="admin@themorashop.com" required autocomplete="email">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div style="position:relative">
          <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
          <button type="button" onclick="const i=document.getElementById('password');i.type=i.type==='password'?'text':'password'" style="position:absolute;right:.875rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-dim);cursor:pointer"><i class="bi bi-eye"></i></button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:.75rem">
        <i class="bi bi-shield-check"></i> Access Dashboard
      </button>
    </form>

    <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border);text-align:center">
      <a href="<?= url('/') ?>" style="font-size:.8rem;color:var(--text-dim)"><i class="bi bi-arrow-left"></i> Back to Storefront</a>
    </div>

    <div style="margin-top:1rem;padding:.875rem;background:rgba(99,102,241,.06);border:1px solid rgba(99,102,241,.15);border-radius:10px;font-size:.8rem;color:var(--text-dim);text-align:center">
      <i class="bi bi-info-circle" style="color:var(--accent-light)"></i>
      Demo: admin@themorashop.com / password
    </div>
  </div>
</div>
</body>
</html>
