-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2025 at 04:17 PM
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
-- Database: `pautos-veture`
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
(1, 'default', 'Regular user with basic privileges', '2025-04-19 15:37:41'),
(2, 'runner', 'User who can perform delivery tasks', '2025-04-19 15:37:41'),
(3, 'owner', 'Business owner with management privileges', '2025-04-19 15:37:41'),
(4, 'admin', 'System administrator with full access', '2025-04-19 15:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `runners`
--

CREATE TABLE `runners` (
  `runner_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_photo` varchar(255) NOT NULL,
  `selfie_photo` varchar(255) NOT NULL,
  `transportation_method` enum('vehicle','walking','commute') NOT NULL,
  `application_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `status_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `runners`
--

INSERT INTO `runners` (`runner_id`, `user_id`, `id_photo`, `selfie_photo`, `transportation_method`, `application_status`, `status_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'uploads/runner/id_1745057121_id.png', 'uploads/runner/selfie_1745057121_Andrea-Imperio_PR.jpg', 'walking', 'approved', NULL, '2025-04-19 10:05:21', '2025-04-19 13:37:24'),
(2, 4, 'uploads/runner/id_1745059728_id.png', 'uploads/runner/selfie_1745059728_unnamed (1).jpg', 'walking', 'approved', 'Verified', '2025-04-19 10:48:48', '2025-04-19 13:59:19'),
(3, 5, '../assets/image/upload/runner_docs/id_1745060928_id.png', '../assets/image/upload/runner_docs/selfie_1745060928_Andrea-Imperio_PR.jpg', 'vehicle', 'rejected', NULL, '2025-04-19 11:08:48', '2025-04-19 13:42:08');

-- --------------------------------------------------------

--
-- Table structure for table `runner_services`
--

CREATE TABLE `runner_services` (
  `runner_service_id` int(11) NOT NULL,
  `runner_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `runner_services`
--

INSERT INTO `runner_services` (`runner_service_id`, `runner_id`, `subcategory_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 1),
(5, 2, 2),
(6, 2, 12),
(7, 2, 14),
(8, 3, 11),
(9, 3, 12),
(10, 3, 21),
(11, 3, 22);

-- --------------------------------------------------------

--
-- Table structure for table `runner_transit`
--

CREATE TABLE `runner_transit` (
  `transit_id` int(11) NOT NULL,
  `runner_id` int(11) NOT NULL,
  `transit_type` enum('Motorcycle','Tricycle','Jeepney','Taxi','Multiple') NOT NULL,
  `transit_radius` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `runner_vehicles`
--

CREATE TABLE `runner_vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `runner_id` int(11) NOT NULL,
  `vehicle_type` enum('Motorcycle','E-Bike','Bicycle','Car','Van') NOT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `vehicle_phone` varchar(20) DEFAULT NULL,
  `vehicle_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `runner_vehicles`
--

INSERT INTO `runner_vehicles` (`vehicle_id`, `runner_id`, `vehicle_type`, `registration_number`, `license_number`, `vehicle_phone`, `vehicle_photo`) VALUES
(1, 3, 'E-Bike', '21312', '12312', '123', '../assets/image/upload/runner_docs/vehicle_1745060928_download.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `runner_walking`
--

CREATE TABLE `runner_walking` (
  `walking_id` int(11) NOT NULL,
  `runner_id` int(11) NOT NULL,
  `service_radius` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `runner_walking`
--

INSERT INTO `runner_walking` (`walking_id`, `runner_id`, `service_radius`) VALUES
(1, 1, 21),
(2, 2, 21);

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `category_code` varchar(30) NOT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`category_id`, `category_name`, `category_code`, `icon`) VALUES
(1, 'Cleaning', 'cleaning', 'bi-house-check'),
(2, 'Shopping + Delivery', 'shopping-delivery', 'bi-cart3'),
(3, 'Babysitter', 'babysitter', 'bi-people'),
(4, 'Personal Assistant', 'personal-assistant', 'bi-briefcase'),
(5, 'Senior Assistance', 'senior-assistance', 'bi-heart-pulse'),
(6, 'Pet Care', 'pet-care', 'bi-piggy-bank');

-- --------------------------------------------------------

--
-- Table structure for table `service_subcategories`
--

CREATE TABLE `service_subcategories` (
  `subcategory_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_subcategories`
--

INSERT INTO `service_subcategories` (`subcategory_id`, `category_id`, `subcategory_name`) VALUES
(1, 1, 'House Cleaning'),
(2, 1, 'Office Cleaning'),
(3, 1, 'Deep Cleaning'),
(4, 1, 'Window Cleaning'),
(5, 1, 'Carpet Cleaning'),
(6, 2, 'Grocery Shopping'),
(7, 2, 'Food Delivery'),
(8, 2, 'Package Pickup'),
(9, 2, 'Medicine Delivery'),
(10, 2, 'Gift Shopping'),
(11, 3, 'Daytime Childcare'),
(12, 3, 'Evening Babysitting'),
(13, 3, 'Infant Care'),
(14, 3, 'Homework Help'),
(15, 3, 'School Drop-off/Pickup'),
(16, 4, 'Administrative Tasks'),
(17, 4, 'Event Planning'),
(18, 4, 'Research'),
(19, 4, 'Bookkeeping'),
(20, 4, 'Scheduling'),
(21, 5, 'Companion Care'),
(22, 5, 'Medication Reminders'),
(23, 5, 'Light Housekeeping'),
(24, 5, 'Meal Preparation'),
(25, 5, 'Transportation'),
(26, 6, 'Dog Walking'),
(27, 6, 'Pet Sitting'),
(28, 6, 'Feeding'),
(29, 6, 'Grooming'),
(30, 6, 'Pet Transportation');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `password`, `role_id`, `created_at`) VALUES
(1, 'John', 'Ruelo', 'evangs111@gmail.com', '09500847723', '$2y$10$JAU5FnsnzA4EMCDffxVyZ.zLAcZRmP827IcnvO3y2Cq65EA03nRN.', 2, '2025-04-19 15:53:19'),
(3, 'owner', 'owner', 'owner@gmail.com', '09271572108', '$2y$10$orIiH2e6458FNp1iIrY.iunrBrqvKga4BSbTwNZDaXt2Pfc3Rnf9m', 3, '2025-04-19 18:33:17'),
(4, 'katrina', 'lolo', 'katrina@gmail.com', '09271572108', '$2y$10$r2Sdldqs/inZZGAz8fLhM.yWoDKDt3ydzWRotcUgdDn/73QUyiSB.', 2, '2025-04-19 18:38:11'),
(5, 'belen', 'evangelio', 'belen@gmail.com', '09500847723', '$2y$10$IbS7190jjNBAFA.V82aYau067riFdwdtrv4iGkwUifqguzjTtP.6u', 1, '2025-04-19 18:49:58');

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
  `postal_code` varchar(20) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`address_id`, `street_number`, `street_name`, `barangay`, `district`, `city_municipality`, `province`, `region`, `postal_code`, `landmark`, `created_at`, `updated_at`) VALUES
(1, '111', 'Virgin Delos Remedios', '', '', 'Davao City', 'Davao del Sur', 'Philippines', '8000', '', '2025-04-19 09:55:37', '2025-04-19 09:55:37'),
(2, '7G', 'PUROK DACUDAO', '', '', 'Davao City', 'Davao del Sur', 'Philippines', '', '', '2025-04-19 12:39:12', '2025-04-19 12:39:12'),
(3, '111', 'Virgin Delos Remedios', '', '', 'Davao City', 'Davao del Sur', 'Philippines', '8000', '', '2025-04-19 12:50:28', '2025-04-19 12:50:28');

-- --------------------------------------------------------

--
-- Table structure for table `user_locations`
--

CREATE TABLE `user_locations` (
  `location_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `location_type` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_locations`
--

INSERT INTO `user_locations` (`location_id`, `user_id`, `location_type`, `latitude`, `longitude`, `address_id`, `timestamp`, `created_at`, `updated_at`) VALUES
(1, 1, 'auto', 7.0242357, 125.4891363, 1, '2025-04-19 15:55:37', '2025-04-19 09:55:37', '2025-04-19 09:55:37'),
(2, 4, 'adjusted', 6.9993135, 125.4981914, 2, '2025-04-19 18:39:12', '2025-04-19 12:39:12', '2025-04-19 12:39:12'),
(3, 5, 'auto', 7.0242357, 125.4891363, 3, '2025-04-19 18:50:28', '2025-04-19 12:50:28', '2025-04-19 12:50:28');

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
(1, 1, 'assests/image/uploads/profile_pictures/profile_68035739a872b5.30145829.png', '2025-04-19 15:56:41', '2025-04-19 15:56:41'),
(2, 4, 'assests/image/uploads/profile_pictures/profile_68037d5eb51e71.66588065.jfif', '2025-04-19 18:39:26', '2025-04-19 18:39:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `runners`
--
ALTER TABLE `runners`
  ADD PRIMARY KEY (`runner_id`),
  ADD KEY `fk_runner_user` (`user_id`);

--
-- Indexes for table `runner_services`
--
ALTER TABLE `runner_services`
  ADD PRIMARY KEY (`runner_service_id`),
  ADD KEY `runner_id` (`runner_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `runner_transit`
--
ALTER TABLE `runner_transit`
  ADD PRIMARY KEY (`transit_id`),
  ADD KEY `runner_id` (`runner_id`);

--
-- Indexes for table `runner_vehicles`
--
ALTER TABLE `runner_vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `runner_id` (`runner_id`);

--
-- Indexes for table `runner_walking`
--
ALTER TABLE `runner_walking`
  ADD PRIMARY KEY (`walking_id`),
  ADD KEY `runner_id` (`runner_id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `service_subcategories`
--
ALTER TABLE `service_subcategories`
  ADD PRIMARY KEY (`subcategory_id`),
  ADD KEY `category_id` (`category_id`);

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
-- AUTO_INCREMENT for table `runners`
--
ALTER TABLE `runners`
  MODIFY `runner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `runner_services`
--
ALTER TABLE `runner_services`
  MODIFY `runner_service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `runner_transit`
--
ALTER TABLE `runner_transit`
  MODIFY `transit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `runner_vehicles`
--
ALTER TABLE `runner_vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `runner_walking`
--
ALTER TABLE `runner_walking`
  MODIFY `walking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service_subcategories`
--
ALTER TABLE `service_subcategories`
  MODIFY `subcategory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_locations`
--
ALTER TABLE `user_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `runners`
--
ALTER TABLE `runners`
  ADD CONSTRAINT `fk_runner_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `runner_services`
--
ALTER TABLE `runner_services`
  ADD CONSTRAINT `runner_services_ibfk_1` FOREIGN KEY (`runner_id`) REFERENCES `runners` (`runner_id`),
  ADD CONSTRAINT `runner_services_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `service_subcategories` (`subcategory_id`);

--
-- Constraints for table `runner_transit`
--
ALTER TABLE `runner_transit`
  ADD CONSTRAINT `runner_transit_ibfk_1` FOREIGN KEY (`runner_id`) REFERENCES `runners` (`runner_id`);

--
-- Constraints for table `runner_vehicles`
--
ALTER TABLE `runner_vehicles`
  ADD CONSTRAINT `runner_vehicles_ibfk_1` FOREIGN KEY (`runner_id`) REFERENCES `runners` (`runner_id`);

--
-- Constraints for table `runner_walking`
--
ALTER TABLE `runner_walking`
  ADD CONSTRAINT `runner_walking_ibfk_1` FOREIGN KEY (`runner_id`) REFERENCES `runners` (`runner_id`);

--
-- Constraints for table `service_subcategories`
--
ALTER TABLE `service_subcategories`
  ADD CONSTRAINT `service_subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`category_id`);

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
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
