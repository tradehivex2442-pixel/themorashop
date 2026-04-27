<?php
// THEMORA SHOP — Admin Products List
$sym = setting('currency_symbol', '$');
?>
<div class="page-header">
  <div>
    <div class="page-header-title">Products</div>
    <div class="page-header-sub"><?= number_format($pagination['total']) ?> products total</div>
  </div>
  <div class="page-header-actions">
    <a href="<?= url('admin/products/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Product</a>
  </div>
</div>

<!-- Filters -->
<div class="filter-bar">
  <form method="GET" action="<?= url('admin/products') ?>" style="display:flex;gap:.75rem;flex-wrap:wrap">
    <input type="text" name="q" class="form-control" placeholder="Search products…" value="<?= e($filters['search']) ?>">
    <select name="category" class="form-control">
      <option value="">All Product Categories</option>
      <?php foreach ($categories as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $filters['category'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="status" class="form-control">
      <option value="">All Status</option>
      <option value="active"   <?= $filters['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
      <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
      <option value="draft"    <?= $filters['status'] === 'draft'    ? 'selected' : '' ?>>Draft</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Search</button>
    <a href="<?= url('admin/products') ?>" class="btn btn-ghost btn-sm">Clear</a>
  </form>
</div>

<div class="table-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width:50px"><input type="checkbox" id="select-all"></th>
          <th>Product</th>
          <th>Product Category</th>
          <th>Price</th>
          <th>Sales</th>
          <th>Rating</th>
          <th>Status</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><input type="checkbox" class="row-checkbox" value="<?= $p['id'] ?>"></td>
          <td>
            <div style="display:flex;align-items:center;gap:.75rem">
              <?php if ($p['thumbnail']): ?>
                <img src="<?= asset($p['thumbnail']) ?>" class="product-thumb-sm" alt="" onerror="this.src='https://ui-avatars.com/api/?name=P&background=6366f1&color=fff'">
              <?php else: ?>
                <div class="product-thumb-sm" style="background:linear-gradient(135deg,var(--accent-dark),#a855f7);display:flex;align-items:center;justify-content:center"><i class="bi bi-box" style="color:rgba(255,255,255,.7)"></i></div>
              <?php endif; ?>
              <div>
                <div style="font-weight:600;font-size:.875rem"><?= e($p['title']) ?></div>
                <div style="font-size:.73rem;color:var(--text-dim)"><?= e($p['slug']) ?></div>
              </div>
            </div>
          </td>
          <td style="font-size:.85rem;color:var(--text-muted)"><?= e($p['category_name'] ?? '—') ?></td>
          <td style="font-weight:700"><?= $sym ?><?= number_format((float)$p['price'], 2) ?></td>
          <td><?= number_format($p['total_sales']) ?></td>
          <td style="color:#f59e0b"><?= $p['avg_rating'] > 0 ? '★ ' . number_format($p['avg_rating'], 1) : '—' ?></td>
          <td>
            <?php $sMap = ['active'=>'badge-success','inactive'=>'badge-dark','draft'=>'badge-warning']; ?>
            <span class="badge <?= $sMap[$p['status']] ?? 'badge-dark' ?>"><?= ucfirst($p['status']) ?></span>
          </td>
          <td style="font-size:.8rem;color:var(--text-dim)"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
          <td>
            <div class="action-btns">
              <a href="<?= url('products/' . $p['slug']) ?>" target="_blank" class="action-btn" title="View"><i class="bi bi-eye"></i></a>
              <a href="<?= url('admin/products/' . $p['id'] . '/edit') ?>" class="action-btn" title="Edit"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="<?= url('admin/products/' . $p['id'] . '/delete') ?>" style="display:inline">
                <?= csrf_field() ?>
                <button type="submit" class="action-btn danger" data-confirm="Delete '<?= e($p['title']) ?>'?" title="Delete"><i class="bi bi-trash3"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
        <tr><td colspan="9" style="text-align:center;padding:3rem;color:var(--text-dim)">No products found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pagination['pages'] > 1): ?>
  <div style="padding:1rem;border-top:1px solid var(--border)">
    <div class="pagination" style="margin:0">
      <?php if ($pagination['has_prev']): ?><a href="<?= url('admin/products?' . http_build_query(array_merge($_GET, ['page' => $pagination['current'] - 1]))) ?>" class="page-btn"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
      <?php for ($i = max(1, $pagination['current'] - 2); $i <= min($pagination['pages'], $pagination['current'] + 2); $i++): ?>
        <a href="<?= url('admin/products?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>" class="page-btn <?= $i === $pagination['current'] ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($pagination['has_next']): ?><a href="<?= url('admin/products?' . http_build_query(array_merge($_GET, ['page' => $pagination['current'] + 1]))) ?>" class="page-btn"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
