<?php // THEMORA SHOP — 500 Internal Server Error ?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>500 Server Error</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark')</script>
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:3rem 1.25rem;background:var(--bg)">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse 50% 40% at 50% 50%,rgba(239,68,68,.08),transparent 70%);pointer-events:none"></div>
  <div style="position:relative;z-index:1">
    <div style="font-size:8rem;font-weight:900;line-height:1;background:linear-gradient(135deg,#f87171,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">500</div>
    <h1 style="font-size:1.75rem;margin:1rem 0 .75rem">Internal Server Error</h1>
    <p style="color:var(--text-muted);max-width:380px;margin:0 auto 2rem;line-height:1.7">Something went wrong on our end. We've been notified and are looking into it.</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="<?= url('/') ?>" class="btn btn-primary btn-lg"><i class="bi bi-house"></i> Go Home</a>
      <button onclick="history.back()" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Go Back</button>
    </div>
  </div>
</div>
</body>
</html>
