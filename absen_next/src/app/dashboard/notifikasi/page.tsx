'use client';
import React, { useState, useEffect } from 'react';
import { Bell, AlertTriangle, CheckCircle2, MessageCircle, Phone, Search, RefreshCw, Users } from 'lucide-react';

export default function NotifikasiPage() {
  const [showSettingsGuide, setShowSettingsGuide] = useState(false);
  const [muridList, setMuridList] = useState<any[]>([]);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);

  // State untuk default pesan WA dinamis
  const [role, setRole] = useState('guru');
  const [tipePesan, setTipePesan] = useState<'madin' | 'quran' | 'kamar'>('quran');
  const [selectedKategoriId, setSelectedKategoriId] = useState('');
  const [selectedKategoriNama, setSelectedKategoriNama] = useState('');
  const [statusAbsen, setStatusAbsen] = useState('Hadir');
  const [listKategori, setListKategori] = useState<any[]>([]);
  const [loadingKategori, setLoadingKategori] = useState(false);

  useEffect(() => {
    // Ambil detail profil untuk mendapatkan role
    fetch('/api/auth/me')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          setRole(data.user.role);
        }
      })
      .catch(() => {});

    // Ambil list santri untuk WA
    fetch('/api/whatsapp-list')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          setMuridList(data.data);
        }
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, []);

  // Fetch daftar kelas/kamar sesuai tipePesan (terbatasi dinamis oleh API kelas sesuai role yang login)
  useEffect(() => {
    setLoadingKategori(true);
    fetch(`/api/kelas?type=${tipePesan === 'kamar' ? 'kamar' : tipePesan}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          setListKategori(data.data);
          if (data.data.length > 0) {
            setSelectedKategoriId(data.data[0].id.toString());
            setSelectedKategoriNama(data.data[0].nama);
          } else {
            setSelectedKategoriId('');
            setSelectedKategoriNama('');
          }
        } else {
          setListKategori([]);
          setSelectedKategoriId('');
          setSelectedKategoriNama('');
        }
        setLoadingKategori(false);
      })
      .catch(() => {
        setListKategori([]);
        setSelectedKategoriId('');
        setSelectedKategoriNama('');
        setLoadingKategori(false);
      });
  }, [tipePesan]);

  const handleKategoriChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const val = e.target.value;
    setSelectedKategoriId(val);
    const found = listKategori.find(k => k.id.toString() === val);
    if (found) setSelectedKategoriNama(found.nama);
  };

  const formatPhoneNumber = (phone: string) => {
    if (!phone) return '';
    let p = phone.replace(/\D/g, '');
    if (p.startsWith('0')) p = '62' + p.substring(1);
    return p;
  };

  const getWaLink = (murid: any) => {
    const phone = formatPhoneNumber(murid.no_wali);
    if (!phone) return '#';

    let tipeLabel = tipePesan === 'madin' ? 'Kegiatan Madin' : tipePesan === 'quran' ? "Kegiatan Al-Qur'an" : 'Kegiatan Asrama';
    let tempatLabel = selectedKategoriNama ? `${selectedKategoriNama}` : '';

    const text = `Assalamu'alaikum Wr. Wb. Bapak/Ibu Wali dari Ananda *${murid.nama}*.\n\nKami dari pengurus PPMA menginformasikan perkembangan kehadiran ananda hari ini:\n\n* Kegiatan: ${tipeLabel}\n* Tempat/Kelas: ${tempatLabel || '-'}\n* Status Absensi: *${statusAbsen}*\n\nDemikian informasi yang dapat kami sampaikan. Atas perhatiannya kami ucapkan terima kasih.\n\nWassalamu'alaikum Wr. Wb.`;

    return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
  };

  // Hanya tampilkan hasil jika ada pencarian (min 1 karakter)
  const hasSearch = search.trim().length > 0;
  const filteredMurid = hasSearch
    ? muridList.filter(m =>
        m.nama?.toLowerCase().includes(search.toLowerCase()) ||
        m.nama_wali?.toLowerCase().includes(search.toLowerCase())
      )
    : [];

  return (
    <div className="max-w-4xl mx-auto animate-[fadeIn_0.5s_ease-out] pb-20">
      <div className="bg-gradient-to-r from-green-800 to-green-900 rounded-2xl p-6 text-white shadow-lg mb-6">
        <h2 className="text-2xl font-bold flex items-center gap-2"><Bell className="animate-bounce" /> Pusat Notifikasi</h2>
        <p className="text-green-100 text-sm mt-1">Daftar pemberitahuan dan informasi jadwal.</p>
      </div>

      <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 text-center mb-6">
        <div className="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
          <Bell className="text-gray-400 dark:text-gray-500" size={32} />
        </div>
        <h3 className="font-bold text-gray-800 dark:text-gray-200 mb-2">Push Notifikasi (Perangkat)</h3>
        <p className="text-gray-500 dark:text-gray-400 text-sm mb-6">Notifikasi jadwal absensi Anda akan muncul di perangkat ini.</p>
        
        <button 
          onClick={async () => {
            if (typeof window === 'undefined' || !('Notification' in window)) {
              alert('Browser Anda tidak mendukung notifikasi.');
              return;
            }

            const fireTestNotification = async () => {
              const title = 'Tes Notifikasi PPMA';
              const options = {
                body: 'Ini adalah tes notifikasi. Jika Anda melihat ini, berarti fitur Push Notification BEKERJA!',
                icon: '/logo.png',
                badge: '/logo.png',
                vibrate: [200, 100, 200]
              };
              try {
                if ('serviceWorker' in navigator) {
                  const reg = await navigator.serviceWorker.ready;
                  await reg.showNotification(title, options);
                  alert('Berhasil ditembakkan dari Service Worker! Cek bagian atas layar/laci notifikasi Anda.');
                } else {
                  new Notification(title, options);
                  alert('Berhasil ditembakkan (API Standar).');
                }
              } catch (err: any) {
                alert('Gagal memunculkan: ' + err.message);
              }
              setShowSettingsGuide(true);
            };

            if (Notification.permission === 'granted') {
              await fireTestNotification();
            } else if (Notification.permission !== 'denied') {
              const p = await Notification.requestPermission();
              if (p === 'granted') {
                alert('Izin diberikan! Menembakkan notifikasi pertama...');
                await fireTestNotification();
              } else {
                alert('Izin notifikasi ditolak.');
              }
            } else {
              alert('Anda sebelumnya telah memblokir notifikasi. Silakan ubah di pengaturan browser.');
            }
          }}
          className="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl transition-colors text-sm shadow-md w-full sm:w-auto"
        >
          Tes Izin Notifikasi Perangkat
        </button>

        {showSettingsGuide && (
          <div className="mt-8 text-left bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-5 animate-[slideDown_0.3s_ease-out]">
            <h4 className="font-bold text-orange-800 dark:text-orange-400 mb-2 flex items-center gap-2">
              <AlertTriangle size={18} /> Notifikasi tidak muncul/berbunyi?
            </h4>
            <p className="text-sm text-gray-700 dark:text-gray-300 mb-4">
              Jika notifikasi tes tidak muncul di layar atau masuk ke mode <strong>Senyap</strong>, sistem HP Android Anda mungkin memblokirnya. Aplikasi web (PWA) tidak memiliki akses untuk membuka pengaturan HP secara otomatis. Anda harus mengubahnya secara manual:
            </p>
            <ol className="text-sm text-gray-600 dark:text-gray-400 space-y-3 list-decimal pl-4">
              <li className="pl-1">Buka aplikasi <strong>Pengaturan (Settings)</strong> bawaan HP Anda.</li>
              <li className="pl-1">Pilih menu <strong className="text-gray-800 dark:text-gray-200">Aplikasi (Apps)</strong> lalu cari dan pilih <strong>Chrome</strong>.</li>
              <li className="pl-1">Pilih menu <strong className="text-gray-800 dark:text-gray-200">Notifikasi</strong>.</li>
              <li className="pl-1">Pastikan pengaturan <strong>TIDAK</strong> disetel ke Senyap (Silent). Ubah menjadi <strong className="text-green-600 dark:text-green-400">&quot;Izinkan Suara dan Getaran&quot;</strong> atau &quot;Penting&quot;.</li>
            </ol>
            <div className="mt-4 p-3 bg-white dark:bg-gray-800 rounded-lg flex gap-3 border border-gray-100 dark:border-gray-700 shadow-sm">
              <CheckCircle2 className="text-green-500 shrink-0 mt-0.5" size={16} />
              <p className="text-[11px] text-gray-500 dark:text-gray-400">Langkah ini hanya perlu dilakukan satu kali agar notifikasi jadwal mengajar Anda tidak terlewat.</p>
            </div>
          </div>
        )}
      </div>

      <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 overflow-hidden">
        <h3 className="font-bold text-xl text-gray-800 dark:text-gray-200 mb-2 flex items-center gap-2">
          <MessageCircle className="text-green-500" /> Siaran WhatsApp (Manual)
        </h3>
        <p className="text-gray-500 dark:text-gray-400 text-sm mb-6">
          Kirim pesan siaran langsung ke aplikasi WhatsApp asli Anda (anti-blokir). Pesan otomatis diisi, Anda hanya perlu klik tombol kirim di aplikasi WA.
        </p>

        {/* Dropdown default pesan WA */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-green-50/50 dark:bg-green-950/20 p-4 rounded-2xl border border-green-100 dark:border-green-900/30">
          <div>
            <label className="block text-xs font-bold text-green-800 dark:text-green-400 mb-1.5">Kategori Kegiatan</label>
            <select
              value={tipePesan}
              onChange={(e) => setTipePesan(e.target.value as any)}
              className="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none"
            >
              <option value="quran">Kelas Qur'an (Majlis)</option>
              <option value="madin">Kelas Madin</option>
              <option value="kamar">Asrama (Kamar)</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-bold text-green-800 dark:text-green-400 mb-1.5">Pilihan Kelas / Kamar</label>
            <select
              value={selectedKategoriId}
              onChange={handleKategoriChange}
              disabled={loadingKategori || listKategori.length === 0}
              className="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none disabled:opacity-60"
            >
              {loadingKategori ? (
                <option>Memuat...</option>
              ) : listKategori.length === 0 ? (
                <option>Tidak ada data</option>
              ) : (
                listKategori.map(k => (
                  <option key={k.id} value={k.id}>{k.nama}</option>
                ))
              )}
            </select>
          </div>
          <div>
            <label className="block text-xs font-bold text-green-800 dark:text-green-400 mb-1.5">Status Absensi</label>
            <select
              value={statusAbsen}
              onChange={(e) => setStatusAbsen(e.target.value)}
              className="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none"
            >
              <option value="Hadir">Hadir</option>
              <option value="Sakit">Sakit</option>
              <option value="Izin">Izin</option>
              <option value="Absen (Alpha)">Absen (Alpha)</option>
            </select>
          </div>
        </div>

        {/* Search bar */}
        <div className="relative mb-4">
          <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
          <input
            type="text"
            placeholder="Ketik nama santri atau nama wali untuk mencari..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm transition-all"
          />
        </div>

        {/* Hint saat belum ada pencarian */}
        {!hasSearch && (
          <div className="flex flex-col items-center justify-center py-12 text-center">
            <div className="w-14 h-14 bg-green-50 dark:bg-green-900/20 rounded-full flex items-center justify-center mb-3">
              <Users size={28} className="text-green-400" />
            </div>
            <p className="text-gray-500 dark:text-gray-400 text-sm font-medium">
              {loading ? 'Memuat data santri...' : `${muridList.length} data santri tersedia`}
            </p>
            <p className="text-gray-400 dark:text-gray-500 text-xs mt-1">
              Ketik nama santri atau nama wali di kolom pencarian di atas
            </p>
          </div>
        )}

        {/* Hasil pencarian */}
        {hasSearch && (
          <div className="overflow-x-auto">
            {loading ? (
              <div className="flex justify-center p-8"><RefreshCw className="animate-spin text-green-500" /></div>
            ) : (
              <table className="w-full text-left text-sm whitespace-nowrap">
                <thead className="bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">
                  <tr>
                    <th className="px-5 py-4 font-bold rounded-l-xl">Nama Santri</th>
                    <th className="px-5 py-4 font-bold">Wali Murid</th>
                    <th className="px-5 py-4 font-bold text-right rounded-r-xl">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-gray-800">
                  {filteredMurid.length === 0 ? (
                    <tr>
                      <td colSpan={3} className="text-center py-8 text-gray-500">
                        Santri &quot;{search}&quot; tidak ditemukan
                      </td>
                    </tr>
                  ) : (
                    filteredMurid.map(m => (
                      <tr key={m.murid_id} className="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td className="px-5 py-4 font-semibold text-gray-800 dark:text-gray-200">{m.nama}</td>
                        <td className="px-5 py-4 text-gray-600 dark:text-gray-400">
                          {m.nama_wali || '-'} <br/>
                          <span className="text-xs">{m.no_wali || 'Nomor HP tidak ada'}</span>
                        </td>
                        <td className="px-5 py-4 text-right">
                          {m.no_wali ? (
                            <a 
                              href={getWaLink(m)}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#1DA851] text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition-transform hover:scale-105 active:scale-95"
                            >
                              <MessageCircle size={16} /> WA Wali
                            </a>
                          ) : (
                            <span className="text-xs text-gray-400 italic">No WA Kosong</span>
                          )}
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
