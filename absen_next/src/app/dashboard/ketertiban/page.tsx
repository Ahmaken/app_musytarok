'use client';

import { useState, useEffect } from 'react';
import { AlertTriangle, UserX, FileWarning, Search, Filter, Plus, Calendar, Clock, ChevronDown, CheckCircle, Trash2, Edit } from 'lucide-react';
import Link from 'next/link';

export default function KetertibanPage() {
  const [activeTab, setActiveTab] = useState<'alpa' | 'pelanggaran'>('alpa');

  const [dataAlpa, setDataAlpa] = useState<any[]>([]);
  const [dataPelanggaran, setDataPelanggaran] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [role, setRole] = useState('');

  const fetchData = async () => {
    setLoading(true);
    try {
      const resAlpa = await fetch('/api/ketertiban?tab=alpa');
      const jsonAlpa = await resAlpa.json();
      if (jsonAlpa.success) setDataAlpa(jsonAlpa.data);

      const resPelanggaran = await fetch('/api/ketertiban?tab=pelanggaran');
      const jsonPelanggaran = await resPelanggaran.json();
      if (jsonPelanggaran.success) setDataPelanggaran(jsonPelanggaran.data);
    } catch (err) {
      console.error('Failed to fetch data:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
    // Ambil Role
    fetch('/api/auth/me')
      .then(res => res.json())
      .then(data => {
        if (data.success) setRole(data.user.role);
      })
      .catch(console.error);
  }, []);

  const handleDelete = async (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus data pelanggaran ini?')) {
      await fetch(`/api/ketertiban?id=${id}`, { method: 'DELETE' });
      fetchData(); // reload
    }
  };

  return (
    <div className="space-y-6 max-w-7xl mx-auto pb-20">
      {/* Header Halaman */}
      <div className="bg-gradient-to-br from-red-50 to-orange-100 dark:from-red-900/40 dark:to-orange-900/40 rounded-3xl p-6 shadow-sm border border-red-200 dark:border-red-800/50 relative overflow-hidden transition-colors duration-300">
        <div className="absolute top-0 right-0 -mt-4 -mr-4 text-red-200/50 dark:text-red-800/30">
          <AlertTriangle size={120} />
        </div>
        <div className="relative z-10 flex items-start justify-between">
          <div>
            <h1 className="text-2xl font-extrabold text-red-800 dark:text-red-400 drop-shadow-sm flex items-center gap-2">
              <FileWarning size={28} /> Ketertiban Murid
            </h1>
            <p className="text-red-600 dark:text-red-300 text-sm mt-1 font-medium max-w-xs">
              Sistem pencatatan otomatis siswa alpa dan riwayat pelanggaran tata tertib pesantren.
            </p>
          </div>
          <button className="bg-red-600 hover:bg-red-700 text-white p-3 rounded-full shadow-lg transition-transform hover:scale-105 flex items-center justify-center">
            <Plus size={20} />
          </button>
        </div>
      </div>

      {/* Ringkasan Statistik */}
      <div className="grid grid-cols-3 gap-4">
        <div className="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center transition-colors">
          <div className="bg-red-100 dark:bg-red-900/50 p-2 rounded-full mb-2">
            <UserX size={20} className="text-red-600 dark:text-red-400" />
          </div>
          <p className="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Total Alpa</p>
          <p className="text-2xl font-black text-gray-800 dark:text-gray-100">{dataAlpa.length}</p>
        </div>
        <div className="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center transition-colors">
          <div className="bg-orange-100 dark:bg-orange-900/50 p-2 rounded-full mb-2">
            <Clock size={20} className="text-orange-600 dark:text-orange-400" />
          </div>
          <p className="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Total Izin</p>
          <p className="text-2xl font-black text-gray-800 dark:text-gray-100">0</p>
        </div>
        <div className="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center transition-colors">
          <div className="bg-purple-100 dark:bg-purple-900/50 p-2 rounded-full mb-2">
            <AlertTriangle size={20} className="text-purple-600 dark:text-purple-400" />
          </div>
          <p className="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Pelanggaran</p>
          <p className="text-2xl font-black text-gray-800 dark:text-gray-100">{dataPelanggaran.length}</p>
        </div>
      </div>

      {/* Kontrol Pencarian & Filter */}
      <div className="flex gap-2">
        <div className="relative flex-1">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search size={16} className="text-gray-400" />
          </div>
          <input 
            type="text" 
            placeholder="Cari nama murid..." 
            className="w-full pl-9 pr-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-gray-200 transition-colors"
          />
        </div>
        <button className="px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <Filter size={18} />
        </button>
      </div>

      {/* Tabs */}
      <div className="flex bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
        <button 
          onClick={() => setActiveTab('alpa')}
          className={`flex-1 py-2 text-sm font-bold rounded-lg transition-colors ${activeTab === 'alpa' ? 'bg-white dark:bg-gray-700 shadow-sm text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'}`}
        >
          Rekap Alpa & Izin
        </button>
        <button 
          onClick={() => setActiveTab('pelanggaran')}
          className={`flex-1 py-2 text-sm font-bold rounded-lg transition-colors ${activeTab === 'pelanggaran' ? 'bg-white dark:bg-gray-700 shadow-sm text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'}`}
        >
          Pelanggaran Lain
        </button>
      </div>

      {/* Daftar Konten Tabel */}
      <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
        <div className="overflow-x-auto">
          {activeTab === 'alpa' ? (
            <table className="w-full text-left text-sm whitespace-nowrap">
              <thead className="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-100 dark:border-gray-700">
                <tr>
                  <th className="px-4 py-4">Nama Siswa</th>
                  <th className="px-4 py-4">Kelas</th>
                  <th className="px-4 py-4">Keterangan</th>
                  <th className="px-4 py-4">Tanggal</th>
                  <th className="px-4 py-4 text-center">Status</th>
                  <th className="px-4 py-4 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                {dataAlpa.length === 0 ? (
                  <tr><td colSpan={6} className="text-center py-4">Tidak ada data Alpa/Izin.</td></tr>
                ) : dataAlpa.map((item) => (
                  <tr key={item.id} className="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors text-gray-700 dark:text-gray-200">
                    <td className="px-4 py-3 font-bold">{item.nama}</td>
                    <td className="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400">{item.kelas}</td>
                    <td className="px-4 py-3">
                      <span className={`text-[10px] px-2.5 py-1 rounded-full font-bold border ${
                        String(item.keterangan || '').includes('Alpa') 
                          ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800/50' 
                          : 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800/50'
                      }`}>
                        {item.keterangan || 'Tanpa Keterangan'}
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <span className="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5 font-medium">
                        <Calendar size={12} className="text-gray-400" /> {item.tanggal}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-center">
                      {item.ditindak ? (
                        <span className="inline-flex items-center gap-1 text-[10px] text-green-600 dark:text-green-400 font-bold bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full border border-green-200 dark:border-green-800/30">
                          <CheckCircle size={12} /> Selesai
                        </span>
                      ) : (
                        <span className="inline-flex items-center gap-1 text-[10px] text-orange-600 dark:text-orange-400 font-bold bg-orange-50 dark:bg-orange-900/20 px-2 py-1 rounded-full border border-orange-200 dark:border-orange-800/30">
                          Menunggu
                        </span>
                      )}
                    </td>
                    <td className="px-4 py-3 text-center">
                      <div className="flex items-center justify-center gap-2">
                        {!item.ditindak && (
                          <button className="text-[10px] bg-red-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-red-700 transition-colors shadow-sm">
                            Tindak Lanjut
                          </button>
                        )}
                        {(role === 'admin' || role === 'staff') && (
                          <>
                            <button 
                              onClick={() => alert('Fitur edit akan segera hadir!')}
                              className="p-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors"
                              title="Edit"
                            >
                              <Edit size={14} />
                            </button>
                            <button 
                              onClick={() => handleDelete(item.id)}
                              className="p-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                              title="Hapus"
                            >
                              <Trash2 size={14} />
                            </button>
                          </>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <table className="w-full text-left text-sm whitespace-nowrap">
              <thead className="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-100 dark:border-gray-700">
                <tr>
                  <th className="px-4 py-4">Nama Siswa</th>
                  <th className="px-4 py-4">Kelas</th>
                  <th className="px-4 py-4">Jenis Pelanggaran</th>
                  <th className="px-4 py-4">Poin</th>
                  <th className="px-4 py-4">Tanggal</th>
                  <th className="px-4 py-4 text-center">Status</th>
                  <th className="px-4 py-4 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                {dataPelanggaran.length === 0 ? (
                  <tr><td colSpan={7} className="text-center py-4">Tidak ada data pelanggaran lain.</td></tr>
                ) : dataPelanggaran.map((item) => (
                  <tr key={item.id} className="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors text-gray-700 dark:text-gray-200">
                    <td className="px-4 py-3 font-bold">{item.nama}</td>
                    <td className="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400">{item.kelas}</td>
                    <td className="px-4 py-3">
                      <div className="bg-gray-50 dark:bg-gray-900/50 px-2.5 py-1 rounded-lg border border-gray-100 dark:border-gray-700 inline-block">
                        <span className="text-xs font-bold text-gray-700 dark:text-gray-300">{item.jenis}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <span className="bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[10px] px-2 py-1 rounded-full font-bold border border-purple-200 dark:border-purple-800/50">
                        -{item.poin || 0} Poin
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <span className="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5 font-medium">
                        <Calendar size={12} className="text-gray-400" /> {item.tanggal}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-center">
                      {item.ditindak ? (
                        <span className="inline-flex items-center gap-1 text-[10px] text-green-600 dark:text-green-400 font-bold bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full border border-green-200 dark:border-green-800/30">
                          <CheckCircle size={12} /> Selesai
                        </span>
                      ) : (
                        <span className="inline-flex items-center gap-1 text-[10px] text-orange-600 dark:text-orange-400 font-bold bg-orange-50 dark:bg-orange-900/20 px-2 py-1 rounded-full border border-orange-200 dark:border-orange-800/30">
                          Menunggu
                        </span>
                      )}
                    </td>
                    <td className="px-4 py-3 text-center">
                      <div className="flex items-center justify-center gap-2">
                        {!item.ditindak && (
                          <button className="text-[10px] bg-red-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-red-700 transition-colors shadow-sm">
                            Tindak Lanjut
                          </button>
                        )}
                        {(role === 'admin' || role === 'staff') && (
                          <>
                            <button 
                              onClick={() => alert('Fitur edit akan segera hadir!')}
                              className="p-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors"
                              title="Edit"
                            >
                              <Edit size={14} />
                            </button>
                            <button 
                              onClick={() => handleDelete(item.id)}
                              className="p-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                              title="Hapus"
                            >
                              <Trash2 size={14} />
                            </button>
                          </>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
      </div>
    </div>
  );
}
