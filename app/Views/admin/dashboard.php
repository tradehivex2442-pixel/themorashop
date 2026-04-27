<?php
// THEMORA SHOP — Admin Dashboard View
$sym = setting('currency_symbol', '$');
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Dashboard Overview</div>
    <div class="page-header-sub">Welcome back, <?= e(auth()['name']) ?>! Here's what's happening today.</div>
  </div>
  <div class="page-header-actions">
    <a href="<?= url('admin/analytics/export?type=orders') ?>" class="btn btn-secondary btn-sm"><i class="bi bi-download"></i> Export</a>
    <a href="<?= url('admin/products/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Product</a>
  </div>
</div>

<!-- Stats Grid -->
<div class="admin-stats-grid">
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(99,102,241,.12);color:var(--accent)"><i class="bi bi-currency-dollar"></i></div>
    <div>
      <div class="stat-num"><?= $sym ?><?= number_format((float)$revenue, 0) ?></div>
      <div class="stat-label">Total Revenue</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(34,197,94,.12);color:var(--success)"><i class="bi bi-receipt"></i></div>
    <div>
      <div class="stat-num"><?= number_format($orders) ?></div>
      <div class="stat-label">Total Orders</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(59,130,246,.12);color:var(--info)"><i class="bi bi-people"></i></div>
    <div>
      <div class="stat-num"><?= number_format($users) ?></div>
      <div class="stat-label">Registered Users</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(245,158,11,.12);color:var(--warning)"><i class="bi bi-box-seam"></i></div>
    <div>
      <div class="stat-num"><?= number_format($products) ?></div>
      <div class="stat-label">Active Products</div>
    </div>
  </div>
  <?php if ($pendingTickets): ?>
  <div class="stat-card" style="border-color:rgba(239,68,68,.3)">
    <div class="stat-icon" style="background:rgba(239,68,68,.12);color:var(--danger)"><i class="bi bi-chat-dots"></i></div>
    <div>
      <div class="stat-num"><?= $pendingTickets ?></div>
      <div class="stat-label">Open Tickets</div>
      <a href="<?= url('admin/tickets') ?>" style="font-size:.75rem;color:var(--danger)">View all →</a>
    </div>
  </div>
  <?php endif; ?>
  <?php if ($pendingPayouts): ?>
  <div class="stat-card" style="border-color:rgba(245,158,11,.3)">
    <div class="stat-icon" style="background:rgba(245,158,11,.12);color:var(--warning)"><i class="bi bi-wallet2"></i></div>
    <div>
      <div class="stat-num"><?= $pendingPayouts ?></div>
      <div class="stat-label">Pending Payouts</div>
      <a href="<?= url('admin/affiliates') ?>" style="font-size:.75rem;color:var(--warning)">Review →</a>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Revenue Chart -->
<div class="chart-wrap" style="margin-bottom:1.75rem">
  <div class="chart-header">
    <div class="chart-title">Revenue — Last 30 Days</div>
    <div style="display:flex;gap:.5rem">
      <span class="badge badge-primary"><i class="bi bi-circle-fill" style="color:#6366f1;font-size:.5rem"></i> Revenue</span>
      <span class="badge badge-success"><i class="bi bi-circle-fill" style="color:#22c55e;font-size:.5rem"></i> Orders</span>
    </div>
  </div>
  <div style="height:300px">
    <canvas id="revenue-chart"></canvas>
  </div>
</div>

<!-- Bottom Grid: Recent Orders + Top Products -->
<div style="display:grid;grid-template-columns:1fr 380px;gap:1.5rem">
  <!-- Recent Orders -->
  <div class="table-card">
    <div class="table-card-header">
      <span class="table-card-title">Recent Orders</span>
      <a href="<?= url('admin/orders') ?>" class="btn btn-ghost btn-sm">View All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>#ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Gateway</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><a href="<?= url('admin/orders/' . $o['id']) ?>" style="font-weight:600;color:var(--accent-light)">#<?= $o['id'] ?></a></td>
            <td>
              <div style="font-weight:500;font-size:.875rem"><?= e($o['user_name'] ?? 'Guest') ?></div>
              <div style="font-size:.75rem;color:var(--text-dim)"><?= e($o['user_email'] ?? $o['guest_email']) ?></div>
            </td>
            <td style="font-weight:700"><?= $sym ?><?= number_format($o['total'], 2) ?></td>
            <td><span class="badge badge-dark"><?= ucfirst($o['payment_gateway']) ?></span></td>
            <td>
              <?php $statusMap = ['paid'=>'badge-success','pending'=>'badge-warning','failed'=>'badge-danger','refunded'=>'badge-dark']; ?>
              <span class="badge <?= $statusMap[$o['status']] ?? 'badge-dark' ?>"><?= ucfirst($o['status']) ?></span>
            </td>
            <td style="font-size:.8rem;color:var(--text-dim)"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Top Products -->
  <div class="table-card">
    <div class="table-card-header">
      <span class="table-card-title">Top Products</span>
      <a href="<?= url('admin/products') ?>" class="btn btn-ghost btn-sm">All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div style="padding:1rem">
      <?php foreach ($topProducts as $i => $p): ?>
      <div style="display:flex;align-items:center;gap:.875rem;padding:.75rem;border-bottom:1px solid var(--border)">
        <span style="font-size:.75rem;font-weight:700;color:var(--text-dim);width:20px;text-align:center"><?= $i + 1 ?></span>
        <?php if ($p['thumbnail']): ?>
          <img src="<?= e($p['thumbnail']) ?>" class="product-thumb-sm" alt="">
        <?php else: ?>
          <div class="product-thumb-sm" style="background:var(--bg-3);display:flex;align-items:center;justify-content:center"><i class="bi bi-box" style="color:var(--text-dim)"></i></div>
        <?php endif; ?>
        <div style="flex:1;min-width:0">
          <div style="font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($p['title']) ?></div>
          <div style="font-size:.75rem;color:var(--text-dim)"><?= $p['sales'] ?> sales · <?= $sym ?><?= number_format($p['revenue'], 0) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const chartData = <?= json_encode($revenueChart) ?>;
  initRevenueChart(
    chartData.map(r => r.date),
    chartData.map(r => parseFloat(r.revenue || 0)),
    chartData.map(r => parseInt(r.orders || 0))
  );
});
</script>
