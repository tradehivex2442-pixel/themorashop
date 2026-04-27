<?php
// THEMORA SHOP — User Dashboard
?>
<div class="container" style="padding-top:2.5rem;padding-bottom:5rem">
  <div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
      <div class="sidebar-user" style="color:white">
        <?php $u = auth(); ?>
        <?php if ($u['avatar']): ?>
          <img src="<?= e($u['avatar']) ?>" class="sidebar-avatar" alt="<?= e($u['name']) ?>">
        <?php else: ?>
          <div class="sidebar-avatar" style="background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;margin:0 auto .75rem"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
        <?php endif; ?>
        <div style="font-weight:700;font-size:.95rem"><?= e($u['name']) ?></div>
        <div style="font-size:.75rem;opacity:.8;margin-top:.25rem"><?= e($u['email']) ?></div>
      </div>
      <nav class="sidebar-nav">
        <a href="<?= url('dashboard') ?>" class="sidebar-nav-item active"><i class="bi bi-speedometer2"></i> Overview</a>
        <a href="<?= url('dashboard/orders') ?>" class="sidebar-nav-item"><i class="bi bi-bag"></i> My Orders</a>
        <a href="<?= url('dashboard/wishlist') ?>" class="sidebar-nav-item"><i class="bi bi-heart"></i> Wishlist <span class="badge badge-primary" style="margin-left:auto"><?= $wishlistCount ?></span></a>
        <a href="<?= url('dashboard/affiliate') ?>" class="sidebar-nav-item"><i class="bi bi-link-45deg"></i> Affiliate</a>
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item"><i class="bi bi-chat-dots"></i> Support</a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item"><i class="bi bi-person-gear"></i> Profile</a>
        <hr style="border:none;border-top:1px solid var(--border);margin:.75rem 0">
        <a href="<?= url('logout') ?>" class="sidebar-nav-item" style="color:var(--danger)"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </nav>
    </aside>

    <!-- Main -->
    <div class="dashboard-main">
      <!-- Affiliate Banner -->
      <?php if ($affiliate): ?>
      <div style="background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(168,85,247,.1));border:1px solid rgba(99,102,241,.3);border-radius:var(--radius);padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem">
        <div>
          <div style="font-weight:700;margin-bottom:.25rem"><i class="bi bi-link-45deg" style="color:var(--accent)"></i> Your Affiliate Earnings</div>
          <div style="font-size:.85rem;color:var(--text-muted)">Pending: <strong style="color:var(--warning)"><?= currency((float)$affiliate['pending_earnings']) ?></strong> · Paid: <strong style="color:var(--success)"><?= currency((float)$affiliate['paid_earnings']) ?></strong></div>
        </div>
        <a href="<?= url('dashboard/affiliate') ?>" class="btn btn-secondary btn-sm">View Dashboard <i class="bi bi-arrow-right"></i></a>
      </div>
      <?php endif; ?>

      <!-- Recent Orders -->
      <div class="table-card" style="margin-bottom:1.5rem">
        <div class="table-card-header">
          <span class="table-card-title">Recent Orders</span>
          <a href="<?= url('dashboard/orders') ?>" class="btn btn-ghost btn-sm">All Orders <i class="bi bi-arrow-right"></i></a>
        </div>
        <?php if (empty($orders)): ?>
        <div style="text-align:center;padding:3rem;color:var(--text-dim)">
          <i class="bi bi-bag" style="font-size:3rem;display:block;margin-bottom:.75rem"></i>
          No orders yet. <a href="<?= url('products') ?>" style="color:var(--accent-light)">Start shopping!</a>
        </div>
        <?php else: ?>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Order</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
              <tr>
                <td><a href="<?= url('dashboard/order/' . $o['id']) ?>" style="font-weight:600;color:var(--accent-light)">#<?= $o['id'] ?></a></td>
                <td style="font-weight:700"><?= currency((float)$o['total']) ?></td>
                <td><?php $sm = ['paid'=>'badge-success','pending'=>'badge-warning','failed'=>'badge-danger','refunded'=>'badge-dark']; ?><span class="badge <?= $sm[$o['status']] ?? 'badge-dark' ?>"><?= ucfirst($o['status']) ?></span></td>
                <td style="font-size:.8rem;color:var(--text-dim)"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                <td><a href="<?= url('dashboard/order/' . $o['id']) ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)">Details</a></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>

      <!-- Recent Tickets -->
      <?php if (!empty($tickets)): ?>
      <div class="table-card">
        <div class="table-card-header">
          <span class="table-card-title">Recent Support Tickets</span>
          <a href="<?= url('dashboard/tickets') ?>" class="btn btn-ghost btn-sm">All Tickets <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Subject</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach ($tickets as $t): ?>
              <tr>
                <td><a href="<?= url('dashboard/tickets/' . $t['id']) ?>" style="font-weight:500;color:var(--text)"><?= e($t['subject']) ?></a></td>
                <td><?php $ts = ['open'=>'badge-warning','in-progress'=>'badge-primary','resolved'=>'badge-success','closed'=>'badge-dark']; ?><span class="badge <?= $ts[$t['status']] ?? 'badge-dark' ?>"><?= ucfirst($t['status']) ?></span></td>
                <td style="font-size:.8rem;color:var(--text-dim)"><?= time_ago($t['created_at']) ?></td>
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
