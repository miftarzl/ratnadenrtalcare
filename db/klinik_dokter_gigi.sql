  -- phpMyAdmin SQL Dump
  -- version 5.2.1
  -- https://www.phpmyadmin.net/
  --
  -- Host: 127.0.0.1
  -- Generation Time: Aug 07, 2025 at 09:36 AM
  -- Server version: 10.4.32-MariaDB
  -- PHP Version: 8.2.12

  CREATE DATABASE IF NOT EXISTS `klinik_dokter_gigi`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

  USE `klinik_dokter_gigi`;

  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
  START TRANSACTION;
  SET time_zone = "+00:00";


  /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
  /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
  /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
  /*!40101 SET NAMES utf8mb4 */;

  --
  -- Database: `klinik_dokter_gigi`
  --

  -- --------------------------------------------------------

  --
  -- Table structure for table `doctors`
  --

  CREATE TABLE `doctors` (
    `id_dokter` int(11) NOT NULL,
    `nama` varchar(100) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  --
  -- Dumping data for table `doctors`
  --

  INSERT INTO `doctors` (`id_dokter`, `nama`) VALUES
  (6, 'drg Ratna Ernafury'),
  (7, 'drg Ratna Ningsih');

  -- --------------------------------------------------------

  --
  -- Table structure for table `jadwal_dokter`
  --

  CREATE TABLE `jadwal_dokter` (
    `id_jadwal` int(11) NOT NULL,
    `id_dokter` int(11) NOT NULL,
    `hari` varchar(20) NOT NULL,
    `jam_mulai` time NOT NULL,
    `jam_selesai` time NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  --
  -- Dumping data for table `jadwal_dokter`
  --

  INSERT INTO `jadwal_dokter` (`id_jadwal`, `id_dokter`, `hari`, `jam_mulai`, `jam_selesai`) VALUES
  (26, 6, 'Senin', '09:00:00', '09:30:00'),
  (27, 6, 'Senin', '10:00:00', '10:30:00'),
  (28, 6, 'Senin', '11:00:00', '11:30:00'),
  (29, 6, 'Senin', '12:00:00', '12:30:00'),
  (30, 6, 'Senin', '13:00:00', '13:30:00'),
  (31, 6, 'Senin', '14:00:00', '14:30:00'),
  (32, 6, 'Senin', '15:00:00', '15:30:00'),
  (33, 6, 'Senin', '16:00:00', '16:30:00'),
  (34, 6, 'Senin', '17:00:00', '17:30:00'),
  (35, 6, 'Rabu', '09:00:00', '09:30:00'),
  (36, 6, 'Rabu', '10:00:00', '10:30:00'),
  (37, 6, 'Rabu', '11:00:00', '11:30:00'),
  (38, 6, 'Rabu', '12:00:00', '12:30:00'),
  (39, 6, 'Rabu', '13:00:00', '13:30:00'),
  (40, 6, 'Rabu', '14:00:00', '14:30:00'),
  (41, 6, 'Rabu', '15:00:00', '15:30:00'),
  (42, 6, 'Rabu', '16:00:00', '16:30:00'),
  (43, 6, 'Rabu', '17:00:00', '17:30:00'),
  (44, 6, 'Jumat', '09:00:00', '09:30:00'),
  (45, 6, 'Jumat', '10:00:00', '10:30:00'),
  (46, 6, 'Jumat', '11:00:00', '11:30:00'),
  (47, 6, 'Jumat', '12:00:00', '12:30:00'),
  (48, 6, 'Jumat', '13:00:00', '13:30:00'),
  (49, 6, 'Jumat', '14:00:00', '14:30:00'),
  (50, 6, 'Jumat', '15:00:00', '15:30:00'),
  (51, 6, 'Jumat', '16:00:00', '16:30:00'),
  (52, 6, 'Jumat', '17:00:00', '17:30:00'),
  (53, 7, 'Selasa', '09:00:00', '09:30:00'),
  (54, 7, 'Selasa', '10:00:00', '10:30:00'),
  (55, 7, 'Selasa', '11:00:00', '11:30:00'),
  (56, 7, 'Selasa', '12:00:00', '12:30:00'),
  (57, 7, 'Selasa', '13:00:00', '13:30:00'),
  (58, 7, 'Selasa', '14:00:00', '14:30:00'),
  (59, 7, 'Selasa', '15:00:00', '15:30:00'),
  (60, 7, 'Selasa', '16:00:00', '16:30:00'),
  (61, 7, 'Selasa', '17:00:00', '17:30:00'),
  (62, 7, 'Kamis', '09:00:00', '09:30:00'),
  (63, 7, 'Kamis', '10:00:00', '10:30:00'),
  (64, 7, 'Kamis', '11:00:00', '11:30:00'),
  (65, 7, 'Kamis', '12:00:00', '12:30:00'),
  (66, 7, 'Kamis', '13:00:00', '13:30:00'),
  (67, 7, 'Kamis', '14:00:00', '14:30:00'),
  (68, 7, 'Kamis', '15:00:00', '15:30:00'),
  (69, 7, 'Kamis', '16:00:00', '16:30:00'),
  (70, 7, 'Kamis', '17:00:00', '17:30:00'),
  (71, 7, 'Sabtu', '09:00:00', '09:30:00'),
  (72, 7, 'Sabtu', '10:00:00', '10:30:00'),
  (73, 7, 'Sabtu', '11:00:00', '11:30:00'),
  (74, 7, 'Sabtu', '12:00:00', '12:30:00'),
  (75, 7, 'Sabtu', '13:00:00', '13:30:00'),
  (76, 7, 'Sabtu', '14:00:00', '14:30:00'),
  (77, 7, 'Sabtu', '15:00:00', '15:30:00'),
  (78, 7, 'Sabtu', '16:00:00', '16:30:00'),
  (79, 7, 'Sabtu', '17:00:00', '17:30:00');

  -- --------------------------------------------------------

  --
  -- Table structure for table `reservations`
  --

  CREATE TABLE `reservations` (
    `id_reservasi` int(11) NOT NULL,
    `id_pasien` int(11) NOT NULL,
    `id_dokter` int(11) NOT NULL,
    `tanggal` date NOT NULL,
    `jam` time NOT NULL,
    `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `antrian` int(11) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  --
  -- Dumping data for table `reservations`
  --

  INSERT INTO `reservations` (`id_reservasi`, `id_pasien`, `id_dokter`, `tanggal`, `jam`, `status`, `updated_at`, `antrian`) VALUES
  (7, 3, 6, '2025-06-20', '16:00:00', 'confirmed', '2025-06-16 17:22:45', 1);

  -- --------------------------------------------------------

  --
  -- Table structure for table `users`
  --

  CREATE TABLE `users` (
    `id_user` int(11) NOT NULL,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `role` enum('admin','pasien') NOT NULL DEFAULT 'pasien'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  --
  -- Dumping data for table `users`
  --

  INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
  (1, 'admin', '$2y$10$mGgLZnhu/PE8UbXALHLcieItW.1zidvUI98eVJ8E/MdCa09xZojJq', 'admin'),
  (2, 'pasien1', '$2y$10$hGz3nkfqLRsgVpRgkglvZuETGaRAvbSiJQQdR4YfZUCxqcH91gwky', 'pasien'),
  (3, 'fadhil', '$2y$10$vhmE5yUYVq/.f0LG.JPjm.g43Kh3/JLvbi4XQvEuSkLFEy.VJPOSG', 'pasien'),
  (4, 'hotman', '$2y$10$MQrhKYVZbWbdllyedMTgKeqjYENcGTjxlNZ7pmOe4RyqZgbRmpLz6', 'pasien');

  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `doctors`
  --
  ALTER TABLE `doctors`
    ADD PRIMARY KEY (`id_dokter`);

  --
  -- Indexes for table `jadwal_dokter`
  --
  ALTER TABLE `jadwal_dokter`
    ADD PRIMARY KEY (`id_jadwal`),
    ADD KEY `id_dokter` (`id_dokter`);

  --
  -- Indexes for table `reservations`
  --
  ALTER TABLE `reservations`
    ADD PRIMARY KEY (`id_reservasi`),
    ADD KEY `id_pasien` (`id_pasien`),
    ADD KEY `id_dokter` (`id_dokter`);

  --
  -- Indexes for table `users`
  --
  ALTER TABLE `users`
    ADD PRIMARY KEY (`id_user`),
    ADD UNIQUE KEY `username` (`username`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `doctors`
  --
  ALTER TABLE `doctors`
    MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

  --
  -- AUTO_INCREMENT for table `jadwal_dokter`
  --
  ALTER TABLE `jadwal_dokter`
    MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

  --
  -- AUTO_INCREMENT for table `reservations`
  --
  ALTER TABLE `reservations`
    MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

  --
  -- AUTO_INCREMENT for table `users`
  --
  ALTER TABLE `users`
    MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

  --
  -- Constraints for dumped tables
  --

  --
  -- Constraints for table `jadwal_dokter`
  --
  ALTER TABLE `jadwal_dokter`
    ADD CONSTRAINT `jadwal_dokter_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `doctors` (`id_dokter`) ON DELETE CASCADE;

  --
  -- Constraints for table `reservations`
  --
  ALTER TABLE `reservations`
    ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `users` (`id_user`),
    ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `doctors` (`id_dokter`);
  COMMIT;

  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
  /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
