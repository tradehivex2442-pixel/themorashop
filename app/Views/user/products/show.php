<?php
// THEMORA SHOP — Product Detail Page
$sym = setting('currency_symbol', '$');
$schema = json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'Product',
  'name' => $product['title'],
  'description' => $product['short_desc'] ?? '',
  'image' => $product['thumbnail'] ?? '',
  'offers' => ['@type' => 'Offer', 'price' => $product['display_price'], 'priceCurrency' => setting('currency', 'USD'), 'availability' => 'https://schema.org/InStock'],
  'aggregateRating' => $product['avg_rating'] > 0 ? ['@type' => 'AggregateRating', 'ratingValue' => $product['avg_rating'], 'reviewCount' => $totalReviews] : null,
]);
?>
<script type="application/ld+json"><?= $schema ?></script>

<!-- Breadcrumb -->
<div style="background:var(--bg-2);border-bottom:1px solid var(--border);padding:.75rem 0">
  <div class="container" style="font-size:.8rem;color:var(--text-dim)">
    <a href="<?= url('/') ?>" style="color:var(--text-dim)">Home</a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;margin:0 .25rem"></i>
    <a href="<?= url('products') ?>" style="color:var(--text-dim)">Products</a>
    <i class="bi bi-chevron-right" style="font-size:.7rem;margin:0 .25rem"></i>
    <?php if ($product['category_name']): ?>
      <a href="<?= url('products?category=' . $product['cat_slug']) ?>" style="color:var(--text-dim)"><?= e($product['category_name']) ?></a>
      <i class="bi bi-chevron-right" style="font-size:.7rem;margin:0 .25rem"></i>
    <?php endif; ?>
    <span style="color:var(--text)"><?= e(substr($product['title'], 0, 40)) ?><?= strlen($product['title']) > 40 ? '…' : '' ?></span>
  </div>
</div>

<div class="container" style="padding-top:2.5rem;padding-bottom:5rem">
  <div style="display:grid;grid-template-columns:1fr 380px;gap:3rem;align-items:start">
    <!-- ── Left: Images + Description ───────────────────── -->
    <div>
      <!-- Main Image / Gallery -->
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:1.5rem">
        <div id="main-img" style="aspect-ratio:16/9;overflow:hidden;background:var(--bg-3);display:flex;align-items:center;justify-content:center">
          <?php if ($product['thumbnail']): ?>
            <img src="<?= asset($product['thumbnail']) ?>" alt="<?= e($product['title']) ?>" id="main-img-el" style="width:100%;height:100%;object-fit:cover" onerror="this.src='/themora_Shop/public/assets/images/hero/product-placeholder.svg'">
          <?php else: ?>
            <i class="bi bi-box-seam" style="font-size:5rem;color:var(--text-dim)"></i>
          <?php endif; ?>
        </div>
        <?php if (!empty($images)): ?>
        <div style="display:flex;gap:.75rem;padding:1rem;overflow-x:auto">
          <?php if ($product['thumbnail']): ?>
          <img src="<?= asset($product['thumbnail']) ?>" onclick="document.getElementById('main-img-el').src=this.src" style="width:72px;height:72px;border-radius:8px;object-fit:cover;cursor:pointer;border:2px solid var(--accent)" alt="Main">
          <?php endif; ?>
          <?php foreach ($images as $img): ?>
          <img src="<?= e($img['image_path']) ?>" onclick="document.getElementById('main-img-el').src=this.src" style="width:72px;height:72px;border-radius:8px;object-fit:cover;cursor:pointer;border:2px solid var(--border)" alt="Image">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Demo Video -->
      <?php if ($product['demo_video_url']): ?>
      <div style="margin-bottom:1.5rem">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem"><i class="bi bi-play-circle" style="color:var(--accent)"></i> Product Demo</h3>
        <div style="aspect-ratio:16/9;border-radius:var(--radius);overflow:hidden">
          <?php
          $vid = $product['demo_video_url'];
          if (str_contains($vid, 'youtube.com') || str_contains($vid, 'youtu.be')) {
            preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $vid, $m);
            echo '<iframe src="https://www.youtube.com/embed/' . ($m[1] ?? '') . '" width="100%" height="100%" frameborder="0" allowfullscreen style="display:block"></iframe>';
          } else {
            echo '<video src="' . e($vid) . '" controls style="width:100%;border-radius:var(--radius)"></video>';
          }
          ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Description -->
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:2rem;margin-bottom:1.5rem">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem">About This Product</h3>
        <div style="font-size:.9rem;color:var(--text-muted);line-height:1.8;white-space:pre-wrap"><?= nl2br(e($product['description'])) ?></div>
      </div>

      <!-- Tags -->
      <?php if (!empty($tags)): ?>
      <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.5rem">
        <?php foreach ($tags as $tag): ?>
        <a href="<?= url('products?tag=' . urlencode($tag)) ?>" class="category-pill" style="font-size:.75rem;padding:.25rem .75rem">
          # <?= e($tag) ?>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Reviews Section -->
      <div id="reviews">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem">
          <h3 style="font-size:1.1rem;font-weight:700">
            Customer Reviews
            <?php if ($totalReviews): ?>
              <span style="font-size:.8rem;font-weight:500;color:var(--text-dim);margin-left:.5rem">(<?= $totalReviews ?>)</span>
            <?php endif; ?>
          </h3>
          <?php if ($hasPurchased && !$userReview): ?>
          <button class="btn btn-secondary btn-sm" onclick="openModal('review-modal')"><i class="bi bi-star"></i> Write a Review</button>
          <?php endif; ?>
        </div>

        <?php if ($product['avg_rating'] > 0): ?>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap">
          <div style="text-align:center">
            <div style="font-size:3.5rem;font-weight:900;line-height:1"><?= number_format($product['avg_rating'], 1) ?></div>
            <div style="color:#f59e0b;font-size:1.25rem;margin:.25rem 0"><?= stars($product['avg_rating']) ?></div>
            <div style="font-size:.8rem;color:var(--text-dim)"><?= $totalReviews ?> reviews</div>
          </div>
          <div style="flex:1">
            <?php foreach ($ratingBreakdown as $rb): ?>
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.38rem">
              <span style="font-size:.8rem;color:var(--text-dim);width:20px;text-align:right"><?= $rb['rating'] ?>★</span>
              <div style="flex:1;height:6px;background:var(--bg-3);border-radius:99px;overflow:hidden">
                <div style="height:100%;background:var(--warning);border-radius:99px;width:<?= $totalReviews ? round($rb['cnt'] / $totalReviews * 100) : 0 ?>%"></div>
              </div>
              <span style="font-size:.75rem;color:var(--text-dim);width:20px"><?= $rb['cnt'] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (empty($reviews)): ?>
        <div style="text-align:center;padding:3rem;color:var(--text-dim)">
          <i class="bi bi-chat-square-text" style="font-size:2.5rem;display:block;margin-bottom:.75rem"></i>
          No reviews yet. <?= $hasPurchased ? 'Be the first to review!' : 'Purchase to leave a review.' ?>
        </div>
        <?php else: ?>
        <?php foreach ($reviews as $r): ?>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:1rem">
          <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem">
            <?php if ($r['reviewer_avatar']): ?>
              <img src="<?= e($r['reviewer_avatar']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
            <?php else: ?>
              <div style="width:40px;height:40px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-weight:700"><?= strtoupper(substr($r['reviewer_name'], 0, 1)) ?></div>
            <?php endif; ?>
            <div style="flex:1">
              <div style="font-weight:600;font-size:.9rem"><?= e($r['reviewer_name']) ?></div>
              <div style="font-size:.75rem;color:var(--text-dim)"><?= time_ago($r['created_at']) ?> · Verified buyer</div>
            </div>
            <span style="color:#f59e0b"><?= str_repeat('★', $r['rating']) ?></span>
          </div>
          <?php if ($r['body']): ?>
          <p style="font-size:.9rem;color:var(--text-muted);line-height:1.7"><?= e($r['body']) ?></p>
          <?php endif; ?>
          <?php if ($r['media_path']): ?>
          <img src="<?= e($r['media_path']) ?>" style="width:80px;height:80px;border-radius:8px;margin-top:.75rem;object-fit:cover">
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- ── Right: Purchase Sidebar ───────────────────────────── -->
    <div style="position:sticky;top:80px">
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.75rem;margin-bottom:1rem">
        <div class="product-category" style="margin-bottom:.5rem"><?= e($product['category_name'] ?? '') ?></div>
        <h1 style="font-size:1.375rem;margin-bottom:1rem;line-height:1.3"><?= e($product['title']) ?></h1>

        <!-- Rating -->
        <?php if ($product['avg_rating'] > 0): ?>
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1rem">
          <span style="color:#f59e0b"><?= str_repeat('★', round($product['avg_rating'])) ?></span>
          <a href="#reviews" style="font-size:.85rem;color:var(--text-dim)"><?= number_format($product['avg_rating'], 1) ?> (<?= $totalReviews ?> reviews)</a>
          <span style="color:var(--text-dim);font-size:.8rem">· <?= $product['total_sales'] ?> sales</span>
        </div>
        <?php endif; ?>

        <!-- Price -->
        <div style="margin-bottom:1.25rem">
          <span style="font-size:2.25rem;font-weight:900;letter-spacing:-.02em">
            <?= $sym ?><?= number_format((float)$product['display_price'], 2) ?>
          </span>
          <?php if ($product['original_display'] && $product['original_display'] > $product['display_price']): ?>
          <span style="font-size:1.1rem;color:var(--text-dim);text-decoration:line-through;margin-left:.75rem">
            <?= $sym ?><?= number_format((float)$product['original_display'], 2) ?>
          </span>
          <?php endif; ?>
          <?php if ($product['on_flash_sale']): ?>
          <div class="badge badge-danger" style="margin-top:.5rem;display:inline-flex">
            <i class="bi bi-lightning-fill"></i> Flash Sale — Ends <?= date('M j h:i A', strtotime($product['flash_sale_ends'])) ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Short Desc -->
        <?php if ($product['short_desc']): ?>
        <p style="font-size:.875rem;color:var(--text-muted);line-height:1.7;margin-bottom:1.25rem;border-bottom:1px solid var(--border);padding-bottom:1.25rem">
          <?= e($product['short_desc']) ?>
        </p>
        <?php endif; ?>

        <!-- Buttons -->
        <form action="<?= url('cart/add') ?>" method="POST" style="margin-bottom:.75rem">
          <?= csrf_field() ?>
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <button type="submit" class="btn btn-primary btn-lg btn-full" id="add-cart-main">
            <i class="bi bi-bag-plus"></i> Add to Cart
          </button>
        </form>

        <form action="<?= url('cart/buy-now') ?>" method="POST">
          <?= csrf_field() ?>
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <button type="submit" class="btn btn-success btn-lg btn-full">
            <i class="bi bi-lightning-charge-fill"></i> Buy Now
          </button>
        </form>

        <!-- Preview Download -->
        <?php if ($product['preview_file']): ?>
        <a href="<?= url('products/' . $product['slug'] . '?preview=1') ?>" class="btn btn-ghost btn-full" style="margin-top:.5rem;border:1px solid var(--border)">
          <i class="bi bi-eye"></i> Free Preview
        </a>
        <?php endif; ?>

        <!-- Meta Info -->
        <div style="margin-top:1.25rem;border-top:1px solid var(--border);padding-top:1.25rem;display:flex;flex-direction:column;gap:.625rem">
          <?php $infoItems = [['bi-shield-check', 'Secure payment guaranteed'], ['bi-lightning-charge', 'Instant digital delivery'], ['bi-arrow-repeat', '14-day money-back guarantee'], ['bi-download', 'Download limit: ' . $product['download_limit'] . ' times'], ['bi-clock', 'Link valid for ' . setting('download_expiry_hours', '48') . ' hours']]; ?>
          <?php foreach ($infoItems as [$icon, $label]): ?>
          <div style="display:flex;align-items:center;gap:.625rem;font-size:.82rem;color:var(--text-muted)">
            <i class="bi <?= $icon ?>" style="color:var(--success);font-size:.9rem;width:16px"></i>
            <?= $label ?>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Social Share -->
        <div style="margin-top:1.25rem;border-top:1px solid var(--border);padding-top:1.25rem">
          <div style="font-size:.8rem;font-weight:600;color:var(--text-dim);margin-bottom:.625rem">Share this product</div>
          <div style="display:flex;gap:.5rem">
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($product['title']) ?>&url=<?= urlencode(url('products/' . $product['slug'])) ?>" target="_blank" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)"><i class="bi bi-twitter-x"></i></a>
            <a href="https://wa.me/?text=<?= urlencode($product['title'] . ' - ' . url('products/' . $product['slug'])) ?>" target="_blank" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)"><i class="bi bi-whatsapp"></i></a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(url('products/' . $product['slug'])) ?>" target="_blank" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)"><i class="bi bi-linkedin"></i></a>
            <button onclick="navigator.clipboard.writeText(window.location.href);showToast('Link copied!','success')" class="btn btn-ghost btn-sm" style="border:1px solid var(--border)"><i class="bi bi-link-45deg"></i></button>
          </div>
        </div>
      </div>

      <!-- Version info -->
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1rem;font-size:.8rem;color:var(--text-muted);display:flex;align-items:center;gap:.5rem">
        <i class="bi bi-code-square" style="color:var(--accent)"></i>
        Version <?= e($product['version'] ?? '1.0') ?> — Free updates for all buyers
      </div>
    </div>
  </div>

  <!-- Related Products -->
  <?php if (!empty($related)): ?>
  <div style="margin-top:4rem">
    <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:1.5rem">You May Also Like</h2>
    <div class="products-grid">
      <?php foreach ($related as $p): ?>
      <?php include dirname(dirname(dirname(__FILE__))) . '/partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Review Modal -->
<?php if ($hasPurchased && !$userReview): ?>
<div class="modal-overlay" id="review-modal">
  <div class="modal-box">
    <h3 style="margin-bottom:1.5rem">Write Your Review</h3>
    <form action="<?= url('api/reviews') ?>" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
      <div class="form-group">
        <div class="form-label">Rating</div>
        <div style="display:flex;flex-direction:row-reverse;justify-content:flex-end;font-size:1.75rem">
          <?php for ($i = 5; $i >= 1; $i--): ?>
          <input type="radio" name="rating" value="<?= $i ?>" id="star-<?= $i ?>" class="star-input">
          <label for="star-<?= $i ?>" class="star-label" style="cursor:pointer">★</label>
          <?php endfor; ?>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Your Review</label>
        <textarea name="body" class="form-control" placeholder="Share your experience…" rows="4"></textarea>
      </div>
      <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:1.5rem">
        <button type="button" class="btn btn-ghost" data-close-modal="review-modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit Review</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<style>
@media (max-width: 860px) {
  .container > [style*="grid-template-columns:1fr 380px"] {
    grid-template-columns: 1fr !important;
  }
  [style*="position:sticky;top:80px"] { position: static !important; }
}
</style>
