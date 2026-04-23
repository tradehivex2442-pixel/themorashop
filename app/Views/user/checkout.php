<?php
// THEMORA SHOP — Checkout Page
$sym = setting('currency_symbol', '$');
?>
<div class="container" style="padding-top:2.5rem;padding-bottom:5rem;max-width:1000px">
  <h1 style="font-size:1.75rem;margin-bottom:2rem"><i class="bi bi-lock-fill" style="color:var(--accent)"></i> Secure Checkout</h1>

  <form method="POST" action="<?= url('checkout/process') ?>" id="checkout-form">
    <?= csrf_field() ?>
    <div style="display:grid;grid-template-columns:1fr 360px;gap:2rem;align-items:start">

      <!-- Left: Billing + Payment -->
      <div>
        <!-- Contact / Billing -->
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem;margin-bottom:1.25rem">
          <h2 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem"><i class="bi bi-person" style="color:var(--accent)"></i> Contact Details</h2>
          <?php if (!logged_in()): ?>
          <div style="margin-bottom:1.25rem;padding:.875rem;background:rgba(99,102,241,.06);border:1px solid rgba(99,102,241,.2);border-radius:10px;font-size:.85rem;color:var(--text-muted)">
            <i class="bi bi-info-circle" style="color:var(--accent)"></i> <a href="<?= url('login?next=checkout') ?>" style="color:var(--accent-light);font-weight:600">Log in</a> to save your details and access your orders anytime.
          </div>
          <?php endif; ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
              <?php $u = auth() ?: []; $uName = $u['name'] ?? ''; ?>
            <div class="form-group">
              <label class="form-label">First Name *</label>
              <input type="text" name="first_name" class="form-control" value="<?= old('first_name', $uName ? explode(' ', $uName)[0] : '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="<?= old('last_name', $uName ? (explode(' ', $uName)[1] ?? '') : '') ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control" value="<?= old('email', $u['email'] ?? '') ?>" required>
          </div>
        </div>

        <!-- Payment Method -->
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem;margin-bottom:1.25rem">
          <h2 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem"><i class="bi bi-credit-card" style="color:var(--accent)"></i> Payment Method</h2>
          <div style="display:flex;flex-direction:column;gap:.75rem" id="payment-methods">
            <?php $gateways = [
              ['id' => 'razorpay', 'name' => 'Razorpay', 'desc' => 'Credit/Debit cards, UPI, netbanking', 'icon' => 'bi-lightning-charge'],
              ['id' => 'stripe',   'name' => 'Stripe',   'desc' => 'International credit/debit cards',  'icon' => 'bi-credit-card-2-front'],
              ['id' => 'paypal',   'name' => 'PayPal',   'desc' => 'Pay with your PayPal account',      'icon' => 'bi-paypal'],
            ]; ?>
            <?php foreach ($gateways as $i => $gw): ?>
            <label style="display:flex;align-items:flex-start;gap:1rem;padding:1rem;background:var(--bg-2);border:2px solid <?= $i === 0 ? 'var(--accent)' : 'var(--border)' ?>;border-radius:var(--radius-sm);cursor:pointer;transition:border-color .2s" onclick="document.querySelectorAll('[data-gateway-label]').forEach(l=>l.style.borderColor='var(--border)');this.style.borderColor='var(--accent)'">
              <input type="radio" name="gateway" value="<?= $gw['id'] ?>" <?= $i === 0 ? 'checked' : '' ?> style="margin-top:3px;accent-color:var(--accent)">
              <div style="flex:1">
                <div style="font-weight:600;margin-bottom:.2rem"><i class="bi <?= $gw['icon'] ?>" style="color:var(--accent)"></i> <?= $gw['name'] ?></div>
                <div style="font-size:.8rem;color:var(--text-dim)"><?= $gw['desc'] ?></div>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-full btn-lg" id="place-order-btn">
          <i class="bi bi-shield-check-fill"></i> Place Secure Order · <?= $sym ?><?= number_format((float)$total, 2) ?>
        </button>
        <div style="text-align:center;font-size:.75rem;color:var(--text-dim);margin-top:.875rem">
          <i class="bi bi-lock"></i> Your payment is encrypted and secured with SSL
        </div>
      </div>

      <!-- Right: Order Summary -->
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;position:sticky;top:80px">
        <h2 style="font-size:.9rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:1.25rem">Your Order</h2>
        <div style="display:flex;flex-direction:column;gap:.875rem;margin-bottom:1.25rem;padding-bottom:1.25rem;border-bottom:1px solid var(--border)">
          <?php foreach ($products as $item): ?>
          <div style="display:flex;align-items:center;gap:.875rem">
            <div style="width:44px;height:44px;border-radius:8px;background:var(--bg-3);overflow:hidden;flex-shrink:0">
              <?php if ($item['thumbnail']): ?>
                <img src="<?= asset($item['thumbnail']) ?>" style="width:100%;height:100%;object-fit:cover" alt="<?= e($item['title']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=P&background=6366f1&color=fff'">
              <?php else: ?>
                <div style="width:100%;height:100%;background:var(--accent);display:flex;align-items:center;justify-content:center"><i class="bi bi-box" style="color:white;font-size:.8rem"></i></div>
              <?php endif; ?>
            </div>
            <div style="flex:1;min-width:0">
              <div style="font-size:.85rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($item['title']) ?></div>
            </div>
            <div style="font-weight:700;font-size:.875rem;flex-shrink:0"><?= $sym ?><?= number_format((float)$item['effective_price'], 2) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div style="display:flex;flex-direction:column;gap:.5rem;font-size:.875rem;color:var(--text-muted);margin-bottom:1rem">
          <div style="display:flex;justify-content:space-between"><span>Subtotal</span><span><?= $sym ?><?= number_format((float)$subtotal, 2) ?></span></div>
          <?php if ($discount > 0): ?><div style="display:flex;justify-content:space-between;color:var(--success)"><span>Discount</span><span>−<?= $sym ?><?= number_format((float)$discount, 2) ?></span></div><?php endif; ?>
          <div style="display:flex;justify-content:space-between"><span>Tax</span><span><?= $sym ?><?= number_format((float)$tax, 2) ?></span></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-weight:800;font-size:1.25rem;padding-top:1rem;border-top:1px solid var(--border)">
          <span>Total</span><span><?= $sym ?><?= number_format((float)$total, 2) ?></span>
        </div>
        <div style="margin-top:1.25rem;font-size:.78rem;color:var(--text-dim);display:flex;flex-direction:column;gap:.375rem">
          <div><i class="bi bi-check-circle" style="color:var(--success)"></i> Instant digital delivery</div>
          <div><i class="bi bi-check-circle" style="color:var(--success)"></i> 14-day money-back guarantee</div>
        </div>
      </div>
    </div>
  </form>
</div>

<style>@media(max-width:768px){.container > form div[style*="grid-template-columns:1fr 360px"]{grid-template-columns:1fr !important}[style*="position:sticky;top:80px"]{position:static !important}}</style>
