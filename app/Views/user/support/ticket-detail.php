<?php // THEMORA SHOP — Ticket Detail (User side) ?>
<div class="container" style="padding-top:2.5rem;padding-bottom:5rem">
  <div class="dashboard-layout">
    <aside class="dashboard-sidebar">
      <div class="sidebar-user" style="color:white">
        <?php $u = auth(); ?>
        <div class="sidebar-avatar" style="background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;margin:0 auto .75rem"><?= strtoupper(substr($u['name'],0,1)) ?></div>
        <div style="font-weight:700;font-size:.95rem"><?= e($u['name']) ?></div>
      </div>
      <nav class="sidebar-nav">
        <a href="<?= url('dashboard') ?>" class="sidebar-nav-item"><i class="bi bi-speedometer2"></i> Overview</a>
        <a href="<?= url('dashboard/orders') ?>" class="sidebar-nav-item"><i class="bi bi-bag"></i> My Orders</a>
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item active"><i class="bi bi-chat-dots"></i> Support</a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item"><i class="bi bi-person-gear"></i> Profile</a>
      </nav>
    </aside>
    <div class="dashboard-main">
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem">
        <a href="<?= url('dashboard/tickets') ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)"><i class="bi bi-arrow-left"></i></a>
        <div>
          <h1 style="font-size:1.25rem;font-weight:800;line-height:1.2"><?= e($ticket['subject']) ?></h1>
          <?php $sm = ['open'=>'badge-warning','in-progress'=>'badge-primary','resolved'=>'badge-success','closed'=>'badge-dark']; ?>
          <span class="badge <?= $sm[$ticket['status']] ?? 'badge-dark' ?>"><?= ucfirst($ticket['status']) ?></span>
          <span style="font-size:.78rem;color:var(--text-dim);margin-left:.5rem">Opened <?= time_ago($ticket['created_at']) ?></span>
        </div>
      </div>

      <!-- Original Message -->
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1rem">
        <div style="font-size:.75rem;color:var(--text-dim);margin-bottom:.75rem">Original message</div>
        <div style="font-size:.9rem;color:var(--text-muted);line-height:1.7;white-space:pre-wrap"><?= e($ticket['message']) ?></div>
      </div>

      <!-- Replies -->
      <?php foreach ($replies as $r): ?>
      <div style="background:<?= $r['is_admin_reply'] ? 'rgba(99,102,241,.06)' : 'var(--surface)' ?>;border:1px solid <?= $r['is_admin_reply'] ? 'rgba(99,102,241,.25)' : 'var(--border)' ?>;border-radius:var(--radius);padding:1.25rem;margin-bottom:1rem">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem">
          <div style="width:36px;height:36px;border-radius:50%;background:<?= $r['is_admin_reply'] ? 'var(--accent)' : 'var(--bg-3)' ?>;display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:.85rem"><?= strtoupper(substr($r['sender_name'] ?? 'U',0,1)) ?></div>
          <div>
            <span style="font-weight:600;font-size:.875rem"><?= e($r['sender_name'] ?? 'You') ?></span>
            <?php if ($r['is_admin_reply']): ?><span class="badge badge-primary" style="font-size:.65rem;margin-left:.375rem">Support Staff</span><?php endif; ?>
            <span style="font-size:.73rem;color:var(--text-dim);display:block"><?= time_ago($r['created_at']) ?></span>
          </div>
        </div>
        <div style="font-size:.875rem;color:var(--text-muted);line-height:1.7;white-space:pre-wrap"><?= e($r['message']) ?></div>
      </div>
      <?php endforeach; ?>

      <!-- Reply Form -->
      <?php if (!in_array($ticket['status'], ['resolved','closed'])): ?>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-top:1.5rem">
        <form method="POST" action="<?= url('dashboard/tickets/' . $ticket['id'] . '/reply') ?>">
          <?= csrf_field() ?>
          <div class="form-group"><textarea name="message" class="form-control" rows="4" placeholder="Type your reply…" required></textarea></div>
          <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Send Reply</button>
        </form>
      </div>
      <?php else: ?>
      <div class="alert alert-info" style="margin-top:1rem"><i class="bi bi-check-circle-fill"></i> This ticket is <?= $ticket['status'] ?>. <a href="<?= url('dashboard/tickets/new') ?>" style="color:inherit;text-decoration:underline">Open a new ticket</a> if you need further help.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
