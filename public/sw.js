// ============================================================
// THEMORA SHOP — Service Worker (PWA)
// Caching strategy: Cache-First for assets, Network-First for pages
// ============================================================

const CACHE_NAME  = 'themora-v2';
const STATIC_URLS = [
  '/themora_Shop/public/',
  '/themora_Shop/public/assets/css/app.css',
  '/themora_Shop/public/assets/js/app.js',
  '/themora_Shop/public/manifest.json',
];

// ── Install: cache static assets ─────────────────────────────
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_URLS)).then(() => self.skipWaiting())
  );
});

// ── Activate: clean old caches ────────────────────────────────
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

// ── Fetch: Cache-First for assets, Network-First for HTML ─────
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // Skip non-GET, API calls, admin
  if (event.request.method !== 'GET') return;
  if (url.pathname.startsWith('/themora_Shop/public/api/')) return;
  if (url.pathname.startsWith('/themora_Shop/public/admin/')) return;

  // Cache-first for static assets
  if (event.request.destination === 'style' ||
      event.request.destination === 'script' ||
      event.request.destination === 'image' ||
      event.request.destination === 'font') {
    event.respondWith(
      caches.match(event.request).then(cached => cached || fetch(event.request).then(response => {
        const clone = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
        return response;
      }))
    );
    return;
  }

  // Network-first for HTML pages
  event.respondWith(
    fetch(event.request).then(response => {
      const clone = response.clone();
      caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
      return response;
    }).catch(() => caches.match(event.request))
  );
});

// ── Push Notifications ────────────────────────────────────────
self.addEventListener('push', event => {
  const data = event.data?.json() ?? { title: 'Themora Shop', body: 'New notification' };
  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: '/themora_Shop/public/assets/images/icon-192.png',
      badge: '/themora_Shop/public/assets/images/icon-192.png',
      data: { url: data.url || '/themora_Shop/public/' },
    })
  );
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(clients.openWindow(event.notification.data.url));
});
