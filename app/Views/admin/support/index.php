<?php
// THEMORA SHOP — Admin Support Tickets List
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Support Tickets</div>
    <div class="page-header-sub"><?= $openCount ?> open · <?= $totalCount ?> total</div>
  </div>
</div>

<div class="filter-bar">
  <form method="GET" action="<?= url('admin/tickets') ?>" style="display:flex;gap:.75rem;flex-wrap:wrap">
    <input type="text" name="q" class="form-control" placeholder="Search subject…" value="<?= e($filters['search'] ?? '') ?>">
    <select name="status" class="form-control">
      <option value="">All Status</option>
      <?php foreach (['open'=>'Open','in-progress'=>'In Progress','resolved'=>'Resolved','closed'=>'Closed'] as $v => $l): ?>
      <option value="<?= $v ?>" <?= ($filters['status'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <select name="priority" class="form-control">
      <option value="">All Priority</option>
      <?php foreach (['low'=>'Low','medium'=>'Medium','high'=>'High'] as $v => $l): ?>
      <option value="<?= $v ?>" <?= ($filters['priority'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filter</button>
    <a href="<?= url('admin/tickets') ?>" class="btn btn-ghost btn-sm">Clear</a>
  </form>
</div>

<div class="table-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Subject</th>
          <th>Category</th>
          <th>Priority</th>
          <th>Status</th>
          <th>Replies</th>
          <th>Updated</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tickets as $t): ?>
        <tr>
          <td style="color:var(--text-dim);font-weight:600">#<?= $t['id'] ?></td>
          <td>
            <div style="font-size:.85rem;font-weight:500"><?= e($t['user_name'] ?? 'Guest') ?></div>
            <div style="font-size:.73rem;color:var(--text-dim)"><?= e($t['user_email'] ?? '') ?></div>
          </td>
          <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500"><?= e($t['subject']) ?></td>
          <td><span class="badge badge-dark"><?= e($t['category'] ?? 'General') ?></span></td>
          <td>
            <?php $pm = ['low'=>'badge-dark','medium'=>'badge-warning','high'=>'badge-danger']; ?>
            <span class="badge <?= $pm[$t['priority'] ?? 'low'] ?>"><?= ucfirst($t['priority'] ?? 'Low') ?></span>
          </td>
          <td>
            <?php $sm = ['open'=>'badge-warning','in-progress'=>'badge-primary','resolved'=>'badge-success','closed'=>'badge-dark']; ?>
            <span class="badge <?= $sm[$t['status']] ?? 'badge-dark' ?>"><?= ucfirst($t['status']) ?></span>
          </td>
          <td style="color:var(--text-muted)"><?= $t['reply_count'] ?></td>
          <td style="font-size:.8rem;color:var(--text-dim)"><?= time_ago($t['updated_at'] ?? $t['created_at']) ?></td>
          <td><a href="<?= url('admin/tickets/' . $t['id']) ?>" class="btn btn-secondary btn-sm">Reply</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($tickets)): ?>
        <tr><td colspan="9" style="text-align:center;padding:3rem;color:var(--text-dim)">🎉 No open tickets!</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
