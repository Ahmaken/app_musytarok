import mysql from 'mysql2/promise';

async function test() {
  const pool = mysql.createPool({
    host: '127.0.0.1',
    user: 'root',
    password: '',
    database: 'ppmawaro_absensi_ppma',
  });

  try {
    const [q1] = await pool.execute(`
        SELECT 'Madin' as tipe_kelas, g.nama as nama_guru, km.nama_kelas as nama_kelas,
               jm.hari, jm.jam_mulai, jm.jam_selesai
        FROM jadwal_madin jm
        LEFT JOIN guru g ON jm.guru_id = g.guru_id
        LEFT JOIN kelas_madin km ON jm.kelas_madin_id = km.kelas_id
        ORDER BY jm.hari, jm.jam_mulai
    `);
    console.log('Jadwal Madin OK', (q1 as any[]).length);
  } catch (e: any) {
    console.error('Jadwal Madin Error:', e.message);
  }

  try {
    const [q2] = await pool.execute(`
        SELECT 'Qur\\'an' as tipe_kelas, g.nama as nama_guru, kq.nama_kelas as nama_kelas,
               jq.hari, jq.jam_mulai, jq.jam_selesai
        FROM jadwal_quran jq
        LEFT JOIN guru g ON jq.guru_id = g.guru_id
        LEFT JOIN kelas_quran kq ON jq.kelas_quran_id = kq.id
        ORDER BY jq.hari, jq.jam_mulai
    `);
    console.log('Jadwal Quran OK', (q2 as any[]).length);
  } catch (e: any) {
    console.error('Jadwal Quran Error:', e.message);
  }

  try {
    const [q3] = await pool.execute(`
        SELECT 'Kegiatan' as tipe_kelas, g.nama as nama_guru, k.nama_kamar as nama_kelas,
               jk.hari, jk.jam_mulai, jk.jam_selesai
        FROM jadwal_kegiatan jk
        LEFT JOIN guru g ON jk.guru_id = g.guru_id
        LEFT JOIN kamar k ON jk.kamar_id = k.kamar_id
        ORDER BY jk.hari, jk.jam_mulai
    `);
    console.log('Jadwal Kegiatan OK', (q3 as any[]).length);
  } catch (e: any) {
    console.error('Jadwal Kegiatan Error:', e.message);
  }
  
  try {
    const [q4] = await pool.execute(`
        SELECT g.nip as nis, g.nama, 'Guru' as tipe,
               DATE_FORMAT(a.tanggal, '%d/%m/%Y') as tanggal,
               a.status, '' as kelas
        FROM absensi_guru a
        JOIN guru g ON a.guru_id = g.guru_id
        WHERE a.tanggal >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY a.tanggal DESC, g.nama ASC
    `);
    console.log('Absensi Guru OK', (q4 as any[]).length);
  } catch (e: any) {
    console.error('Absensi Guru Error:', e.message);
  }

  process.exit(0);
}
test();
