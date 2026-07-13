const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');

// Parse .env manually
const envPath = path.join(__dirname, '../.env');
const envContent = fs.readFileSync(envPath, 'utf8');
const env = {};
envContent.split('\n').forEach(line => {
  const match = line.match(/^\s*([\w.-]+)\s*=\s*(.*)?\s*$/);
  if (match) {
    const key = match[1];
    let value = match[2] || '';
    if (value.startsWith('"') && value.endsWith('"')) value = value.slice(1, -1);
    env[key] = value;
  }
});

async function run() {
  const connection = await mysql.createConnection({
    host: env.DB_HOST || '127.0.0.1',
    user: env.DB_USER || 'root',
    password: env.DB_PASSWORD || '',
    database: env.DB_NAME || 'ppmawaro_absensi_ppma',
    port: parseInt(env.DB_PORT || '3306')
  });

  try {
    const [users] = await connection.execute("SELECT id, username, role, kamar_id FROM users");
    console.log("=== Users list ===");
    console.log(users);

    const [kamar] = await connection.execute("SELECT kamar_id, nama_kamar, nama_asrama FROM kamar");
    console.log("=== Daftar Kamar ===");
    console.log(kamar);

    const [allCreds] = await connection.execute("SELECT w.*, u.username FROM webauthn_credentials w JOIN users u ON w.user_id = u.id");
    console.log("=== All Webauthn Credentials ===", allCreds);
  } catch (err) {
    console.error("Error:", err.message);
  } finally {
    await connection.end();
  }
}

run();
