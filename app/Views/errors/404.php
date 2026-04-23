<?php // THEMORA SHOP — 404 Error ?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>404 Not Found</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:3rem 1.25rem;background:var(--bg)">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse 50% 40% at 50% 50%,rgba(99,102,241,.1),transparent 70%);pointer-events:none"></div>
  <div style="position:relative;z-index:1">
    <div style="font-size:8rem;font-weight:900;line-height:1;background:linear-gradient(135deg,#818cf8,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">404</div>
    <h1 style="font-size:1.75rem;margin:1rem 0 .75rem">Page Not Found</h1>
    <p style="color:var(--text-muted);max-width:380px;margin:0 auto 2rem;line-height:1.7">The page you're looking for doesn't exist or has been moved.</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="<?= url('/') ?>" class="btn btn-primary btn-lg"><i class="bi bi-house"></i> Go Home</a>
      <a href="<?= url('products') ?>" class="btn btn-secondary btn-lg"><i class="bi bi-grid"></i> Browse Products</a>
    </div>
  </div>
</div>
</body>
</html>
