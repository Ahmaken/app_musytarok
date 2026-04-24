// firebase-messaging-sw.js
importScripts('https://www.gstatic.com/firebasejs/9.22.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.1/firebase-messaging-compat.js');

// ==========================================
// PENGATURAN FIREBASE CONFIG (SAMA SEPERTI DI FRONTEND)
// ==========================================
// Ganti dengan konfigurasi Firebase Anda (Dari Firebase Console -> Project Settings -> General -> Web App)
const firebaseConfig = {
  apiKey: "AIzaSyAYB6ugpcovJjNtKwoL6abmHcdN24YR1M0",
  authDomain: "absensi-ppma.firebaseapp.com",
  projectId: "absensi-ppma",
  storageBucket: "absensi-ppma.firebasestorage.app",
  messagingSenderId: "305003752686",
  appId: "1:305003752686:web:993a5f2566e0a0408497ff",
  measurementId: "G-QGN7XYCYBD"
};

// Inisialisasi Firebase App
firebase.initializeApp(firebaseConfig);

// Inisialisasi Firebase Messaging
const messaging = firebase.messaging();

// Tangani notifikasi saat aplikasi berjalan di background
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Menerima pesan di background ', payload);
    
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/assets/icons/icon-192x192.png', // Ganti dengan path logo Anda
        badge: '/assets/icons/icon-72x72.png',
        data: payload.data,
        vibrate: [200, 100, 200]
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Event click notifikasi
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    // Buka URL yang dikirim via payload data (atau default ke dashboard)
    const urlToOpen = (event.notification.data && event.notification.data.url) 
        ? event.notification.data.url 
        : '/pages/dashboard.php';
        
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(windowClients => {
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                if (client.url.includes('absen.ppma.or.id') && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});
