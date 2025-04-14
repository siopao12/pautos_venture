-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 11:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pautos_venture`
--

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `description`, `created_at`) VALUES
(1, 'default', 'Regular user with basic privileges', '2025-04-11 19:04:25'),
(2, 'runner', 'User who can perform delivery tasks', '2025-04-11 19:04:25'),
(3, 'owner', 'Business owner with management privileges', '2025-04-11 19:04:25'),
(4, 'admin', 'System administrator with full access', '2025-04-11 19:04:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `password`, `role_id`, `created_at`) VALUES
(1, 'John', 'Ruelo', 'evangs111@gmail.com', '09271572108', '$2y$10$80KoSmmrOB/uQfvYpHjYB.GlEOhCE4d.r6nYnANSCJVPUloNEAn5y', 1, '2025-04-11 19:07:09'),
(2, 'katrina', 'lolo', 'katrina@gmail.com', '09500847723', '$2y$10$/lEt65PFqKSjO9k/O2nHjeFPGn34ny1jIcIvfpb3PUr9rA91h7Uxi', 1, '2025-04-11 20:24:54'),
(3, 'arniel', 'ruelo', 'arniel@gmail.com', '09500847723', '$2y$10$bl43dEmGwO7zGQYBpiPx7OwFl/5VT2Hqn3WtDcq97Lny9KqEhE1QO', 1, '2025-04-11 20:29:08'),
(4, 'belen', 'ruelo', 'belen@gmail.com', '091091500876', '$2y$10$ikUAN0w5nQjr2mlH9tzZbeoLnvRacuIPOJjVxAIAED1O0J20xoRom', 1, '2025-04-11 21:33:02'),
(5, 'rica', 'ricaric', 'rica@gmail.com', '09700500821', '$2y$10$8eaSlqMsnQ9BVShQYapkVekq.sXoK.EWKuhEj5jZmkvl/ClT5hgq2', 1, '2025-04-11 21:52:57'),
(6, 'pipay', 'barny', 'pipay@gmail.com', '091091500876', '$2y$10$6fnIokLLtyEmPd7kdKTw7eec3DXMs6/xRjESLZBEpTWzvRFzY..TW', 1, '2025-04-13 21:54:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `address_id` int(11) NOT NULL,
  `street_number` varchar(20) DEFAULT NULL,
  `street_name` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `city_municipality` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`address_id`, `street_number`, `street_name`, `barangay`, `district`, `city_municipality`, `province`, `region`, `postal_code`, `landmark`, `created_at`, `updated_at`) VALUES
(2, '8000', 'Davao del Sur', '', '', 'Davao City', 'Davao del Sur', 'Philippines', '8000', '', '2025-04-13 10:07:20', '2025-04-13 10:07:20'),
(3, '', '', '', '', 'Davao City', 'Davao del Sur', 'Philippines', '', '', '2025-04-13 10:11:26', '2025-04-13 10:11:26'),
(4, '', '', '', '', 'Davao City', 'Davao del Sur', 'Philippines', '', '', '2025-04-14 03:47:26', '2025-04-14 03:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `user_locations`
--

CREATE TABLE `user_locations` (
  `location_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `location_type` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_locations`
--

INSERT INTO `user_locations` (`location_id`, `user_id`, `location_type`, `latitude`, `longitude`, `address_id`, `timestamp`, `created_at`, `updated_at`) VALUES
(2, 6, 'adjusted', 7.02253197, 125.48804196, 2, '2025-04-13 16:07:20', '2025-04-13 10:07:20', '2025-04-13 10:07:20'),
(3, 2, 'adjusted', 6.99959724, 125.49837417, 3, '2025-04-13 16:11:26', '2025-04-13 10:11:26', '2025-04-13 10:11:26'),
(4, 5, 'adjusted', 7.02271011, 125.48919261, 4, '2025-04-14 09:47:26', '2025-04-14 03:47:26', '2025-04-14 03:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `profile_picture`, `created_at`, `updated_at`) VALUES
(1, 1, 'assests/image/uploads/profile_pictures/profile_67f8ffbcef40f4.23426465.jpg', '2025-04-11 19:40:44', '2025-04-11 19:40:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `user_locations`
--
ALTER TABLE `user_locations`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_locations`
--
ALTER TABLE `user_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `user_locations`
--
ALTER TABLE `user_locations`
  ADD CONSTRAINT `user_locations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_locations_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `user_address` (`address_id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
