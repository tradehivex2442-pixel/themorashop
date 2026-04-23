<?php
// THEMORA SHOP — FAQ / Support Page
?>
<div style="background:var(--bg-2);border-bottom:1px solid var(--border);padding:3.5rem 0;text-align:center">
  <div class="container container-md">
    <span class="section-eyebrow">❓ Help Center</span>
    <h1 style="margin:.5rem 0 1rem">Frequently Asked Questions</h1>
    <p style="color:var(--text-muted);font-size:1rem;margin-bottom:1.5rem">Can't find what you're looking for? <a href="<?= url('contact') ?>" style="color:var(--accent-light)">Contact our support team</a></p>
    <div class="search-wrap" style="max-width:480px;margin:0 auto">
      <i class="bi bi-search search-icon" aria-hidden="true"></i>
      <input type="text" id="faq-search" class="search-input" placeholder="Search for answers…" aria-label="Search FAQ">
    </div>
  </div>
</div>

<div class="container container-md" style="padding-top:3rem;padding-bottom:5rem">
  <?php
  $grouped = [];
  foreach ($faqs as $faq) {
    $grouped[$faq['category'] ?? 'General'][] = $faq;
  }
  ?>

  <?php foreach ($grouped as $cat => $items): ?>
  <div style="margin-bottom:2.5rem" class="faq-category">
    <h2 style="font-size:1rem;font-weight:700;color:var(--text-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:1rem"><?= e($cat) ?></h2>
    <?php foreach ($items as $faq): ?>
    <div class="faq-item" data-question="<?= strtolower(e($faq['question'])) ?>">
      <button class="faq-question">
        <?= e($faq['question']) ?>
        <i class="bi bi-chevron-down faq-arrow"></i>
      </button>
      <div class="faq-answer">
        <div class="faq-answer-inner"><?= nl2br(e($faq['answer'])) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>

  <?php if (empty($faqs)): ?>
  <div style="text-align:center;padding:3rem;color:var(--text-dim)">No FAQs available yet.</div>
  <?php endif; ?>

  <!-- Contact CTA -->
  <div style="background:linear-gradient(135deg,var(--accent-dark),var(--accent));border-radius:var(--radius-lg);padding:2.5rem;text-align:center;margin-top:2rem">
    <h3 style="color:white;margin-bottom:.75rem">Still have questions?</h3>
    <p style="color:rgba(255,255,255,.8);margin-bottom:1.5rem;font-size:.9rem">Our support team is here to help you 24/7</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="<?= url('contact') ?>" class="btn" style="background:white;color:var(--accent-dark);font-weight:700"><i class="bi bi-chat-dots"></i> Contact Us</a>
      <?php if (logged_in()): ?>
      <a href="<?= url('dashboard/tickets') ?>" class="btn" style="background:rgba(255,255,255,.15);color:white;border:1.5px solid rgba(255,255,255,.3)"><i class="bi bi-ticket-perforated"></i> Open Ticket</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.getElementById('faq-search').addEventListener('input', function() {
  const q = this.value.toLowerCase().trim();
  document.querySelectorAll('.faq-item').forEach(item => {
    const match = !q || item.dataset.question.includes(q) || item.querySelector('.faq-answer-inner').textContent.toLowerCase().includes(q);
    item.style.display = match ? '' : 'none';
  });
  document.querySelectorAll('.faq-category').forEach(cat => {
    const visible = Array.from(cat.querySelectorAll('.faq-item')).some(i => i.style.display !== 'none');
    cat.style.display = visible ? '' : 'none';
  });
});
</script>
