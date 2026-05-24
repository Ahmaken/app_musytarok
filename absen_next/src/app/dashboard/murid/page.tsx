'use client';

import { useState, useEffect } from 'react';
import { Users, Search, Plus, Filter, User, MapPin, CheckSquare, Edit, UserPlus, Camera, RefreshCw } from 'lucide-react';
import Link from 'next/link';

export default function DataMuridPage() {
  const [murid, setMurid] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [role, setRole] = useState('murid');

  // State untuk bulk actions
  const [selectedMurid, setSelectedMurid] = useState<number[]>([]);
  const [isBulkModalOpen, setIsBulkModalOpen] = useState(false);
  const [bulkType, setBulkType] = useState<'madin' | 'quran' | 'kamar'>('madin');
  const [bulkTargetId, setBulkTargetId] = useState('');
  const [classOptions, setClassOptions] = useState<any[]>([]);
  const [savingBulk, setSavingBulk] = useState(false);

  // State untuk Detail & Edit
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [viewingMurid, setViewingMurid] = useState<any>(null);

  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [editingMurid, setEditingMurid] = useState<any>(null);
  const [photoFile, setPhotoFile] = useState<File | null>(null);
  const [savingEdit, setSavingEdit] = useState(false);
  const [zoomPhoto, setZoomPhoto] = useState<string | null>(null);

  // Helper untuk menentukan URL Foto Santri (Lokal vs Mitra)
  const getFotoUrl = (fotoName: string | null) => {
    if (!fotoName || fotoName === '-') return '';
    // Sudah berupa URL lengkap
    if (fotoName.startsWith('http://') || fotoName.startsWith('https://')) {
      return fotoName;
    }
    // File dari API Mitra yang diawali "Berkas_"
    if (fotoName.startsWith('Berkas_')) {
      const baseUrl = process.env.NEXT_PUBLIC_API_MITRA_FOTO_URL || 'https://mawar.smartpesantren.id/sekretariat/berkas/';
      const cleanBaseUrl = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
      return `${cleanBaseUrl}${fotoName}`;
    }
    // Coba deteksi dari ekstensi file: jika nama terlihat seperti file foto dari mitra (ada ekstensi gambar tapi bukan nama file lokal biasa)
    const lowerName = fotoName.toLowerCase();
    const hasImageExt = /\.(jpg|jpeg|png|gif|webp)$/i.test(fotoName);
    // Jika ada ekstensi gambar dan nama filenya panjang (kemungkinan dari mitra), coba server mitra
    if (hasImageExt && fotoName.length > 20 && !fotoName.startsWith('foto_') && !fotoName.startsWith('upload_')) {
      const baseUrl = process.env.NEXT_PUBLIC_API_MITRA_FOTO_URL || 'https://mawar.smartpesantren.id/sekretariat/berkas/';
      const cleanBaseUrl = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
      return `${cleanBaseUrl}${fotoName}`;
    }
    return `/uploads/${fotoName}`;
  };


  // State untuk filter
  const [showFilters, setShowFilters] = useState(true);
  const [filterMadin, setFilterMadin] = useState('');
  const [filterQuran, setFilterQuran] = useState('');
  const [filterKamar, setFilterKamar] = useState('');
  const [showAll, setShowAll] = useState(false);

  // State untuk sinkronisasi API Mitra
  const [syncing, setSyncing] = useState(false);
  const [syncResult, setSyncResult] = useState<any>(null);
  const [isSyncModalOpen, setIsSyncModalOpen] = useState(false);

  useEffect(() => {
    const fetchMe = async () => {
      try {
        const res = await fetch('/api/auth/me');
        const data = await res.json();
        if (data.success) setRole(data.user.role);
      } catch (err) { }
    };
    fetchMe();

    const fetchData = async () => {
      setLoading(true);
      try {
        const res = await fetch('/api/murid');
        const json = await res.json();
        if (json.success) setMurid(json.data);
      } catch (err) {
        console.error('Failed to fetch murid:', err);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  const handleConvertUser = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin membuat/memperbarui User Wali Murid untuk Santri ini?')) return;
    try {
      const res = await fetch('/api/users/convert', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'murid', id })
      });
      const data = await res.json();
      if (res.ok) {
        alert(`${data.message}\nUsername: ${data.username}\nPassword: ${data.defaultPassword}`);
      } else {
        alert(data.error || 'Terjadi kesalahan');
      }
    } catch (err) {
      alert('Gagal melakukan konversi user');
    }
  };

  useEffect(() => {
    if (!isBulkModalOpen) return;
    const fetchOptions = async () => {
      try {
        const res = await fetch(`/api/kelas?type=${bulkType}`);
        const json = await res.json();
        if (json.success) setClassOptions(json.data);
      } catch (err) {
        console.error('Failed to fetch options', err);
      }
    };
    fetchOptions();
  }, [isBulkModalOpen, bulkType]);

  const handleLuluskan = async (id: number) => {
    if (!confirm('Pindahkan santri ini ke daftar Alumni? Peringatan: Data santri ini akan dipindahkan ke tabel Alumni dan Akun Wali Murid terkait akan dihapus otomatis.')) return;
    try {
      const res = await fetch('/api/murid/lulus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await res.json();
      if (res.ok) {
        alert(data.message);
        setMurid(murid.filter(m => m.murid_id !== id));
      } else {
        alert(data.error || 'Gagal memproses data');
      }
    } catch (err) {
      alert('Terjadi kesalahan sistem');
    }
  };

  const handleConvertUserBulk = async () => {
    if (!confirm(`Apakah Anda yakin ingin membuat/memperbarui User Wali Murid untuk ${selectedMurid.length} Santri ini?`)) return;
    try {
      const res = await fetch('/api/users/convert', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'murid', ids: selectedMurid })
      });
      const data = await res.json();
      if (res.ok) {
        alert(data.message);
        setSelectedMurid([]);
      } else {
        alert(data.error || 'Terjadi kesalahan');
      }
    } catch (err) {
      alert('Gagal melakukan konversi user massal');
    }
  };

  const handleLuluskanBulk = async () => {
    if (!confirm(`Pindahkan ${selectedMurid.length} santri ini ke daftar Alumni? Peringatan: Data santri ini akan dipindahkan ke tabel Alumni dan Akun Wali Murid terkait akan dihapus otomatis.`)) return;
    try {
      const res = await fetch('/api/murid/lulus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ids: selectedMurid })
      });
      const data = await res.json();
      if (res.ok) {
        alert(data.message);
        setMurid(murid.filter(m => !selectedMurid.includes(m.murid_id)));
        setSelectedMurid([]);
      } else {
        alert(data.error || 'Gagal memproses data');
      }
    } catch (err) {
      alert('Terjadi kesalahan sistem');
    }
  };

  const handleViewDetail = (item: any) => {
    setViewingMurid(item);
    setIsDetailModalOpen(true);
  };

  const handleEditClick = (item: any) => {
    setEditingMurid(item);
    setPhotoFile(null);
    setIsEditModalOpen(true);
  };

  const handleSaveEdit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSavingEdit(true);
    try {
      let fotoName = editingMurid.foto;

      // Handle photo upload if any
      if (photoFile) {
        const formData = new FormData();
        formData.append('file', photoFile);
        const uploadRes = await fetch('/api/upload', {
          method: 'POST',
          body: formData
        });
        const uploadJson = await uploadRes.json();
        if (uploadJson.success) {
          fotoName = uploadJson.fileName;
        } else {
          alert('Gagal mengupload foto: ' + uploadJson.error);
          setSavingEdit(false);
          return;
        }
      }

      const res = await fetch('/api/murid', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...editingMurid, foto: fotoName })
      });
      const json = await res.json();
      if (json.success) {
        setMurid(murid.map(m => m.murid_id === editingMurid.murid_id ? { ...editingMurid, foto: fotoName } : m));
        setIsEditModalOpen(false);
      } else {
        alert('Gagal menyimpan: ' + json.error);
      }
    } catch (err) {
      alert('Terjadi kesalahan sistem saat menyimpan.');
    } finally {
      setSavingEdit(false);
    }
  };

  const madinList = Array.from(new Set(murid.map(m => m.kelas_madin).filter(Boolean))).sort() as string[];
  const quranList = Array.from(new Set(murid.map(m => m.kelas_quran).filter(Boolean))).sort() as string[];
  const kamarList = Array.from(new Set(murid.map(m => m.nama_kamar).filter(Boolean))).sort() as string[];

  const filteredMurid = murid.filter(m => {
    const s = search.toLowerCase();
    const matchSearch = m.nama.toLowerCase().includes(s) ||
      (m.nis && m.nis.toLowerCase().includes(s)) ||
      (m.kelas_madin && m.kelas_madin.toLowerCase().includes(s)) ||
      (m.kelas_quran && m.kelas_quran.toLowerCase().includes(s)) ||
      (m.nama_kamar && m.nama_kamar.toLowerCase().includes(s)) ||
      (m.alamat && m.alamat.toLowerCase().includes(s));

    const matchMadin = filterMadin ? m.kelas_madin === filterMadin : true;
    const matchQuran = filterQuran ? m.kelas_quran === filterQuran : true;
    const matchKamar = filterKamar ? m.nama_kamar === filterKamar : true;

    return matchSearch && matchMadin && matchQuran && matchKamar;
  });

  const [sortConfig, setSortConfig] = useState<{ key: string; direction: 'ascending' | 'descending' } | null>(null);

  const requestSort = (key: string) => {
    let direction: 'ascending' | 'descending' = 'ascending';
    if (sortConfig && sortConfig.key === key && sortConfig.direction === 'ascending') {
      direction = 'descending';
    }
    setSortConfig({ key, direction });
  };

  const sortedMurid = [...filteredMurid].sort((a, b) => {
    if (!sortConfig) return 0;
    let valA = a[sortConfig.key];
    let valB = b[sortConfig.key];
    if (valA === null || valA === undefined) valA = '';
    if (valB === null || valB === undefined) valB = '';

    // Gunakan numeric localeCompare untuk natural sort
    const compareResult = valA.toString().localeCompare(valB.toString(), undefined, { numeric: true, sensitivity: 'base' });
    return sortConfig.direction === 'ascending' ? compareResult : -compareResult;
  });

  const getSortIcon = (key: string) => {
    if (!sortConfig || sortConfig.key !== key) return ' ⇅';
    return sortConfig.direction === 'ascending' ? ' ▲' : ' ▼';
  };

  const toggleSelectAll = () => {
    if (selectedMurid.length === filteredMurid.length) {
      setSelectedMurid([]);
    } else {
      setSelectedMurid(filteredMurid.map(m => m.murid_id));
    }
  };

  const toggleSelect = (id: number) => {
    if (selectedMurid.includes(id)) {
      setSelectedMurid(selectedMurid.filter(m => m !== id));
    } else {
      setSelectedMurid([...selectedMurid, id]);
    }
  };

  const openBulkModal = (type: 'madin' | 'quran' | 'kamar') => {
    setBulkType(type);
    setBulkTargetId('');
    setClassOptions([]);
    setIsBulkModalOpen(true);
  };

  const handleSaveBulk = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!bulkTargetId || selectedMurid.length === 0) return;

    setSavingBulk(true);
    try {
      const payload: any = { murid_ids: selectedMurid };
      if (bulkType === 'madin') payload.kelas_madin_id = bulkTargetId;
      else if (bulkType === 'quran') payload.kelas_quran_id = bulkTargetId;
      else payload.kamar_id = bulkTargetId;

      const res = await fetch('/api/murid', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.success) {
        // Update local state
        const targetOption = classOptions.find(o => o.id == bulkTargetId);
        const targetName = targetOption ? targetOption.nama : bulkTargetId;

        setMurid(murid.map(m => {
          if (selectedMurid.includes(m.murid_id)) {
            if (bulkType === 'madin') return { ...m, kelas_madin_id: bulkTargetId, kelas_madin: targetName };
            if (bulkType === 'quran') return { ...m, kelas_quran_id: bulkTargetId, kelas_quran: targetName };
            if (bulkType === 'kamar') return { ...m, kamar_id: bulkTargetId, nama_kamar: targetName };
          }
          return m;
        }));
        setSelectedMurid([]);
        setIsBulkModalOpen(false);
        alert('Data berhasil diperbarui secara massal!');
      } else {
        alert('Gagal memperbarui: ' + json.error);
      }
    } catch (err) {
      alert('Terjadi kesalahan sistem.');
    } finally {
      setSavingBulk(false);
    }
  };

  const handleSyncMitra = async () => {
    if (!confirm('Apakah Anda yakin ingin melakukan sinkronisasi data santri dengan API Mitra Pembayaran sekarang? Proses ini akan mengunduh data terbaru dan memperbarui database absensi.')) return;
    setSyncing(true);
    try {
      const res = await fetch('/api/sync/murid');
      const data = await res.json();
      if (data.success) {
        setSyncResult(data);
        setIsSyncModalOpen(true);
        // Refresh data murid di tabel
        const refreshRes = await fetch('/api/murid');
        const refreshJson = await refreshRes.json();
        if (refreshJson.success) setMurid(refreshJson.data);
      } else {
        alert('Gagal sinkronisasi: ' + (data.error || data.message || 'Terjadi kesalahan'));
      }
    } catch (err: any) {
      alert('Gagal menghubungi server sinkronisasi: ' + err.message);
    } finally {
      setSyncing(false);
    }
  };

  return (
    <div className="space-y-6 max-w-7xl mx-auto pb-20">
      {/* Header Halaman */}
      <div className="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/40 rounded-3xl p-6 shadow-sm border border-blue-200 dark:border-blue-800/50 relative overflow-hidden transition-colors duration-300">
        <div className="absolute top-0 right-0 -mt-4 -mr-4 text-blue-200/50 dark:text-blue-800/30">
          <Users size={120} />
        </div>
        <div className="relative z-10 flex items-start justify-between">
          <div>
            <h1 className="text-2xl font-extrabold text-blue-800 dark:text-blue-400 drop-shadow-sm flex items-center gap-2">
              <Users size={28} /> Data Santri
            </h1>
            <p className="text-blue-600 dark:text-blue-300 text-sm mt-1 font-medium max-w-xs">
              Manajemen informasi santri PPMA. Total {murid.length} santri terdaftar.
            </p>
          </div>
          {(role === 'admin' || role === 'staff') && (
            <div className="flex gap-2">
              <button className="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg transition-transform hover:scale-105 flex items-center justify-center" title="Tambah Santri">
                <Plus size={20} />
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Tombol Aksi Admin */}
      {(role === 'admin' || role === 'staff') && (
        <div className="flex flex-col sm:flex-row justify-center gap-3 -mt-2 max-w-2xl mx-auto w-full px-4">
          <Link href="/dashboard/pairing" className="w-full sm:w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3.5 rounded-2xl shadow-lg shadow-indigo-600/10 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2.5 text-sm font-extrabold tracking-wide border border-indigo-500/50">
            <Camera size={18} className="text-indigo-200" /> Pairing Kartu Barcode Santri
          </Link>
          <button 
            onClick={handleSyncMitra} 
            disabled={syncing}
            className="w-full sm:w-1/2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-6 py-3.5 rounded-2xl shadow-lg shadow-emerald-600/10 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2.5 text-sm font-extrabold tracking-wide border border-emerald-500/30 disabled:opacity-75"
          >
            <RefreshCw size={18} className={`text-emerald-100 ${syncing ? 'animate-spin' : ''}`} /> 
            {syncing ? 'Mensinkronkan Data...' : 'Sinkronisasi Data Mitra'}
          </button>
        </div>
      )}

      {/* Kontrol Pencarian & Bulk Actions */}
      <div className="flex flex-col sm:flex-row gap-3">
        <div className="relative flex-1">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search size={16} className="text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari Nama, NIS, Kelas, Kamar atau Alamat santri..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-9 pr-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-gray-200 transition-colors"
          />
        </div>

        {selectedMurid.length > 0 && (role === 'admin' || role === 'staff') && (
          <div className="flex flex-wrap gap-2 animate-in fade-in slide-in-from-right-4 duration-300">
            <button onClick={() => openBulkModal('quran')} className="px-3 py-2.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs font-bold hover:bg-emerald-100 transition-colors flex items-center gap-1.5">
              <CheckSquare size={14} /> Pindah Qur'an ({selectedMurid.length})
            </button>
            <button onClick={() => openBulkModal('madin')} className="px-3 py-2.5 bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400 border border-teal-200 dark:border-teal-800 rounded-xl text-xs font-bold hover:bg-teal-100 transition-colors flex items-center gap-1.5">
              <CheckSquare size={14} /> Pindah Madin ({selectedMurid.length})
            </button>
            <button onClick={() => openBulkModal('kamar')} className="px-3 py-2.5 bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 border border-orange-200 dark:border-orange-800 rounded-xl text-xs font-bold hover:bg-orange-100 transition-colors flex items-center gap-1.5">
              <CheckSquare size={14} /> Pindah Kamar ({selectedMurid.length})
            </button>
            <button onClick={handleConvertUserBulk} className="px-3 py-2.5 bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800 rounded-xl text-xs font-bold hover:bg-purple-100 transition-colors flex items-center gap-1.5">
              <UserPlus size={14} />Perbarui Akun({selectedMurid.length})
            </button>
            <button onClick={handleLuluskanBulk} className="px-3 py-2.5 bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800 rounded-xl text-xs font-bold hover:bg-green-100 transition-colors flex items-center gap-1.5">
              <CheckSquare size={14} /> Luluskan ({selectedMurid.length})
            </button>
          </div>
        )}

        <button onClick={() => setShowFilters(!showFilters)} className={`px-3 py-2.5 border rounded-xl flex items-center justify-center transition-colors shrink-0 ${showFilters || filterMadin || filterQuran || filterKamar ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'}`}>
          <Filter size={18} />
        </button>
      </div>

      {/* Filter Panel */}
      {showFilters && (
        <div className="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 animate-in slide-in-from-top-2 duration-200 grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kelas Madin</label>
            <select value={filterMadin} onChange={(e) => setFilterMadin(e.target.value)} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
              <option value="">Semua Madin</option>
              {madinList.map(k => <option key={k} value={k}>{k}</option>)}
            </select>
          </div>
          <div>
            <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kelas Qur'an</label>
            <select value={filterQuran} onChange={(e) => setFilterQuran(e.target.value)} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
              <option value="">Semua Qur'an</option>
              {quranList.map(k => <option key={k} value={k}>{k}</option>)}
            </select>
          </div>
          <div>
            <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kamar Asrama</label>
            <select value={filterKamar} onChange={(e) => setFilterKamar(e.target.value)} className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-orange-500">
              <option value="">Semua Kamar</option>
              {kamarList.map(k => <option key={k} value={k}>{k}</option>)}
            </select>
          </div>
        </div>
      )}

      {/* Tabel Data Murid */}
      <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm whitespace-nowrap">
            <thead className="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-100 dark:border-gray-700">
              <tr>
                {(role === 'admin' || role === 'staff') && (
                  <th className="px-4 py-4 w-12 text-center">
                    <input
                      type="checkbox"
                      className="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                      checked={selectedMurid.length === filteredMurid.length && filteredMurid.length > 0}
                      onChange={toggleSelectAll}
                    />
                  </th>
                )}
                <th className="px-4 py-4 w-12 text-center">FOTO</th>
                <th className="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 select-none" onClick={() => requestSort('nama')}>SANTRI & NIS{getSortIcon('nama')}</th>
                <th className="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 select-none" onClick={() => requestSort('kelas_madin')}>KELAS & KAMAR{getSortIcon('kelas_madin')}</th>
                <th className="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 select-none" onClick={() => requestSort('alamat')}>ALAMAT{getSortIcon('alamat')}</th>
                <th className="px-4 py-4 text-center">AKSI</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
              {loading ? (
                <tr>
                  <td colSpan={6} className="text-center py-8 text-gray-500">Memuat data santri...</td>
                </tr>
              ) : filteredMurid.length === 0 ? (
                <tr>
                  <td colSpan={6} className="text-center py-8 text-gray-500">Data santri tidak ditemukan.</td>
                </tr>
              ) : !showAll && !search && !filterMadin && !filterQuran && !filterKamar ? (
                <tr>
                  <td colSpan={6} className="text-center py-12">
                    <p className="text-gray-500 dark:text-gray-400 mb-4 text-sm font-medium">Gunakan fitur pencarian atau filter di atas untuk menemukan data santri,<br/>atau klik tombol di bawah ini untuk melihat seluruh data santri.</p>
                    <button onClick={() => setShowAll(true)} className="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 px-6 py-2.5 rounded-xl font-bold transition-colors text-sm shadow-sm inline-flex items-center gap-2">
                      <Users size={16} /> Tampilkan Semua Data Santri
                    </button>
                  </td>
                </tr>
              ) : (
                sortedMurid.map((item) => (
                  <tr key={item.murid_id} className={`transition-colors text-gray-700 dark:text-gray-200 ${(role === 'admin' || role === 'staff') && selectedMurid.includes(item.murid_id) ? 'bg-blue-50/50 dark:bg-blue-900/20' : 'hover:bg-gray-50/50 dark:hover:bg-gray-800/50'}`}>
                    {(role === 'admin' || role === 'staff') && (
                      <td className="px-4 py-3 text-center">
                        <input
                          type="checkbox"
                          className="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                          checked={selectedMurid.includes(item.murid_id)}
                          onChange={() => toggleSelect(item.murid_id)}
                        />
                      </td>
                    )}
                    <td className="px-4 py-3 text-center">
                      <div
                        className={`w-10 h-10 rounded-full flex items-center justify-center mx-auto overflow-hidden bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 ${item.foto ? 'cursor-pointer hover:opacity-80' : ''} relative group`}
                        onClick={() => item.foto ? setZoomPhoto(getFotoUrl(item.foto)) : null}
                      >
                        {item.foto ? (
                          <img
                            src={getFotoUrl(item.foto)}
                            alt={item.nama}
                            className="w-full h-full object-cover"
                            onError={(e) => {
                              const target = e.currentTarget;
                              target.style.display = 'none';
                              const parent = target.parentElement;
                              if (parent && !parent.querySelector('.fallback-icon')) {
                                const icon = document.createElement('div');
                                icon.className = 'fallback-icon flex items-center justify-center w-full h-full';
                                icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
                                parent.appendChild(icon);
                              }
                            }}
                          />
                        ) : (
                          <User size={20} />
                        )}
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="font-bold text-gray-900 dark:text-white">{item.nama}</div>
                      <div className="font-mono text-xs text-gray-500">{item.nis || '-'}</div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex flex-col gap-1">
                        <span className="inline-flex items-center gap-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-0.5 rounded text-[10px] font-semibold w-max">
                          Madin: {item.kelas_madin || '-'}
                        </span>
                        <span className="inline-flex items-center gap-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 px-2 py-0.5 rounded text-[10px] font-semibold w-max">
                          Qur'an: {item.kelas_quran || '-'}
                        </span>
                        <span className="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded text-[10px] font-semibold w-max">
                          <MapPin size={10} /> Kamar: {item.nama_kamar || (item.kamar_id ? `ID ${item.kamar_id}` : '-')}
                        </span>
                      </div>
                    </td>
                    <td className="px-4 py-3 text-xs truncate max-w-[150px]" title={item.alamat}>
                      {item.alamat || '-'}
                    </td>
                    <td className="px-4 py-3 text-center">
                      <div className="flex items-center justify-center gap-2">
                        <button onClick={() => handleViewDetail(item)} className="text-[10px] bg-blue-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-sm">
                          Detail
                        </button>
                        {(role === 'admin' || role === 'staff') && (
                          <>
                            <button onClick={() => handleConvertUser(item.murid_id)} className="text-[10px] bg-purple-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-purple-700 transition-colors shadow-sm" title="Jadikan User Wali Murid">
                              <UserPlus size={14} className="inline-block" />
                            </button>
                            <button onClick={() => handleEditClick(item)} className="text-[10px] bg-indigo-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-indigo-700 transition-colors shadow-sm">
                              Ubah
                            </button>
                            <button onClick={() => handleLuluskan(item.murid_id)} className="text-[10px] bg-green-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-green-700 transition-colors shadow-sm" title="Pindahkan ke Alumni">
                              Luluskan
                            </button>
                          </>
                        )}
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Bulk Move Modal */}
      {isBulkModalOpen && (
        <div className="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
            <div className={`p-4 text-white ${bulkType === 'madin' ? 'bg-teal-600' : bulkType === 'quran' ? 'bg-emerald-600' : 'bg-orange-600'}`}>
              <h2 className="text-lg font-bold">
                Pindah {bulkType === 'madin' ? 'Kelas Madin' : bulkType === 'quran' ? "Kelas Qur'an" : 'Kamar'} Massal
              </h2>
              <p className="text-xs opacity-90 mt-1">{selectedMurid.length} Santri terpilih</p>
            </div>
            <form onSubmit={handleSaveBulk} className="p-5 space-y-4">
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">
                  Pilih {bulkType === 'kamar' ? 'Kamar' : 'Kelas'} Tujuan
                </label>
                <select
                  value={bulkTargetId}
                  onChange={(e) => setBulkTargetId(e.target.value)}
                  className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500"
                  required
                >
                  <option value="" disabled>-- Pilih Tujuan --</option>
                  {classOptions.map(k => (
                    <option key={k.id} value={k.id}>
                      {k.nama}
                    </option>
                  ))}
                </select>
              </div>
              <div className="flex gap-3 pt-2">
                <button
                  type="button"
                  onClick={() => setIsBulkModalOpen(false)}
                  className="flex-1 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 transition-colors"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={savingBulk || !bulkTargetId}
                  className={`flex-1 py-2 text-white font-bold rounded-xl transition-colors disabled:opacity-50 ${bulkType === 'madin' ? 'bg-teal-600 hover:bg-teal-700' : bulkType === 'quran' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-orange-600 hover:bg-orange-700'}`}
                >
                  {savingBulk ? 'Memproses...' : 'Pindahkan'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal View Detail */}
      {isDetailModalOpen && viewingMurid && (
        <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xl w-full max-w-2xl border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in duration-200 flex flex-col max-h-[80vh] mb-16 overflow-hidden">
            <div className="bg-blue-600 dark:bg-blue-900 p-5 text-white flex justify-between items-start shrink-0">
              <div className="flex gap-4 items-center">
                <div
                  className="w-16 h-16 rounded-full overflow-hidden border-2 border-white/30 bg-white/10 flex items-center justify-center cursor-pointer hover:opacity-80"
                  onClick={() => viewingMurid.foto ? setZoomPhoto(getFotoUrl(viewingMurid.foto)) : null}
                >
                  {viewingMurid.foto ? (
                    <img src={getFotoUrl(viewingMurid.foto)} alt={viewingMurid.nama} className="w-full h-full object-cover" />
                  ) : (
                    <User size={32} className="text-white/70" />
                  )}
                </div>
                <div>
                  <h2 className="text-xl font-bold">{viewingMurid.nama} {viewingMurid.nama_panggilan ? `(${viewingMurid.nama_panggilan})` : ''}</h2>
                  <p className="text-xs text-blue-200 font-mono mt-0.5">NIS: {viewingMurid.nis || '-'} | NIK: {viewingMurid.nik || '-'}</p>
                </div>
              </div>
              <button onClick={() => setIsDetailModalOpen(false)} className="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                X
              </button>
            </div>

            <div className="p-6 overflow-y-auto flex-1 text-sm text-gray-700 dark:text-gray-200">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-4">
                  <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">No. WhatsApp / HP</p>
                    <p className="font-semibold">{viewingMurid.no_hp || '-'}</p>
                  </div>
                  <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Alamat Lengkap</p>
                    <p className="font-semibold leading-relaxed">{viewingMurid.alamat || '-'}</p>
                  </div>
                  <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nilai Rata-rata</p>
                    <p className="font-bold text-lg text-emerald-600 dark:text-emerald-400">{viewingMurid.nilai || '0.00'}</p>
                  </div>
                </div>

                <div className="space-y-4">
                  <div className="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl space-y-2 border border-gray-100 dark:border-gray-700">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Data Akademik & Asrama</p>
                    <div className="flex justify-between items-center text-xs">
                      <span className="text-gray-500">Kelas Madin:</span>
                      <span className="font-bold">{viewingMurid.kelas_madin || '-'}</span>
                    </div>
                    <div className="flex justify-between items-center text-xs">
                      <span className="text-gray-500">Kelas Qur'an:</span>
                      <span className="font-bold">{viewingMurid.kelas_quran || '-'}</span>
                    </div>
                    <div className="flex justify-between items-center text-xs">
                      <span className="text-gray-500">Kamar Asrama:</span>
                      <span className="font-bold">{viewingMurid.nama_kamar || '-'}</span>
                    </div>
                  </div>

                  <div className="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl space-y-2 border border-gray-100 dark:border-gray-700">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Data Wali</p>
                    <div className="flex justify-between items-center text-xs">
                      <span className="text-gray-500">Nama Wali:</span>
                      <span className="font-bold">{viewingMurid.nama_wali || '-'}</span>
                    </div>
                    <div className="flex justify-between items-center text-xs">
                      <span className="text-gray-500">No. HP Wali:</span>
                      <span className="font-bold">{viewingMurid.no_wali || '-'}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="p-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex justify-end shrink-0">
              <button
                onClick={() => setIsDetailModalOpen(false)}
                className="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-300 transition-colors"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal Edit Murid */}
      {isEditModalOpen && editingMurid && (
        <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-xl w-full max-w-2xl border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in duration-200 flex flex-col max-h-[85vh] mb-10 overflow-hidden">
            <div className="bg-indigo-600 dark:bg-indigo-900 p-5 text-white shrink-0">
              <h2 className="text-xl font-bold flex items-center gap-2">
                <Edit size={20} /> Edit Data Santri
              </h2>
            </div>
            <form onSubmit={handleSaveEdit} className="p-6 space-y-5 overflow-y-auto flex-1">

              <div className="flex flex-col sm:flex-row gap-5">
                <div className="w-full sm:w-1/3 space-y-3">
                  <div className="w-32 h-32 bg-gray-100 dark:bg-gray-900 rounded-2xl mx-auto overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center relative">
                    {photoFile ? (
                      <img src={URL.createObjectURL(photoFile)} alt="Preview" className="w-full h-full object-cover" />
                    ) : editingMurid.foto ? (
                      <img src={getFotoUrl(editingMurid.foto)} alt={editingMurid.nama} className="w-full h-full object-cover" />
                    ) : (
                      <User size={40} className="text-gray-400" />
                    )}
                    <label className="absolute bottom-2 bg-black/60 text-white text-[10px] px-3 py-1 rounded-full cursor-pointer hover:bg-black transition-colors">
                      Ubah Foto
                      <input type="file" className="hidden" accept="image/*" onChange={(e) => setPhotoFile(e.target.files?.[0] || null)} />
                    </label>
                  </div>
                </div>

                <div className="w-full sm:w-2/3 space-y-4">
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-bold text-gray-500 mb-1">Nama Lengkap</label>
                      <input
                        type="text"
                        value={editingMurid.nama || ''}
                        onChange={(e) => setEditingMurid({ ...editingMurid, nama: e.target.value })}
                        className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-xs font-bold text-gray-500 mb-1">Nama Panggilan</label>
                      <input
                        type="text"
                        value={editingMurid.nama_panggilan || ''}
                        onChange={(e) => setEditingMurid({ ...editingMurid, nama_panggilan: e.target.value })}
                        className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        placeholder="Cth: Ahmad"
                      />
                    </div>
                  </div>
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-bold text-gray-500 mb-1">NIS</label>
                      <input
                        type="text"
                        value={editingMurid.nis || ''}
                        onChange={(e) => setEditingMurid({ ...editingMurid, nis: e.target.value })}
                        className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500"
                      />
                    </div>
                    <div>
                      <label className="block text-xs font-bold text-gray-500 mb-1">NIK</label>
                      <input
                        type="text"
                        value={editingMurid.nik || ''}
                        onChange={(e) => setEditingMurid({ ...editingMurid, nik: e.target.value })}
                        className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-gray-500 mb-1">Kode Barcode Card (Scan di sini)</label>
                    <input
                      type="text"
                      value={editingMurid.barcode_id || ''}
                      onChange={(e) => setEditingMurid({ ...editingMurid, barcode_id: e.target.value })}
                      className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 text-indigo-600 dark:text-indigo-400"
                      placeholder="Arahkan kursor ke sini, lalu scan kartu"
                    />
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">No. WhatsApp / HP</label>
                  <input
                    type="text"
                    value={editingMurid.no_hp || ''}
                    onChange={(e) => setEditingMurid({ ...editingMurid, no_hp: e.target.value })}
                    className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">Nilai Rata-rata</label>
                  <input
                    type="number" step="0.01"
                    value={editingMurid.nilai || ''}
                    onChange={(e) => setEditingMurid({ ...editingMurid, nilai: e.target.value })}
                    className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Alamat Lengkap</label>
                <textarea
                  value={editingMurid.alamat || ''}
                  onChange={(e) => setEditingMurid({ ...editingMurid, alamat: e.target.value })}
                  className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-h-[60px]"
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">Nama Wali</label>
                  <input
                    type="text"
                    value={editingMurid.nama_wali || ''}
                    onChange={(e) => setEditingMurid({ ...editingMurid, nama_wali: e.target.value })}
                    className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold text-gray-500 mb-1">No. HP Wali</label>
                  <input
                    type="text"
                    value={editingMurid.no_wali || ''}
                    onChange={(e) => setEditingMurid({ ...editingMurid, no_wali: e.target.value })}
                    className="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                  />
                </div>
              </div>

              <div className="flex gap-3 pt-4">
                <button
                  type="button"
                  onClick={() => setIsEditModalOpen(false)}
                  className="flex-1 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 transition-colors"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={savingEdit}
                  className="flex-1 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50"
                >
                  {savingEdit ? 'Menyimpan...' : 'Simpan Perubahan'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Zoom Photo Modal */}
      {zoomPhoto && (
        <div className="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm cursor-zoom-out" onClick={() => setZoomPhoto(null)}>
          <div className="relative max-w-4xl max-h-[90vh] flex items-center justify-center animate-in zoom-in duration-200">
            <img src={zoomPhoto} alt="Zoomed" className="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl" />
            <button className="absolute -top-4 -right-4 bg-white text-black rounded-full w-8 h-8 flex items-center justify-center font-bold hover:scale-110 transition-transform">X</button>
          </div>
        </div>
      )}

      {/* Modal Hasil Sinkronisasi */}
      {isSyncModalOpen && syncResult && (
        <div className="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
          <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
            <div className="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white text-center relative">
              <div className="mx-auto w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-3">
                <RefreshCw size={32} className="text-white animate-spin-slow" />
              </div>
              <h2 className="text-xl font-black">Sinkronisasi Berhasil!</h2>
              <p className="text-xs text-emerald-100 mt-1">Koneksi API Mitra Pembayaran</p>
            </div>
            <div className="p-6 space-y-4 text-sm text-gray-700 dark:text-gray-200">
              <p className="text-center font-medium text-gray-500 dark:text-gray-400">
                Database absensi telah diperbarui secara real-time dengan data terbaru dari SmartPesantren.
              </p>
              
              <div className="grid grid-cols-3 gap-3 text-center">
                <div className="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                  <div className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Data</div>
                  <div className="text-lg font-extrabold text-blue-600 dark:text-blue-400 mt-1">
                    {syncResult.total_data_mitra || 0}
                  </div>
                </div>
                <div className="bg-green-50 dark:bg-green-950/20 p-3 rounded-2xl border border-green-100 dark:border-green-900/30">
                  <div className="text-[10px] font-bold text-green-500 uppercase tracking-wider">Santri Baru</div>
                  <div className="text-xl font-extrabold text-green-600 dark:text-green-400 mt-1">
                    {syncResult.new_students || 0}
                  </div>
                </div>
                <div className="bg-purple-50 dark:bg-purple-950/20 p-3 rounded-2xl border border-purple-100 dark:border-purple-900/30">
                  <div className="text-[10px] font-bold text-purple-500 uppercase tracking-wider">Diperbarui</div>
                  <div className="text-xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">
                    {syncResult.updated_students || 0}
                  </div>
                </div>
              </div>

              <div className="bg-blue-50/50 dark:bg-blue-900/10 p-3 rounded-xl border border-blue-100/50 dark:border-blue-900/30 text-xs text-blue-700 dark:text-blue-400 leading-relaxed">
                ℹ️ <strong>Informasi Keamanan:</strong> Kolom <code>barcode_id</code> (kartu QR) santri lama tetap dipertahankan dan tidak ditimpa demi keamanan data kartu absensi.
              </div>

              <button
                type="button"
                onClick={() => setIsSyncModalOpen(false)}
                className="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold rounded-2xl shadow-lg shadow-emerald-600/20 transition-all hover:scale-[1.02] active:scale-100 mt-2"
              >
                Mantap, Selesai!
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
