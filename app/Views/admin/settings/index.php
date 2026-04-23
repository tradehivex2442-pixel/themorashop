<?php
// THEMORA SHOP — Admin Settings Page
$set = function(string $key) use ($settings): string { return e($settings[$key] ?? ''); };
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Settings</div>
    <div class="page-header-sub">Configure your store preferences and integrations</div>
  </div>
</div>

<div data-tabs>
  <div class="tab-nav">
    <button class="tab-btn active" data-tab="tab-general">General</button>
    <button class="tab-btn" data-tab="tab-payments">Payments</button>
    <button class="tab-btn" data-tab="tab-email">Email & SMTP</button>
    <button class="tab-btn" data-tab="tab-integrations">Integrations</button>
    <button class="tab-btn" data-tab="tab-security">Security</button>
  </div>

  <!-- ── General Tab ────────────────────────────────────────── -->
  <div id="tab-general" class="tab-pane active">
    <form method="POST" action="<?= url('admin/settings') ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
        <div class="table-card" style="padding:1.5rem">
          <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem">Site Information</h3>
          <div class="form-group"><label class="form-label">Site Name</label><input type="text" name="site_name" class="form-control" value="<?= $set('site_name') ?>"></div>
          <div class="form-group"><label class="form-label">Tagline</label><input type="text" name="site_tagline" class="form-control" value="<?= $set('site_tagline') ?>"></div>
          <div class="form-group"><label class="form-label">Support Email</label><input type="email" name="support_email" class="form-control" value="<?= $set('support_email') ?>"></div>
          <div class="form-group"><label class="form-label">Footer Text</label><input type="text" name="footer_text" class="form-control" value="<?= $set('footer_text') ?>"></div>
          <div class="form-group">
            <label class="form-label">Maintenance Mode</label>
            <select name="maintenance_mode" class="form-control">
              <option value="0" <?= !$settings['maintenance_mode'] ? 'selected' : '' ?>>Off</option>
              <option value="1" <?= !empty($settings['maintenance_mode']) ? 'selected' : '' ?>>On</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Maintenance Message</label><textarea name="maintenance_msg" class="form-control" rows="2"><?= $set('maintenance_msg') ?></textarea></div>
        </div>

        <div class="table-card" style="padding:1.5rem">
          <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem">Commerce</h3>
          <div class="form-group"><label class="form-label">Currency Code</label><input type="text" name="currency" id="curr_code" class="form-control" placeholder="USD" value="<?= $set('currency') ?>"></div>
          <div class="form-group"><label class="form-label">Currency Symbol</label><input type="text" name="currency_symbol" id="curr_sym" class="form-control" placeholder="$" value="<?= $set('currency_symbol') ?>"></div>
          
          <div style="margin-bottom:1.25rem">
            <label class="form-label" style="font-size:.7rem;text-transform:uppercase;color:var(--text-dim)">Quick Presets</label>
            <div style="display:flex;gap:.5rem">
              <button type="button" class="btn btn-ghost btn-sm" onclick="setCurr('USD','$')">USD ($)</button>
              <button type="button" class="btn btn-ghost btn-sm" onclick="setCurr('INR','₹')">INR (₹)</button>
              <button type="button" class="btn btn-ghost btn-sm" onclick="setCurr('EUR','€')">EUR (€)</button>
            </div>
          </div>

          <div class="form-group"><label class="form-label">Tax Rate (%)</label><input type="number" name="tax_rate" class="form-control" step=".01" value="<?= $set('tax_rate') ?>"></div>
          <div class="form-group"><label class="form-label">Download Expiry (hours)</label><input type="number" name="download_expiry_hours" class="form-control" value="<?= $set('download_expiry_hours') ?>"></div>
          <div class="form-group"><label class="form-label">Default Download Limit</label><input type="number" name="default_download_limit" class="form-control" min="1" value="<?= $set('default_download_limit') ?>"></div>
          <div class="form-group"><label class="form-label">Affiliate Commission (%)</label><input type="number" name="affiliate_commission" class="form-control" step=".01" value="<?= $set('affiliate_commission') ?>"></div>
        </div>
      </div>
      <div style="margin-top:1.25rem"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Settings</button></div>
    </form>
  </div>

  <!-- ── Payments Tab ───────────────────────────────────────── -->
  <div id="tab-payments" class="tab-pane">
    <form method="POST" action="<?= url('admin/settings') ?>">
      <?= csrf_field() ?>
      <?php
      $gws = [
        'Razorpay' => ['razorpay_key_id', 'razorpay_key_secret'],
        'Stripe'   => ['stripe_public_key', 'stripe_secret_key', 'stripe_webhook_secret'],
        'PayPal'   => ['paypal_client_id', 'paypal_client_secret', 'paypal_mode'],
      ];
      foreach ($gws as $name => $fields): ?>
      <div class="table-card" style="padding:1.5rem;margin-bottom:1.25rem">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem"><?= $name ?></h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <?php foreach ($fields as $key): ?>
          <div class="form-group">
            <label class="form-label"><?= ucwords(str_replace('_', ' ', $key)) ?></label>
            <?php if ($key === 'paypal_mode'): ?>
            <select name="<?= $key ?>" class="form-control">
              <option value="sandbox" <?= ($settings[$key] ?? '') === 'sandbox' ? 'selected' : '' ?>>Sandbox</option>
              <option value="live" <?= ($settings[$key] ?? '') === 'live' ? 'selected' : '' ?>>Live</option>
            </select>
            <?php else: ?>
            <input type="text" name="<?= $key ?>" class="form-control" value="<?= $set($key) ?>" placeholder="Enter <?= $name ?> <?= str_replace('_', ' ', $key) ?>">
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Payment Settings</button>
    </form>
  </div>

  <!-- ── Email Tab ──────────────────────────────────────────── -->
  <div id="tab-email" class="tab-pane">
    <form method="POST" action="<?= url('admin/settings') ?>">
      <?= csrf_field() ?>
      <div class="table-card" style="padding:1.5rem;max-width:600px">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem">SMTP Configuration</h3>
        <div class="form-group"><label class="form-label">SMTP Host</label><input type="text" name="smtp_host" class="form-control" value="<?= $set('smtp_host') ?>" placeholder="smtp.gmail.com"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group"><label class="form-label">Port</label><input type="number" name="smtp_port" class="form-control" value="<?= $set('smtp_port') ?>" placeholder="587"></div>
          <div class="form-group"><label class="form-label">Encryption</label>
            <select name="smtp_encryption" class="form-control">
              <?php foreach (['tls'=>'TLS','ssl'=>'SSL','none'=>'None'] as $v => $l): ?>
              <option value="<?= $v ?>" <?= ($settings['smtp_encryption'] ?? 'tls') === $v ? 'selected' : '' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group"><label class="form-label">SMTP Username</label><input type="text" name="smtp_user" class="form-control" value="<?= $set('smtp_user') ?>"></div>
        <div class="form-group"><label class="form-label">SMTP Password</label><input type="password" name="smtp_pass" class="form-control" placeholder="Enter password"></div>
        <div class="form-group"><label class="form-label">From Name</label><input type="text" name="mail_from_name" class="form-control" value="<?= $set('mail_from_name') ?>"></div>
        <div class="form-group"><label class="form-label">From Email</label><input type="email" name="mail_from_email" class="form-control" value="<?= $set('mail_from_email') ?>"></div>
      </div>
      <div style="margin-top:1.25rem;display:flex;gap:.75rem">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save</button>
        <a href="<?= url('admin/settings/test-email') ?>" class="btn btn-secondary"><i class="bi bi-envelope"></i> Send Test Email</a>
      </div>
    </form>
  </div>

  <!-- ── Integrations Tab ───────────────────────────────────── -->
  <div id="tab-integrations" class="tab-pane">
    <form method="POST" action="<?= url('admin/settings') ?>">
      <?= csrf_field() ?>
      <div class="table-card" style="padding:1.5rem;max-width:600px">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem">Analytics & AI</h3>
        <div class="form-group"><label class="form-label">Google Analytics Measurement ID</label><input type="text" name="ga_id" class="form-control" value="<?= $set('ga_id') ?>" placeholder="G-XXXXXXXXXX"></div>
        <div class="form-group"><label class="form-label">Meta Pixel ID</label><input type="text" name="meta_pixel_id" class="form-control" value="<?= $set('meta_pixel_id') ?>"></div>
        <div class="form-group"><label class="form-label">OpenAI API Key</label><input type="text" name="openai_api_key" class="form-control" value="<?= $set('openai_api_key') ?>" placeholder="sk-…"></div>
        <div class="form-group"><label class="form-label">Google OAuth Client ID</label><input type="text" name="google_client_id" class="form-control" value="<?= $set('google_client_id') ?>"></div>
        <div class="form-group"><label class="form-label">Google OAuth Client Secret</label><input type="text" name="google_client_secret" class="form-control" value="<?= $set('google_client_secret') ?>"></div>
        <div class="form-group"><label class="form-label">GitHub OAuth Client ID</label><input type="text" name="github_client_id" class="form-control" value="<?= $set('github_client_id') ?>"></div>
        <div class="form-group"><label class="form-label">GitHub OAuth Client Secret</label><input type="text" name="github_client_secret" class="form-control" value="<?= $set('github_client_secret') ?>"></div>
      </div>
      <div style="margin-top:1.25rem"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Integrations</button></div>
    </form>
  </div>

  <!-- ── Security Tab ───────────────────────────────────────── -->
  <div id="tab-security" class="tab-pane">
    <form method="POST" action="<?= url('admin/settings') ?>">
      <?= csrf_field() ?>
      <div class="table-card" style="padding:1.5rem;max-width:600px">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem">Security Settings</h3>
        <div class="form-group"><label class="form-label">Admin IP Whitelist (one per line, empty to disable)</label><textarea name="admin_ip_whitelist" class="form-control" rows="4" placeholder="192.168.1.1&#10;203.0.113.0"><?= $set('admin_ip_whitelist') ?></textarea></div>
        <div class="form-group"><label class="form-label">Max Login Attempts</label><input type="number" name="max_login_attempts" class="form-control" value="<?= $set('max_login_attempts') ?: 5 ?>"></div>
        <div class="form-group"><label class="form-label">Lockout Duration (minutes)</label><input type="number" name="lockout_minutes" class="form-control" value="<?= $set('lockout_minutes') ?: 30 ?>"></div>
      </div>
      <div style="margin-top:1.25rem"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Security</button></div>
    </form>
  </div>
</div>
<script>
function setCurr(code, sym) {
  document.getElementById('curr_code').value = code;
  document.getElementById('curr_sym').value = sym;
}
</script>
