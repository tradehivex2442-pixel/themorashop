<?php
// THEMORA SHOP — Order Success / Confirmation Page
$sym = setting('currency_symbol', '$');
?>
<div class="container" style="padding-top:4rem;padding-bottom:6rem;max-width:680px">
  <!-- Success Header -->
  <div style="text-align:center;margin-bottom:3rem">
    <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--success),#16a34a);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2.25rem;color:white;box-shadow:0 12px 40px rgba(34,197,94,.4);animation:pulse 2s infinite">
      <i class="bi bi-check-lg"></i>
    </div>
    <h1 style="font-size:2rem;margin-bottom:.75rem">Order Confirmed!</h1>
    <p style="color:var(--text-muted);font-size:1rem;line-height:1.7">
      Your payment was successful. Your download links are ready below.<br>
      A confirmation email has been sent to <strong><?= e($order['guest_email'] ?? auth()['email'] ?? '') ?></strong>
    </p>
  </div>

  <!-- Order Summary Card -->
  <div class="card" style="margin-bottom:1.5rem">
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;background:var(--bg-2)">
      <div>
        <span style="font-size:.8rem;color:var(--text-dim)">Order</span>
        <span style="font-weight:800;font-size:1rem;margin-left:.5rem">#<?= $order['id'] ?></span>
      </div>
      <span class="badge badge-success"><i class="bi bi-check-circle"></i> Paid</span>
    </div>
    <div class="card-body">
      <?php foreach ($items as $item): ?>
      <div class="order-item" style="border-bottom:1px solid var(--border);padding-bottom:1.25rem;margin-bottom:1.25rem">
        <?php if ($item['thumbnail']): ?>
          <img src="<?= asset($item['thumbnail']) ?>" class="order-item-thumb" alt="<?= e($item['title']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=P&background=6366f1&color=fff'">
        <?php else: ?>
          <div class="order-item-thumb" style="background:linear-gradient(135deg,var(--accent-dark),#a855f7);display:flex;align-items:center;justify-content:center"><i class="bi bi-box" style="color:rgba(255,255,255,.8)"></i></div>
        <?php endif; ?>
        <div style="flex:1">
          <div style="font-weight:600;margin-bottom:.25rem"><?= e($item['title']) ?></div>
          <div style="font-size:.8rem;color:var(--text-dim)">
            Downloads remaining: <?= $item['max_downloads'] - $item['download_count'] ?> / <?= $item['max_downloads'] ?>
          </div>
        </div>
        <a href="<?= $item['download_url'] ?>" class="btn btn-primary btn-sm">
          <i class="bi bi-download"></i> Download
        </a>
      </div>
      <?php endforeach; ?>

      <!-- Totals -->
      <div style="border-top:1px solid var(--border);padding-top:1rem">
        <div style="display:flex;justify-content:space-between;font-size:.875rem;color:var(--text-muted);margin-bottom:.375rem">
          <span>Subtotal</span><span><?= $sym ?><?= number_format((float)$order['subtotal'], 2) ?></span>
        </div>
        <?php if ($order['discount'] > 0): ?>
        <div style="display:flex;justify-content:space-between;font-size:.875rem;color:var(--success);margin-bottom:.375rem">
          <span>Discount</span><span>−<?= $sym ?><?= number_format((float)$order['discount'], 2) ?></span>
        </div>
        <?php endif; ?>
        <div style="display:flex;justify-content:space-between;font-size:.875rem;color:var(--text-muted);margin-bottom:.875rem">
          <span>Tax</span><span><?= $sym ?><?= number_format((float)$order['tax'], 2) ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;font-weight:800;font-size:1.125rem">
          <span>Total Paid</span><span><?= $sym ?><?= number_format((float)$order['total'], 2) ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
    <a href="<?= url('dashboard/orders') ?>" class="btn btn-secondary">
      <i class="bi bi-bag"></i> View All Orders
    </a>
    <a href="<?= url('products') ?>" class="btn btn-primary">
      <i class="bi bi-grid-fill"></i> Continue Shopping
    </a>
  </div>

  <!-- Tips -->
  <div style="margin-top:2.5rem;padding:1.5rem;background:rgba(99,102,241,.06);border:1px solid rgba(99,102,241,.15);border-radius:var(--radius);font-size:.875rem;color:var(--text-muted)">
    <div style="font-weight:700;color:var(--text);margin-bottom:.75rem"><i class="bi bi-lightbulb" style="color:var(--warning)"></i> Download Tips</div>
    <ul style="list-style:none;display:flex;flex-direction:column;gap:.5rem">
      <li><i class="bi bi-check" style="color:var(--success)"></i> Links expire in <?= setting('download_expiry_hours', '48') ?> hours — download soon!</li>
      <li><i class="bi bi-check" style="color:var(--success)"></i> Links can be used <?= setting('default_download_limit', '5') ?> times each.</li>
      <li><i class="bi bi-check" style="color:var(--success)"></i> You can always re-access from your <a href="<?= url('dashboard/orders') ?>" style="color:var(--accent-light)">Orders Dashboard</a>.</li>
    </ul>
  </div>
</div>
