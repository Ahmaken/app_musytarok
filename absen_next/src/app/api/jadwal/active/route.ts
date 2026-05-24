import { NextResponse } from 'next/server';
import pool from '@/lib/db';
import { RowDataPacket } from 'mysql2';
import { cookies } from 'next/headers';
import { verifyToken } from '@/lib/auth/jwt';

export async function GET() {
  try {
    const cookieStore = await cookies();
    const token = cookieStore.get('token')?.value;
    if (!token) return NextResponse.json({ success: true, activeSchedule: null });

    const payload = verifyToken(token);
    if (!payload) return NextResponse.json({ success: true, activeSchedule: null });

    const { role, guruId } = payload as any;

    if (role === 'wali_murid' || (role === 'guru' && !guruId)) {
      return NextResponse.json({ success: true, activeSchedule: null });
    }

    const d = new Date();
    const days = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const currentDay = days[d.getDay()];
    
    const hours = d.getHours().toString().padStart(2, '0');
    const minutes = d.getMinutes().toString().padStart(2, '0');
    const currentTime = `${hours}:${minutes}:00`;

    let queryMadin = `
      SELECT j.jam_mulai, j.jam_selesai, j.mata_pelajaran, 
             m.nama_kelas, 'madin' as tipe
      FROM jadwal_madin j
      JOIN kelas_madin m ON j.kelas_madin_id = m.kelas_id
      WHERE j.hari = ?
    `;
    
    let queryQuran = `
      SELECT j.jam_mulai, j.jam_selesai, j.mata_pelajaran, 
             q.nama_kelas, 'quran' as tipe
      FROM jadwal_quran j
      JOIN kelas_quran q ON j.kelas_quran_id = q.id
      WHERE j.hari = ?
    `;

    let paramsMadin: any[] = [currentDay];
    let paramsQuran: any[] = [currentDay];

    if (role === 'guru') {
      queryMadin += ` AND j.guru_id = ?`;
      paramsMadin.push(guruId);
      queryQuran += ` AND j.guru_id = ?`;
      paramsQuran.push(guruId);
    }

    const [madinRows] = await pool.execute<RowDataPacket[]>(queryMadin, paramsMadin);
    const [quranRows] = await pool.execute<RowDataPacket[]>(queryQuran, paramsQuran);

    const allSchedules = [...madinRows, ...quranRows];

    const parseTime = (timeStr: string) => {
      const [h, m, s] = timeStr.split(':').map(Number);
      return (h || 0) * 3600 + (m || 0) * 60 + (s || 0);
    };

    const currentSecs = parseTime(currentTime);

    let activeSchedule = null;

    // Find the first active schedule (30 mins before to 3 hours after)
    for (const sched of allSchedules) {
      const mulaiSecs = parseTime(sched.jam_mulai);
      const selesaiSecs = parseTime(sched.jam_selesai);
      
      const windowStart = mulaiSecs - 30 * 60;
      const windowEnd = selesaiSecs + 3 * 3600;

      if (currentSecs >= windowStart && currentSecs <= windowEnd) {
        activeSchedule = {
          title: `Mengajar ${sched.tipe === 'madin' ? 'Madin' : "Al-Qur'an"} ${sched.nama_kelas}`,
          time: `${sched.jam_mulai.substring(0,5)} - ${sched.jam_selesai.substring(0,5)}`,
          status: currentSecs < mulaiSecs ? 'Persiapan Absen' : (currentSecs > selesaiSecs ? 'Batas Akhir Absen' : 'Sedang Berlangsung'),
          type: sched.tipe
        };
        break; // Show only the first active one
      }
    }

    return NextResponse.json({
      success: true,
      activeSchedule: activeSchedule
    });
  } catch (error) {
    console.error('Error fetching active schedule:', error);
    return NextResponse.json({ error: 'Server error' }, { status: 500 });
  }
}
