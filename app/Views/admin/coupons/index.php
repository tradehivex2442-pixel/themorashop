<?php
// THEMORA SHOP — Admin Coupons
$sym = setting('currency_symbol', '$');
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Coupons</div>
    <div class="page-header-sub">Create and manage discount codes</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start">
  <!-- Coupons Table -->
  <div class="table-card">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Uses</th>
            <th>Expires</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($coupons as $c): ?>
          <tr>
            <td>
              <code style="background:var(--bg-3);padding:.2rem .5rem;border-radius:6px;font-size:.85rem;font-weight:700;letter-spacing:.05em"><?= e($c['code']) ?></code>
            </td>
            <td style="font-size:.85rem;color:var(--text-muted)"><?= $c['type'] === 'percent' ? 'Percentage' : 'Fixed Amount' ?></td>
            <td style="font-weight:700"><?= $c['type'] === 'percent' ? $c['value'] . '%' : $sym . number_format((float)$c['value'], 2) ?></td>
            <td style="color:var(--text-muted)"><?= $c['used_count'] ?><?= $c['max_uses'] ? ' / ' . $c['max_uses'] : '' ?></td>
            <td style="font-size:.8rem;color:var(--text-dim)"><?= $c['expiry'] ? date('M j, Y', strtotime($c['expiry'])) : '—' ?></td>
            <td>
              <?php
              $expired = $c['expiry'] && strtotime($c['expiry']) < time();
              $maxed   = $c['max_uses'] && $c['used_count'] >= $c['max_uses'];
              if ($expired || $maxed): ?><span class="badge badge-danger">Expired</span>
              elseif (!$c['is_active']): ?><span class="badge badge-dark">Inactive</span>
              <?php else: ?><span class="badge badge-success">Active</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="action-btns">
                <button onclick="copyToClipboard('<?= e($c['code']) ?>',this)" class="action-btn" title="Copy code"><i class="bi bi-copy"></i></button>
                <form method="POST" action="<?= url('admin/coupons/' . $c['id'] . '/delete') ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="action-btn danger" data-confirm="Delete coupon <?= e($c['code']) ?>?"><i class="bi bi-trash3"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($coupons)): ?>
          <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-dim)">No coupons yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Create Coupon -->
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem">
    <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem"><i class="bi bi-tag" style="color:var(--accent)"></i> Create Coupon</h3>
    <form method="POST" action="<?= url('admin/coupons') ?>">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Coupon Code *</label>
        <div style="display:flex;gap:.5rem">
          <input type="text" name="code" id="coupon-code" class="form-control" placeholder="e.g. SUMMER30" required style="text-transform:uppercase">
          <button type="button" onclick="document.getElementById('coupon-code').value = Math.random().toString(36).substring(2,10).toUpperCase()" class="btn btn-secondary btn-sm" title="Generate">🎲</button>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Discount Type *</label>
        <select name="type" class="form-control">
          <option value="percent">Percentage (%)</option>
          <option value="flat">Fixed Amount</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Value *</label>
        <input type="number" name="value" class="form-control" step=".01" min="0" placeholder="e.g. 20" required>
      </div>
      <div class="form-group">
        <label class="form-label">Min Order Amount</label>
        <input type="number" name="min_order_amount" class="form-control" step=".01" min="0" placeholder="0 for none">
      </div>
      <div class="form-group">
        <label class="form-label">Max Uses (0 = unlimited)</label>
        <input type="number" name="max_uses" class="form-control" min="0" value="0">
      </div>
      <div class="form-group">
        <label class="form-label">Expires At</label>
        <input type="date" name="expires_at" class="form-control" min="<?= date('Y-m-d') ?>">
      </div>
      <div class="form-check" style="margin-bottom:1rem">
        <input type="checkbox" name="is_active" value="1" id="active-check" checked>
        <label for="active-check" style="font-size:.85rem">Active immediately</label>
      </div>
      <button type="submit" class="btn btn-primary btn-full"><i class="bi bi-plus-lg"></i> Create Coupon</button>
    </form>
  </div>
</div>
