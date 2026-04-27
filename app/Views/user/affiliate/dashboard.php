<?php
// THEMORA SHOP — Affiliate Dashboard
$sym = setting('currency_symbol', '$');
?>
<div class="container" style="padding-top:2.5rem;padding-bottom:5rem">
  <div class="dashboard-layout">
    <aside class="dashboard-sidebar">
      <div class="sidebar-user" style="color:white">
        <?php $u = auth(); ?>
        <div class="sidebar-avatar" style="background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;margin:0 auto .75rem"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
        <div style="font-weight:700;font-size:.95rem"><?= e($u['name']) ?></div>
        <div style="font-size:.75rem;opacity:.8"><?= e($u['email']) ?></div>
      </div>
      <nav class="sidebar-nav">
        <a href="<?= url('dashboard') ?>" class="sidebar-nav-item"><i class="bi bi-speedometer2"></i> Overview</a>
        <a href="<?= url('dashboard/orders') ?>" class="sidebar-nav-item"><i class="bi bi-bag"></i> My Orders</a>
        <a href="<?= url('dashboard/wishlist') ?>" class="sidebar-nav-item"><i class="bi bi-heart"></i> Wishlist</a>
        <a href="<?= url('dashboard/affiliate') ?>" class="sidebar-nav-item active"><i class="bi bi-link-45deg"></i> Affiliate</a>
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item"><i class="bi bi-chat-dots"></i> Support</a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item"><i class="bi bi-person-gear"></i> Profile</a>
      </nav>
    </aside>

    <div class="dashboard-main">
      <h1 style="font-size:1.375rem;font-weight:800;margin-bottom:1.5rem">Affiliate Dashboard</h1>

      <?php if (!$affiliate): ?>
      <!-- Not enrolled -->
      <div style="background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(168,85,247,.08));border:1px solid rgba(99,102,241,.3);border-radius:var(--radius-lg);padding:3rem;text-align:center">
        <div style="font-size:3.5rem;margin-bottom:1.25rem">🤝</div>
        <h2 style="margin-bottom:.75rem">Join Our Affiliate Program</h2>
        <p style="color:var(--text-muted);max-width:480px;margin:0 auto 2rem;line-height:1.7">
          Earn <strong style="color:var(--accent)"><?= setting('affiliate_commission', '15') ?>%</strong> commission on every sale you refer.
          Get paid monthly via bank transfer or PayPal.
        </p>
        <form method="POST" action="<?= url('affiliate/payout') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="enroll">
          <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-link-45deg"></i> Join Affiliate Program</button>
        </form>
      </div>
      <?php else: ?>

      <!-- Stats -->
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1.25rem;margin-bottom:1.75rem">
        <?php $cards = [
          ['$.pending_earnings', 'Pending Earnings', 'bi-hourglass-split', 'warning'],
          ['$.paid_earnings',    'Total Paid',       'bi-check-circle',    'success'],
          ['$.clicks',           'Link Clicks',      'bi-cursor',          'info'],
          ['$.conversions',      'Conversions',      'bi-bag-check',       'primary'],
        ]; ?>
        <?php foreach ($cards as [$valKey, $label, $icon, $color]): ?>
        <?php
        $key = ltrim($valKey, '$.');
        $val = $affiliate[$key] ?? 0;
        $display = in_array($key, ['pending_earnings', 'paid_earnings']) ? currency((float)$val) : number_format($val);
        ?>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(99,102,241,.1);color:var(--accent)"><i class="bi <?= $icon ?>"></i></div>
          <div>
            <div class="stat-num" style="font-size:1.35rem"><?= $display ?></div>
            <div class="stat-label"><?= $label ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Referral Link -->
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
        <div style="font-size:.85rem;font-weight:700;margin-bottom:.75rem"><i class="bi bi-link-45deg" style="color:var(--accent)"></i> Your Referral Link</div>
        <div class="input-group" style="max-width:560px">
          <input type="text" id="ref-link" class="form-control" value="<?= url('signup?ref=' . e($affiliate['code'])) ?>" readonly onclick="this.select()">
          <button class="btn btn-primary" onclick="copyToClipboard(document.getElementById('ref-link').value, this)"><i class="bi bi-copy"></i> Copy</button>
        </div>
        <div style="font-size:.78rem;color:var(--text-dim);margin-top:.5rem">Your code: <strong><?= e($affiliate['code']) ?></strong> · Commission: <?= setting('affiliate_commission', '15') ?>%</div>
      </div>

      <!-- Referrals Table -->
      <div class="table-card" style="margin-bottom:1.5rem">
        <div class="table-card-header"><span class="table-card-title">Recent Referrals</span></div>
        <?php if (empty($referrals)): ?>
        <div style="text-align:center;padding:2.5rem;color:var(--text-dim)">No referrals yet. Share your link to start earning!</div>
        <?php else: ?>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Order</th><th>Amount</th><th>Commission</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach ($referrals as $r): ?>
              <tr>
                <td><span style="font-weight:600;color:var(--accent-light)">#<?= $r['order_id'] ?></span></td>
                <td><?= currency((float)$r['order_total']) ?></td>
                <td style="font-weight:700;color:var(--success)"><?= currency((float)$r['commission']) ?></td>
                <td>
                  <?php $sm = ['pending'=>'badge-warning','approved'=>'badge-success','paid'=>'badge-primary','rejected'=>'badge-danger']; ?>
                  <span class="badge <?= $sm[$r['status']] ?? 'badge-dark' ?>"><?= ucfirst($r['status']) ?></span>
                </td>
                <td style="font-size:.8rem;color:var(--text-dim)"><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>

      <!-- Payout Request -->
      <?php if ((float)($affiliate['pending_earnings'] ?? 0) >= (float)(setting('min_payout', '10'))): ?>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;max-width:480px">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1rem"><i class="bi bi-wallet" style="color:var(--accent)"></i> Request Payout</h3>
        <form method="POST" action="<?= url('affiliate/payout') ?>">
          <?= csrf_field() ?>
          <div class="form-group"><label class="form-label">Payout Method</label>
            <select name="method" class="form-control">
              <option value="paypal">PayPal</option>
              <option value="bank">Bank Transfer</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Account Details</label><input type="text" name="account" class="form-control" placeholder="PayPal email or bank details"></div>
          <button type="submit" class="btn btn-primary btn-full"><i class="bi bi-wallet2"></i> Request <?= currency((float)$affiliate['pending_earnings']) ?></button>
        </form>
      </div>
      <?php else: ?>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;font-size:.875rem;color:var(--text-muted)">
        <i class="bi bi-info-circle" style="color:var(--accent)"></i>
        Minimum payout is <?= currency((float)setting('min_payout', '10')) ?>. Keep referred sales to unlock your payout!
      </div>
      <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</div>
