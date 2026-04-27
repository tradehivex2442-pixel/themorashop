<?php
// THEMORA SHOP — Product Listing Page
$sym = setting('currency_symbol', '$');
?>
<!-- Breadcrumb -->
<div style="background:var(--bg-2);border-bottom:1px solid var(--border);padding:.75rem 0">
  <div class="container" style="font-size:.8rem;color:var(--text-dim)">
    <a href="<?= url('/') ?>" style="color:var(--text-dim)">Home</a> <i class="bi bi-chevron-right" style="font-size:.7rem;margin:0 .25rem"></i>
    <span style="color:var(--text)">Products</span>
    <?php if (!empty($filters['search'])): ?>
      <i class="bi bi-chevron-right" style="font-size:.7rem;margin:0 .25rem"></i>
      <span style="color:var(--text)">Search: <?= e($filters['search']) ?></span>
    <?php endif; ?>
  </div>
</div>

<!-- Header Hero Section -->
<div class="hero-bg" style="height: 200px; position: relative; overflow: hidden; display: flex; align-items: center; background: var(--bg-2); border-bottom: 1px solid var(--border);">
    <div class="hero-dots"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div style="max-width: 600px;">
            <div class="hero-eyebrow" style="margin-bottom: 0.5rem;"><i class="bi bi-grid"></i> Explore Our Collection</div>
            <h1 style="font-size: 2.25rem; margin-bottom: 0.5rem; letter-spacing: -0.03em;">Discover <span class="text-gradient">Premium Assets</span></h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Curated high-quality digital products for your next big project.</p>
        </div>
    </div>
</div>

<div class="container products-page-layout" style="display: grid; grid-template-columns: 280px 1fr; gap: 2.5rem; padding-top: 2rem; padding-bottom: 5rem; align-items: start;">
    
    <!-- ── Filter Sidebar ────────────────────────────────────── -->
    <aside class="aside-filter">
        <div class="card" style="padding: 1.5rem; position: sticky; top: 100px; background: rgba(var(--bg-rgb), 0.5); backdrop-filter: blur(10px);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                <h3 style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                    <i class="bi bi-filter-left" style="color: var(--accent); font-size: 1.1rem;"></i> Filters
                </h3>
                <?php if (count(array_filter($_GET)) > 0): ?>
                    <a href="<?= url('products') ?>" style="font-size: 0.75rem; color: var(--accent-light);">Reset All</a>
                <?php endif; ?>
            </div>

            <form method="GET" action="<?= url('products') ?>" id="filter-form">
                <!-- Search Group -->
                <div class="filter-group" style="margin-bottom: 2rem;">
                    <label class="filter-title">Keyword</label>
                    <div style="position: relative;">
                        <input type="text" name="q" class="form-control" placeholder="Search..." value="<?= e($filters['search']) ?>" style="padding-left: 2.5rem;">
                        <i class="bi bi-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 0.85rem;"></i>
                    </div>
                </div>

                <!-- Categories Group -->
                <div class="filter-group" style="margin-bottom: 2rem;">
                    <label class="filter-title">Product Category</label>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label class="category-pill-item" style="cursor: pointer; display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); transition: var(--transition);">
                            <input type="radio" name="category" value="" style="display: none;" <?= !$filters['category'] ? 'checked' : '' ?> onchange="this.form.submit()">
                            <div class="radio-custom" style="width: 16px; height: 16px; border: 2px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <div class="radio-inner" style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%; opacity: <?= !$filters['category'] ? '1' : '0' ?>;"></div>
                            </div>
                            <span style="font-size: 0.875rem; color: <?= !$filters['category'] ? 'var(--text)' : 'var(--text-muted)' ?>;">All Collection</span>
                        </label>
                        <?php foreach ($categories as $cat): ?>
                            <label class="category-pill-item" style="cursor: pointer; display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); transition: var(--transition);">
                                <input type="radio" name="category" value="<?= e($cat['slug']) ?>" style="display: none;" <?= $filters['category'] === $cat['slug'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                <div class="radio-custom" style="width: 16px; height: 16px; border: 2px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <div class="radio-inner" style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%; opacity: <?= $filters['category'] === $cat['slug'] ? '1' : '0' ?>;"></div>
                                </div>
                                <span style="font-size: 0.875rem; color: <?= $filters['category'] === $cat['slug'] ? 'var(--text)' : 'var(--text-muted)' ?>;"><?= e($cat['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="filter-group" style="margin-bottom: 2rem;">
                    <label class="filter-title">Price Filter</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="position: relative; flex: 1;">
                            <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 0.75rem;"><?= $sym ?></span>
                            <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?= $filters['minPrice'] ?: '' ?>" style="padding-left: 1.5rem; font-size: 0.85rem;">
                        </div>
                        <span style="color: var(--border);">/</span>
                        <div style="position: relative; flex: 1;">
                            <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 0.75rem;"><?= $sym ?></span>
                            <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?= $filters['maxPrice'] < 9999 ? $filters['maxPrice'] : '' ?>" style="padding-left: 1.5rem; font-size: 0.85rem;">
                        </div>
                    </div>
                </div>

                <!-- Star Rating -->
                <div class="filter-group" style="margin-bottom: 2.5rem;">
                    <label class="filter-title">Min Rating</label>
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <?php for ($r = 5; $r >= 1; $r--): ?>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="rating" value="<?= $r ?>" style="accent-color: var(--accent);" <?= (int)$filters['rating'] === $r ? 'checked' : '' ?> onchange="this.form.submit()">
                                <div style="color: #f59e0b; font-size: 0.8rem; letter-spacing: 1px;">
                                    <?= str_repeat('★', $r) ?><span style="color: var(--surface-2);"><?= str_repeat('★', 5 - $r) ?></span>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-dim);">& up</span>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full shadow-glow">Go Search</button>
            </form>
        </div>
    </aside>

    <!-- ── Product Listings ─────────────────────────────────── -->
    <main>
        <!-- Top Info Bar -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; background: var(--surface); padding: 0.75rem 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                Showing <span style="color: var(--text); font-weight: 600;"><?= count($products) ?></span> of <span style="color: var(--text); font-weight: 600;"><?= $pagination['total'] ?></span> products
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <label style="font-size: 0.8rem; color: var(--text-dim); white-space: nowrap;">Sort by:</label>
                <select class="form-control" style="width: auto; height: 32px; padding: 0 2rem 0 0.75rem; font-size: 0.8rem; cursor: pointer; border: none; background: transparent;" onchange="window.location.href=this.value">
                    <?php foreach (['latest' => 'Newest First', 'price_asc' => 'Price: Low-High', 'price_desc' => 'Price: High-Low', 'popular' => 'Most Popular', 'rating' => 'Top Rated'] as $val => $label): ?>
                        <option value="<?= url('products?' . http_build_query(array_merge($_GET, ['sort' => $val]))) ?>" <?= ($filters['sort'] ?? 'latest') === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <!-- Empty State -->
            <div class="card" style="padding: 5rem 2rem; text-align: center; background: rgba(var(--bg-rgb), 0.3);">
                <div style="width: 80px; height: 80px; background: var(--bg-3); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <i class="bi bi-search" style="font-size: 2rem; color: var(--text-dim);"></i>
                </div>
                <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">No products matched</h2>
                <p style="color: var(--text-muted); margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">We couldn't find any products matching your current filters. Try adjusting your preferences or starting over.</p>
                <a href="<?= url('products') ?>" class="btn btn-primary">Clear All Filters</a>
            </div>
        <?php else: ?>
            <!-- Grid -->
            <div class="products-grid reveal" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
                <?php foreach ($products as $p): ?>
                <?php 
                    $now = date('Y-m-d H:i:s');
                    $effectivePrice = ($p['flash_sale_price'] && ($p['flash_sale_ends'] ?? '') > $now) ? $p['flash_sale_price'] : $p['price'];
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

                    <?php if ($discount >= 10): ?>
                      <span class="product-badge badge-sale"><?= $discount ?>% OFF</span>
                    <?php elseif ($isOnSale): ?>
                      <span class="product-badge badge-sale">SALE</span>
                    <?php elseif ($effectivePrice == 0): ?>
                      <span class="product-badge badge-free">FREE</span>
                    <?php endif; ?>

                    <div class="product-card-actions">
                      <a href="<?= url('products/' . $p['slug']) ?>" class="product-action-btn" aria-label="Quick view"><i class="bi bi-eye"></i></a>
                    </div>
                  </div>

                  <div class="product-card-body">
                    <div class="product-category"><?= e($p['category_name'] ?? 'Digital') ?></div>
                    <a href="<?= url('products/' . $p['slug']) ?>" style="text-decoration:none">
                      <h3 class="product-title"><?= e($p['title']) ?></h3>
                    </a>

                    <?php if (($p['avg_rating'] ?? 0) > 0): ?>
                    <div class="product-rating">
                      <span class="rating-stars"><?= stars((float)$p['avg_rating']) ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="product-price-row">
                      <div>
                        <span class="product-price"><?= $sym ?><?= number_format((float)$effectivePrice, 2) ?></span>
                      </div>
                      <button class="add-cart-btn" data-add-cart="<?= $p['id'] ?>"><i class="bi bi-bag-plus"></i></button>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($pagination['pages'] > 1): ?>
            <div class="pagination" style="margin-top: 4rem; justify-content: center; gap: 0.5rem; display: flex;">
                <?php if ($pagination['has_prev']): ?>
                    <a href="<?= url('products?' . http_build_query(array_merge($_GET, ['page' => $pagination['current'] - 1]))) ?>" class="page-btn"><i class="bi bi-chevron-left"></i></a>
                <?php endif; ?>

                <?php for ($i = max(1, $pagination['current'] - 2); $i <= min($pagination['pages'], $pagination['current'] + 2); $i++): ?>
                    <a href="<?= url('products?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>" class="page-btn <?= $i === $pagination['current'] ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($pagination['has_next']): ?>
                    <a href="<?= url('products?' . http_build_query(array_merge($_GET, ['page' => $pagination['current'] + 1]))) ?>" class="page-btn"><i class="bi bi-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
.category-pill-item:hover {
    background: var(--surface-2);
}
.category-pill-item:has(input:checked) {
    background: var(--accent-glow);
}
.category-pill-item:has(input:checked) .radio-custom {
    border-color: var(--accent);
}
.shadow-glow {
    box-shadow: 0 10px 20px -10px var(--accent-glow);
}

@media (max-width: 992px) {
    .products-page-layout {
        grid-template-columns: 1fr !important;
    }
    .aside-filter {
        display: none;
    }
}
/* Safety Fallback: Ensure visibility if JS fails */
.reveal { opacity: 1 !important; transform: none !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const revealItems = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    revealItems.forEach(item => {
        // Remove the safety fallback once JS is confirmed running
        item.style.opacity = "0";
        item.style.transform = "translateY(20px)";
        observer.observe(item);
    });
});
</script>
