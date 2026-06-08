const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
const { loadEnvConfig } = require('@next/env');

async function main() {
  // Load .env variables
  loadEnvConfig(process.cwd());

  const dbConfig = {
    host: process.env.DB_HOST || '127.0.0.1',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'ppmawaro_absensi_ppma',
  };

  console.log('Connecting to database:', {
    host: dbConfig.host,
    user: dbConfig.user,
    database: dbConfig.database,
  });

  const connection = await mysql.createConnection(dbConfig);

  try {
    const sqlPath = path.join(__dirname, 'create_webauthn_credentials.sql');
    const sql = fs.readFileSync(sqlPath, 'utf8');
    
    console.log('Executing SQL...');
    await connection.query(sql);
    console.log('Table webauthn_credentials successfully created or already exists!');

    // Show tables to verify
    const [rows] = await connection.query('SHOW TABLES');
    console.log('Current tables in database:');
    console.table(rows);

  } catch (error) {
    console.error('Migration failed:', error);
  } finally {
    await connection.end();
  }
}

main().catch(console.error);
