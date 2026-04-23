<?php
// ============================================================
// THEMORA SHOP — User Layout (Storefront Shell)
// ============================================================
$siteName   = setting('site_name', 'Themora Shop');
$cartCount  = cart_count();
$user       = auth();
$successMsg = \App\Core\Session::getFlash('success');
$errorMsg   = \App\Core\Session::getFlash('error');
$infoMsg    = \App\Core\Session::getFlash('info');
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? $siteName) ?></title>
  <meta name="description" content="<?= e($meta_desc ?? 'Premium digital products — templates, ebooks, software, courses and more.') ?>">
  <meta property="og:title" content="<?= e($title ?? $siteName) ?>">
  <meta property="og:description" content="<?= e($meta_desc ?? '') ?>">
  <meta property="og:type" content="website">
  <meta name="csrf-token" content="<?= csrf_token() ?>">
  <meta name="theme-color" content="#6366f1">
  <link rel="manifest" href="/themora_Shop/public/manifest.json">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- App CSS -->
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <script>
    // Inline theme to prevent flash
    const t = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
    const BASE_URL = '<?= url() ?>';
  </script>
</head>
<body>

<!-- ── Sticky Header ─────────────────────────────────────────── -->
<header class="site-header" role="banner">
  <div class="container header-inner">
    <a href="<?= url('/') ?>" class="site-logo">
      <i class="bi bi-lightning-charge-fill"></i> <?= e($siteName) ?>
    </a>

    <!-- Desktop Nav -->
    <nav aria-label="Main navigation">
      <ul class="nav-links">
        <li><a href="<?= url('/') ?>" class="<?= active('/') ?>">Home</a></li>
        <li><a href="<?= url('products') ?>" class="<?= active('products') ?>">Products</a></li>
        <li><a href="<?= url('faq') ?>">FAQ</a></li>
        <li><a href="<?= url('contact') ?>">Contact</a></li>
      </ul>
    </nav>

    <!-- Search -->
    <div class="search-wrap" role="search">
      <i class="bi bi-search search-icon" aria-hidden="true"></i>
      <input type="text" id="search-input" class="search-input" placeholder="Search products…" autocomplete="off" aria-label="Search products">
      <div id="search-dropdown" class="search-dropdown" role="listbox" aria-label="Search results"></div>
    </div>

    <!-- Actions -->
    <div class="header-actions">
      <!-- Theme Toggle -->
      <button class="icon-btn" data-theme-toggle aria-label="Toggle theme" title="Toggle theme">
        <i class="bi bi-moon-fill theme-icon-light hidden"></i>
        <i class="bi bi-sun-fill theme-icon-dark"></i>
      </button>

      <!-- Cart -->
      <a href="<?= url('cart') ?>" class="icon-btn" aria-label="Cart (<?= $cartCount ?> items)">
        <i class="bi bi-bag"></i>
        <span class="cart-badge" id="cart-count" <?= !$cartCount ? 'style="display:none"' : '' ?>><?= $cartCount ?></span>
      </a>

      <!-- User menu -->
      <?php if ($user): ?>
        <div style="position:relative">
          <button class="icon-btn" onclick="openModal('user-menu-modal')" aria-label="User menu">
            <?php if ($user['avatar']): ?>
              <img src="<?= asset($user['avatar']) ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;" alt="<?= e($user['name']) ?>">
            <?php else: ?>
              <i class="bi bi-person-circle"></i>
            <?php endif; ?>
          </button>
        </div>
      <?php else: ?>
        <a href="<?= url('login') ?>" class="btn btn-secondary btn-sm">Log in</a>
        <a href="<?= url('signup') ?>" class="btn btn-primary btn-sm">Sign up</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- User Menu Modal -->
<?php if ($user): ?>
<div class="modal-overlay" id="user-menu-modal">
  <div class="modal-box" style="max-width:280px;padding:1rem">
    <div style="display:flex;align-items:center;gap:.75rem;padding:.5rem;border-bottom:1px solid var(--border);margin-bottom:.75rem">
      <?php if ($user['avatar']): ?>
        <img src="<?= asset($user['avatar']) ?>" style="width:48px;height:48px;border-radius:50%;object-fit:cover">
      <?php else: ?>
        <div style="width:48px;height:48px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.25rem"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
      <?php endif; ?>
      <div>
        <div style="font-weight:700;font-size:.9rem"><?= e($user['name']) ?></div>
        <div style="font-size:.75rem;color:var(--text-dim)"><?= e($user['email']) ?></div>
      </div>
    </div>
    <nav style="display:flex;flex-direction:column;gap:2px">
      <a href="<?= url('dashboard') ?>" style="display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;border-radius:8px;color:var(--text-muted);font-size:.875rem;font-weight:500" onclick="closeModal('user-menu-modal')"><i class="bi bi-speedometer2" style="width:16px"></i> Dashboard</a>
      <a href="<?= url('dashboard/orders') ?>" style="display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;border-radius:8px;color:var(--text-muted);font-size:.875rem;font-weight:500" onclick="closeModal('user-menu-modal')"><i class="bi bi-bag" style="width:16px"></i> My Orders</a>
      <a href="<?= url('dashboard/wishlist') ?>" style="display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;border-radius:8px;color:var(--text-muted);font-size:.875rem;font-weight:500" onclick="closeModal('user-menu-modal')"><i class="bi bi-heart" style="width:16px"></i> Wishlist</a>
      <a href="<?= url('dashboard/affiliate') ?>" style="display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;border-radius:8px;color:var(--text-muted);font-size:.875rem;font-weight:500" onclick="closeModal('user-menu-modal')"><i class="bi bi-link-45deg" style="width:16px"></i> Affiliate</a>
      <a href="<?= url('dashboard/profile') ?>" style="display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;border-radius:8px;color:var(--text-muted);font-size:.875rem;font-weight:500" onclick="closeModal('user-menu-modal')"><i class="bi bi-person" style="width:16px"></i> Profile</a>
      <?php if (is_admin()): ?>
      <a href="<?= url('admin') ?>" style="display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;border-radius:8px;color:var(--accent-light);font-size:.875rem;font-weight:600"><i class="bi bi-shield-check" style="width:16px"></i> Admin Panel</a>
      <?php endif; ?>
      <hr style="border:none;border-top:1px solid var(--border);margin:.375rem 0">
      <a href="<?= url('logout') ?>" style="display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;border-radius:8px;color:#f87171;font-size:.875rem;font-weight:500"><i class="bi bi-box-arrow-right" style="width:16px"></i> Logout</a>
    </nav>
  </div>
</div>
<?php endif; ?>

<!-- ── Flash Messages ─────────────────────────────────────────── -->
<?php if ($successMsg || $errorMsg || $infoMsg): ?>
<div style="position:fixed;top:70px;left:50%;transform:translateX(-50%);z-index:9000;max-width:480px;width:calc(100% - 2rem)">
  <?php if ($successMsg): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><?= $successMsg ?></div><?php endif; ?>
  <?php if ($errorMsg):   ?><div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i><?= $errorMsg ?></div><?php endif; ?>
  <?php if ($infoMsg):    ?><div class="alert alert-info"><i class="bi bi-info-circle-fill"></i><?= $infoMsg ?></div><?php endif; ?>
</div>
<?php endif; ?>

<!-- ── Main Content ───────────────────────────────────────────── -->
<main id="main-content">
  <?= $content ?>
</main>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="site-logo" style="font-size:1.2rem"><i class="bi bi-lightning-charge-fill"></i> <?= e($siteName) ?></div>
        <p>Premium digital products for creators, developers, and entrepreneurs. Download and use instantly.</p>
        <div class="social-links" style="margin-top:1rem">
          <a href="#" class="social-link" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-link" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          <a href="#" class="social-link" aria-label="Discord"><i class="bi bi-discord"></i></a>
        </div>
      </div>
      <div>
        <p class="footer-title">Products</p>
        <ul class="footer-links">
          <li><a href="<?= url('products?category=templates') ?>">Templates</a></li>
          <li><a href="<?= url('products?category=ebooks') ?>">eBooks</a></li>
          <li><a href="<?= url('products?category=software') ?>">Software</a></li>
          <li><a href="<?= url('products?category=courses') ?>">Courses</a></li>
          <li><a href="<?= url('products?category=presets') ?>">Presets</a></li>
        </ul>
      </div>
      <div>
        <p class="footer-title">Support</p>
        <ul class="footer-links">
          <li><a href="<?= url('faq') ?>">FAQs</a></li>
          <li><a href="<?= url('contact') ?>">Contact Us</a></li>
          <li><a href="<?= url('dashboard/tickets') ?>">My Tickets</a></li>
        </ul>
      </div>
      <div>
        <p class="footer-title">Account</p>
        <ul class="footer-links">
          <li><a href="<?= url('login') ?>">Login</a></li>
          <li><a href="<?= url('signup') ?>">Create Account</a></li>
          <li><a href="<?= url('dashboard') ?>">Dashboard</a></li>
          <li><a href="<?= url('dashboard/affiliate') ?>">Affiliate</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span><?= e(setting('footer_text', '© ' . date('Y') . ' Themora Shop. All rights reserved.')) ?></span>
      <div style="display:flex;gap:1.25rem">
        <a href="<?= url('privacy-policy') ?>" style="color:var(--text-dim)">Privacy Policy</a>
        <a href="<?= url('terms-of-service') ?>" style="color:var(--text-dim)">Terms of Service</a>
        <a href="<?= url('refund-policy') ?>" style="color:var(--text-dim)">Refund Policy</a>
      </div>
    </div>
  </div>
</footer>

<!-- ── Mobile Bottom Navigation ──────────────────────────────── -->
<nav class="mobile-nav" aria-label="Mobile navigation">
  <div class="mobile-nav-inner container">
    <a href="<?= url('/') ?>" class="mobile-nav-item <?= active('/') ?>" aria-label="Home">
      <i class="bi bi-house<?= active('/') ? '-fill' : '' ?>"></i>Home
    </a>
    <a href="<?= url('products') ?>" class="mobile-nav-item <?= active('products') ?>" aria-label="Products">
      <i class="bi bi-grid<?= active('products') ? '-fill' : '' ?>"></i>Shop
    </a>
    <a href="<?= url('cart') ?>" class="mobile-nav-item <?= active('cart') ?>" aria-label="Cart" style="position:relative">
      <i class="bi bi-bag<?= active('cart') ? '-fill' : '' ?>"></i>
      <?php if ($cartCount): ?><span class="cart-badge" style="top:2px;right:26px"><?= $cartCount ?></span><?php endif; ?>
      Cart
    </a>
    <a href="<?= url($user ? 'dashboard' : 'login') ?>" class="mobile-nav-item <?= active('dashboard') ?>" aria-label="Profile">
      <i class="bi bi-person<?= active('dashboard') ? '-fill' : '' ?>"></i>
      <?= $user ? 'Account' : 'Login' ?>
    </a>
  </div>
</nav>

<!-- ── Newsletter Popup ───────────────────────────────────────── -->
<div class="newsletter-popup" id="newsletter-popup" role="dialog" aria-modal="true" aria-label="Newsletter signup">
  <div class="newsletter-modal">
    <button data-close-newsletter style="position:absolute;top:1rem;right:1rem;background:none;border:none;color:var(--text-dim);font-size:1.25rem;cursor:pointer">×</button>
    <div style="font-size:2.5rem;margin-bottom:1rem">🎁</div>
    <h3 style="margin-bottom:.5rem">Get 20% Off Your First Order!</h3>
    <p style="color:var(--text-muted);font-size:.9rem;margin-bottom:1.5rem">Subscribe to our newsletter for exclusive deals, new product alerts, and creator tips.</p>
    <form>
      <div class="input-group">
        <input type="email" class="form-control" placeholder="Your email address" required style="flex:1">
        <button type="submit" class="btn btn-primary">Subscribe</button>
      </div>
      <p style="font-size:.72rem;color:var(--text-dim);margin-top:.75rem">No spam, ever. Unsubscribe anytime.</p>
    </form>
  </div>
</div>

<!-- ── Scripts ────────────────────────────────────────────────── -->
<script src="<?= asset('js/app.js') ?>" defer></script>

<!-- PWA Service Worker -->
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => navigator.serviceWorker.register('/themora_Shop/public/sw.js'));
}
</script>
</body>
</html>
