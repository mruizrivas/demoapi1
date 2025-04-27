const CACHE_NAME = 'medidas-cache-v1';
const urlsToCache = [
  './index.html',
  './manifest.json',
  './icon-192.png',
  './icon-512.png',
  './icon.svg',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
];

// Instalar Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Archivos cacheados');
        return cache.addAll(urlsToCache);
      })
  );
});

// Interceptar solicitudes
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
  );
});

// Push Notifications
self.addEventListener('push', event => {
  const data = event.data.json();

  const title = data.title || "Notificación";
  const options = {
    body: data.body || "Nuevo evento",
    icon: 'icon-192.png',
    badge: 'icon-192.png'
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Clic en notificación
self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  event.waitUntil(
    clients.openWindow('./index.html')
  );
});
