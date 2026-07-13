const net = require('net');

const targets = [
  { host: '127.0.0.1', port: 3306 },
  { host: 'localhost', port: 3306 },
  { host: '::1', port: 3306 },
  { host: '10.124.112.232', port: 3306 }
];

async function testTarget(target) {
  return new Promise((resolve) => {
    console.log(`Mengetes koneksi TCP ke ${target.host}:${target.port}...`);
    const socket = new net.Socket();
    let resolved = false;

    socket.setTimeout(3000); // 3 detik timeout

    socket.on('connect', () => {
      if (!resolved) {
        resolved = true;
        console.log(`✅ BERHASIL: Koneksi TCP ke ${target.host}:${target.port} sukses!`);
        socket.destroy();
        resolve(true);
      }
    });

    socket.on('error', (err) => {
      if (!resolved) {
        resolved = true;
        console.log(`❌ GAGAL: Koneksi TCP ke ${target.host}:${target.port} gagal dengan error: ${err.message}`);
        socket.destroy();
        resolve(false);
      }
    });

    socket.on('timeout', () => {
      if (!resolved) {
        resolved = true;
        console.log(`⏳ TIMEOUT: Koneksi TCP ke ${target.host}:${target.port} waktu habis (timeout)`);
        socket.destroy();
        resolve(false);
      }
    });

    socket.connect(target.port, target.host);
  });
}

async function run() {
  console.log('--- DIAGNOSTIK KONEKSI PORT MYSQL 3306 ---');
  for (const target of targets) {
    await testTarget(target);
    console.log('');
  }
}

run();
