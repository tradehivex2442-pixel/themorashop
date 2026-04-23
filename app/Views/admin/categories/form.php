<div class="page-header">
    <div>
        <div class="page-header-title"><?= $category ? 'Edit Category' : 'Add New Category' ?></div>
        <div class="page-header-sub"><?= $category ? 'Editing: ' . e($category['name']) : 'Fill in the details to create a new category' ?></div>
    </div>
    <div class="page-header-actions">
        <a href="<?= url('admin/categories') ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="table-card" style="max-width: 800px; margin: 0 auto;">
    <form action="<?= $category ? url('admin/categories/' . $category['id']) : url('admin/categories/store') ?>" method="POST" style="padding: 2rem;">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label class="form-label">Category Name *</label>
            <input type="text" name="name" class="form-control" value="<?= e($category['name'] ?? '') ?>" placeholder="e.g. Mobile Apps" required>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.25rem;">
            <div class="form-group">
                <label class="form-label">Slug (Optional)</label>
                <input type="text" name="slug" class="form-control" value="<?= e($category['slug'] ?? '') ?>" placeholder="e.g. mobile-apps">
                <div style="font-size: .75rem; color: var(--text-dim); margin-top: .375rem;">Leave empty to auto-generate.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Icon (Bootstrap Icon Class)</label>
                <input type="text" name="icon" class="form-control" value="<?= e($category['icon'] ?? 'bi-folder') ?>" placeholder="e.g. bi-phone">
                <div style="font-size: .75rem; color: var(--text-dim); margin-top: .375rem;">Example: <code>bi-box-seam</code></div>
            </div>
        </div>

        <div class="form-group" style="margin-top: 1.25rem;">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="<?= e($category['sort_order'] ?? '0') ?>">
        </div>

        <div style="margin-top: 2.5rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> <?= $category ? 'Update Category' : 'Create Category' ?>
            </button>
            <a href="<?= url('admin/categories') ?>" class="btn btn-ghost" style="border: 1px solid var(--border)">Cancel</a>
        </div>
    </form>
</div>
