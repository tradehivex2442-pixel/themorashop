<?php
// THEMORA SHOP — User Order Detail
$sym = setting('currency_symbol', '$');
?>
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
        <a href="<?= url('dashboard/orders') ?>" class="sidebar-nav-item active"><i class="bi bi-bag"></i> My Orders</a>
        <a href="<?= url('dashboard/wishlist') ?>" class="sidebar-nav-item"><i class="bi bi-heart"></i> Wishlist</a>
        <a href="<?= url('dashboard/affiliate') ?>" class="sidebar-nav-item"><i class="bi bi-link-45deg"></i> Affiliate</a>
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item"><i class="bi bi-chat-dots"></i> Support</a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item"><i class="bi bi-person-gear"></i> Profile</a>
      </nav>
    </aside>

    <div class="dashboard-main">
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem">
        <a href="<?= url('dashboard/orders') ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)"><i class="bi bi-arrow-left"></i></a>
        <div>
          <h1 style="font-size:1.25rem;font-weight:800;line-height:1.2">Order #<?= $order['id'] ?></h1>
          <?php $sm=['paid'=>'badge-success','pending'=>'badge-warning','failed'=>'badge-danger','refunded'=>'badge-dark']; ?>
          <span class="badge <?= $sm[$order['status']] ?? 'badge-dark' ?>"><?= ucfirst($order['status']) ?></span>
          <span style="font-size:.78rem;color:var(--text-dim);margin-left:.5rem"><?= date('M j, Y', strtotime($order['created_at'])) ?></span>
        </div>
      </div>

      <!-- Items -->
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.25rem">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem"><i class="bi bi-bag" style="color:var(--accent)"></i> Items</h3>
        <div style="display:flex;flex-direction:column;gap:1rem">
          <?php foreach ($order['items'] as $item): ?>
          <div style="display:flex;align-items:center;gap:1rem;padding-bottom:1rem;border-bottom:1px solid var(--border)">
            <div style="width:60px;height:50px;border-radius:8px;background:var(--bg-3);overflow:hidden;flex-shrink:0">
              <?php if ($item['thumbnail']): ?>
                <img src="<?= asset($item['thumbnail']) ?>" style="width:100%;height:100%;object-fit:cover" alt="<?= e($item['title']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=P&background=6366f1&color=fff'">
              <?php else: ?>
                <div style="width:100%;height:100%;background:var(--accent);display:flex;align-items:center;justify-content:center"><i class="bi bi-box" style="color:white"></i></div>
              <?php endif; ?>
            </div>
            <div style="flex:1">
              <div style="font-weight:600;font-size:.9rem"><?= e($item['title']) ?></div>
              <div style="font-size:.78rem;color:var(--text-dim)">Downloads: <?= $item['download_count'] ?> / <?= $item['max_downloads'] ?></div>
            </div>
            <div style="font-weight:800;flex-shrink:0"><?= $sym ?><?= number_format((float)$item['price'], 2) ?></div>
            <?php if ($order['status'] === 'paid' && $item['download_count'] < $item['max_downloads']): ?>
            <a href="<?= generate_download_link($item['id']) ?>" class="btn btn-primary btn-sm" download><i class="bi bi-download"></i></a>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Summary / Invoice -->
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;max-width:400px">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1rem"><i class="bi bi-receipt" style="color:var(--accent)"></i> Payment Summary</h3>
        <div style="display:flex;flex-direction:column;gap:.5rem;font-size:.875rem;color:var(--text-muted)">
          <div style="display:flex;justify-content:space-between"><span>Subtotal</span><span><?= $sym ?><?= number_format((float)$order['subtotal'], 2) ?></span></div>
          <?php if ($order['discount'] > 0): ?>
          <div style="display:flex;justify-content:space-between;color:var(--success)"><span>Discount</span><span>−<?= $sym ?><?= number_format((float)$order['discount'], 2) ?></span></div>
          <?php endif; ?>
          <div style="display:flex;justify-content:space-between"><span>Tax</span><span><?= $sym ?><?= number_format((float)$order['tax'], 2) ?></span></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-weight:800;font-size:1.125rem;margin-top:.875rem;padding-top:.875rem;border-top:1px solid var(--border)">
          <span>Total</span><span><?= $sym ?><?= number_format((float)$order['total'], 2) ?></span>
        </div>
        <div style="margin-top:1rem;font-size:.8rem;color:var(--text-dim)">
          <div>Gateway: <strong><?= ucfirst($order['payment_gateway'] ?? '—') ?></strong></div>
          <?php if ($order['transaction_id']): ?>
          <div>Txn: <code style="font-size:.75rem"><?= e($order['transaction_id']) ?></code></div>
          <?php endif; ?>
        </div>

        <div style="margin-top:1.25rem;display:flex;gap:.75rem;flex-wrap:wrap">
          <a href="<?= url('dashboard/tickets/new?order=' . $order['id']) ?>" class="btn btn-secondary btn-sm"><i class="bi bi-chat-dots"></i> Need Help?</a>
        </div>
      </div>
    </div>
  </div>
</div>
