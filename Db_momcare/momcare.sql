-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Sep 2025 pada 07.46
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `momcare`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ibu`
--

CREATE TABLE `ibu` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp(),
  `tanggal_diupdate` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `risiko_tinggi` tinyint(1) DEFAULT 0,
  `usia_kehamilan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `nama_pasien` varchar(100) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `petugas` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_periksa`
--

CREATE TABLE `jadwal_periksa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_periksa` date NOT NULL,
  `waktu_periksa` time DEFAULT NULL,
  `nama_dokter` varchar(100) DEFAULT NULL,
  `status_jadwal` enum('Dijadwalkan','Selesai','Dibatalkan') DEFAULT 'Dijadwalkan',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeriksaan_ibu`
--

CREATE TABLE `pemeriksaan_ibu` (
  `id` int(11) NOT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `tanggal_pemeriksaan` date DEFAULT NULL,
  `tekanan_darah` varchar(10) DEFAULT NULL,
  `berat_badan` float DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `usia_kehamilan` varchar(50) DEFAULT NULL,
  `tinggi_fundus` varchar(50) DEFAULT NULL,
  `letak_janin` varchar(50) DEFAULT NULL,
  `denyut_jantung_janin` int(11) DEFAULT NULL,
  `tbj` decimal(7,2) DEFAULT NULL,
  `keluhan` text DEFAULT NULL,
  `hpl` date DEFAULT NULL,
  `hpht` date DEFAULT NULL,
  `glukosa_darah` varchar(50) DEFAULT NULL,
  `rujukan` varchar(50) DEFAULT NULL,
  `risiko_tinggi` tinyint(2) DEFAULT 0,
  `hb` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rujukan`
--

CREATE TABLE `rujukan` (
  `id` int(11) NOT NULL,
  `nama_ibu` varchar(255) NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `alasan_rujukan` varchar(255) NOT NULL,
  `rumah_sakit_tujuan` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp(),
  `tanggal_diupdate` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rujukan`
--

INSERT INTO `rujukan` (`id`, `nama_ibu`, `tanggal_pemeriksaan`, `alasan_rujukan`, `rumah_sakit_tujuan`, `catatan`, `tanggal_dibuat`, `tanggal_diupdate`) VALUES
(1, '', '2025-06-09', '', '', '', '2025-06-09 17:52:07', '2025-06-09 17:52:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `tempat_lahir` varchar(50) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `usia_kehamilan` varchar(50) DEFAULT NULL,
  `estimasi_kelahiran` date DEFAULT NULL,
  `kondisi_bayi` varchar(255) DEFAULT NULL,
  `role` enum('Petugas','Ibu Hamil') NOT NULL DEFAULT 'Ibu Hamil',
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nik`, `nama`, `no_hp`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `usia_kehamilan`, `estimasi_kelahiran`, `kondisi_bayi`, `role`, `email`, `password`, `created_at`) VALUES
(8, '3517106310790000', 'praja', '08672662111', 'terggalek', '2025-03-05', 'Kayen morosunggingan', '7', '2025-09-26', 'sehat', 'Ibu Hamil', 'prajalophinesti@gmail.com', '$2y$10$Sg1rMzWP328RZYXPJEFnCuzFzuVhP2d/nJ9q4JiGjyx1p5NXSycga', '2025-06-02 14:46:25'),
(9, '3567188435665614', 'Dani', '087765447661', 'Trenggalek', '1999-11-03', 'Jl P. Sudirman No 54', '6', '2025-11-19', 'sakit ', 'Ibu Hamil', 'aloganteng@gmail.com', '$2y$10$DRyY8xKtjFk.gp.GJly1KOPd4LgKK/ypgY6ezr1PdBWGgIHPahjza', '2025-06-02 15:05:36'),
(12, '3517106310790001', 'haidar', '085942921118', 'jombang', '2005-07-05', 'Kayen morosunggingan', NULL, NULL, NULL, 'Petugas', 'ardhaidar90@gmail.com', '$2y$10$8JzddMC95qhkGmLAoXJ5zecTinbdmkec0K9sRfatTmRWxZocbAZgS', '2025-06-07 07:05:21'),
(13, '2456098390', 'haidar  adlan', '0867578291911', 'jombang', '2005-07-05', 'Kayen', NULL, NULL, NULL, 'Petugas', 'haidartamako@gmail.com', '$2y$10$.U5Jv05eJjLzMrcSL8p0TePxleOCCkkOygDKXgjFdtYMgbF81KPg2', '2025-06-09 10:22:50'),
(14, '3517106310790008', 'jajaja', '08273777372', 'terggalek', '2025-06-19', 'jombang jawa timur ', NULL, NULL, NULL, 'Petugas', 'haidarzeta@gmaill.com', '$2y$10$cI8eu4fk7R5yBvYM6YIUQOJQOaqWsgmfk/nI.N7rbDm9mSgvmnl7m', '2025-06-09 10:30:02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ibu`
--
ALTER TABLE `ibu`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pemeriksaan_ibu`
--
ALTER TABLE `pemeriksaan_ibu`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rujukan`
--
ALTER TABLE `rujukan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ibu`
--
ALTER TABLE `ibu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pemeriksaan_ibu`
--
ALTER TABLE `pemeriksaan_ibu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `rujukan`
--
ALTER TABLE `rujukan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD CONSTRAINT `jadwal_periksa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
