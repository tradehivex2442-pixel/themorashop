<?php
// THEMORA SHOP — Admin Orders List
$sym = setting('currency_symbol', '$');
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Orders</div>
    <div class="page-header-sub"><?= number_format($pagination['total']) ?> total orders</div>
  </div>
  <div class="page-header-actions">
    <a href="<?= url('admin/analytics/export?type=orders') ?>" class="btn btn-secondary btn-sm"><i class="bi bi-download"></i> Export CSV</a>
  </div>
</div>

<div class="filter-bar">
  <form method="GET" action="<?= url('admin/orders') ?>" style="display:flex;gap:.75rem;flex-wrap:wrap">
    <input type="text" name="q" class="form-control" placeholder="Order ID, email…" value="<?= e($filters['search'] ?? '') ?>">
    <select name="status" class="form-control">
      <option value="">All Status</option>
      <?php foreach (['paid'=>'Paid','pending'=>'Pending','failed'=>'Failed','refunded'=>'Refunded'] as $v => $l): ?>
      <option value="<?= $v ?>" <?= ($filters['status'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <select name="gateway" class="form-control">
      <option value="">All Gateways</option>
      <?php foreach (['razorpay'=>'Razorpay','stripe'=>'Stripe','paypal'=>'PayPal'] as $v => $l): ?>
      <option value="<?= $v ?>" <?= ($filters['gateway'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <input type="date" name="from" class="form-control" value="<?= $filters['from'] ?? '' ?>" style="max-width:150px">
    <input type="date" name="to" class="form-control" value="<?= $filters['to'] ?? '' ?>" style="max-width:150px">
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filter</button>
    <a href="<?= url('admin/orders') ?>" class="btn btn-ghost btn-sm">Clear</a>
  </form>
</div>

<div class="table-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th><input type="checkbox" id="select-all"></th>
          <th>Order #</th>
          <th>Customer</th>
          <th>Items</th>
          <th>Total</th>
          <th>Gateway</th>
          <th>Status</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><input type="checkbox" class="row-checkbox" value="<?= $o['id'] ?>"></td>
          <td><a href="<?= url('admin/orders/' . $o['id']) ?>" style="font-weight:700;color:var(--accent-light)">#<?= $o['id'] ?></a></td>
          <td>
            <div style="font-weight:500;font-size:.875rem"><?= e($o['user_name'] ?? 'Guest') ?></div>
            <div style="font-size:.73rem;color:var(--text-dim)"><?= e($o['user_email'] ?? $o['guest_email'] ?? '') ?></div>
          </td>
          <td style="color:var(--text-muted);font-size:.85rem"><?= $o['item_count'] ?> item<?= $o['item_count'] !== 1 ? 's' : '' ?></td>
          <td style="font-weight:700"><?= $sym ?><?= number_format((float)$o['total'], 2) ?></td>
          <td><span class="badge badge-dark"><?= ucfirst($o['payment_gateway'] ?? '—') ?></span></td>
          <td>
            <?php $sm = ['paid'=>'badge-success','pending'=>'badge-warning','failed'=>'badge-danger','refunded'=>'badge-dark']; ?>
            <span class="badge <?= $sm[$o['status']] ?? 'badge-dark' ?>"><?= ucfirst($o['status']) ?></span>
          </td>
          <td style="font-size:.8rem;color:var(--text-dim)"><?= date('M j, Y H:i', strtotime($o['created_at'])) ?></td>
          <td>
            <div class="action-btns">
              <a href="<?= url('admin/orders/' . $o['id']) ?>" class="action-btn" title="View"><i class="bi bi-eye"></i></a>
              <?php if ($o['status'] === 'paid'): ?>
              <form method="POST" action="<?= url('admin/orders/' . $o['id'] . '/refund') ?>" style="display:inline">
                <?= csrf_field() ?>
                <button type="submit" class="action-btn danger" data-confirm="Refund order #<?= $o['id'] ?>?" title="Refund"><i class="bi bi-arrow-counterclockwise"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
        <tr><td colspan="9" style="text-align:center;padding:3rem;color:var(--text-dim)">No orders found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pagination['pages'] > 1): ?>
  <div style="padding:1rem;border-top:1px solid var(--border)">
    <div class="pagination" style="margin:0">
      <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['current'] - 1 ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
      <?php for ($i = max(1, $pagination['current'] - 2); $i <= min($pagination['pages'], $pagination['current'] + 2); $i++): ?>
        <a href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn <?= $i === $pagination['current'] ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['current'] + 1 ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
