<?php
// THEMORA SHOP — Admin Ticket Detail / Reply
?>
<div class="page-header">
  <div>
    <a href="<?= url('admin/tickets') ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border);margin-bottom:.5rem"><i class="bi bi-arrow-left"></i> Back</a>
    <div class="page-header-title">Ticket #<?= $ticket['id'] ?></div>
    <div class="page-header-sub"><?= e($ticket['subject']) ?></div>
  </div>
  <div class="page-header-actions">
    <!-- Quick Status Update -->
    <form method="POST" action="<?= url('admin/tickets/' . $ticket['id'] . '/assign') ?>" style="display:flex;gap:.5rem">
      <?= csrf_field() ?>
      <select name="status" class="form-control" style="height:36px;padding:.4rem .875rem;font-size:.85rem;width:150px">
        <?php foreach (['open','in-progress','resolved','closed'] as $s): ?>
        <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm"><i class="bi bi-check2"></i> Update</button>
    </form>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:1.5rem;align-items:start">
  <!-- Thread -->
  <div>
    <!-- Original Message -->
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.25rem">
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem">
        <div style="width:40px;height:40px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-weight:700"><?= strtoupper(substr($ticket['user_name'] ?? 'U', 0, 1)) ?></div>
        <div>
          <div style="font-weight:600;font-size:.9rem"><?= e($ticket['user_name'] ?? 'Guest') ?></div>
          <div style="font-size:.75rem;color:var(--text-dim)"><?= date('M j, Y H:i', strtotime($ticket['created_at'])) ?></div>
        </div>
        <span class="badge badge-dark" style="margin-left:auto"><?= e($ticket['category'] ?? 'General') ?></span>
      </div>
      <div style="font-size:.9rem;color:var(--text-muted);line-height:1.7;white-space:pre-wrap"><?= e($ticket['message']) ?></div>
      <?php if ($ticket['attachment_path']): ?>
      <div style="margin-top:.875rem">
        <a href="<?= e($ticket['attachment_path']) ?>" class="btn btn-secondary btn-sm" target="_blank"><i class="bi bi-paperclip"></i> View Attachment</a>
      </div>
      <?php endif; ?>
    </div>

    <!-- Replies -->
    <?php foreach ($replies as $reply): ?>
    <div style="background:<?= $reply['is_admin_reply'] ? 'rgba(99,102,241,.06)' : 'var(--surface)' ?>;border:1px solid <?= $reply['is_admin_reply'] ? 'rgba(99,102,241,.2)' : 'var(--border)' ?>;border-radius:var(--radius);padding:1.25rem;margin-bottom:1rem">
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.875rem">
        <div style="width:36px;height:36px;border-radius:50%;background:<?= $reply['is_admin_reply'] ? 'var(--accent)' : 'var(--bg-3)' ?>;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.85rem">
          <?= strtoupper(substr($reply['sender_name'] ?? 'A', 0, 1)) ?>
        </div>
        <div>
          <div style="font-weight:600;font-size:.875rem"><?= e($reply['sender_name'] ?? 'Support') ?> <?= $reply['is_admin_reply'] ? '<span class="badge badge-primary" style="font-size:.65rem;margin-left:.375rem">Staff</span>' : '' ?></div>
          <div style="font-size:.73rem;color:var(--text-dim)"><?= time_ago($reply['created_at']) ?></div>
        </div>
      </div>
      <div style="font-size:.875rem;color:var(--text-muted);line-height:1.7;white-space:pre-wrap"><?= e($reply['message']) ?></div>
    </div>
    <?php endforeach; ?>

    <!-- Reply Form -->
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem">
      <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1rem"><i class="bi bi-reply" style="color:var(--accent)"></i> Reply</h3>
      <form method="POST" action="<?= url('admin/tickets/' . $ticket['id'] . '/reply') ?>">
        <?= csrf_field() ?>
        <div class="form-group"><textarea name="message" class="form-control" rows="5" placeholder="Type your reply…" required></textarea></div>
        <div style="display:flex;gap:.75rem">
          <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Send Reply</button>
          <button type="submit" name="resolve" value="1" class="btn btn-success"><i class="bi bi-check-circle"></i> Reply & Resolve</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Info Sidebar -->
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem">
    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-dim);margin-bottom:1rem">Ticket Details</div>
    <?php $details = [
      ['Status',   ucfirst($ticket['status'])],
      ['Priority', ucfirst($ticket['priority'] ?? 'Medium')],
      ['Category', $ticket['category'] ?? 'General'],
      ['Created',  date('M j, Y', strtotime($ticket['created_at']))],
      ['Order',    $ticket['related_order_id'] ? '#' . $ticket['related_order_id'] : '—'],
    ]; ?>
    <?php foreach ($details as [$label, $val]): ?>
    <div style="display:flex;justify-content:space-between;font-size:.8rem;padding:.5rem 0;border-bottom:1px solid var(--border)">
      <span style="color:var(--text-dim)"><?= $label ?></span>
      <span style="font-weight:600"><?= e($val) ?></span>
    </div>
    <?php endforeach; ?>

    <div style="margin-top:1.25rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-dim);margin-bottom:.875rem">Customer</div>
    <div style="font-weight:600;font-size:.875rem"><?= e($ticket['user_name'] ?? 'Guest') ?></div>
    <div style="font-size:.78rem;color:var(--text-dim)"><?= e($ticket['user_email'] ?? '') ?></div>
    <?php if ($ticket['user_id']): ?>
    <a href="<?= url('admin/users/' . $ticket['user_id']) ?>" class="btn btn-ghost btn-sm btn-full" style="margin-top:.875rem;border:1px solid var(--border)"><i class="bi bi-person"></i> View Profile</a>
    <?php endif; ?>
  </div>
</div>
