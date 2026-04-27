<?php
// THEMORA SHOP — Admin Users list (corrected: is_blocked not is_banned)
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Users</div>
    <div class="page-header-sub"><?= number_format($pagination['total']) ?> registered users</div>
  </div>
  <div class="page-header-actions">
    <a href="<?= url('admin/analytics/export?type=users') ?>" class="btn btn-secondary btn-sm"><i class="bi bi-download"></i> Export</a>
  </div>
</div>

<div class="filter-bar">
  <form method="GET" action="<?= url('admin/users') ?>" style="display:flex;gap:.75rem;flex-wrap:wrap">
    <input type="text" name="q" class="form-control" placeholder="Name or email…" value="<?= e($filters['search'] ?? '') ?>">
    <select name="role" class="form-control">
      <option value="">All Roles</option>
      <?php foreach (['user'=>'User','editor'=>'Editor','support'=>'Support','super_admin'=>'Super Admin'] as $v => $l): ?>
      <option value="<?= $v ?>" <?= ($filters['role'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Search</button>
    <a href="<?= url('admin/users') ?>" class="btn btn-ghost btn-sm">Clear</a>
  </form>
</div>

<div class="table-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Role</th>
          <th>Orders</th>
          <th>Spent</th>
          <th>Joined</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:.75rem">
              <?php if ($u['avatar']): ?>
                <img src="<?= e($u['avatar']) ?>" class="user-avatar-sm" alt="">
              <?php else: ?>
                <div class="user-avatar-sm" style="background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.85rem"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
              <?php endif; ?>
              <div>
                <div style="font-weight:600;font-size:.875rem"><?= e($u['name']) ?></div>
                <div style="font-size:.73rem;color:var(--text-dim)"><?= e($u['email']) ?></div>
              </div>
            </div>
          </td>
          <td>
            <?php $rMap=['super_admin'=>'badge-danger','editor'=>'badge-primary','support'=>'badge-warning','user'=>'badge-dark']; ?>
            <span class="badge <?= $rMap[$u['role']] ?? 'badge-dark' ?>"><?= ucfirst($u['role']) ?></span>
          </td>
          <td style="color:var(--text-muted)"><?= $u['order_count'] ?? 0 ?></td>
          <td style="font-weight:600"><?= currency((float)($u['total_spent'] ?? 0)) ?></td>
          <td style="font-size:.8rem;color:var(--text-dim)"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
          <td>
            <?php if ($u['is_blocked']): ?>
              <span class="badge badge-danger">Blocked</span>
            <?php else: ?>
              <span class="badge badge-success">Active</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="action-btns">
              <a href="<?= url('admin/users/' . $u['id']) ?>" class="action-btn" title="View"><i class="bi bi-eye"></i></a>
              <a href="<?= url('admin/users/' . $u['id'] . '/impersonate') ?>" class="action-btn" title="Impersonate" data-confirm="Impersonate <?= e($u['name']) ?>?"><i class="bi bi-person-badge"></i></a>
              <?php if (!$u['is_blocked']): ?>
              <form method="POST" action="<?= url('admin/users/' . $u['id'] . '/block') ?>" style="display:inline">
                <?= csrf_field() ?>
                <button type="submit" class="action-btn danger" data-confirm="Block <?= e($u['name']) ?>?" title="Block"><i class="bi bi-slash-circle"></i></button>
              </form>
              <?php else: ?>
              <form method="POST" action="<?= url('admin/users/' . $u['id'] . '/block') ?>" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="unblock" value="1">
                <button type="submit" class="action-btn" title="Unblock"><i class="bi bi-check-circle"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
        <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-dim)">No users found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pagination['pages'] > 1): ?>
  <div style="padding:1rem;border-top:1px solid var(--border)">
    <div class="pagination" style="margin:0">
      <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['current']-1 ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
      <?php for ($i=max(1,$pagination['current']-2); $i<=min($pagination['pages'],$pagination['current']+2); $i++): ?>
        <a href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn <?= $i===$pagination['current']?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['current']+1 ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
