<?php // THEMORA SHOP — User Wishlist ?>
<div class="container" style="padding-top:2.5rem;padding-bottom:5rem">
  <div class="dashboard-layout">
    <aside class="dashboard-sidebar">
      <div class="sidebar-user" style="color:white">
        <?php $u = auth(); ?>
        <div class="sidebar-avatar" style="background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;margin:0 auto .75rem"><?= strtoupper(substr($u['name'],0,1)) ?></div>
        <div style="font-weight:700;font-size:.95rem"><?= e($u['name']) ?></div>
      </div>
      <nav class="sidebar-nav">
        <a href="<?= url('dashboard') ?>" class="sidebar-nav-item"><i class="bi bi-speedometer2"></i> Overview</a>
        <a href="<?= url('dashboard/orders') ?>" class="sidebar-nav-item"><i class="bi bi-bag"></i> My Orders</a>
        <a href="<?= url('dashboard/wishlist') ?>" class="sidebar-nav-item active"><i class="bi bi-heart"></i> Wishlist <span class="badge badge-primary" style="margin-left:auto"><?= count($wishlist) ?></span></a>
        <a href="<?= url('dashboard/affiliate') ?>" class="sidebar-nav-item"><i class="bi bi-link-45deg"></i> Affiliate</a>
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item"><i class="bi bi-chat-dots"></i> Support</a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item"><i class="bi bi-person-gear"></i> Profile</a>
      </nav>
    </aside>

    <div class="dashboard-main">
      <h1 style="font-size:1.375rem;font-weight:800;margin-bottom:1.5rem">My Wishlist <span style="font-size:.9rem;font-weight:400;color:var(--text-muted)">(<?= count($wishlist) ?> items)</span></h1>

      <?php if (empty($wishlist)): ?>
      <div style="text-align:center;padding:5rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)">
        <div style="font-size:4rem;margin-bottom:1rem">❤️</div>
        <h3 style="margin-bottom:.75rem">Your wishlist is empty</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem">Save products you love for later</p>
        <a href="<?= url('products') ?>" class="btn btn-primary"><i class="bi bi-grid-fill"></i> Explore Products</a>
      </div>
      <?php else: ?>
      <div class="products-grid">
        <?php foreach ($wishlist as $p): ?>
        <?php include dirname(dirname(__DIR__)) . '/partials/product-card.php'; ?>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
