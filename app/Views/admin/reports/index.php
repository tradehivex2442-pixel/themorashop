<?php
// THEMORA SHOP — Admin Analytics / Reports Page
$sym = setting('currency_symbol', '$');
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Analytics & Reports</div>
    <div class="page-header-sub">Revenue insights and sales performance</div>
  </div>
  <div class="page-header-actions">
    <select onchange="window.location.href='?period='+this.value" class="form-control" style="height:36px;padding:.4rem .875rem;font-size:.85rem;width:auto">
      <?php foreach (['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days', '365' => 'Last year'] as $d => $l): ?>
      <option value="<?= $d ?>" <?= ($period ?? '30') == $d ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <a href="<?= url('admin/analytics/export?type=orders&period=' . ($period ?? '30')) ?>" class="btn btn-secondary btn-sm"><i class="bi bi-download"></i> Export CSV</a>
  </div>
</div>

<!-- Stat Summary Cards -->
<div class="admin-stats-grid" style="margin-bottom:1.75rem">
  <?php
  $cards = [
    [$sym . number_format((float)$summary['revenue'], 0),    'Revenue',         'bi-currency-dollar', '#6366f1'],
    [number_format($summary['orders']),                       'Orders',          'bi-receipt',         '#22c55e'],
    [$sym . number_format((float)$summary['avg_order'], 2),  'Avg Order Value', 'bi-bar-chart',       '#f59e0b'],
    [number_format($summary['new_users']),                    'New Users',       'bi-person-plus',     '#3b82f6'],
    [number_format($summary['refunds'] ?? 0),                'Refunds',         'bi-arrow-counterclockwise', '#ef4444'],
  ];
  foreach ($cards as [$val, $label, $icon, $color]):
  ?>
  <div class="stat-card">
    <div class="stat-icon" style="background:<?= $color ?>1a;color:<?= $color ?>"><i class="bi <?= $icon ?>"></i></div>
    <div>
      <div class="stat-num"><?= $val ?></div>
      <div class="stat-label"><?= $label ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Revenue Chart -->
<div class="chart-wrap" style="margin-bottom:1.75rem">
  <div class="chart-header">
    <div class="chart-title">Revenue Over Time</div>
  </div>
  <div style="height:320px"><canvas id="revenue-chart"></canvas></div>
</div>

<!-- Two Column Charts -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.75rem">
  <div class="chart-wrap">
    <div class="chart-header"><div class="chart-title">Products by Revenue</div></div>
    <div style="height:220px"><canvas id="top-products-chart"></canvas></div>
  </div>
  <div class="chart-wrap">
    <div class="chart-header"><div class="chart-title">Orders by Gateway</div></div>
    <div style="height:220px"><canvas id="gateway-chart"></canvas></div>
  </div>
</div>

<!-- Top Products Table -->
<div class="table-card">
  <div class="table-card-header">
    <span class="table-card-title">Top Products Performance</span>
    <a href="<?= url('admin/products') ?>" class="btn btn-ghost btn-sm">All Products <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>#</th><th>Product</th><th>Category</th><th>Sales</th><th>Revenue</th><th>Avg Rating</th></tr></thead>
      <tbody>
        <?php foreach ($topProducts as $i => $p): ?>
        <tr>
          <td style="font-weight:700;color:var(--text-dim)"><?= $i + 1 ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:.75rem">
              <?php if ($p['thumbnail']): ?><img src="<?= e($p['thumbnail']) ?>" class="product-thumb-sm"><?php endif; ?>
              <div style="font-weight:500;font-size:.875rem"><?= e($p['title']) ?></div>
            </div>
          </td>
          <td style="color:var(--text-muted)"><?= e($p['category_name'] ?? '—') ?></td>
          <td><?= number_format($p['sales']) ?></td>
          <td style="font-weight:700"><?= $sym ?><?= number_format((float)$p['revenue'], 0) ?></td>
          <td style="color:#f59e0b"><?= $p['avg_rating'] > 0 ? '★ ' . number_format($p['avg_rating'], 1) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const chartData = <?= json_encode($chartData) ?>;
  initRevenueChart(chartData.map(r=>r.date), chartData.map(r=>parseFloat(r.revenue||0)), chartData.map(r=>parseInt(r.orders||0)));

  const tp = <?= json_encode($topProducts) ?>;
  initBarChart('top-products-chart', tp.map(p=>truncate(p.title,18)), tp.map(p=>parseFloat(p.revenue||0)), 'Revenue', '#6366f1');

  const gw = <?= json_encode($gatewayBreakdown) ?>;
  new Chart(document.getElementById('gateway-chart'), {
    type: 'doughnut',
    data: { labels: gw.map(g => g.gateway), datasets: [{ data: gw.map(g => g.count), backgroundColor: ['#6366f1','#22c55e','#f59e0b','#3b82f6'], borderWidth: 0, hoverOffset: 6 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { color: '#94a3b8', font: { size: 12 } } }, tooltip: { backgroundColor: '#1f2937', titleColor: '#f1f5f9', bodyColor: '#94a3b8' } } }
  });
});

function truncate(str, len) { return str.length > len ? str.substr(0, len) + '…' : str; }
</script>
