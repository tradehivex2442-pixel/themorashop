<?php
// THEMORA SHOP — Admin Product Form (Create / Edit)
$isEdit = $product !== null;
$title  = $isEdit ? 'Edit Product' : 'Add New Product';
?>
<div class="page-header">
  <div>
    <div class="page-header-title"><?= $title ?></div>
    <div class="page-header-sub"><?= $isEdit ? 'Editing: ' . e($product['title']) : 'Fill in the details to create a new product' ?></div>
  </div>
  <div class="page-header-actions">
    <a href="<?= url('admin/products') ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>

<form method="POST" action="<?= $isEdit ? url('admin/products/' . $product['id']) : url('admin/products/store') ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

    <!-- Left: Main Fields -->
    <div style="display:flex;flex-direction:column;gap:1.25rem">

      <div class="table-card" style="padding:1.5rem">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid var(--border)">Product Information</h3>

        <div class="form-group">
          <label class="form-label">Product Title *</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. Pro UI Kit 2024" value="<?= e($product['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">Short Description</label>
          <input type="text" name="short_desc" class="form-control" placeholder="One line summary (shown on cards)" value="<?= e($product['short_desc'] ?? '') ?>" maxlength="200">
        </div>

        <div class="form-group">
          <label class="form-label">Full Description</label>
          <textarea name="description" class="form-control" rows="8" placeholder="Detailed product description…" data-autoresize><?= e($product['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Demo Video URL</label>
          <input type="url" name="demo_video_url" class="form-control" placeholder="YouTube or direct video URL" value="<?= e($product['demo_video_url'] ?? '') ?>">
        </div>
      </div>

      <!-- SEO -->
      <div class="table-card" style="padding:1.5rem">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid var(--border)"><i class="bi bi-search" style="color:var(--accent)"></i> SEO & Meta</h3>
        <div class="form-group">
          <label class="form-label">Meta Title</label>
          <input type="text" name="meta_title" class="form-control" placeholder="SEO title (60 chars)" value="<?= e($product['meta_title'] ?? '') ?>" maxlength="80">
        </div>
        <div class="form-group">
          <label class="form-label">Meta Description</label>
          <textarea name="meta_desc" class="form-control" rows="3" placeholder="SEO description (160 chars)" maxlength="200"><?= e($product['meta_desc'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Files -->
      <div class="table-card" style="padding:1.5rem">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid var(--border)"><i class="bi bi-folder" style="color:var(--accent)"></i> Product Files</h3>

        <div class="form-group">
          <label class="form-label">Main Product File <?= $isEdit ? '(leave empty to keep current)' : '*' ?></label>
          <input type="file" name="product_file" class="form-control" <?= !$isEdit ? 'required' : '' ?>>
          <?php if ($isEdit && $product['file_path']): ?>
          <div style="font-size:.78rem;color:var(--success);margin-top:.375rem"><i class="bi bi-check-circle"></i> Current file: <?= e(basename($product['file_path'])) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label">Free Preview / Sample File</label>
          <input type="file" name="preview_file" class="form-control">
        </div>
      </div>

      <!-- Tags -->
      <div class="table-card" style="padding:1.5rem">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid var(--border)"><i class="bi bi-tags" style="color:var(--accent)"></i> Tags</h3>
        <label class="form-label">Product Tags (press Enter or comma to add)</label>
        <div id="tags-display" style="display:flex;flex-wrap:wrap;gap:.375rem;padding:.5rem 0;min-height:28px"></div>
        <input type="text" id="tags-input" class="form-control" placeholder="Add a tag…">
        <input type="hidden" name="tags" id="tags-value" value="<?= e($product['tags'] ?? '') ?>">
      </div>
    </div>

    <!-- Right: Settings Sidebar -->
    <div style="display:flex;flex-direction:column;gap:1.25rem">

      <!-- Thumbnail -->
      <div class="table-card" style="padding:1.25rem">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1rem">Thumbnail</h3>
        <?php if ($isEdit && $product['thumbnail']): ?>
        <img id="thumb-preview" src="<?= e($product['thumbnail']) ?>" style="width:100%;border-radius:10px;margin-bottom:.875rem;object-fit:cover;aspect-ratio:16/10">
        <?php else: ?>
        <div id="thumb-preview-placeholder" style="width:100%;aspect-ratio:16/10;background:var(--bg-3);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:.875rem;color:var(--text-dim);font-size:1.5rem">
          <i class="bi bi-image"></i>
        </div>
        <img id="thumb-preview" style="display:none;width:100%;border-radius:10px;margin-bottom:.875rem;object-fit:cover;aspect-ratio:16/10">
        <?php endif; ?>
        <input type="file" name="thumbnail" class="form-control" accept="image/*" data-preview="thumb-preview">
      </div>

      <!-- Status & Pricing -->
      <div class="table-card" style="padding:1.25rem">
        <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1rem">Pricing & Status</h3>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <?php foreach (['active' => 'Active', 'draft' => 'Draft', 'inactive' => 'Inactive'] as $val => $label): ?>
            <option value="<?= $val ?>" <?= ($product['status'] ?? 'active') === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Product Category *</label>
          <select name="category_id" class="form-control" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($product['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Price *</label>
          <div class="input-group">
            <span style="padding:0 .875rem;display:flex;align-items:center;color:var(--text-muted);background:var(--bg-3);border-right:1px solid var(--border)"><?= setting('currency_symbol', '$') ?></span>
            <input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="0.00" value="<?= $product['price'] ?? '' ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Original Price (for strikethrough)</label>
          <div class="input-group">
            <span style="padding:0 .875rem;display:flex;align-items:center;color:var(--text-muted);background:var(--bg-3);border-right:1px solid var(--border)"><?= setting('currency_symbol', '$') ?></span>
            <input type="number" name="original_price" class="form-control" step="0.01" min="0" placeholder="0.00" value="<?= $product['original_price'] ?? '' ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Version</label>
          <input type="text" name="version" class="form-control" placeholder="1.0" value="<?= e($product['version'] ?? '1.0') ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Max Downloads per Order</label>
          <input type="number" name="download_limit" class="form-control" min="1" max="99" value="<?= $product['download_limit'] ?? 5 ?>">
        </div>
      </div>

      <!-- Submit -->
      <div style="display:flex;flex-direction:column;gap:.75rem">
        <button type="submit" class="btn btn-primary btn-full btn-lg">
          <i class="bi bi-<?= $isEdit ? 'check-lg' : 'plus-lg' ?>"></i>
          <?= $isEdit ? 'Update Product' : 'Create Product' ?>
        </button>
        <a href="<?= url('admin/products') ?>" class="btn btn-ghost btn-full" style="border:1px solid var(--border)">Cancel</a>
      </div>
    </div>
  </div>
</form>
