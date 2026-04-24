-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 22 Apr 2026 pada 10.30
-- Versi server: 10.6.24-MariaDB-cll-lve
-- Versi PHP: 8.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ppmawaro_absensi_online`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `absensi_id` int(11) NOT NULL,
  `jadwal_madin_id` int(11) NOT NULL COMMENT 'Jadwal yang dihadiri',
  `murid_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpa') NOT NULL,
  `keterangan` text DEFAULT NULL COMMENT 'Alasan jika tidak hadir',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi_guru`
--

CREATE TABLE `absensi_guru` (
  `absensi_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `jadwal_madin_id` int(11) DEFAULT NULL,
  `jadwal_quran_id` int(11) DEFAULT NULL,
  `kegiatan_id` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `waktu_absensi` timestamp NULL DEFAULT NULL,
  `deadline_absensi` timestamp NULL DEFAULT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpa') DEFAULT 'Alpa',
  `keterangan` text DEFAULT NULL COMMENT 'Alasan jika tidak hadir',
  `is_otomatis` tinyint(1) DEFAULT 0 COMMENT 'Apakah absensi dibuat otomatis oleh sistem',
  `notifikasi_terkirim` tinyint(1) DEFAULT 0,
  `bisa_diubah` tinyint(1) DEFAULT 1 COMMENT 'Apakah absensi bisa diubah manual',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `alumni`
--

CREATE TABLE `alumni` (
  `alumni_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nis` varchar(50) NOT NULL COMMENT 'Nomor Induk Santri saat masih aktif',
  `nik` varchar(20) DEFAULT NULL COMMENT 'Nomor Induk Kependudukan',
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tahun_masuk` year(4) NOT NULL,
  `tahun_keluar` year(4) NOT NULL,
  `status_keluar` enum('Lulus','Berhenti','Dikeluarkan') NOT NULL DEFAULT 'Lulus',
  `keterangan` text DEFAULT NULL COMMENT 'Alasan keluar atau informasi tambahan',
  `pekerjaan` varchar(255) DEFAULT NULL COMMENT 'Pekerjaan saat ini',
  `pendidikan_lanjut` varchar(255) DEFAULT NULL COMMENT 'Jenjang pendidikan yang sedang/telah ditempuh',
  `foto` varchar(255) DEFAULT NULL COMMENT 'Foto terbaru alumni',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `guru_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'ID user untuk login sistem',
  `nama` varchar(100) NOT NULL,
  `nip` varchar(20) DEFAULT NULL COMMENT 'Nomor Induk Pegawai',
  `nik` varchar(20) DEFAULT NULL COMMENT 'Nomor Induk Kependudukan',
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL COMMENT 'Jabatan di pesantren (Wali Kelas, Guru Mata Pelajaran, dll)',
  `foto` varchar(100) DEFAULT NULL COMMENT 'File foto guru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_kegiatan`
--

CREATE TABLE `jadwal_kegiatan` (
  `kegiatan_id` int(11) NOT NULL,
  `hari` enum('Sabtu','Ahad','Senin','Selasa','Rabu','Kamis','Jumat') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `kamar_id` int(11) NOT NULL,
  `guru_id` int(11) DEFAULT NULL COMMENT 'Pembina kamar (boleh kosong)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_madin`
--

CREATE TABLE `jadwal_madin` (
  `jadwal_id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `mata_pelajaran` varchar(100) NOT NULL,
  `kelas_madin_id` int(11) NOT NULL,
  `guru_id` int(11) DEFAULT NULL COMMENT 'Guru pengajar (boleh kosong)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_quran`
--

CREATE TABLE `jadwal_quran` (
  `id` int(11) NOT NULL,
  `kelas_quran_id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `mata_pelajaran` varchar(100) NOT NULL COMMENT 'Materi pembelajaran Quran',
  `guru_id` int(11) DEFAULT NULL COMMENT 'Guru pengajar Quran (boleh kosong)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kamar`
--

CREATE TABLE `kamar` (
  `kamar_id` int(11) NOT NULL,
  `nama_kamar` varchar(50) NOT NULL COMMENT 'Nama kamar asrama',
  `kapasitas` int(11) DEFAULT 0 COMMENT 'Jumlah maksimal santri dalam kamar',
  `keterangan` text DEFAULT NULL COMMENT 'Informasi tambahan tentang kamar',
  `guru_id` int(11) DEFAULT NULL COMMENT 'Pembina kamar (boleh kosong)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas_madin`
--

CREATE TABLE `kelas_madin` (
  `kelas_id` int(11) NOT NULL,
  `nama_kelas` varchar(20) NOT NULL COMMENT 'Contoh: VII-A, VIII-B',
  `guru_id` int(11) DEFAULT NULL COMMENT 'Wali kelas (boleh kosong)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas_quran`
--

CREATE TABLE `kelas_quran` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL COMMENT 'Nama kelas Quran (Tahfidz, Pemula, Menengah, Lanjut)',
  `guru_id` int(11) DEFAULT NULL COMMENT 'Guru pengampu kelas Quran',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) DEFAULT 0 COMMENT 'Apakah login berhasil'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `murid`
--

CREATE TABLE `murid` (
  `murid_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nis` varchar(20) NOT NULL COMMENT 'Nomor Induk Santri',
  `nik` varchar(20) DEFAULT NULL COMMENT 'Nomor Induk Kependudukan',
  `kelas_madin_id` int(11) DEFAULT NULL COMMENT 'Kelas Madrasah Diniyah',
  `kelas_quran_id` int(11) DEFAULT NULL COMMENT 'Kelas Al-Quran',
  `kamar_id` int(11) DEFAULT NULL COMMENT 'Kamar asrama tempat tinggal',
  `no_hp` varchar(15) DEFAULT NULL COMMENT 'Nomor HP santri',
  `alamat` text DEFAULT NULL COMMENT 'Alamat asal santri',
  `nama_wali` varchar(100) DEFAULT NULL COMMENT 'Nama wali santri',
  `no_wali` varchar(15) DEFAULT NULL COMMENT 'Nomor HP wali santri',
  `nilai` decimal(5,2) DEFAULT 0.00 COMMENT 'Nilai rata-rata santri',
  `foto` varchar(100) DEFAULT NULL COMMENT 'File foto santri',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggaran`
--

CREATE TABLE `pelanggaran` (
  `pelanggaran_id` int(11) NOT NULL,
  `murid_id` int(11) NOT NULL,
  `jenis` varchar(100) NOT NULL COMMENT 'Jenis pelanggaran',
  `tanggal` date NOT NULL,
  `deskripsi` text DEFAULT NULL COMMENT 'Detail pelanggaran',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_absensi_otomatis`
--

CREATE TABLE `pengaturan_absensi_otomatis` (
  `id` int(11) NOT NULL,
  `nama_pengaturan` varchar(100) NOT NULL,
  `nilai` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL COMMENT 'Penjelasan tentang pengaturan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `pengaturan_absensi_otomatis`
--

INSERT INTO `pengaturan_absensi_otomatis` (`id`, `nama_pengaturan`, `nilai`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'absensi_otomatis_guru', '0', 'Aktifkan absensi otomatis untuk guru (0=nonaktif, 1=aktif)', '2025-11-24 03:26:35', '2025-11-24 03:26:35'),
(2, 'waktu_tenggang_absensi', '2', 'Waktu tenggang untuk absensi guru dalam jam', '2025-11-24 03:26:35', '2025-11-24 03:26:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_notifikasi`
--

CREATE TABLE `pengaturan_notifikasi` (
  `id` int(11) NOT NULL,
  `nama_pengaturan` varchar(100) NOT NULL,
  `nilai` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL COMMENT 'Penjelasan tentang pengaturan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `pengaturan_notifikasi`
--

INSERT INTO `pengaturan_notifikasi` (`id`, `nama_pengaturan`, `nilai`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'notifikasi_aktif', '1', 'Aktifkan notifikasi jadwal belum diisi (0=nonaktif, 1=aktif)', '2025-11-24 03:26:35', '2025-11-24 03:26:35'),
(2, 'waktu_tampil_notifikasi', '1', 'Waktu notifikasi muncul setelah jadwal dimulai (dalam jam)', '2025-11-24 03:26:35', '2025-11-24 03:26:35'),
(3, 'batas_waktu_notifikasi', '24', 'Batas waktu notifikasi tetap muncul (dalam jam)', '2025-11-24 03:26:35', '2025-11-24 03:26:35'),
(4, 'refresh_otomatis', '5', 'Interval refresh notifikasi otomatis (dalam menit)', '2025-11-24 03:26:35', '2025-11-24 03:26:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perizinan`
--

CREATE TABLE `perizinan` (
  `perizinan_id` int(11) NOT NULL,
  `murid_id` int(11) NOT NULL,
  `jenis` varchar(100) NOT NULL COMMENT 'Jenis izin (sakit, keluar, dll)',
  `tanggal` date NOT NULL,
  `deskripsi` text DEFAULT NULL COMMENT 'Alasan izin',
  `status_izin` enum('Disetujui','Menunggu','Ditolak') DEFAULT 'Menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','wali_kelas','wali_murid','guru','staff') NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `dark_mode` tinyint(1) DEFAULT 0 COMMENT 'Mode gelap/tema terang',
  `foto_profil` varchar(255) DEFAULT 'default-avatar.png',
  `kelas_id` int(11) DEFAULT NULL COMMENT 'Untuk wali_kelas: kelas yang diampu',
  `murid_id` int(11) DEFAULT NULL COMMENT 'Untuk wali_murid: murid yang menjadi tanggungan',
  `email` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Status aktif/nonaktif user',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'Waktu login terakhir',
  `nama` varchar(100) DEFAULT NULL COMMENT 'Nama lengkap user',
  `nip` varchar(20) DEFAULT NULL COMMENT 'Nomor Induk Pegawai (untuk guru/staff)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`absensi_id`),
  ADD KEY `jadwal_madin_id` (`jadwal_madin_id`),
  ADD KEY `idx_absensi_tanggal` (`tanggal`),
  ADD KEY `idx_absensi_murid_tanggal` (`murid_id`,`tanggal`),
  ADD KEY `idx_absensi_status` (`status`);

--
-- Indeks untuk tabel `absensi_guru`
--
ALTER TABLE `absensi_guru`
  ADD PRIMARY KEY (`absensi_id`),
  ADD UNIQUE KEY `unique_guru_tanggal` (`guru_id`,`tanggal`),
  ADD KEY `jadwal_madin_id` (`jadwal_madin_id`),
  ADD KEY `jadwal_quran_id` (`jadwal_quran_id`),
  ADD KEY `kegiatan_id` (`kegiatan_id`);

--
-- Indeks untuk tabel `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`alumni_id`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`guru_id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_guru_nip` (`nip`);

--
-- Indeks untuk tabel `jadwal_kegiatan`
--
ALTER TABLE `jadwal_kegiatan`
  ADD PRIMARY KEY (`kegiatan_id`),
  ADD KEY `kamar_id` (`kamar_id`),
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `idx_jadwal_kegiatan_hari` (`hari`);

--
-- Indeks untuk tabel `jadwal_madin`
--
ALTER TABLE `jadwal_madin`
  ADD PRIMARY KEY (`jadwal_id`),
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `idx_jadwal_hari` (`hari`),
  ADD KEY `idx_jadwal_kelas` (`kelas_madin_id`);

--
-- Indeks untuk tabel `jadwal_quran`
--
ALTER TABLE `jadwal_quran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_quran_id` (`kelas_quran_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`kamar_id`),
  ADD UNIQUE KEY `nama_kamar` (`nama_kamar`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `kelas_madin`
--
ALTER TABLE `kelas_madin`
  ADD PRIMARY KEY (`kelas_id`),
  ADD UNIQUE KEY `nama_kelas` (`nama_kelas`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `kelas_quran`
--
ALTER TABLE `kelas_quran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_attempt_time` (`attempt_time`);

--
-- Indeks untuk tabel `murid`
--
ALTER TABLE `murid`
  ADD PRIMARY KEY (`murid_id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `kelas_quran_id` (`kelas_quran_id`),
  ADD KEY `idx_murid_nis` (`nis`),
  ADD KEY `idx_murid_kelas` (`kelas_madin_id`),
  ADD KEY `idx_murid_kamar` (`kamar_id`);

--
-- Indeks untuk tabel `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD PRIMARY KEY (`pelanggaran_id`),
  ADD KEY `murid_id` (`murid_id`);

--
-- Indeks untuk tabel `pengaturan_absensi_otomatis`
--
ALTER TABLE `pengaturan_absensi_otomatis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_pengaturan` (`nama_pengaturan`);

--
-- Indeks untuk tabel `pengaturan_notifikasi`
--
ALTER TABLE `pengaturan_notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_pengaturan` (`nama_pengaturan`);

--
-- Indeks untuk tabel `perizinan`
--
ALTER TABLE `perizinan`
  ADD PRIMARY KEY (`perizinan_id`),
  ADD KEY `murid_id` (`murid_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `murid_id` (`murid_id`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `absensi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `absensi_guru`
--
ALTER TABLE `absensi_guru`
  MODIFY `absensi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `alumni`
--
ALTER TABLE `alumni`
  MODIFY `alumni_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `guru_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_kegiatan`
--
ALTER TABLE `jadwal_kegiatan`
  MODIFY `kegiatan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_madin`
--
ALTER TABLE `jadwal_madin`
  MODIFY `jadwal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_quran`
--
ALTER TABLE `jadwal_quran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kamar`
--
ALTER TABLE `kamar`
  MODIFY `kamar_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kelas_madin`
--
ALTER TABLE `kelas_madin`
  MODIFY `kelas_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kelas_quran`
--
ALTER TABLE `kelas_quran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `murid`
--
ALTER TABLE `murid`
  MODIFY `murid_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pelanggaran`
--
ALTER TABLE `pelanggaran`
  MODIFY `pelanggaran_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengaturan_absensi_otomatis`
--
ALTER TABLE `pengaturan_absensi_otomatis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pengaturan_notifikasi`
--
ALTER TABLE `pengaturan_notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `perizinan`
--
ALTER TABLE `perizinan`
  MODIFY `perizinan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`jadwal_madin_id`) REFERENCES `jadwal_madin` (`jadwal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`murid_id`) REFERENCES `murid` (`murid_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `absensi_guru`
--
ALTER TABLE `absensi_guru`
  ADD CONSTRAINT `absensi_guru_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensi_guru_ibfk_2` FOREIGN KEY (`jadwal_madin_id`) REFERENCES `jadwal_madin` (`jadwal_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `absensi_guru_ibfk_3` FOREIGN KEY (`jadwal_quran_id`) REFERENCES `jadwal_quran` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `absensi_guru_ibfk_4` FOREIGN KEY (`kegiatan_id`) REFERENCES `jadwal_kegiatan` (`kegiatan_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `jadwal_kegiatan`
--
ALTER TABLE `jadwal_kegiatan`
  ADD CONSTRAINT `jadwal_kegiatan_ibfk_1` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`kamar_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_kegiatan_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `jadwal_madin`
--
ALTER TABLE `jadwal_madin`
  ADD CONSTRAINT `jadwal_madin_ibfk_1` FOREIGN KEY (`kelas_madin_id`) REFERENCES `kelas_madin` (`kelas_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_madin_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `jadwal_quran`
--
ALTER TABLE `jadwal_quran`
  ADD CONSTRAINT `jadwal_quran_ibfk_1` FOREIGN KEY (`kelas_quran_id`) REFERENCES `kelas_quran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_quran_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kamar`
--
ALTER TABLE `kamar`
  ADD CONSTRAINT `kamar_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kelas_madin`
--
ALTER TABLE `kelas_madin`
  ADD CONSTRAINT `kelas_madin_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kelas_quran`
--
ALTER TABLE `kelas_quran`
  ADD CONSTRAINT `kelas_quran_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `murid`
--
ALTER TABLE `murid`
  ADD CONSTRAINT `murid_ibfk_1` FOREIGN KEY (`kelas_madin_id`) REFERENCES `kelas_madin` (`kelas_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `murid_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`kamar_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `murid_ibfk_3` FOREIGN KEY (`kelas_quran_id`) REFERENCES `kelas_quran` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD CONSTRAINT `pelanggaran_ibfk_1` FOREIGN KEY (`murid_id`) REFERENCES `murid` (`murid_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `perizinan`
--
ALTER TABLE `perizinan`
  ADD CONSTRAINT `perizinan_ibfk_1` FOREIGN KEY (`murid_id`) REFERENCES `murid` (`murid_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas_madin` (`kelas_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`murid_id`) REFERENCES `murid` (`murid_id`) ON DELETE SET NULL;

DELIMITER $$
--
-- Event
--
CREATE DEFINER=`root`@`localhost` EVENT `cleanup_old_login_attempts` ON SCHEDULE EVERY 1 DAY STARTS '2025-11-24 10:29:24' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Hapus data percobaan login yang lebih dari 30 hari
    DELETE FROM login_attempts 
    WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 30 DAY);
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
