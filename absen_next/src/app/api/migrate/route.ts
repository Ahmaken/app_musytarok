import { NextResponse } from 'next/server';
import pool from '@/lib/db';

export async function GET() {
  try {
    const queries = [
      // 1. Tambah barcode_id di tabel murid
      "ALTER TABLE murid ADD COLUMN IF NOT EXISTS barcode_id VARCHAR(255) DEFAULT NULL UNIQUE;",
      
      // 2. Tambah nama_panggilan di tabel murid
      "ALTER TABLE murid ADD COLUMN IF NOT EXISTS nama_panggilan VARCHAR(50) DEFAULT NULL;",
      
      // 3. Tambah nama_asrama di tabel kamar untuk sistem hierarki baru
      "ALTER TABLE kamar ADD COLUMN IF NOT EXISTS nama_asrama VARCHAR(100) DEFAULT NULL;",

      // 4. Update enum role di users untuk pengurus_asrama
      "ALTER TABLE users MODIFY COLUMN role enum('admin','wali_kelas','wali_murid','guru','staff','pengurus_asrama') NOT NULL;",

      // 5. Tambah kamar_id di tabel users
      "ALTER TABLE users ADD COLUMN IF NOT EXISTS kamar_id int(11) DEFAULT NULL;"
    ];

    let results = [];
    for (const query of queries) {
      try {
        await pool.execute(query);
        results.push({ query, status: 'Success' });
      } catch (err: any) {
        // Abaikan error "Duplicate column name" (ER_DUP_FIELDNAME)
        if (err.code === 'ER_DUP_FIELDNAME') {
          results.push({ query, status: 'Already exists' });
        } else {
          throw err;
        }
      }
    }

    return NextResponse.json({ 
      success: true, 
      message: 'Migrasi Database Berhasil!', 
      details: results 
    });
  } catch (error: any) {
    console.error('Migrate Error:', error);
    return NextResponse.json({ error: 'Gagal melakukan migrasi: ' + error.message }, { status: 500 });
  }
}
