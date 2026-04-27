<?php
// THEMORA SHOP — Premium Cart Page
$sym = setting('currency_symbol', '$');
?>
<div class="container cart-page-container" style="padding-top:3.5rem;padding-bottom:6rem">
  <div class="cart-header-section mb-8">
    <h1 class="text-gradient" style="font-size:2rem;font-weight:900;letter-spacing:-0.03em">Shopping Bag</h1>
    <p class="text-dim"><?= count($cartItems) ?> exquisite items ready for your collection</p>
  </div>

  <?php if (empty($cartItems)): ?>
  <div class="empty-cart-card reveal visible">
    <div class="empty-cart-icon-wrapper">
      <i class="bi bi-bag-plus"></i>
    </div>
    <h2 class="mb-2">Your bag is currently empty</h2>
    <p class="text-dim mb-6">Discovery awaits! Explore our curated digital goods and find something unique today.</p>
    <a href="<?= url('products') ?>" class="btn btn-primary btn-lg btn-glow">Explore Catalog</a>
  </div>
  <?php else: ?>
  <div class="cart-main-grid">
    <!-- List of Products -->
    <div class="cart-items-list-wrap">
      <?php foreach ($cartItems as $item): ?>
      <div class="cart-item-premium reveal visible mb-4">
        <div class="item-image-box">
          <?php if ($item['thumbnail']): ?>
            <img src="<?= asset($item['thumbnail']) ?>" alt="<?= e($item['title']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=P&background=6366f1&color=fff'">
          <?php else: ?>
            <div class="item-placeholder-img"><i class="bi bi-file-earmark-code"></i></div>
          <?php endif; ?>
        </div>
        <div class="item-content-box">
          <div class="item-info">
            <a href="<?= url('products/' . $item['slug']) ?>" class="item-title-link"><?= e($item['title']) ?></a>
            <div class="item-category-tag"><?= e($item['category_name'] ?? 'Digital Product') ?></div>
          </div>
          <div class="item-pricing-actions">
            <div class="item-final-price"><?= $sym ?><?= number_format((float)$item['effective_price'], 2) ?></div>
            <form method="POST" action="<?= url('cart/remove') ?>" class="m-0">
              <?= csrf_field() ?>
              <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
              <button type="submit" class="item-remove-btn" title="Remove from bag"><i class="bi bi-trash3"></i></button>
            </form>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Coupon Panel -->
      <div class="coupon-panel-premium mt-6">
        <div class="panel-header mb-3">
          <i class="bi bi-brightness-high"></i> <span>Promotional Discount</span>
        </div>
        <form id="coupon-form" class="coupon-form-grid">
          <?= csrf_field() ?>
          <input type="text" name="code" class="form-control-premium" placeholder="Enter coupon code" value="<?= $appliedCoupon ? e($appliedCoupon['code']) : '' ?>">
          <button type="submit" class="btn btn-secondary btn-apply">Apply</button>
        </form>
        <?php if ($appliedCoupon): ?>
        <div class="coupon-active-badge mt-3">
          <i class="bi bi-check-circle-fill"></i>
          <span>Code <strong><?= e($appliedCoupon['code']) ?></strong> applied: <strong>-<?= $appliedCoupon['type'] === 'percent' ? $appliedCoupon['value'] . '%' : $sym . $appliedCoupon['value'] ?></strong> off</span>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Summary / Checkout Sticky -->
    <aside class="cart-summary-sidebar">
      <div class="summary-card-premium shadow-2xl">
        <h2 class="summary-title mb-6">Order Summary</h2>
        
        <div class="summary-details">
          <div class="summary-line">
            <span class="label">Subtotal</span>
            <span class="value"><?= $sym ?><?= number_format((float)$subtotal, 2) ?></span>
          </div>
          
          <?php if ($discount > 0): ?>
          <div class="summary-line discount-line">
            <span class="label">Savings</span>
            <span class="value">-<?= $sym ?><?= number_format((float)$discount, 2) ?></span>
          </div>
          <?php endif; ?>
          
          <div class="summary-line">
            <span class="label">Estimated Tax (<?= setting('tax_rate', '0') ?>%)</span>
            <span class="value"><?= $sym ?><?= number_format((float)$tax, 2) ?></span>
          </div>

          <div class="summary-total-line mt-4 pt-4 border-t border-white/[0.08]">
            <span class="label">Total Amount</span>
            <span class="value"><?= $sym ?><?= number_format((float)$total, 2) ?></span>
          </div>
        </div>

        <div class="summary-actions mt-8">
          <a href="<?= url('checkout') ?>" class="btn btn-primary btn-checkout btn-full btn-lg mb-3" style="display:flex;align-items:center;justify-content:center;font-weight:700;color:#ffffff !important">
            <!-- Hardcoded white fill to fix dark-mode visibility -->
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffffff" viewBox="0 0 16 16" style="margin-right:12px;flex-shrink:0">
              <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
            </svg>
            Secure Checkout Now
          </a>
          <a href="<?= url('products') ?>" class="btn btn-ghost btn-full" style="border:1px solid var(--border)">
            <i class="bi bi-arrow-left"></i> Keep Shopping
          </a>
        </div>

        <div class="summary-trust-badges mt-6 pt-6 border-t border-white/[0.08]">
          <div class="badge-row mb-4">
            <img src="https://www.svgrepo.com/show/349436/mastercard.svg" alt="Mastercard">
            <img src="https://www.svgrepo.com/show/349478/stripe.svg" alt="Stripe">
            <img src="https://www.svgrepo.com/show/349451/paypal.svg" alt="PayPal">
            <img src="https://www.svgrepo.com/show/24967/visa.svg" alt="Visa" style="filter: brightness(0) invert(1) opacity(0.7)">
          </div>
          <p class="trust-note"><i class="bi bi-patch-check-fill"></i> 100% Secure & Encrypted Payments</p>
          <p class="trust-note mt-2"><i class="bi bi-arrow-counterclockwise"></i> 14-Day Satisfaction Guarantee</p>
        </div>
      </div>
    </aside>
  </div>
  <?php endif; ?>
</div>

<style>
/* Premium Cart Styles */
.cart-main-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 2.5rem;
  align-items: start;
}

.cart-item-premium {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  display: flex;
  gap: 1.5rem;
  padding: 1.25rem;
  transition: all 0.3s ease;
}

.cart-item-premium:hover {
  transform: translateX(5px);
  border-color: rgba(99, 102, 241, 0.3);
  background: var(--bg-2);
}

.item-image-box {
  width: 100px;
  height: 70px;
  border-radius: var(--radius-sm);
  overflow: hidden;
  background: var(--bg-3);
  flex-shrink: 0;
}

.item-image-box img { width: 100%; height: 100%; object-fit: cover; }
.item-placeholder-img { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--accent-dark), #a855f7); color: white; font-size: 1.5rem; }

.item-content-box {
  display: flex;
  justify-content: space-between;
  flex: 1;
  align-items: center;
  gap: 1rem;
}

.item-title-link { font-weight: 700; font-size: 1.1rem; color: var(--text); display: block; margin-bottom: 0.25rem; }
.item-title-link:hover { color: var(--accent-light); }
.item-category-tag { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-dim); }

.item-pricing-actions { text-align: right; display: flex; align-items: center; gap: 1.5rem; }
.item-final-price { font-size: 1.25rem; font-weight: 800; color: var(--text); }
.item-remove-btn { background: none; border: none; color: var(--text-dim); cursor: pointer; font-size: 1.1rem; transition: all 0.2s; padding: 0.5rem; border-radius: 50%; }
.item-remove-btn:hover { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

/* Sidebar Summary */
.cart-summary-sidebar { position: sticky; top: 100px; }
.summary-card-premium {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 2rem;
  backdrop-filter: blur(10px);
}

.summary-title { font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em; }
.summary-line { display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 0.9rem; color: var(--text-muted); }
.summary-line.discount-line { color: var(--success); font-weight: 600; }
.summary-total-line { display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 900; color: var(--text); }

.btn-checkout {
  background: linear-gradient(135deg, var(--accent), var(--accent-dark));
  box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5);
  height: 54px;
  font-size: 1rem;
}

.btn-checkout i { font-size: 1.25rem; margin-right: 0.75rem; }

/* Coupon Panel */
.coupon-panel-premium {
  background: rgba(255,255,255,0.03);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.5rem;
}

.panel-header { display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; font-weight: 700; color: var(--text); text-transform: uppercase; letter-spacing: 0.05em; }
.coupon-form-grid { display: flex; gap: 0.75rem; }
.form-control-premium { flex: 1; background: var(--bg-3); border: 1px solid var(--border); border-radius: 10px; padding: 0.75rem 1rem; color: var(--text); outline: none; }
.form-control-premium:focus { border-color: var(--accent); }

.coupon-active-badge {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: rgba(34, 197, 94, 0.1);
  border: 1px solid rgba(34, 197, 94, 0.2);
  padding: 0.75rem 1rem;
  border-radius: 10px;
  color: #4ade80;
  font-size: 0.85rem;
}

/* Trust Badges */
.summary-trust-badges { text-align: center; }
.badge-row { display: flex; justify-content: center; gap: 1.25rem; }
.badge-row img { height: 22px; opacity: 0.6; filter: grayscale(1); transition: all 0.3s; }
.badge-row img:hover { opacity: 1; filter: grayscale(0); }
.trust-note { font-size: 0.72rem; color: var(--text-dim); display: flex; align-items: center; justify-content: center; gap: 0.4rem; }
.trust-note i { color: var(--success); }

/* Empty State */
.empty-cart-card { text-align: center; padding: 6rem 2rem; background: var(--surface); border: 2px dashed var(--border); border-radius: var(--radius-lg); }
.empty-cart-icon-wrapper { width: 100px; height: 100px; background: rgba(99, 102, 241, 0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 3rem; color: var(--accent); }

@media (max-width: 1024px) {
  .cart-main-grid { grid-template-columns: 1fr; }
  .cart-summary-sidebar { position: static; }
}

@media (max-width: 640px) {
  .cart-item-premium { flex-direction: column; gap: 1rem; }
  .item-image-box { width: 100%; height: 150px; }
  .item-content-box { flex-direction: column; align-items: flex-start; }
  .item-pricing-actions { width: 100%; justify-content: space-between; border-top: 1px solid var(--border); padding-top: 1rem; }
}
</style>
