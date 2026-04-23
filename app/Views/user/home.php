<?php
// THEMORA SHOP — Home / Landing Page
$sym = setting('currency_symbol', '$');
?>

<!-- ── HERO ──────────────────────────────────────────────────── -->
<section class="hero" id="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid-lines"></div>
  <div class="container" style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:center">
    <div class="hero-content">
      <div class="hero-eyebrow">
        <i class="bi bi-lightning-charge-fill"></i>
        <span>60+ Premium Digital Products</span>
      </div>
      <h1 class="hero-title">
        Download. Use.<br>
        <span class="text-gradient">Create Magic.</span>
      </h1>
      <p class="hero-desc">
        The #1 marketplace for premium digital goods — templates, ebooks, software, presets, and courses. One-click purchase, instant download.
      </p>
      <div class="hero-actions">
        <a href="<?= url('products') ?>" class="btn btn-primary btn-lg" id="explore-btn">
          <i class="bi bi-grid-fill"></i> Explore Products
        </a>
        <?php if (!logged_in()): ?>
        <a href="<?= url('signup') ?>" class="btn btn-secondary btn-lg">
          <i class="bi bi-person-plus"></i> Join Free
        </a>
        <?php endif; ?>
      </div>
      <div class="hero-stats">
        <div>
          <span class="hero-stat-num"><?= number_format($stats['products']) ?>+</span>
          <span class="hero-stat-label">Products</span>
        </div>
        <div>
          <span class="hero-stat-num"><?= number_format($stats['buyers']) ?>+</span>
          <span class="hero-stat-label">Happy Buyers</span>
        </div>
        <div>
          <span class="hero-stat-num"><?= number_format($stats['rating'], 1) ?>★</span>
          <span class="hero-stat-label">Avg Rating</span>
        </div>
      </div>
    </div>

    <!-- Hero Visual: Floating product cards -->
    <div class="hero-visual" style="display:flex;flex-direction:column;gap:1rem;position:relative">
      <?php 
      // Use real featured products for the hero visual
      $heroDisplay = array_slice($featured, 0, 3);
      $margins = ['2rem', '0rem', '3rem'];
      foreach ($heroDisplay as $i => $p): 
      ?>
      <div class="float-card" style="position:relative;margin-left:<?= $margins[$i] ?? '0rem' ?>">
        <div style="display:flex;align-items:center;gap:.875rem">
          <div style="width:52px;height:52px;border-radius:10px;background:var(--bg-3);overflow:hidden;flex-shrink:0">
            <img src="<?= asset($p['thumbnail']) ?>" alt="" style="width:100%;height:100%;object-fit:cover" onerror="this.src='https://ui-avatars.com/api/?name=P&background=6366f1&color=fff'">
          </div>
          <div>
            <div style="font-size:.85rem;font-weight:600;margin-bottom:.1rem"><?= e($p['title']) ?></div>
            <div style="font-size:.75rem;color:var(--text-dim)"><?= e($p['category_name'] ?? 'Digital') ?></div>
          </div>
          <div style="margin-left:auto;font-size:1rem;font-weight:800;color:var(--accent)"><?= $sym ?><?= number_format($p['price'], 0) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
      <!-- Decorative glow -->
      <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 50%,rgba(99,102,241,.1),transparent 70%);pointer-events:none;border-radius:50%"></div>
    </div>
  </div>
</section>

<!-- ── PRODUCT CATEGORIES ────────────────────────────────────── -->
<section class="section-sm" style="padding-top:0">
  <div class="container text-center">
    <div class="section-header">
      <span class="section-eyebrow">Explore</span>
      <h2>Product Categories</h2>
    </div>
    <div class="category-grid">
      <a href="<?= url('products') ?>" class="category-item">
        <div class="category-icon">
          <i class="bi bi-grid"></i>
        </div>
        <div class="category-name">All Products</div>
      </a>
      <?php foreach ($categories as $cat): ?>
      <a href="<?= url('products?category=' . e($cat['slug'])) ?>" class="category-item">
        <div class="category-icon">
          <i class="bi <?= e($cat['icon'] ?? 'bi-box') ?>"></i>
        </div>
        <div class="category-name"><?= e($cat['name']) ?></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── FEATURED PRODUCTS ──────────────────────────────────────── -->
<section class="section" style="padding-top:0" id="featured">
  <div class="container">
    <div class="section-header reveal" style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:1rem">
      <div>
        <span class="section-eyebrow">⭐ Featured</span>
        <h2>Best-Selling Products</h2>
        <p style="color:var(--text-muted);margin-top:.5rem;font-size:.925rem">Hand-picked products loved by thousands of creators</p>
      </div>
      <a href="<?= url('products') ?>" class="btn btn-secondary">View All <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="products-grid reveal">
      <?php foreach ($featured as $p): ?>
      <?php include __DIR__ . '/../partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── TRENDING CAROUSEL ──────────────────────────────────────── -->
<section class="section" style="background:var(--bg-2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)" id="trending">
  <div class="container">
    <div class="section-header reveal" style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:1rem">
      <div>
        <span class="section-eyebrow">🔥 Trending Now</span>
        <h2>Top Rated This Week</h2>
      </div>
      <div class="carousel-controls">
        <button class="carousel-btn" data-carousel-prev aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
        <button class="carousel-btn" data-carousel-next aria-label="Next"><i class="bi bi-chevron-right"></i></button>
      </div>
    </div>

    <div data-carousel data-autoplay="true">
      <div class="products-carousel">
        <?php foreach ($trending as $p): ?>
        <div style="width:280px;flex-shrink:0">
          <?php include __DIR__ . '/../partials/product-card.php'; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ── WHY THEMORA ────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="section-eyebrow">Why Choose Us</span>
      <h2>Everything You Need to Create</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem">
      <?php
      $features = [
        ['bi-lightning-charge', 'Instant Delivery', 'Download files immediately after payment — no waiting.'],
        ['bi-shield-check', 'Secure Payments', 'Razorpay, Stripe, PayPal — all with full encryption.'],
        ['bi-arrow-clockwise', '14-Day Refund', 'Unsatisfied? Get a full refund within 14 days.'],
        ['bi-patch-check', 'Verified Sellers', 'Every product is quality-checked before listing.'],
        ['bi-headset', '24/7 Support', 'Live chat, tickets — we\'re always here to help.'],
        ['bi-stars', 'Free Updates', 'Get all future updates for your purchased products free.'],
      ];
      foreach ($features as [$icon, $title, $desc]):
      ?>
      <div class="card reveal" style="padding:1.5rem;text-align:center">
        <div style="width:52px;height:52px;border-radius:14px;background:rgba(99,102,241,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:var(--accent);font-size:1.4rem">
          <i class="bi <?= $icon ?>"></i>
        </div>
        <h3 style="font-size:1rem;margin-bottom:.5rem"><?= $title ?></h3>
        <p style="font-size:.85rem;color:var(--text-muted);line-height:1.6"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── AI RECOMMENDATIONS ─────────────────────────────────────── -->
<?php if (!empty($recommendations)): ?>
<section class="section" style="background:var(--bg-2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-eyebrow">🤖 For You</span>
      <h2>Recommended Picks</h2>
      <p style="color:var(--text-muted);margin-top:.5rem;font-size:.925rem">Personalized suggestions based on your interests</p>
    </div>
    <div class="products-grid reveal">
      <?php foreach ($recommendations as $p): ?>
      <?php include __DIR__ . '/../partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── TESTIMONIALS ───────────────────────────────────────────── -->
<?php if (!empty($testimonials)): ?>
<section class="section">
  <div class="container">
    <div class="section-header text-center reveal">
      <span class="section-eyebrow">💬 Testimonials</span>
      <h2>Loved by 1,000+ Creators</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem">
      <?php foreach ($testimonials as $t): ?>
      <div class="testimonial-card reveal">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem">
          <?php if (!empty($t['avatar'])): ?>
            <img src="<?= e($t['avatar']) ?>" class="testimonial-avatar" alt="<?= e($t['user_name']) ?>">
          <?php else: ?>
            <div class="testimonial-avatar" style="background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-weight:700">
              <?= strtoupper(substr($t['user_name'] ?? 'U', 0, 1)) ?>
            </div>
          <?php endif; ?>
          <div>
            <div style="font-weight:600;font-size:.9rem"><?= e($t['user_name']) ?></div>
            <div style="font-size:.75rem;color:var(--text-dim)">Verified buyer · <?= e($t['product_title']) ?></div>
          </div>
        </div>
        <div style="color:var(--warning);margin-bottom:.75rem;font-size:.9rem">
          <?= stars((float)($t['rating'] ?? 5)) ?>
        </div>
        <p style="font-size:.875rem;color:var(--text-muted);line-height:1.7;font-style:italic">
          "<?= e($t['body']) ?>"
        </p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── FAQ ───────────────────────────────────────────────────── -->
<?php if (!empty($faqs)): ?>
<section class="section" style="background:var(--bg-2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
  <div class="container container-md">
    <div class="section-header text-center reveal">
      <span class="section-eyebrow">❓ FAQ</span>
      <h2>Frequently Asked Questions</h2>
    </div>
    <div class="reveal">
      <?php foreach ($faqs as $faq): ?>
      <div class="faq-item">
        <button class="faq-question">
          <?= e($faq['question']) ?>
          <i class="bi bi-chevron-down faq-arrow"></i>
        </button>
        <div class="faq-answer">
          <div class="faq-answer-inner"><?= e($faq['answer']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center" style="margin-top:2rem">
      <a href="<?= url('faq') ?>" class="btn btn-secondary">View All FAQs <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── CTA BANNER ─────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="reveal" style="background:linear-gradient(135deg,var(--accent-dark),var(--accent),#a855f7);border-radius:var(--radius-lg);padding:4rem 3rem;text-align:center;position:relative;overflow:hidden">
      <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:40px 40px"></div>
      <div style="position:relative;z-index:1">
        <span style="font-size:2.5rem">🚀</span>
        <h2 style="color:white;margin:1rem 0 .75rem;font-size:2rem">Ready to Download Something Amazing?</h2>
        <p style="color:rgba(255,255,255,.8);margin-bottom:2rem;font-size:1rem">Join thousands of creators. Get your first product with code <strong>WELCOME20</strong> for 20% off.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
          <a href="<?= url('products') ?>" class="btn btn-lg" style="background:white;color:var(--accent-dark);font-weight:700">
            <i class="bi bi-grid-fill"></i> Browse Products
          </a>
          <?php if (!logged_in()): ?>
          <a href="<?= url('signup') ?>" class="btn btn-lg" style="background:rgba(255,255,255,.15);color:white;border:1.5px solid rgba(255,255,255,.3)">
            <i class="bi bi-person-plus"></i> Sign Up Free
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
