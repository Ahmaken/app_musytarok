'use client';

import { useState, useEffect } from 'react';
import { CalendarDays, Clock, MapPin, User, Edit, CheckSquare } from 'lucide-react';

export default function JadwalPage() {
  const [jadwal, setJadwal] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [role, setRole] = useState('murid');

  const [activeTab, setActiveTab] = useState<'quran' | 'madin' | 'kegiatan'>('quran');

  const [selectedJadwal, setSelectedJadwal] = useState<number[]>([]);
  const [isBulkModalOpen, setIsBulkModalOpen] = useState(false);
  const [filterGuru, setFilterGuru] = useState('');
  const [bulkHari, setBulkHari] = useState('');
  const [bulkJamMulai, setBulkJamMulai] = useState('');
  const [bulkJamSelesai, setBulkJamSelesai] = useState('');
  const [savingBulk, setSavingBulk] = useState(false);

  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [editingJadwal, setEditingJadwal] = useState<any>(null);
  const [savingEdit, setSavingEdit] = useState(false);

  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [savingAdd, setSavingAdd] = useState(false);
  const [newJadwal, setNewJadwal] = useState({
    hari: 'Senin',
    jam_mulai: '',
    jam_selesai: '',
    kegiatan: '',
    tempat_id: '',
    guru_id: ''
  });
  const [tempatOptions, setTempatOptions] = useState<any[]>([]);
  const [guruOptions, setGuruOptions] = useState<any[]>([]);

  const fetchOptions = async () => {
    try {
      const resTempat = await fetch(`/api/kelas?type=${activeTab === 'kegiatan' ? 'kamar' : activeTab}`);
      const dataTempat = await resTempat.json();
      if (dataTempat.success) setTempatOptions(dataTempat.data);

      const resGuru = await fetch('/api/kelas?type=guru');
      const dataGuru = await resGuru.json();
      if (dataGuru.success) setGuruOptions(dataGuru.data);
    } catch (e) {}
  };

  const handleOpenAddModal = () => {
    setNewJadwal({ hari: 'Senin', jam_mulai: '', jam_selesai: '', kegiatan: '', tempat_id: '', guru_id: '' });
    fetchOptions();
    setIsAddModalOpen(true);
  };

  const handleSaveAdd = async (e: React.FormEvent) => {
    e.preventDefault();
    setSavingAdd(true);
    try {
      const payload = { ...newJadwal, tipe: activeTab };
      const res = await fetch('/api/jadwal', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        setIsAddModalOpen(false);
        fetchData();
      } else {
        alert(data.error);
      }
    } catch (e) {
      alert('Gagal menambah jadwal');
    } finally {
      setSavingAdd(false);
    }
  };

  useEffect(() => {
    const fetchMe = async () => {
      try {
        const res = await fetch('/api/auth/me');
        const data = await res.json();
        if (data.success) setRole(data.user.role);
      } catch (err) { }
    };
    fetchMe();
    fetchData();
  }, []);

  const fetchData = async () => {
    setLoading(true);
    try {
      const res = await fetch('/api/jadwal');
      const json = await res.json();
      if (json.success) setJadwal(json.data);
    } catch (err) {
      console.error('Failed to fetch jadwal:', err);
    } finally {
      setLoading(false);
    }
  };

  const filteredJadwal = jadwal.filter(j => {
    return j.tipe === activeTab && (filterGuru === '' || j.guru === filterGuru);
  });

  const [sortConfig, setSortConfig] = useState<{ key: string; direction: 'ascending' | 'descending' } | null>(null);

  const requestSort = (key: string) => {
    let direction: 'ascending' | 'descending' = 'ascending';
    if (sortConfig && sortConfig.key === key && sortConfig.direction === 'ascending') {
      direction = 'descending';
    }
    setSortConfig({ key, direction });
  };

  const hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad', 'Minggu'];

  const sortedJadwal = [...filteredJadwal].sort((a, b) => {
    if (!sortConfig) return 0;
    let valA = a[sortConfig.key];
    let valB = b[sortConfig.key];

    if (sortConfig.key === 'hari') {
      const idxA = hariOrder.indexOf(valA);
      const idxB = hariOrder.indexOf(valB);
      return sortConfig.direction === 'ascending' ? idxA - idxB : idxB - idxA;
    }

    if (valA === null || valA === undefined) valA = '';
    if (valB === null || valB === undefined) valB = '';

    const compareResult = valA.toString().localeCompare(valB.toString(), undefined, { numeric: true, sensitivity: 'base' });
    return sortConfig.direction === 'ascending' ? compareResult : -compareResult;
  });

  const getSortIcon = (key: string) => {
    if (!sortConfig || sortConfig.key !== key) return ' ⇅';
    return sortConfig.direction === 'ascending' ? ' ▲' : ' ▼';
  };

  const uniqueGurus = Array.from(new Set(jadwal.map(j => j.guru).filter(Boolean))).sort();

  const groupedJadwal = filteredJadwal.reduce((acc: any, curr: any) => {
    if (!acc[curr.hari]) acc[curr.hari] = [];
    acc[curr.hari].push(curr);
    return acc;
  }, {});

  const sortedHari = Object.keys(groupedJadwal).sort((a, b) => hariOrder.indexOf(a) - hariOrder.indexOf(b));

  const toggleSelectAll = () => {
    if (selectedJadwal.length === filteredJadwal.length) {
      setSelectedJadwal([]);
    } else {
      setSelectedJadwal(filteredJadwal.map(j => j.id));
    }
  };

  const toggleSelect = (id: number) => {
    if (selectedJadwal.includes(id)) {
      setSelectedJadwal(selectedJadwal.filter(i => i !== id));
    } else {
      setSelectedJadwal([...selectedJadwal, id]);
    }
  };

  const handleSaveBulk = async (e: React.FormEvent) => {
    e.preventDefault();
    if (selectedJadwal.length === 0) return;
    setSavingBulk(true);
    try {
      const payload: any = { ids: selectedJadwal, tipe: activeTab };
      if (bulkHari) payload.hari = bulkHari;
      if (bulkJamMulai) payload.jam_mulai = bulkJamMulai;
      if (bulkJamSelesai) payload.jam_selesai = bulkJamSelesai;

      const res = await fetch('/api/jadwal', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        alert(data.message);
        setIsBulkModalOpen(false);
        setSelectedJadwal([]);
        fetchData();
      } else {
        alert(data.error);
      }
    } catch (err) {
      alert('Gagal update massal');
    } finally {
      setSavingBulk(false);
    }
  };

  const handleEditClick = (item: any) => {
    setEditingJadwal({ ...item });
    setIsEditModalOpen(true);
  };

  const handleSaveEdit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSavingEdit(true);
    try {
      const payload = {
        ids: [editingJadwal.id],
        tipe: activeTab,
        hari: editingJadwal.hari,
        jam_mulai: editingJadwal.jam_mulai,
        jam_selesai: editingJadwal.jam_selesai,
        kegiatan: editingJadwal.kegiatan
      };
      const res = await fetch('/api/jadwal', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        setIsEditModalOpen(false);
        fetchData();
      } else {
        alert(data.error);
      }
    } catch (err) {
      alert('Gagal edit jadwal');
    } finally {
      setSavingEdit(false);
    }
  };

  return (
    <div className="space-y-6 max-w-7xl mx-auto pb-20">
      <div className="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/40 dark:to-emerald-900/40 rounded-3xl p-6 shadow-sm border border-green-200 dark:border-green-800/50 relative overflow-hidden transition-colors duration-300">
        <div className="absolute top-0 right-0 -mt-4 -mr-4 text-green-200/50 dark:text-green-800/30">
          <CalendarDays size={120} />
        </div>
        <div className="relative z-10">
          <h1 className="text-2xl font-extrabold text-green-800 dark:text-green-400 drop-shadow-sm flex items-center gap-2">
            <CalendarDays size={28} /> Jadwal Kegiatan
          </h1>
          <p className="text-green-600 dark:text-green-300 text-sm mt-1 font-medium max-w-md">
            Manajemen dan Informasi lengkap jadwal Anda.
          </p>
        </div>
      </div>

      {/* Tabs */}
      <div className="flex bg-white dark:bg-gray-800 p-1.5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-x-auto">
        <button onClick={() => { setActiveTab('quran'); setSelectedJadwal([]); }} className={`flex-1 min-w-[120px] py-2.5 text-sm font-bold rounded-xl transition-all ${activeTab === 'quran' ? 'bg-emerald-500 text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'}`}>Kelas Qur'an</button>
        <button onClick={() => { setActiveTab('madin'); setSelectedJadwal([]); }} className={`flex-1 min-w-[120px] py-2.5 text-sm font-bold rounded-xl transition-all ${activeTab === 'madin' ? 'bg-teal-500 text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'}`}>Kelas Madin</button>
        <button onClick={() => { setActiveTab('kegiatan'); setSelectedJadwal([]); }} className={`flex-1 min-w-[120px] py-2.5 text-sm font-bold rounded-xl transition-all ${activeTab === 'kegiatan' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'}`}>Kegiatan Asrama</button>
      </div>

      {(role === 'admin' || role === 'staff') && (
        <div className="flex flex-col sm:flex-row gap-4 items-start sm:items-center bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
          <label className="text-sm font-bold text-gray-600 dark:text-gray-300 flex items-center gap-2"><User size={16}/> Filter Guru:</label>
          <select value={filterGuru} onChange={e => setFilterGuru(e.target.value)} className="w-full sm:w-64 px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
            <option value="">Semua Guru</option>
            {uniqueGurus.map((g: any) => <option key={g} value={g}>{g}</option>)}
          </select>
          <button onClick={handleOpenAddModal} className="ml-auto bg-green-600 hover:bg-green-700 text-white font-bold px-3 py-2 rounded-xl text-sm transition-colors flex items-center justify-center gap-1" title="Tambah Jadwal">
            <span className="hidden sm:inline">+ Tambah Jadwal</span>
            <span className="sm:hidden text-lg leading-none">+</span>
          </button>
        </div>
      )}

      {(role === 'admin' || role === 'staff') && selectedJadwal.length > 0 && (
        <div className="flex flex-wrap gap-2 animate-in fade-in slide-in-from-right-4 duration-300">
          <button onClick={() => { setBulkHari(''); setBulkJamMulai(''); setBulkJamSelesai(''); setIsBulkModalOpen(true); }} className="px-3 py-2.5 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 rounded-xl text-xs font-bold hover:bg-indigo-100 transition-colors flex items-center gap-1.5">
            <CheckSquare size={14} /> Edit Jam/Hari Massal ({selectedJadwal.length})
          </button>
        </div>
      )}

      {loading ? (
        <div className="text-center py-12 text-gray-500">Memuat jadwal...</div>
      ) : filteredJadwal.length === 0 ? (
        <div className="bg-white dark:bg-gray-800 rounded-3xl p-8 border border-gray-100 dark:border-gray-700 shadow-sm text-center">
          <p className="text-gray-500 dark:text-gray-400">Tidak ada jadwal {activeTab} yang terdaftar.</p>
        </div>
      ) : (
        <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm whitespace-nowrap">
              <thead className="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-100 dark:border-gray-700">
                <tr>
                  {(role === 'admin' || role === 'staff') && (
                    <th className="px-4 py-4 w-12 text-center">
                      <input type="checkbox" className="rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer" checked={selectedJadwal.length === filteredJadwal.length && filteredJadwal.length > 0} onChange={toggleSelectAll} />
                    </th>
                  )}
                  <th className="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 select-none" onClick={() => requestSort('hari')}>HARI{getSortIcon('hari')}</th>
                  <th className="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 select-none" onClick={() => requestSort('jam_mulai')}>JAM{getSortIcon('jam_mulai')}</th>
                  <th className="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 select-none" onClick={() => requestSort('kegiatan')}>MAJLIS / MAPEL / KEGIATAN{getSortIcon('kegiatan')}</th>
                  <th className="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 select-none" onClick={() => requestSort('tempat')}>KELAS / KAMAR{getSortIcon('tempat')}</th>
                  <th className="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 select-none" onClick={() => requestSort('guru')}>GURU{getSortIcon('guru')}</th>
                  {(role === 'admin' || role === 'staff') && (
                    <th className="px-4 py-4 text-center">AKSI</th>
                  )}
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                {sortConfig !== null ? (
                  sortedJadwal.map((item: any, idx: number) => (
                    <tr key={`${item.id}-${idx}`} className={`transition-colors text-gray-700 dark:text-gray-200 ${selectedJadwal.includes(item.id) ? 'bg-green-50/50 dark:bg-green-900/20' : 'hover:bg-gray-50/50 dark:hover:bg-gray-800/50'}`}>
                      {(role === 'admin' || role === 'staff') && (
                        <td className="px-4 py-3 text-center">
                          <input type="checkbox" className="rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer" checked={selectedJadwal.includes(item.id)} onChange={() => toggleSelect(item.id)} />
                        </td>
                      )}
                      <td className="px-4 py-3 font-bold text-gray-900 dark:text-white">
                        {item.hari}
                      </td>
                      <td className="px-4 py-3">
                        <span className="flex items-center gap-1.5 text-xs font-medium">
                          <Clock size={14} className="text-orange-500" />
                          {item.jam_mulai.substring(0, 5)} - {item.jam_selesai.substring(0, 5)}
                        </span>
                      </td>
                      <td className="px-4 py-3 font-bold">{item.kegiatan}</td>
                      <td className="px-4 py-3">
                        <span className="flex items-center gap-1.5 text-xs">
                          <MapPin size={14} className="text-blue-500" /> {item.tempat || '-'}
                        </span>
                      </td>
                      <td className="px-4 py-3">
                        <span className="flex items-center gap-1.5 text-xs">
                          <User size={14} className="text-purple-500" /> {item.guru || '-'}
                        </span>
                      </td>
                      {(role === 'admin' || role === 'staff') && (
                        <td className="px-4 py-3 text-center">
                          <button onClick={() => handleEditClick(item)} className="p-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors" title="Edit Jadwal">
                            <Edit size={14} />
                          </button>
                        </td>
                      )}
                    </tr>
                  ))
                ) : (
                  sortedHari.flatMap(hari =>
                    groupedJadwal[hari].sort((a: any, b: any) => a.jam_mulai.localeCompare(b.jam_mulai)).map((item: any, idx: number) => (
                      <tr key={`${item.id}-${idx}`} className={`transition-colors text-gray-700 dark:text-gray-200 ${selectedJadwal.includes(item.id) ? 'bg-green-50/50 dark:bg-green-900/20' : 'hover:bg-gray-50/50 dark:hover:bg-gray-800/50'}`}>
                        {(role === 'admin' || role === 'staff') && (
                          <td className="px-4 py-3 text-center">
                            <input type="checkbox" className="rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer" checked={selectedJadwal.includes(item.id)} onChange={() => toggleSelect(item.id)} />
                          </td>
                        )}
                        <td className="px-4 py-3 font-bold text-gray-900 dark:text-white">
                          {hari}
                        </td>
                        <td className="px-4 py-3">
                          <span className="flex items-center gap-1.5 text-xs font-medium">
                            <Clock size={14} className="text-orange-500" />
                            {item.jam_mulai.substring(0, 5)} - {item.jam_selesai.substring(0, 5)}
                          </span>
                        </td>
                        <td className="px-4 py-3 font-bold">{item.kegiatan}</td>
                        <td className="px-4 py-3">
                          <span className="flex items-center gap-1.5 text-xs">
                            <MapPin size={14} className="text-blue-500" /> {item.tempat || '-'}
                          </span>
                        </td>
                        <td className="px-4 py-3">
                          <span className="flex items-center gap-1.5 text-xs">
                            <User size={14} className="text-purple-500" /> {item.guru || '-'}
                          </span>
                        </td>
                        {(role === 'admin' || role === 'staff') && (
                          <td className="px-4 py-3 text-center">
                            <button onClick={() => handleEditClick(item)} className="p-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors" title="Edit Jadwal">
                              <Edit size={14} />
                            </button>
                          </td>
                        )}
                      </tr>
                    ))
                  )
                )}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Modal Mass Edit */}
      {isBulkModalOpen && (
        <div className="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xl w-full max-w-sm overflow-hidden animate-in fade-in zoom-in duration-200">
            <div className="p-4 bg-indigo-600 text-white">
              <h2 className="text-lg font-bold">Edit Massal ({selectedJadwal.length} Jadwal)</h2>
              <p className="text-xs opacity-90 mt-1">Kosongkan field yang tidak ingin diubah.</p>
            </div>
            <form onSubmit={handleSaveBulk} className="p-5 space-y-4">
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Hari</label>
                <select value={bulkHari} onChange={(e) => setBulkHari(e.target.value)} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                  <option value="">-- Biarkan --</option>
                  {hariOrder.map(h => <option key={h} value={h}>{h}</option>)}
                </select>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">Jam Mulai</label>
                  <input type="time" value={bulkJamMulai} onChange={(e) => setBulkJamMulai(e.target.value)} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" />
                </div>
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">Jam Selesai</label>
                  <input type="time" value={bulkJamSelesai} onChange={(e) => setBulkJamSelesai(e.target.value)} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" />
                </div>
              </div>
              <div className="flex gap-3 pt-2">
                <button type="button" onClick={() => setIsBulkModalOpen(false)} className="flex-1 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Batal</button>
                <button type="submit" disabled={savingBulk || (!bulkHari && !bulkJamMulai && !bulkJamSelesai)} className="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl disabled:opacity-50 transition-colors">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal Single Edit */}
      {isEditModalOpen && editingJadwal && (
        <div className="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xl w-full max-w-sm overflow-hidden animate-in fade-in zoom-in duration-200">
            <div className="p-4 bg-indigo-600 text-white">
              <h2 className="text-lg font-bold">Edit Jadwal</h2>
            </div>
            <form onSubmit={handleSaveEdit} className="p-5 space-y-4">
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Hari</label>
                <select value={editingJadwal.hari} onChange={(e) => setEditingJadwal({ ...editingJadwal, hari: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required>
                  {hariOrder.map(h => <option key={h} value={h}>{h}</option>)}
                </select>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">Jam Mulai</label>
                  <input type="time" value={editingJadwal.jam_mulai.substring(0, 5)} onChange={(e) => setEditingJadwal({ ...editingJadwal, jam_mulai: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required />
                </div>
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">Jam Selesai</label>
                  <input type="time" value={editingJadwal.jam_selesai.substring(0, 5)} onChange={(e) => setEditingJadwal({ ...editingJadwal, jam_selesai: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required />
                </div>
              </div>
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Kegiatan / Mapel</label>
                <input type="text" value={editingJadwal.kegiatan} onChange={(e) => setEditingJadwal({ ...editingJadwal, kegiatan: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required />
              </div>
              <div className="flex gap-3 pt-2">
                <button type="button" onClick={() => setIsEditModalOpen(false)} className="flex-1 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Batal</button>
                <button type="submit" disabled={savingEdit} className="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl disabled:opacity-50 transition-colors">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      )}
      {/* Modal Add */}
      {isAddModalOpen && (
        <div className="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xl w-full max-w-sm overflow-hidden animate-in fade-in zoom-in duration-200">
            <div className="p-4 bg-green-600 text-white">
              <h2 className="text-lg font-bold">Tambah Jadwal Baru</h2>
            </div>
            <form onSubmit={handleSaveAdd} className="p-5 space-y-4">
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Hari</label>
                <select value={newJadwal.hari} onChange={(e) => setNewJadwal({ ...newJadwal, hari: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required>
                  {hariOrder.map(h => <option key={h} value={h}>{h}</option>)}
                </select>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">Jam Mulai</label>
                  <input type="time" value={newJadwal.jam_mulai} onChange={(e) => setNewJadwal({ ...newJadwal, jam_mulai: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required />
                </div>
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">Jam Selesai</label>
                  <input type="time" value={newJadwal.jam_selesai} onChange={(e) => setNewJadwal({ ...newJadwal, jam_selesai: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required />
                </div>
              </div>
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Kegiatan / Mapel</label>
                <input type="text" value={newJadwal.kegiatan} onChange={(e) => setNewJadwal({ ...newJadwal, kegiatan: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required />
              </div>
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Tempat (Kelas/Kamar)</label>
                <select value={newJadwal.tempat_id} onChange={(e) => setNewJadwal({ ...newJadwal, tempat_id: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" required>
                  <option value="">-- Pilih Tempat --</option>
                  {tempatOptions.map(t => <option key={t.id} value={t.id}>{t.nama}</option>)}
                </select>
              </div>
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Guru Pengajar</label>
                <select value={newJadwal.guru_id} onChange={(e) => setNewJadwal({ ...newJadwal, guru_id: e.target.value })} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                  <option value="">-- Tidak Ada / Kosong --</option>
                  {guruOptions.map(g => <option key={g.id} value={g.id}>{g.nama}</option>)}
                </select>
              </div>
              <div className="flex gap-3 pt-2">
                <button type="button" onClick={() => setIsAddModalOpen(false)} className="flex-1 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Batal</button>
                <button type="submit" disabled={savingAdd} className="flex-1 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl disabled:opacity-50 transition-colors">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
