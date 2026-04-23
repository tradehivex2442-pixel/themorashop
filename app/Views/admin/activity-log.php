<?php
// THEMORA SHOP — Activity Log View (corrected for activity_logs schema)
// Schema: id, admin_id, action, target_type (used as description), target_id, meta, ip, created_at
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Activity Log</div>
    <div class="page-header-sub">Admin and system actions in chronological order</div>
  </div>
  <div class="page-header-actions">
    <a href="<?= url('admin/analytics/export?type=activity') ?>" class="btn btn-secondary btn-sm"><i class="bi bi-download"></i> Export</a>
  </div>
</div>

<div class="filter-bar">
  <form method="GET" action="<?= url('admin/activity-log') ?>" style="display:flex;gap:.75rem;flex-wrap:wrap">
    <input type="text" name="q" class="form-control" placeholder="Search action…" value="<?= e($_GET['q'] ?? '') ?>">
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filter</button>
    <a href="<?= url('admin/activity-log') ?>" class="btn btn-ghost btn-sm">Clear</a>
  </form>
</div>

<div class="table-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Admin</th>
          <th>Action</th>
          <th>Details</th>
          <th>IP</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
          <td>
            <div style="font-size:.85rem;font-weight:500"><?= e($log['admin_name'] ?? 'System') ?></div>
          </td>
          <td>
            <span style="background:rgba(99,102,241,.1);color:#818cf8;padding:.2rem .625rem;border-radius:99px;font-size:.73rem;font-weight:700;font-family:monospace;white-space:nowrap">
              <?= e($log['action']) ?>
            </span>
          </td>
          <td style="font-size:.8rem;color:var(--text-muted);max-width:260px"><?= e(truncate($log['target_type'] ?? '', 80)) ?></td>
          <td><code style="font-size:.75rem;color:var(--text-dim)"><?= e($log['ip'] ?? '') ?></code></td>
          <td style="font-size:.78rem;color:var(--text-dim);white-space:nowrap"><?= date('M j, H:i', strtotime($log['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
        <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-dim)">No activity logs found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if (($pagination['pages'] ?? 1) > 1): ?>
  <div style="padding:1rem;border-top:1px solid var(--border)">
    <div class="pagination" style="margin:0">
      <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
      <a href="?page=<?= $i ?>&q=<?= urlencode($_GET['q'] ?? '') ?>" class="page-btn <?= $i === ($pagination['current'] ?? 1) ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
