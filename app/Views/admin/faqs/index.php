<?php
// THEMORA SHOP — Admin FAQs + Coupons Management
?>
<div class="page-header">
  <div>
    <div class="page-header-title">FAQs</div>
    <div class="page-header-sub">Manage frequently asked questions</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start">
  <!-- FAQ List -->
  <div class="table-card">
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Question</th><th>Category</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($faqs as $faq): ?>
          <tr>
            <td style="max-width:320px">
              <div style="font-weight:500;font-size:.875rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($faq['question']) ?></div>
              <div style="font-size:.73rem;color:var(--text-dim);margin-top:.2rem"><?= e(truncate($faq['answer'], 60)) ?></div>
            </td>
            <td><span class="badge badge-dark"><?= e($faq['category'] ?? 'General') ?></span></td>
            <td style="color:var(--text-muted)"><?= $faq['sort_order'] ?></td>
            <td>
              <?php if ($faq['is_published']): ?>
                <span class="badge badge-success">Published</span>
              <?php else: ?>
                <span class="badge badge-dark">Draft</span>
              <?php endif; ?>
            </td>
            <td>
              <form method="POST" action="<?= url('admin/faqs/' . $faq['id'] . '/delete') ?>" style="display:inline">
                <?= csrf_field() ?>
                <button type="submit" class="action-btn danger" data-confirm="Delete this FAQ?" title="Delete"><i class="bi bi-trash3"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($faqs)): ?>
          <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-dim)">No FAQs yet. Add your first one!</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add FAQ Form -->
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem">
    <h3 style="font-size:.9rem;font-weight:700;margin-bottom:1.25rem"><i class="bi bi-plus-circle" style="color:var(--accent)"></i> Add New FAQ</h3>
    <form method="POST" action="<?= url('admin/faqs') ?>">
      <?= csrf_field() ?>
      <div class="form-group"><label class="form-label">Category</label>
        <select name="category" class="form-control">
          <?php foreach (['General', 'Payments', 'Downloads', 'Refunds', 'Account', 'Technical'] as $c): ?>
          <option><?= $c ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Question *</label><input type="text" name="question" class="form-control" required></div>
      <div class="form-group"><label class="form-label">Answer *</label><textarea name="answer" class="form-control" rows="5" required></textarea></div>
      <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="0" min="0"></div>
      <div class="form-check" style="margin-bottom:1rem">
        <input type="checkbox" name="is_published" value="1" id="pub" checked>
        <label for="pub" style="font-size:.85rem">Published</label>
      </div>
      <button type="submit" class="btn btn-primary btn-full"><i class="bi bi-plus-lg"></i> Add FAQ</button>
    </form>
  </div>
</div>
