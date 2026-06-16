-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 16, 2026 at 07:58 PM
-- Server version: 11.2.2-MariaDB-log
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `posyandu_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `anak`
--

CREATE TABLE `anak` (
  `id` int(11) NOT NULL,
  `id_keluarga` int(11) NOT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `anak_ke` int(11) DEFAULT NULL,
  `berat_lahir` decimal(5,2) DEFAULT NULL,
  `panjang_lahir` decimal(5,2) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `status` enum('aktif','pindah','meninggal') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `anak`
--

INSERT INTO `anak` (`id`, `id_keluarga`, `nik`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `anak_ke`, `berat_lahir`, `panjang_lahir`, `nama_ayah`, `nama_ibu`, `status`, `created_at`, `foto`) VALUES
(2, 3, '11111111111', 'Yusuf Abdurrahmanmm', 'Kebumen', '2000-02-11', 'L', 1, 77.00, 80.00, 'wisnu wis', 'yuriza nurul a', 'aktif', '2026-05-20 20:31:23', NULL),
(4, 3, '23234567893', 'Ashfa Adlan', 'Kebumen', '2022-01-20', 'L', 2, 34.00, 33.99, 'zxcvbnm', 'cvbnm,.,mnbv', 'aktif', '2026-06-01 21:33:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `imunisasi`
--

CREATE TABLE `imunisasi` (
  `id` int(11) NOT NULL,
  `id_anak` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `jenis_imunisasi` varchar(100) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `diberikan_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `imunisasi`
--

INSERT INTO `imunisasi` (`id`, `id_anak`, `id_kegiatan`, `jenis_imunisasi`, `tanggal`, `diberikan_oleh`, `created_at`) VALUES
(2, 4, 4, 'DPT-HB-Hib', '2026-06-16', 1, '2026-06-16 17:58:00'),
(3, 2, 4, 'Campak', '2026-06-16', 1, '2026-06-16 17:58:36'),
(4, 4, 4, 'MR', '2026-06-16', 1, '2026-06-16 18:42:30');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `pertemuan_ke` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('scheduled','selesai') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `tanggal`, `lokasi`, `keterangan`, `pertemuan_ke`, `created_by`, `created_at`, `status`) VALUES
(4, '2026-06-16', 'PKD Kedungwaru', '', 1, 1, '2026-06-15 16:25:48', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran`
--

CREATE TABLE `kehadiran` (
  `id` int(11) NOT NULL,
  `id_anak` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `status_hadir` enum('hadir','tidak') NOT NULL,
  `dicatat_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kehadiran`
--

INSERT INTO `kehadiran` (`id`, `id_anak`, `id_kegiatan`, `status_hadir`, `dicatat_oleh`, `created_at`) VALUES
(7, 4, 4, 'hadir', 1, '2026-06-15 16:46:43'),
(8, 2, 4, 'hadir', 1, '2026-06-15 16:46:43');

-- --------------------------------------------------------

--
-- Table structure for table `keluarga`
--

CREATE TABLE `keluarga` (
  `id` int(11) NOT NULL,
  `no_kk` varchar(30) DEFAULT NULL,
  `nama_kepala_keluarga` varchar(100) NOT NULL,
  `nik_ayah` varchar(30) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `nik_ibu` varchar(30) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `rt` varchar(10) DEFAULT NULL,
  `rw` varchar(10) DEFAULT NULL,
  `desa` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `keluarga`
--

INSERT INTO `keluarga` (`id`, `no_kk`, `nama_kepala_keluarga`, `nik_ayah`, `nama_ayah`, `nik_ibu`, `nama_ibu`, `alamat`, `rt`, `rw`, `desa`, `kecamatan`, `no_hp`, `created_at`) VALUES
(3, '1234567890000', 'Bapak Wisnu Wibisono', '1110000', 'wisnu wis', '00001111', 'belum ada', 'Madureso, Kuwarasan, Kebumen Regency, Central Java, Indonesia', '02', '02', 'vbnm', 'xcvbnmmmmmmmmmmmmmmmmmmm', '111', '2026-05-20 20:26:42');

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id` int(11) NOT NULL,
  `id_anak` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `umur_bulan` int(11) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `lingkar_kepala` decimal(5,2) DEFAULT NULL,
  `status_gizi` varchar(50) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `diukur_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pemeriksaan`
--

INSERT INTO `pemeriksaan` (`id`, `id_anak`, `id_kegiatan`, `umur_bulan`, `berat_badan`, `tinggi_badan`, `lingkar_kepala`, `status_gizi`, `catatan`, `diukur_oleh`, `created_at`) VALUES
(4, 4, 4, 52, 8.00, 50.00, 40.00, 'Baik', '', 1, '2026-06-15 17:28:44'),
(5, 2, 4, 316, 9.00, 50.00, 40.00, 'Baik', '', 1, '2026-06-15 17:28:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kader','bidan') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Wisnu Wibisono', 'wisnu@gmail.com', '$2y$10$2m7TbQ5U9qqS4KvmxFWRDuVXKQ.l/MmG5MiViSCTmiguKNXtDYTQe', 'admin', '2026-04-27 17:18:14', '2026-06-16 19:28:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak`
--
ALTER TABLE `anak`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `idx_anak_keluarga` (`id_keluarga`);

--
-- Indexes for table `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kegiatan` (`id_kegiatan`),
  ADD KEY `diberikan_oleh` (`diberikan_oleh`),
  ADD KEY `idx_imunisasi_anak` (`id_anak`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `kehadiran`
--
ALTER TABLE `kehadiran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_kehadiran` (`id_anak`,`id_kegiatan`),
  ADD KEY `dicatat_oleh` (`dicatat_oleh`),
  ADD KEY `idx_kehadiran_kegiatan` (`id_kegiatan`);

--
-- Indexes for table `keluarga`
--
ALTER TABLE `keluarga`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_kk` (`no_kk`);

--
-- Indexes for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_pemeriksaan` (`id_anak`,`id_kegiatan`),
  ADD KEY `diukur_oleh` (`diukur_oleh`),
  ADD KEY `idx_pemeriksaan_anak` (`id_anak`),
  ADD KEY `idx_pemeriksaan_kegiatan` (`id_kegiatan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anak`
--
ALTER TABLE `anak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `imunisasi`
--
ALTER TABLE `imunisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kehadiran`
--
ALTER TABLE `kehadiran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `keluarga`
--
ALTER TABLE `keluarga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anak`
--
ALTER TABLE `anak`
  ADD CONSTRAINT `anak_ibfk_1` FOREIGN KEY (`id_keluarga`) REFERENCES `keluarga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD CONSTRAINT `imunisasi_ibfk_1` FOREIGN KEY (`id_anak`) REFERENCES `anak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imunisasi_ibfk_2` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imunisasi_ibfk_3` FOREIGN KEY (`diberikan_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kehadiran`
--
ALTER TABLE `kehadiran`
  ADD CONSTRAINT `kehadiran_ibfk_1` FOREIGN KEY (`id_anak`) REFERENCES `anak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kehadiran_ibfk_2` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kehadiran_ibfk_3` FOREIGN KEY (`dicatat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`id_anak`) REFERENCES `anak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pemeriksaan_ibfk_2` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pemeriksaan_ibfk_3` FOREIGN KEY (`diukur_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
