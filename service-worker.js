const CACHE_NAME = 'audio-pwa-v1';
const URLS_TO_CACHE = [
  '/',
  '/index.php',
  '/script.js',
  '/manifest.json',
  'assets/bootstrap-5.3.5/css/bootstrap.css',
  'assets/jQuery/jquery-3.7.1.min.js',
  'assets/fontawesome/css/font_awesome_all.min.css',
  'assets/favicon/apple-touch-icon.png',
  'assets/favicon/favicon-32x32.png',
  'assets/favicon/favicon-16x16.png',
  'assets/favicon/site.webmanifest'
];


self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(URLS_TO_CACHE))
  );
});

self.addEventListener('fetch', function (event) {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(keys => 
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
});

