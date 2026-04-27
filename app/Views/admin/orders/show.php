<?php
// THEMORA SHOP — Admin Order Detail
$sym = setting('currency_symbol', '$');
?>
<div class="page-header">
  <div>
    <a href="<?= url('admin/orders') ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border);margin-bottom:.5rem"><i class="bi bi-arrow-left"></i> Orders</a>
    <div class="page-header-title">Order #<?= $order['id'] ?></div>
    <div class="page-header-sub"><?= date('F j, Y · H:i', strtotime($order['created_at'])) ?></div>
  </div>
  <div class="page-header-actions">
    <?php $sm=['paid'=>'badge-success','pending'=>'badge-warning','failed'=>'badge-danger','refunded'=>'badge-dark','disputed'=>'badge-danger']; ?>
    <span class="badge <?= $sm[$order['status']] ?? 'badge-dark' ?>" style="font-size:.875rem;padding:.5rem 1rem"><?= ucfirst($order['status']) ?></span>
    <?php if ($order['status'] === 'paid'): ?>
    <form method="POST" action="<?= url('admin/orders/' . $order['id'] . '/refund') ?>">
      <?= csrf_field() ?>
      <button class="btn btn-danger btn-sm" data-confirm="Refund this order?"><i class="bi bi-arrow-counterclockwise"></i> Refund</button>
    </form>
    <?php endif; ?>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start">
  <!-- Left: Items + Payment -->
  <div>
    <!-- Order Items -->
    <div class="table-card" style="margin-bottom:1.25rem">
      <div class="table-card-header"><span class="table-card-title">Items (<?= count($items) ?>)</span></div>
      <div style="padding:1rem;display:flex;flex-direction:column;gap:.875rem">
        <?php foreach ($items as $item): ?>
        <div style="display:flex;align-items:center;gap:1rem;padding-bottom:.875rem;border-bottom:1px solid var(--border)">
          <div style="width:52px;height:52px;border-radius:8px;background:var(--bg-3);overflow:hidden;flex-shrink:0">
            <?php if ($item['thumbnail']): ?>
              <img src="<?= e($item['thumbnail']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
            <?php else: ?>
              <div style="width:100%;height:100%;background:var(--accent);display:flex;align-items:center;justify-content:center"><i class="bi bi-box" style="color:white"></i></div>
            <?php endif; ?>
          </div>
          <div style="flex:1">
            <a href="<?= url('admin/products/' . $item['product_id'] . '/edit') ?>" style="font-weight:600;font-size:.875rem;color:var(--text)"><?= e($item['title']) ?></a>
            <div style="font-size:.73rem;color:var(--text-dim)">Downloads: <?= $item['download_count'] ?> / <?= $item['max_downloads'] ?></div>
          </div>
          <div style="font-weight:800"><?= $sym ?><?= number_format((float)$item['price'], 2) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div style="padding:1rem;border-top:1px solid var(--border)">
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
      </div>
    </div>

    <!-- Payment Info -->
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem">
      <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-dim);margin-bottom:.875rem">Payment Information</div>
      <?php $info = [
        ['Gateway',   ucfirst($order['payment_gateway'] ?? '—')],
        ['Txn ID',    $order['transaction_id'] ?? '—'],
        ['Status',    ucfirst($order['status'])],
        ['Coupon',    $order['coupon_code'] ? '#' . $order['coupon_code'] : '—'],
      ]; ?>
      <?php foreach ($info as [$label, $val]): ?>
      <div style="display:flex;justify-content:space-between;font-size:.8rem;padding:.4rem 0;border-bottom:1px solid var(--border)">
        <span style="color:var(--text-dim)"><?= $label ?></span>
        <span style="font-weight:600"><?= e($val) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Right: Customer -->
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem">
    <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-dim);margin-bottom:.875rem">Customer</div>
    <?php if ($order['user_id']): ?>
    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem">
      <div style="width:44px;height:44px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-weight:700"><?= strtoupper(substr($order['user_name'] ?? 'U', 0, 1)) ?></div>
      <div>
        <div style="font-weight:600;font-size:.875rem"><?= e($order['user_name']) ?></div>
        <div style="font-size:.73rem;color:var(--text-dim)"><?= e($order['user_email']) ?></div>
      </div>
    </div>
    <a href="<?= url('admin/users/' . $order['user_id']) ?>" class="btn btn-secondary btn-sm btn-full"><i class="bi bi-person"></i> View Profile</a>
    <?php else: ?>
    <div style="font-weight:600;font-size:.875rem"><?= e($order['guest_name'] ?? 'Guest') ?></div>
    <div style="font-size:.73rem;color:var(--text-dim)"><?= e($order['guest_email'] ?? '') ?></div>
    <div class="badge badge-dark" style="margin-top:.5rem">Guest checkout</div>
    <?php endif; ?>

    <?php if ($order['notes']): ?>
    <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--border)">
      <div style="font-size:.75rem;font-weight:700;color:var(--text-dim);margin-bottom:.5rem">Notes</div>
      <p style="font-size:.8rem;color:var(--text-muted)"><?= e($order['notes']) ?></p>
    </div>
    <?php endif; ?>
  </div>
</div>
