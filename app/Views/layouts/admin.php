<?php
// THEMORA SHOP — Admin Layout
$siteName = setting('site_name', 'Themora Shop');
$adminUser = auth();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Admin') ?> — <?= e($siteName) ?></title>
  <meta name="csrf-token" content="<?= csrf_token() ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
  <script>
    const t = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
    const BASE_URL = '<?= url() ?>';
  </script>
</head>
<body>

<div class="admin-layout">
  <!-- ── Sidebar ─────────────────────────────────────────────── -->
  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-brand">
      <div class="admin-brand-icon"><i class="bi bi-lightning-charge-fill"></i></div>
      <div>
        <div class="admin-brand-name"><?= e($siteName) ?></div>
        <div class="admin-brand-sub">Admin Panel</div>
      </div>
    </div>

    <nav class="admin-nav">
      <div class="admin-nav-group">
        <div class="admin-nav-label">Overview</div>
        <a href="<?= url('admin') ?>" class="admin-nav-item"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="<?= url('admin/analytics') ?>" class="admin-nav-item"><i class="bi bi-bar-chart-line"></i> Analytics</a>
        <a href="<?= url('admin/activity-log') ?>" class="admin-nav-item"><i class="bi bi-journal-text"></i> Activity Log</a>
      </div>

      <div class="admin-nav-group">
        <div class="admin-nav-label">Catalog</div>
        <a href="<?= url('admin/products') ?>" class="admin-nav-item"><i class="bi bi-box-seam"></i> Products</a>
        <a href="<?= url('admin/categories') ?>" class="admin-nav-item"><i class="bi bi-tags"></i> Categories</a>
        <a href="<?= url('admin/products/create') ?>" class="admin-nav-item"><i class="bi bi-plus-lg"></i> Add Product</a>
        <a href="<?= url('admin/reviews') ?>" class="admin-nav-item"><i class="bi bi-star"></i> Reviews</a>
      </div>

      <div class="admin-nav-group">
        <div class="admin-nav-label">Sales</div>
        <a href="<?= url('admin/orders') ?>" class="admin-nav-item"><i class="bi bi-receipt"></i> Orders</a>
        <a href="<?= url('admin/coupons') ?>" class="admin-nav-item"><i class="bi bi-tag"></i> Coupons</a>
        <a href="<?= url('admin/affiliates') ?>" class="admin-nav-item"><i class="bi bi-people"></i> Affiliates</a>
      </div>

      <div class="admin-nav-group">
        <div class="admin-nav-label">Users</div>
        <a href="<?= url('admin/users') ?>" class="admin-nav-item"><i class="bi bi-person-lines-fill"></i> All Users</a>
      </div>

      <div class="admin-nav-group">
        <div class="admin-nav-label">Support</div>
        <a href="<?= url('admin/tickets') ?>" class="admin-nav-item"><i class="bi bi-chat-dots"></i> Tickets</a>
        <a href="<?= url('admin/faqs') ?>" class="admin-nav-item"><i class="bi bi-question-circle"></i> FAQs</a>
      </div>

      <div class="admin-nav-group">
        <div class="admin-nav-label">Config</div>
        <a href="<?= url('admin/settings') ?>" class="admin-nav-item"><i class="bi bi-gear"></i> Settings</a>
        <a href="<?= url('/') ?>" class="admin-nav-item" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Store</a>
        <a href="<?= url('admin/logout') ?>" class="admin-nav-item" style="color:var(--danger)"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </div>
    </nav>
  </aside>

  <!-- ── Main ───────────────────────────────────────────────── -->
  <div class="admin-main">
    <!-- Top Bar -->
    <header class="admin-topbar">
      <button id="sidebar-toggle" class="icon-btn" style="display:none" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <span class="admin-page-title"><?= e($title ?? 'Admin') ?></span>
      <div class="admin-topbar-right">
        <button class="icon-btn" data-theme-toggle aria-label="Toggle theme">
          <i class="bi bi-moon-fill theme-icon-light hidden"></i>
          <i class="bi bi-sun-fill theme-icon-dark"></i>
        </button>
        <a href="<?= url('/') ?>" class="icon-btn" aria-label="View store" title="View store">
          <i class="bi bi-shop"></i>
        </a>
        <div style="font-size:.8rem;color:var(--text-muted);display:flex;align-items:center;gap:.5rem">
          <div style="width:32px;height:32px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.85rem">
            <?= strtoupper(substr($adminUser['name'] ?? 'A', 0, 1)) ?>
          </div>
          <span style="display:none" class="admin-uname"><?= e($adminUser['name'] ?? '') ?></span>
        </div>
      </div>
    </header>

    <!-- Flash Messages -->
    <?php
    $s = \App\Core\Session::getFlash('success');
    $e = \App\Core\Session::getFlash('error');
    if ($s): ?>
    <div style="margin:1rem 1.75rem 0"><div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= $s ?></div></div>
    <?php endif; if ($e): ?>
    <div style="margin:1rem 1.75rem 0"><div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= $e ?></div></div>
    <?php endif; ?>

    <!-- Page Content -->
    <div class="admin-content">
      <?= $content ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js" defer></script>
<script src="<?= asset('js/app.js') ?>" defer></script>
<script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>
