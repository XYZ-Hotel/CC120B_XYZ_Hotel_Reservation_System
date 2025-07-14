-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 01:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_reservations`
--

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','checked-in','checked-out') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `room_id`, `status`, `created_at`) VALUES
(3, 3, 1, 'checked-out', '2025-03-31 03:03:21'),
(4, 3, 2, 'checked-out', '2025-03-31 03:03:24'),
(5, 7, 1, 'checked-out', '2025-04-07 05:16:38'),
(6, 11, 6, 'checked-in', '2025-04-07 06:07:24'),
(7, 13, 5, 'confirmed', '2025-04-14 06:23:39'),
(8, 13, 2, 'checked-in', '2025-04-14 06:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `type` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('available','reserved','occupied') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `type`, `price`, `status`) VALUES
(1, '101', 'Single', 1500.00, 'available'),
(2, '102', 'Double', 2500.00, 'occupied'),
(3, '103', 'Suite', 5000.00, 'available'),
(4, '104', 'Standard Double Room', 2000.00, 'available'),
(5, '105', 'Presidential Suite', 10000.00, 'reserved'),
(6, '106', 'Presidential Suite', 10000.00, 'occupied'),
(7, '107', 'Junior Suite', 5000.00, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','superadmin','receptionist') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cell_number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password`, `role`, `created_at`, `cell_number`) VALUES
(3, 'Paul S. Salida', 'user1', 'paul@gmail.com', '$2y$10$VTadhevwv6LMZcMAIVeQXOhxofyHOItkXsMkaRIEq1pd4TbIO2cZ.', 'user', '2025-03-31 03:03:06', '09506009809'),
(4, 'M-A A. Gonayon', 'desk', 'ma@gmail.com', '$2y$10$zzHXf./bnUTbRfWbAynFqep.XXQYNBMpXYZualbELQI5tevqHnvC6', 'receptionist', '2025-03-31 03:04:28', '09506009802'),
(6, 'Super I. Admin', 'superadmin', 'admin@yahoo.com', '$2y$10$c6Z5KpRQappxetAupwpZoOKw8FeOviWH9FXLfIyDlEP6C8861cbdi', 'superadmin', '2025-03-31 04:49:51', '09123456789'),
(7, 'Arkein S. Bayongasan', 'arkinson', 'arkinson@gmail.com', '$2y$10$4E4MyMasporkA0mwPOHEkeX/lbzayr1RFay4rNyZdPy2GePIAMKtC', 'user', '2025-04-07 05:15:40', '09976804890'),
(8, 'Edward S. Bayongasan', 'Edward', 'edward@gmail.com', '$2y$10$EEsz2orzP1IgO8yVsvS3E.c3SY.z5XK2H8.nJQvkHd6qEg091ZXcG', 'receptionist', '2025-04-07 05:17:34', '09123456789'),
(9, 'Shadrock A. Balacang', 'Shad', 'shad@gmail.com', '$2y$10$ZpQ.EiNogFY/33eshF5rbuyLDa5kAtmDYZNy2NzHLiG3dkTIRNDIu', 'receptionist', '2025-04-07 05:18:44', '09506009802'),
(10, 'Cian L. Lloyd', 'cian', 'p@gmail.com', '$2y$10$tRn6Se.Eh/VHeRx8Xi6CAurMcvOnAgtDIFRUz8SDF2/N/7lbDQQ8K', 'user', '2025-04-07 06:00:58', '09090912122'),
(11, 'Ala D. in', 'larry', 'ala@gmail.com', '$2y$10$lxXkiH9MDTGdkd6fCTVs0.DRNAj3zINl5lZW7TGbmvNyOv29ep5oG', 'user', '2025-04-07 06:06:38', '09090909111'),
(12, 'Ben D. Ten', '1234', 'lop@gmail.com', '$2y$10$O6NOSey0h7ZL0EtsMSKaueZb3dH1HJhA2faNc5APQG/Av77WGBTJ2', 'user', '2025-04-07 06:15:52', '09128768768'),
(13, 'Joseph M. Jones', 'joseph123', 'lsadjf@gmail.com', '$2y$10$1R.pX/2oqWBAEkxE2FBxI.UU1/UEX.YhDY9Zo1pBRxOkpdDir9Bo.', 'user', '2025-04-14 06:07:42', '09232321322');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
