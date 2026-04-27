<?php
// THEMORA SHOP — Support Tickets List + New Ticket Button
?>
<div class="container" style="padding-top:2.5rem;padding-bottom:5rem">
  <div class="dashboard-layout">
    <aside class="dashboard-sidebar">
      <div class="sidebar-user" style="color:white">
        <?php $u = auth(); ?>
        <div class="sidebar-avatar" style="background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;margin:0 auto .75rem"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
        <div style="font-weight:700;font-size:.95rem"><?= e($u['name']) ?></div>
      </div>
      <nav class="sidebar-nav">
        <a href="<?= url('dashboard') ?>" class="sidebar-nav-item"><i class="bi bi-speedometer2"></i> Overview</a>
        <a href="<?= url('dashboard/orders') ?>" class="sidebar-nav-item"><i class="bi bi-bag"></i> My Orders</a>
        <a href="<?= url('dashboard/wishlist') ?>" class="sidebar-nav-item"><i class="bi bi-heart"></i> Wishlist</a>
        <a href="<?= url('dashboard/affiliate') ?>" class="sidebar-nav-item"><i class="bi bi-link-45deg"></i> Affiliate</a>
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item active"><i class="bi bi-chat-dots"></i> Support</a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item"><i class="bi bi-person-gear"></i> Profile</a>
      </nav>
    </aside>

    <div class="dashboard-main">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem">
        <h1 style="font-size:1.375rem;font-weight:800">Support Tickets</h1>
        <a href="<?= url('dashboard/tickets/new') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> New Ticket</a>
      </div>

      <?php if (empty($tickets)): ?>
      <div style="text-align:center;padding:4rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)">
        <div style="font-size:3.5rem;margin-bottom:1rem">💬</div>
        <h3 style="margin-bottom:.5rem">No tickets yet</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem">Need help? Open a support ticket and we'll get back to you.</p>
        <a href="<?= url('dashboard/tickets/new') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Open a Ticket</a>
      </div>
      <?php else: ?>
      <div class="table-card">
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>#</th><th>Subject</th><th>Category</th><th>Status</th><th>Replies</th><th>Last Update</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($tickets as $t): ?>
              <tr>
                <td style="font-weight:600;color:var(--text-dim)">#<?= $t['id'] ?></td>
                <td>
                  <a href="<?= url('dashboard/tickets/' . $t['id']) ?>" style="font-weight:600;color:var(--text)"><?= e($t['subject']) ?></a>
                </td>
                <td><span class="badge badge-dark"><?= e($t['category'] ?? 'General') ?></span></td>
                <td>
                  <?php $sm = ['open'=>'badge-warning','in-progress'=>'badge-primary','resolved'=>'badge-success','closed'=>'badge-dark']; ?>
                  <span class="badge <?= $sm[$t['status']] ?? 'badge-dark' ?>"><?= ucfirst($t['status']) ?></span>
                </td>
                <td style="color:var(--text-muted)"><?= $t['reply_count'] ?></td>
                <td style="font-size:.8rem;color:var(--text-dim)"><?= time_ago($t['updated_at'] ?? $t['created_at']) ?></td>
                <td><a href="<?= url('dashboard/tickets/' . $t['id']) ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)">View</a></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
