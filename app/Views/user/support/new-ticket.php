<?php
// THEMORA SHOP — New Ticket Form
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
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item active"><i class="bi bi-chat-dots"></i> Support</a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item"><i class="bi bi-person-gear"></i> Profile</a>
      </nav>
    </aside>

    <div class="dashboard-main">
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem">
        <a href="<?= url('dashboard/tickets') ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)"><i class="bi bi-arrow-left"></i></a>
        <h1 style="font-size:1.375rem;font-weight:800">Open a Support Ticket</h1>
      </div>

      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:2rem;max-width:640px">
        <form method="POST" action="<?= url('dashboard/tickets') ?>" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label">Subject *</label>
            <input type="text" name="subject" class="form-control" placeholder="Briefly describe your issue" required>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label">Category</label>
              <select name="category" class="form-control">
                <?php foreach (['General', 'Billing', 'Download Issue', 'Technical', 'Refund Request', 'Other'] as $cat): ?>
                <option><?= $cat ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Priority</label>
              <select name="priority" class="form-control">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
              </select>
            </div>
          </div>

          <?php if (!empty($orders)): ?>
          <div class="form-group">
            <label class="form-label">Related Order (optional)</label>
            <select name="related_order_id" class="form-control">
              <option value="">-- Select an order --</option>
              <?php foreach ($orders as $o): ?>
              <option value="<?= $o['id'] ?>" <?= (($_GET['order'] ?? '') == $o['id']) ? 'selected' : '' ?>>
                #<?= $o['id'] ?> — <?= currency((float)$o['total']) ?> (<?= date('M j, Y', strtotime($o['created_at'])) ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>

          <div class="form-group">
            <label class="form-label">Message *</label>
            <textarea name="message" class="form-control" rows="6" placeholder="Describe your issue in detail…" required></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Attachment (optional)</label>
            <input type="file" name="attachment" class="form-control" accept="image/*,.pdf,.zip">
            <div style="font-size:.73rem;color:var(--text-dim);margin-top:.375rem">Max 5 MB · JPG, PNG, PDF, ZIP</div>
          </div>

          <div style="display:flex;gap:.75rem">
            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Submit Ticket</button>
            <a href="<?= url('dashboard/tickets') ?>" class="btn btn-ghost" style="border:1px solid var(--border)">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
