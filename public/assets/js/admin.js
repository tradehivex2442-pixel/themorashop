/* ============================================================
   admin.js — Themora Shop Admin Dashboard JavaScript
   Charts | Tables | Image Preview | Confirm Dialogs
   ============================================================ */

(function () {
  'use strict';

  // ── Revenue Chart ─────────────────────────────────────────────
  window.initRevenueChart = function (labels, revenue, orders) {
    const canvas = document.getElementById('revenue-chart');
    if (!canvas || typeof Chart === 'undefined') return;

    new Chart(canvas, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Revenue',
            data: revenue,
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            fill: true,
            tension: 0.4,
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 6,
            pointBackgroundColor: '#6366f1',
          },
          {
            label: 'Orders',
            data: orders,
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34,197,94,0.05)',
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointRadius: 2,
            yAxisID: 'y2',
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { labels: { color: '#94a3b8', font: { family: 'Inter', size: 12 } } },
          tooltip: {
            backgroundColor: '#1f2937',
            titleColor: '#f1f5f9',
            bodyColor: '#94a3b8',
            borderColor: 'rgba(255,255,255,0.08)',
            borderWidth: 1,
          }
        },
        scales: {
          x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748b', font: { size: 11 } } },
          y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748b', callback: v => '$' + v } },
          y2: { position: 'right', grid: { display: false }, ticks: { color: '#64748b' }, display: false }
        }
      }
    });
  };

  // ── Mini Bar Chart ────────────────────────────────────────────
  window.initBarChart = function (canvasId, labels, data, label, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;

    new Chart(canvas, {
      type: 'bar',
      data: {
        labels,
        datasets: [{ label, data, backgroundColor: color || '#6366f1', borderRadius: 6, borderSkipped: false }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1f2937', titleColor: '#f1f5f9', bodyColor: '#94a3b8' } },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 11 } } },
          y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748b' } }
        }
      }
    });
  };

  // ── Image Preview ─────────────────────────────────────────────
  document.querySelectorAll('[data-preview]').forEach(input => {
    const previewId = input.dataset.preview;
    input.addEventListener('change', () => {
      const file = input.files?.[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => {
        const preview = document.getElementById(previewId);
        if (preview) { preview.src = e.target.result; preview.style.display = 'block'; }
      };
      reader.readAsDataURL(file);
    });
  });

  // ── Rich Text Auto-resize ─────────────────────────────────────
  document.querySelectorAll('textarea[data-autoresize]').forEach(ta => {
    const resize = () => { ta.style.height = 'auto'; ta.style.height = ta.scrollHeight + 'px'; };
    ta.addEventListener('input', resize);
    resize();
  });

  // ── Copy to Clipboard ─────────────────────────────────────────
  window.copyToClipboard = function (text, btn) {
    navigator.clipboard.writeText(text).then(() => {
      const orig = btn.innerHTML;
      btn.innerHTML = '<i class="bi bi-check"></i>';
      setTimeout(() => btn.innerHTML = orig, 1500);
    });
  };

  // ── Confirm Delete ────────────────────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
  });

  // ── Flash message auto-hide ───────────────────────────────────
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => alert.style.opacity = '0', 4000);
    setTimeout(() => alert.remove(), 4500);
  });

  // ── Bulk select ───────────────────────────────────────────────
  const selectAll = document.getElementById('select-all');
  if (selectAll) {
    selectAll.addEventListener('change', () => {
      document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = selectAll.checked);
    });
  }

  // ── Tag input ─────────────────────────────────────────────────
  const tagInput = document.getElementById('tags-input');
  const tagsDisplay = document.getElementById('tags-display');
  const tagsHidden  = document.getElementById('tags-value');

  if (tagInput && tagsDisplay && tagsHidden) {
    let tags = tagsHidden.value ? tagsHidden.value.split(',').map(t => t.trim()).filter(Boolean) : [];
    renderTags();

    tagInput.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const val = tagInput.value.trim().replace(/,/g, '');
        if (val && !tags.includes(val)) { tags.push(val); renderTags(); }
        tagInput.value = '';
      }
      if (e.key === 'Backspace' && !tagInput.value) { tags.pop(); renderTags(); }
    });

    function renderTags() {
      tagsDisplay.innerHTML = '';
      tags.forEach(tag => {
        const span = document.createElement('span');
        span.style.cssText = 'display:inline-flex;align-items:center;gap:4px;background:rgba(99,102,241,.1);color:#818cf8;border-radius:99px;padding:2px 10px;font-size:.75rem;font-weight:600;cursor:pointer';
        span.innerHTML = `${tag} <span onclick="this.parentElement.remove()" style="font-size:.9rem">×</span>`;
        span.querySelector('span').addEventListener('click', () => { tags = tags.filter(t => t !== tag); renderTags(); });
        tagsDisplay.appendChild(span);
      });
      tagsHidden.value = tags.join(', ');
    }
  }

  // ── Sidebar mobile toggle ─────────────────────────────────────
  document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
    document.querySelector('.admin-sidebar')?.classList.toggle('show-mobile');
  });

  document.addEventListener('DOMContentLoaded', () => {
    // Mark active nav item
    const path = window.location.pathname;
    document.querySelectorAll('.admin-nav-item').forEach(a => {
      if (a.getAttribute('href') && path.startsWith(a.getAttribute('href'))) {
        a.classList.add('active');
      }
    });
  });
})();
