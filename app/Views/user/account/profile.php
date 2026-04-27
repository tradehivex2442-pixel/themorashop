<?php
// THEMORA SHOP — User Profile Settings
?>
<div class="container" style="padding-top:2.5rem;padding-bottom:5rem">
  <div class="dashboard-layout">
    <aside class="dashboard-sidebar">
      <div class="sidebar-user" style="color:white;text-align:center;padding:2rem 1rem">
        <?php $u = auth(); ?>
        <div style="width:72px;height:72px;margin:0 auto 1rem;position:relative">
          <?php if (!empty($u['avatar'])): ?>
            <img src="<?= asset($u['avatar']) ?>" style="width:100%;height:100%;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.2)">
          <?php else: ?>
            <div style="width:100%;height:100%;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
          <?php endif; ?>
        </div>
        <div style="font-weight:700;font-size:.95rem"><?= e($u['name']) ?></div>
        <div style="font-size:.75rem;opacity:.8;margin-top:.25rem"><?= e($u['email']) ?></div>
      </div>
      <nav class="sidebar-nav">
        <a href="<?= url('dashboard') ?>" class="sidebar-nav-item"><i class="bi bi-speedometer2"></i> Overview</a>
        <a href="<?= url('dashboard/orders') ?>" class="sidebar-nav-item"><i class="bi bi-bag"></i> My Orders</a>
        <a href="<?= url('dashboard/wishlist') ?>" class="sidebar-nav-item"><i class="bi bi-heart"></i> Wishlist</a>
        <a href="<?= url('dashboard/affiliate') ?>" class="sidebar-nav-item"><i class="bi bi-link-45deg"></i> Affiliate</a>
        <a href="<?= url('dashboard/tickets') ?>" class="sidebar-nav-item"><i class="bi bi-chat-dots"></i> Support</a>
        <a href="<?= url('dashboard/profile') ?>" class="sidebar-nav-item active"><i class="bi bi-person-gear"></i> Profile</a>
      </nav>
    </aside>

    <div class="dashboard-main">
      <h1 style="font-size:1.375rem;font-weight:800;margin-bottom:1.5rem">Profile Settings</h1>

      <div data-tabs>
        <div class="tab-nav">
          <button class="tab-btn active" data-tab="tab-profile">Profile</button>
          <button class="tab-btn" data-tab="tab-security">Security</button>
          <button class="tab-btn" data-tab="tab-notifications">Notifications</button>
        </div>

        <!-- Profile Tab -->
        <div id="tab-profile" class="tab-pane active">
          <form method="POST" action="<?= url('dashboard/profile') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem;margin-bottom:1.25rem">
              <!-- Avatar -->
              <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:1.75rem;padding-bottom:1.75rem;border-bottom:1px solid var(--border)">
                <div style="position:relative">
                  <?php if ($profile['avatar']): ?>
                    <img id="avatar-preview" src="<?= asset($profile['avatar']) ?>" style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--accent)">
                  <?php else: ?>
                    <div id="avatar-fallback" style="width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,var(--accent-dark),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:2rem;font-weight:700;border:3px solid var(--accent)"><?= strtoupper(substr($profile['name'], 0, 1)) ?></div>
                    <img id="avatar-preview" style="display:none;width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--accent)" alt="Avatar">
                  <?php endif; ?>
                </div>
                <div>
                  <div style="font-weight:700;margin-bottom:.25rem"><?= e($profile['name']) ?></div>
                  <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.75rem">Member since <?= date('M Y', strtotime($profile['created_at'])) ?></div>
                  <input type="file" name="avatar" id="avatar-input" accept="image/*" data-preview="avatar-preview" style="display:none">
                  <label for="avatar-input" class="btn btn-secondary btn-sm" style="cursor:pointer"><i class="bi bi-upload"></i> Change Photo</label>
                </div>
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="name" class="form-control" value="<?= e($profile['name']) ?>" required>
                </div>
                <div class="form-group">
                  <label class="form-label">Email Address</label>
                  <input type="email" class="form-control" value="<?= e($profile['email']) ?>" disabled style="opacity:.6" title="Email cannot be changed">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Bio / About</label>
                <textarea name="bio" class="form-control" rows="3" placeholder="Tell us about yourself…"><?= e($profile['bio'] ?? '') ?></textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Profile</button>
          </form>
        </div>

        <!-- Security Tab -->
        <div id="tab-security" class="tab-pane">
          <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem;max-width:480px">
            <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem">Change Password</h3>
            <form method="POST" action="<?= url('dashboard/profile/password') ?>">
              <?= csrf_field() ?>
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
              </div>
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="8">
              </div>
              <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary"><i class="bi bi-shield-lock"></i> Update Password</button>
            </form>

            <hr style="border:none;border-top:1px solid var(--border);margin:1.75rem 0">

            <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem">Two-Factor Authentication</h3>
            <?php if (!empty($profile['is_2fa_enabled'])): ?>
            <div class="alert alert-success"><i class="bi bi-shield-check-fill"></i> 2FA is currently <strong>enabled</strong></div>
            <form method="POST" action="<?= url('dashboard/profile/2fa-disable') ?>">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-danger btn-sm" data-confirm="Disable 2FA?"><i class="bi bi-shield-x"></i> Disable 2FA</button>
            </form>
            <?php else: ?>
            <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> 2FA is <strong>disabled</strong></div>
            <a href="<?= url('dashboard/profile/2fa-setup') ?>" class="btn btn-secondary btn-sm"><i class="bi bi-qr-code"></i> Set up 2FA</a>
            <?php endif; ?>
          </div>
        </div>

        <!-- Notifications -->
        <div id="tab-notifications" class="tab-pane">
          <form method="POST" action="<?= url('dashboard/profile/notifications') ?>">
            <?= csrf_field() ?>
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem;max-width:480px">
              <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem">Email Notifications</h3>
              <?php
              $notifKeys = [
                'notif_order_success'    => 'Order confirmation emails',
                'notif_download_expiry'  => 'Download link expiry warnings',
                'notif_newsletter'       => 'Newsletter & promotions',
                'notif_affiliate'        => 'Affiliate earnings updates',
              ];
              foreach ($notifKeys as $key => $label):
              ?>
              <div class="form-check" style="margin-bottom:1rem;justify-content:space-between">
                <span style="font-size:.875rem;color:var(--text-muted)"><?= $label ?></span>
                <label style="cursor:pointer;display:flex;align-items:center;gap:.5rem">
                  <input type="checkbox" name="<?= $key ?>" value="1" style="accent-color:var(--accent);width:18px;height:18px" <?= !empty($profile[$key]) ? 'checked' : '' ?>>
                </label>
              </div>
              <?php endforeach; ?>
            </div>
            <div style="margin-top:1.25rem"><button type="submit" class="btn btn-primary"><i class="bi bi-bell"></i> Save Preferences</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Avatar Preview ---
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarFallback = document.getElementById('avatar-fallback');

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (avatarPreview) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.display = 'block';
                    }
                    if (avatarFallback) {
                        avatarFallback.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // --- Tab Switching ---
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-tab');
            tabButtons.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => { p.classList.remove('active'); p.style.display = 'none'; });
            btn.classList.add('active');
            const targetPane = document.getElementById(targetId);
            if (targetPane) { targetPane.classList.add('active'); targetPane.style.display = 'block'; }
        });
    });

    // Ensure first tab pane is visible initially
    const activePane = document.querySelector('.tab-pane.active');
    if (activePane) activePane.style.display = 'block';
});
</script>
