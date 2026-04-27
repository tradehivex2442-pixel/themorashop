/* ============================================================
   app.js — Themora Shop Main JavaScript
   Theme Toggle | Search | Cart | Animations | Modals
   ============================================================ */

(function () {
  'use strict';

  // ── Theme Management ─────────────────────────────────────────
  const ThemeManager = {
    init() {
      const saved = localStorage.getItem('theme') || 'dark';
      this.apply(saved);
      document.querySelectorAll('[data-theme-toggle]').forEach(btn =>
        btn.addEventListener('click', () => this.toggle())
      );
    },
    apply(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('theme', theme);
      document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
        btn.querySelector('.theme-icon-dark')?.classList.toggle('hidden', theme === 'dark');
        btn.querySelector('.theme-icon-light')?.classList.toggle('hidden', theme === 'light');
      });
    },
    toggle() {
      const current = document.documentElement.getAttribute('data-theme') || 'dark';
      this.apply(current === 'dark' ? 'light' : 'dark');
    }
  };

  // ── Live Search ──────────────────────────────────────────────
  const Search = {
    input: null, dropdown: null, timer: null,

    init() {
      this.input    = document.querySelector('#search-input');
      this.dropdown = document.querySelector('#search-dropdown');
      if (!this.input) return;

      this.input.addEventListener('input', () => this.onType());
      this.input.addEventListener('focus', () => {
        if (this.dropdown.children.length) this.dropdown.classList.add('show');
      });

      document.addEventListener('click', e => {
        if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
          this.dropdown.classList.remove('show');
        }
      });

      // Keyboard nav
      this.input.addEventListener('keydown', e => {
        const items = this.dropdown.querySelectorAll('.search-result-item');
        if (!items.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); items[0]?.focus(); }
        if (e.key === 'Escape')    { this.dropdown.classList.remove('show'); }
      });
    },

    onType() {
      clearTimeout(this.timer);
      const q = this.input.value.trim();
      if (q.length < 2) { this.dropdown.classList.remove('show'); return; }
      this.timer = setTimeout(() => this.fetch(q), 250);
    },

    async fetch(q) {
      try {
        const res  = await fetch(BASE_URL + '/api/search?q=' + encodeURIComponent(q));
        const data = await res.json();
        this.render(data.results || []);
      } catch (e) { /* silent fail */ }
    },

    render(results) {
      this.dropdown.innerHTML = '';
      if (!results.length) {
        this.dropdown.innerHTML = '<div class="search-result-item" style="color:var(--text-dim)">No results found</div>';
      } else {
        results.forEach(r => {
          const div = document.createElement('a');
          div.className = 'search-result-item';
          div.href = r.url;
          div.tabIndex = 0;
          div.innerHTML = `
            <div class="search-result-thumb">${r.thumbnail ? `<img src="${r.thumbnail}" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:8px;">` : '<i class="bi bi-box" style="font-size:1.1rem;color:var(--text-dim)"></i>'}</div>
            <div style="flex:1;min-width:0">
              <div style="font-size:.875rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${r.title}</div>
              <div style="font-size:.75rem;color:var(--text-dim)">${r.category} · ${r.price}</div>
            </div>`;
          this.dropdown.appendChild(div);
        });
      }
      this.dropdown.classList.add('show');
    }
  };

  // ── Cart ─────────────────────────────────────────────────────
  const Cart = {
    init() {
      this.updateBadge();

      document.querySelectorAll('[data-add-cart]').forEach(btn => {
        btn.addEventListener('click', e => {
          e.preventDefault();
          this.add(btn.dataset.addCart, btn);
        });
      });
    },

    updateBadge() {
      const count = parseInt(document.getElementById('cart-count')?.textContent || '0');
      document.querySelectorAll('.cart-badge').forEach(el => {
        el.textContent = count;
        el.style.display = count ? 'flex' : 'none';
      });
    },

    async add(productId, btn) {
      const orig = btn.innerHTML;
      btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px"></span>';
      btn.disabled = true;

      try {
        const res  = await apiFetch(BASE_URL + '/cart/add', { product_id: productId });
        const data = await res.json();

        if (data.success) {
          showToast(data.message, 'success');
          const badge = document.getElementById('cart-count');
          if (badge) badge.textContent = data.data.count;
          this.updateBadge();
          btn.innerHTML = '<i class="bi bi-check-lg"></i>';
          setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 1500);
        } else {
          showToast(data.message || 'Error', 'error');
          btn.innerHTML = orig; btn.disabled = false;
        }
      } catch (e) {
        showToast('Network error', 'error');
        btn.innerHTML = orig; btn.disabled = false;
      }
    }
  };

  // ── Wishlist ─────────────────────────────────────────────────
  const Wishlist = {
    init() {
      document.querySelectorAll('[data-toggle-wishlist]').forEach(btn => {
        btn.addEventListener('click', e => {
          e.preventDefault();
          this.toggle(btn.dataset.toggleWishlist, btn);
        });
      });
    },

    async toggle(productId, btn) {
      try {
        const res  = await apiFetch(BASE_URL + '/wishlist/toggle', { product_id: productId });
        const data = await res.json();
        if (data.success) {
          const added = data.data.action === 'added';
          btn.classList.toggle('wishlisted', added);
          btn.innerHTML = added ? '<i class="bi bi-heart-fill"></i>' : '<i class="bi bi-heart"></i>';
          showToast(data.message, 'success');
        }
      } catch (e) { showToast('Please log in first', 'error'); }
    }
  };

  // ── FAQ Accordion ─────────────────────────────────────────────
  const FAQ = {
    init() {
      document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
          const item   = btn.closest('.faq-item');
          const answer = item.querySelector('.faq-answer');
          const isOpen = item.classList.contains('open');

          // Close all
          document.querySelectorAll('.faq-item.open').forEach(el => {
            el.classList.remove('open');
            el.querySelector('.faq-answer').style.maxHeight = '0';
          });

          // Open clicked
          if (!isOpen) {
            item.classList.add('open');
            answer.style.maxHeight = answer.scrollHeight + 'px';
          }
        });
      });
    }
  };

  // ── Tabs ──────────────────────────────────────────────────────
  const Tabs = {
    init() {
      document.querySelectorAll('.tab-nav').forEach(nav => {
        nav.querySelectorAll('.tab-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const target  = btn.dataset.tab;
            const wrapper = btn.closest('[data-tabs]') || document;
            wrapper.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            wrapper.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            wrapper.querySelector('#' + target)?.classList.add('active');
          });
        });
      });
    }
  };

  // ── Scroll Reveal ─────────────────────────────────────────────
  const Reveal = {
    init() {
      const observer = new IntersectionObserver(entries => {
        entries.forEach(el => {
          if (el.isIntersecting) {
            el.target.classList.add('visible');
            observer.unobserve(el.target);
          }
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    }
  };

  // ── Carousels ─────────────────────────────────────────────────
  const Carousel = {
    init() {
      document.querySelectorAll('[data-carousel]').forEach(wrap => {
        const track  = wrap.querySelector('.products-carousel');
        const prevBtn = wrap.querySelector('[data-carousel-prev]');
        const nextBtn = wrap.querySelector('[data-carousel-next]');
        if (!track) return;

        const step = 300;

        prevBtn?.addEventListener('click', () => track.scrollBy({ left: -step, behavior: 'smooth' }));
        nextBtn?.addEventListener('click', () => track.scrollBy({ left: step, behavior: 'smooth' }));

        // Auto-rotate every 4s
        if (wrap.dataset.autoplay) {
          setInterval(() => {
            const max = track.scrollWidth - track.clientWidth;
            if (track.scrollLeft >= max - 10) {
              track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
              track.scrollBy({ left: step, behavior: 'smooth' });
            }
          }, 4000);
        }
      });
    }
  };

  // ── Newsletter Popup ──────────────────────────────────────────
  const Newsletter = {
    init() {
      const popup = document.getElementById('newsletter-popup');
      if (!popup) return;
      if (localStorage.getItem('newsletter_shown')) return;

      setTimeout(() => popup.classList.add('show'), 8000);

      popup.querySelector('[data-close-newsletter]')?.addEventListener('click', () => this.close(popup));
      popup.addEventListener('click', e => { if (e.target === popup) this.close(popup); });

      const form = popup.querySelector('form');
      form?.addEventListener('submit', async e => {
        e.preventDefault();
        const email = form.querySelector('input[type="email"]').value;
        const btn   = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        try {
          const res  = await apiFetch(BASE_URL + '/api/newsletter', { email });
          const data = await res.json();
          if (data.success) {
            popup.querySelector('.newsletter-modal').innerHTML = `
              <div style="text-align:center;padding:1rem">
                <div style="font-size:3rem;margin-bottom:1rem">🎉</div>
                <h3 style="margin-bottom:.5rem">You're in!</h3>
                <p style="color:var(--text-muted)">${data.message}</p>
              </div>`;
            setTimeout(() => this.close(popup), 3000);
          } else {
            showToast(data.message, 'error');
            btn.disabled = false;
          }
        } catch (_) { btn.disabled = false; }
      });
    },

    close(popup) {
      popup.classList.remove('show');
      localStorage.setItem('newsletter_shown', '1');
    }
  };

  // ── Modal ─────────────────────────────────────────────────────
  window.openModal = function (id) {
    document.getElementById(id)?.classList.add('show');
    document.body.style.overflow = 'hidden';
  };

  window.closeModal = function (id) {
    document.getElementById(id)?.classList.remove('show');
    document.body.style.overflow = '';
  };

  document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) {
      e.target.classList.remove('show');
      document.body.style.overflow = '';
    }
    if (e.target.dataset.closeModal) closeModal(e.target.dataset.closeModal);
  });

  // ── Toast ─────────────────────────────────────────────────────
  window.showToast = function (message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const icon  = type === 'success' ? '✓' : '✕';
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<span style="font-size:1rem">${icon}</span><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3500);
  };

  // ── Coupon Apply ──────────────────────────────────────────────
  const CouponForm = {
    init() {
      const form = document.getElementById('coupon-form');
      if (!form) return;
      form.addEventListener('submit', async e => {
        e.preventDefault();
        const code  = form.querySelector('input[name="code"]').value;
        const btn   = form.querySelector('button');
        btn.disabled = true; btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px"></span>';
        try {
          const res  = await apiFetch(BASE_URL + '/coupon/apply', { code });
          const data = await res.json();
          if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
          } else {
            showToast(data.message, 'error');
          }
        } catch (_) { showToast('Error applying coupon', 'error'); }
        btn.disabled = false; btn.innerHTML = 'Apply';
      });
    }
  };

  // ── Confirm Dialogs ──────────────────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // ── Sticky Header Shadow ──────────────────────────────────────
  window.addEventListener('scroll', () => {
    const header = document.querySelector('.site-header');
    if (header) header.classList.toggle('scrolled', window.scrollY > 10);
  });

  // ── Helpers ──────────────────────────────────────────────────
  async function apiFetch(url, data = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: new URLSearchParams({ ...data, _token: token }),
    });
  }

  // ── Init Everything ───────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    ThemeManager.init();
    Search.init();
    Cart.init();
    Wishlist.init();
    FAQ.init();
    Tabs.init();
    Reveal.init();
    Carousel.init();
    Newsletter.init();
    CouponForm.init();
  });
})();
