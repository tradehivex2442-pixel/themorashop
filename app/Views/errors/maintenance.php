<?php // THEMORA SHOP — Maintenance Mode ?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="refresh" content="60">
  <title>Maintenance — <?= setting('site_name', 'Themora Shop') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <script>document.documentElement.setAttribute('data-theme','dark')</script>
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:3rem 1.25rem;background:var(--bg)">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse 60% 40% at 50% 50%,rgba(99,102,241,.12),transparent 70%);pointer-events:none"></div>
  <div style="position:relative;z-index:1;max-width:520px">
    <div style="font-size:4rem;margin-bottom:1.5rem;animation:spin 4s linear infinite;display:inline-block"><i class="bi bi-gear-wide-connected" style="color:var(--accent)"></i></div>
    <h1 style="margin-bottom:.75rem">Under Maintenance</h1>
    <p style="color:var(--text-muted);line-height:1.8;font-size:1rem"><?= e($msg) ?></p>
    <div style="margin-top:2rem;padding:1rem 1.5rem;background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);border-radius:var(--radius);font-size:.85rem;color:var(--text-dim)">
      <i class="bi bi-arrow-repeat" style="color:var(--accent)"></i> This page will auto-refresh every 60 seconds.
    </div>
  </div>
</div>
</body>
</html>
