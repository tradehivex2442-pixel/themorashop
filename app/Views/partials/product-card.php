<?php
// THEMORA SHOP — Product Card Partial
// Used in: home, products listing, carousels, wishlist
$sym = setting('currency_symbol', '$');
$now = date('Y-m-d H:i:s');
$effectivePrice = ($p['flash_sale_price'] && ($p['flash_sale_ends'] ?? '') > $now)
    ? $p['flash_sale_price'] : $p['price'];
$isOnSale = $effectivePrice < $p['price'];
$discount = $p['original_price'] ? round((1 - $effectivePrice / $p['original_price']) * 100) : 0;
?>
<div class="product-card">
  <div class="product-card-img">
    <?php if ($p['thumbnail']): ?>
      <img src="<?= asset($p['thumbnail']) ?>" alt="<?= e($p['title']) ?>" loading="lazy" onerror="this.src='https://ui-avatars.com/api/?name=P&background=6366f1&color=fff'">
    <?php else: ?>
      <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--accent-dark) 0%,#a855f7 100%);display:flex;align-items:center;justify-content:center">
        <i class="bi bi-box-seam" style="font-size:2.5rem;color:rgba(255,255,255,.6)"></i>
      </div>
    <?php endif; ?>

    <!-- Badges -->
    <?php if ($discount >= 10): ?>
      <span class="product-badge badge-sale"><?= $discount ?>% OFF</span>
    <?php elseif ($isOnSale): ?>
      <span class="product-badge badge-sale">SALE</span>
    <?php elseif ($effectivePrice == 0): ?>
      <span class="product-badge badge-free">FREE</span>
    <?php endif; ?>

    <!-- Actions -->
    <div class="product-card-actions">
      <?php if (logged_in()): ?>
      <button class="product-action-btn" data-toggle-wishlist="<?= $p['id'] ?>" aria-label="Add to wishlist" title="Wishlist">
        <i class="bi bi-heart"></i>
      </button>
      <?php endif; ?>
      <a href="<?= url('products/' . $p['slug']) ?>" class="product-action-btn" aria-label="Quick view">
        <i class="bi bi-eye"></i>
      </a>
    </div>
  </div>

  <div class="product-card-body">
    <div class="product-category"><?= e($p['category_name'] ?? 'Digital') ?></div>
    <a href="<?= url('products/' . $p['slug']) ?>" style="text-decoration:none">
      <h3 class="product-title"><?= e($p['title']) ?></h3>
    </a>

    <?php if ($p['avg_rating'] > 0): ?>
    <div class="product-rating">
      <span class="rating-stars"><?= stars((float)$p['avg_rating']) ?></span>
      <span class="rating-count">(<?= number_format($p['avg_rating'], 1) ?>)</span>
      <?php if ($p['total_sales'] > 0): ?>
        <span class="rating-count">· <?= $p['total_sales'] ?> sold</span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="product-price-row">
      <div>
        <span class="product-price"><?= $sym ?><?= number_format((float)$effectivePrice, 2) ?></span>
        <?php if ($p['original_price'] && $p['original_price'] > $effectivePrice): ?>
          <span class="product-original" style="margin-left:.375rem"><?= $sym ?><?= number_format((float)$p['original_price'], 2) ?></span>
        <?php endif; ?>
      </div>
      <button class="add-cart-btn" data-add-cart="<?= $p['id'] ?>" aria-label="Add to cart">
        <i class="bi bi-bag-plus"></i>
      </button>
    </div>
  </div>
</div>
