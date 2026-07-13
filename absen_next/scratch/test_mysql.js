const mysql = require('mysql2/promise');

const hosts = ['localhost', '::1', '127.0.0.1'];
const dbConfig = {
  user: 'root',
  password: '',
  database: 'ppmawaro_absensi_ppma',
  connectTimeout: 4000 // 4 detik timeout
};

async function testHost(host) {
  console.log(`--- Mengetes koneksi MySQL ke host: "${host}" ---`);
  try {
    const conn = await mysql.createConnection({ ...dbConfig, host });
    console.log(`✅ BERHASIL: Terhubung ke MySQL menggunakan host "${host}"!`);
    const [rows] = await conn.query('SHOW TABLES LIMIT 5;');
    console.log(`Tabel (5 pertama):`, rows.map(r => Object.values(r)[0]));
    await conn.end();
    return true;
  } catch (err) {
    console.log(`❌ GAGAL ke host "${host}": ${err.message}`);
    return false;
  }
}

async function run() {
  console.log('=== UJI KONEKSI MYSQL2 DENGAN VARIASI HOST ===');
  for (const host of hosts) {
    await testHost(host);
    console.log('');
  }
}

run();
