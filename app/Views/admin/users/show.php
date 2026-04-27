<?php
// THEMORA SHOP — Admin User Detail
?>
<div class="page-header">
  <div>
    <a href="<?= url('admin/users') ?>" class="btn btn-ghost btn-sm" style="border:1px solid var(--border);margin-bottom:.5rem"><i class="bi bi-arrow-left"></i> Users</a>
    <div class="page-header-title"><?= e($user['name']) ?></div>
    <div class="page-header-sub"><?= e($user['email']) ?> · Member since <?= date('M Y', strtotime($user['created_at'])) ?></div>
  </div>
  <div class="page-header-actions">
    <?php $rMap=['super_admin'=>'badge-danger','editor'=>'badge-primary','support'=>'badge-warning','user'=>'badge-dark']; ?>
    <span class="badge <?= $rMap[$user['role']] ?? 'badge-dark' ?>" style="font-size:.875rem;padding:.5rem 1rem"><?= ucfirst($user['role']) ?></span>
    <?php if (!$user['is_blocked']): ?>
    <form method="POST" action="<?= url('admin/users/' . $user['id'] . '/block') ?>">
      <?= csrf_field() ?>
      <button class="btn btn-danger btn-sm" data-confirm="Block <?= e($user['name']) ?>?"><i class="bi bi-slash-circle"></i> Block</button>
    </form>
    <?php else: ?>
    <form method="POST" action="<?= url('admin/users/' . $user['id'] . '/block') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="unblock" value="1">
      <button class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> Unblock</button>
    </form>
    <?php endif; ?>
    <a href="<?= url('admin/users/' . $user['id'] . '/impersonate') ?>" class="btn btn-secondary btn-sm" data-confirm="Impersonate this user?"><i class="bi bi-person-badge"></i> Impersonate</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:1.5rem;align-items:start">
  <!-- Left: Orders -->
  <div>
    <div class="table-card">
      <div class="table-card-header">
        <span class="table-card-title">Purchase History</span>
        <span style="font-size:.8rem;color:var(--text-muted)"><?= count($orders) ?> orders · Total: <?= currency(array_sum(array_column($orders,'total'))) ?></span>
      </div>
      <?php if (empty($orders)): ?>
      <div style="padding:3rem;text-align:center;color:var(--text-dim)">No orders yet</div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>Order #</th><th>Items</th><th>Total</th><th>Gateway</th><th>Status</th><th>Date</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
              <td><a href="<?= url('admin/orders/' . $o['id']) ?>" style="font-weight:700;color:var(--accent-light)">#<?= $o['id'] ?></a></td>
              <td style="color:var(--text-muted)"><?= $o['item_count'] ?></td>
              <td style="font-weight:700"><?= currency((float)$o['total']) ?></td>
              <td><span class="badge badge-dark"><?= ucfirst($o['payment_gateway'] ?? '—') ?></span></td>
              <td>
                <?php $sm=['paid'=>'badge-success','pending'=>'badge-warning','failed'=>'badge-danger','refunded'=>'badge-dark']; ?>
                <span class="badge <?= $sm[$o['status']] ?? 'badge-dark' ?>"><?= ucfirst($o['status']) ?></span>
              </td>
              <td style="font-size:.8rem;color:var(--text-dim)"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
              <td><a href="<?= url('admin/orders/' . $o['id']) ?>" class="action-btn" title="View"><i class="bi bi-eye"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right: Profile Info -->
  <div style="display:flex;flex-direction:column;gap:1rem">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;text-align:center">
      <?php if ($user['avatar']): ?>
        <img src="<?= e($user['avatar']) ?>" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--accent);margin-bottom:.875rem" alt="">
      <?php else: ?>
        <div style="width:80px;height:80px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-size:2rem;font-weight:700;margin:0 auto .875rem"><?= strtoupper(substr($user['name'],0,1)) ?></div>
      <?php endif; ?>
      <div style="font-weight:700"><?= e($user['name']) ?></div>
      <div style="font-size:.8rem;color:var(--text-dim)"><?= e($user['email']) ?></div>
      <?php if ($user['is_blocked']): ?><div class="badge badge-danger" style="margin-top:.5rem">Blocked</div><?php endif; ?>
    </div>

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem">
      <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-dim);margin-bottom:.875rem">Account Details</div>
      <?php $info = [
        ['Role',           ucfirst($user['role'])],
        ['Email Verified', $user['email_verified'] ? '✓ Yes' : '✗ No'],
        ['2FA',            $user['is_2fa_enabled'] ? '✓ Enabled' : 'Disabled'],
        ['Google Login',   $user['google_id'] ? '✓ Connected' : '—'],
        ['GitHub Login',   $user['github_id'] ? '✓ Connected' : '—'],
        ['Referral Code',  $user['referral_code'] ?? '—'],
        ['Joined',         date('M j, Y', strtotime($user['created_at']))],
      ];
      foreach ($info as [$label, $val]): ?>
      <div style="display:flex;justify-content:space-between;font-size:.8rem;padding:.4rem 0;border-bottom:1px solid var(--border)">
        <span style="color:var(--text-dim)"><?= $label ?></span>
        <span style="font-weight:600"><?= e($val) ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Admin role change -->
    <?php if (is_admin() && auth()['role'] === 'super_admin'): ?>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem">
      <div style="font-size:.8rem;font-weight:700;margin-bottom:.75rem">Change Role</div>
      <form method="POST" action="<?= url('admin/users/' . $user['id'] . '/block') ?>">
        <?= csrf_field() ?>
        <select name="role" class="form-control" style="margin-bottom:.75rem">
          <?php foreach (['user','editor','support','super_admin'] as $r): ?>
          <option value="<?= $r ?>" <?= $user['role'] === $r ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$r)) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" name="change_role" value="1" class="btn btn-primary btn-sm btn-full"><i class="bi bi-check2"></i> Update Role</button>
      </form>
    </div>
    <?php endif; ?>
  </div>
</div>
