<?php
// THEMORA SHOP — User Orders List
?>
<div class="container" style="padding-top:3rem;padding-bottom:6rem">
  <div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar shadow-lg">
      <div class="sidebar-user">
        <?php $u = auth(); ?>
        <div class="sidebar-avatar-wrapper">
          <?php if (!empty($u['avatar'])): ?>
            <img src="<?= e($u['avatar']) ?>" class="sidebar-avatar" alt="Avatar">
          <?php else: ?>
            <div class="sidebar-avatar-placeholder"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
          <?php endif; ?>
        </div>
        <div class="sidebar-user-name"><?= e($u['name']) ?></div>
        <div class="sidebar-user-email"><?= e($u['email']) ?></div>
      </div>
      <nav class="sidebar-nav">
        <a href="<?= url('dashboard') ?>" class="sidebar-nav-item"><i class="bi bi-grid-1x2-fill"></i> <span>Overview</span></a>
        <a href="<?= url('dashboard/orders') ?>" class="sidebar-nav-item active"><i class="bi bi-bag-check-fill"></i> <span>My Orders</span></a>
        <a href="<?= url('dashboard/wishlist') ?>" class="sidebar-nav-item"><i class="bi bi-heart-fill"></i> <span>Wishlist</span></a>
        <a href="<?= url('dashboard/affiliate') ?>" class="sidebar-nav-item"><i class="bi bi-percent"></i> <span>Affiliate</span></a>
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item"><i class="bi bi-headset"></i> <span>Support</span></a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item"><i class="bi bi-person-fill-gear"></i> <span>Profile</span></a>
        <div class="sidebar-divider"></div>
        <a href="<?= url('logout') ?>" class="sidebar-nav-item logout-link"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="dashboard-main">
      <div class="dashboard-header-row mb-6">
        <div>
          <h1 class="text-gradient mb-1" style="font-size:1.75rem">Order History</h1>
          <p class="text-dim" style="font-size:.9rem">Track your digital purchases and downloads</p>
        </div>
        <a href="<?= url('products') ?>" class="btn btn-primary btn-glow">
          <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Explore Products</span>
        </a>
      </div>

      <?php if (empty($orders)): ?>
      <div class="empty-state-card reveal visible">
        <div class="empty-icon-wrap">
          <i class="bi bi-box2-heart"></i>
        </div>
        <h2 class="mb-2">Your library is empty</h2>
        <p class="text-dim mb-6">Start your journey with Themora Shop by exploring our premium digital products.</p>
        <a href="<?= url('products') ?>" class="btn btn-primary btn-lg">Browse Latest Items</a>
      </div>
      <?php else: ?>
      <div class="orders-timeline">
        <?php foreach ($orders as $o): ?>
        <div class="order-card-premium reveal visible mb-6">
          <div class="order-card-header">
            <div class="order-meta">
              <div class="order-id">
                <span class="label">ORDER</span>
                <a href="<?= url('dashboard/order/' . $o['id']) ?>">#<?= $o['id'] ?></a>
              </div>
              <div class="order-date"><i class="bi bi-calendar3"></i> <?= date('M j, Y', strtotime($o['created_at'])) ?></div>
            </div>
            <div class="order-status-price">
              <?php 
                $badges = [
                  'paid'     => ['class' => 'badge-success', 'icon' => 'bi-check-circle-fill'],
                  'pending'  => ['class' => 'badge-warning', 'icon' => 'bi-clock-fill'],
                  'failed'   => ['class' => 'badge-danger',  'icon' => 'bi-x-circle-fill'],
                  'refunded' => ['class' => 'badge-dark',    'icon' => 'bi-arrow-left-circle-fill']
                ];
                $b = $badges[$o['status']] ?? ['class' => 'badge-dark', 'icon' => 'bi-info-circle-fill'];
              ?>
              <span class="badge <?= $b['class'] ?>-soft"><i class="<?= $b['icon'] ?>"></i> <?= ucfirst($o['status']) ?></span>
              <div class="order-total"><?= currency((float)$o['total']) ?></div>
            </div>
          </div>
          
          <div class="order-card-body">
            <div class="order-items-grid">
              <?php foreach ($o['items'] as $item): ?>
              <div class="order-product-row">
                <div class="product-info-wrap">
                  <?php if ($item['thumbnail']): ?>
                    <img src="<?= asset($item['thumbnail']) ?>" class="product-thumb-sm" alt="<?= e($item['title']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=P&background=6366f1&color=fff'">
                  <?php else: ?>
                    <div class="product-thumb-sm-placeholder"><i class="bi bi-file-earmark-zip"></i></div>
                  <?php endif; ?>
                  <div class="product-details">
                    <div class="product-title"><?= e($item['title']) ?></div>
                    <div class="product-subtitle">
                      <span class="download-counter"><i class="bi bi-arrow-down-circle"></i> <?= $item['download_count'] ?> / <?= $item['max_downloads'] ?> downloads</span>
                    </div>
                  </div>
                </div>
                <div class="product-actions">
                  <?php if ($o['status'] === 'paid' && $item['download_count'] < $item['max_downloads']): ?>
                  <a href="<?= generate_download_link($item['id']) ?>" class="btn btn-secondary btn-sm btn-download">
                    <i class="bi bi-cloud-download-fill"></i> Download
                  </a>
                  <?php elseif ($item['download_count'] >= $item['max_downloads']): ?>
                  <span class="btn btn-ghost btn-sm disabled" title="Download limit reached">
                    <i class="bi bi-lock-fill"></i> Limit Reached
                  </span>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="order-card-footer">
            <div class="footer-left">
              <a href="<?= url('dashboard/order/' . $o['id']) ?>" class="link-hover"><i class="bi bi-receipt"></i> Details</a>
            </div>
            <div class="footer-right">
              <?php if ($o['status'] === 'paid'): ?>
              <a href="<?= url('dashboard/tickets/new?order=' . $o['id']) ?>" class="support-link">
                <i class="bi bi-chat-left-dots"></i> Get Support
              </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination Premium -->
      <?php if (($pagination['pages'] ?? 1) > 1): ?>
      <div class="pagination-wrapper mt-8">
        <div class="pagination-nav">
          <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
          <a href="?page=<?= $i ?>" class="page-link <?= $i === ($pagination['current'] ?? 1) ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
/* Dashboard Layout Enhancements */
.dashboard-sidebar {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 0;
  transition: transform 0.3s ease;
}

.sidebar-user {
  padding: 2.5rem 1.5rem;
  background: linear-gradient(135deg, var(--accent-dark), var(--accent));
  color: white;
  text-align: center;
}

.sidebar-avatar-wrapper {
  position: relative;
  width: 80px;
  height: 80px;
  margin: 0 auto 1rem;
}

.sidebar-avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 3px solid rgba(255,255,255,0.25);
  object-fit: cover;
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.sidebar-avatar-placeholder {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: rgba(255,255,255,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 800;
  border: 3px solid rgba(255,255,255,0.2);
}

.sidebar-user-name { font-weight: 700; font-size: 1.15rem; margin-bottom: 0.25rem; }
.sidebar-user-email { font-size: 0.8rem; opacity: 0.8; }

.sidebar-nav { padding: 1rem; }
.sidebar-divider { height: 1px; background: var(--border); margin: 0.75rem 1rem; }

/* Premium Order Card */
.order-card-premium {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.order-card-premium:hover {
  transform: translateY(-4px);
  border-color: rgba(99, 102, 241, 0.3);
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2), 0 10px 10px -5px rgba(0,0,0,0.1);
}

.order-card-header {
  padding: 1.25rem 1.5rem;
  background: var(--bg-2);
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.order-id .label {
  font-size: 0.65rem;
  font-weight: 800;
  color: var(--text-dim);
  letter-spacing: 0.05em;
  margin-right: 0.5rem;
}

.order-id a {
  font-weight: 700;
  color: var(--text);
  font-size: 1rem;
}

.order-id a:hover { color: var(--accent-light); }

.order-date {
  font-size: 0.8rem;
  color: var(--text-dim);
  margin-top: 0.25rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.order-status-price {
  text-align: right;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.5rem;
}

.order-total {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -0.01em;
}

/* Row Styling */
.order-product-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 0;
  border-bottom: 1px solid var(--border);
  gap: 1.5rem;
}

.order-product-row:last-child { border-bottom: none; }

.product-info-wrap {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  flex: 1;
}

.product-thumb-sm {
  width: 54px;
  height: 54px;
  border-radius: var(--radius-sm);
  object-fit: cover;
  background: var(--bg-3);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.product-thumb-sm-placeholder {
  width: 54px;
  height: 54px;
  border-radius: var(--radius-sm);
  background: linear-gradient(135deg, var(--accent-dark), #a855f7);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
}

.product-title {
  font-weight: 600;
  font-size: 0.95rem;
  color: var(--text);
  margin-bottom: 0.25rem;
}

.product-subtitle { font-size: 0.78rem; color: var(--text-dim); }
.download-counter i { margin-right: 0.25rem; font-size: 0.85rem; }

/* Status Badges-soft */
.badge-success-soft { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
.badge-warning-soft { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2); }
.badge-danger-soft  { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
.badge-dark-soft    { background: rgba(255, 255, 255, 0.05); color: var(--text-muted); border: 1px solid var(--border); }

/* Download Button */
.btn-download {
  border: 1px solid var(--border);
  padding: 0.5rem 1rem;
  border-radius: 99px;
  font-weight: 700;
  font-size: 0.8rem;
  background: var(--bg-3);
  color: var(--text);
  transition: all 0.2s;
}

.btn-download:hover {
  background: var(--accent);
  color: white;
  border-color: var(--accent);
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Card Footer */
.order-card-footer {
  padding: 0.75rem 1.5rem;
  background: var(--bg-2);
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.link-hover { color: var(--text-dim); font-size: 0.85rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; }
.link-hover:hover { color: var(--accent-light); }

.support-link {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--accent-light);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.8rem;
  border-radius: 8px;
  transition: background 0.2s;
}

.support-link:hover { background: rgba(99, 102, 241, 0.1); }

/* Empty State */
.empty-state-card {
  text-align: center;
  padding: 6rem 2rem;
  background: var(--surface);
  border: 2px dashed var(--border);
  border-radius: var(--radius-lg);
}

.empty-icon-wrap {
  width: 100px;
  height: 100px;
  background: rgba(99, 102, 241, 0.05);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  font-size: 3rem;
  color: var(--accent);
}

/* Pagination */
.pagination-wrapper { display: flex; justify-content: center; }
.pagination-nav {
  display: inline-flex;
  gap: 0.5rem;
  background: var(--surface);
  padding: 0.5rem;
  border-radius: 99px;
  border: 1px solid var(--border);
}

.page-link {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  color: var(--text-dim);
  font-weight: 700;
  transition: all 0.2s;
  text-decoration: none;
}

.page-link:hover { background: var(--bg-3); color: var(--text); }
.page-link.active { background: var(--accent); color: white; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.4); }

@media (max-width: 640px) {
  .order-card-header { padding: 1rem; }
  .order-card-body { padding: 0 1rem; }
  .order-product-row { flex-direction: column; align-items: flex-start; gap: 1rem; }
  .product-actions { width: 100%; border-top: 1px solid var(--border); padding-top: 1rem; }
  .product-actions .btn { width: 100%; }
}
</style>
