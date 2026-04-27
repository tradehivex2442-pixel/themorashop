<div class="page-header">
    <div>
        <div class="page-header-title">Categories</div>
        <div class="page-header-sub"><?= number_format($total) ?> categories total</div>
    </div>
    <div class="page-header-actions">
        <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add New Category
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 50px;">Icon</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th style="width: 100px;">Sort</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td style="font-size: 1.1rem;">
                        <i class="bi <?= e($cat['icon'] ?: 'bi-folder') ?>" style="color:var(--accent)"></i>
                    </td>
                    <td><div style="font-weight:600"><?= e($cat['name']) ?></div></td>
                    <td><code><?= e($cat['slug']) ?></code></td>
                    <td><?= $cat['sort_order'] ?></td>
                    <td>
                        <div class="action-btns" style="justify-content: flex-end;">
                            <a href="<?= url('admin/categories/' . $cat['id'] . '/edit') ?>" class="action-btn" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= url('admin/categories/' . $cat['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this category?')" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="action-btn danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-dim);">
                        No categories found. Start by adding one!
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
