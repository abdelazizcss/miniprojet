-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 21, 2024 at 01:45 AM
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
-- Database: `dental`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `maladie` varchar(255) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `presence` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `nom`, `prenom`, `date_naissance`, `maladie`, `doctor_id`, `date`, `telephone`, `email`, `status`, `presence`) VALUES
(15, 'boukhari', 'abdelfateh', '2005-01-01', 'Consultation et diagnostic dentaire', 11, '2024-12-21 09:00:00', '0799442733', 'patientttt@exemple.com', 'validé', 1);

-- --------------------------------------------------------

--
-- Table structure for table `prostheses`
--

CREATE TABLE `prostheses` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `prosthetist_id` int(11) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `prosthetist_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prostheses`
--

INSERT INTO `prostheses` (`id`, `patient_id`, `prosthetist_id`, `type`, `status`, `payment_status`, `prosthetist_name`) VALUES
(11, 21, NULL, 'amovible', 'en cours', 'payé', 'boukhari'),
(12, 21, NULL, 'amovible', 'en cours', 'payé', 'boukhari');

-- --------------------------------------------------------

--
-- Table structure for table `protheses`
--

CREATE TABLE `protheses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `protheses_payments`
--

CREATE TABLE `protheses_payments` (
  `id` int(11) NOT NULL,
  `prothese_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'admin'),
(2, 'medecin'),
(3, 'secretaire'),
(4, 'patient'),
(8, 'prothésiste');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `seuil_min` int(11) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `purchase_order` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `name`, `quantity`, `expiration_date`, `seuil_min`, `supplier`, `status`, `purchase_order`) VALUES
(7, 'legent', 11, '2024-12-31', 10, 'boukhari', 'demander', 'Screenshot from 2024-10-17 22-18-38.png'),
(8, 'pavete', 100, '2025-01-01', 10, 'boukhari', 'disponible', 'Screenshot from 2024-10-17 22-18-38.png');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`) VALUES
(11, 'dr boukhari', 'medecin@example.com', '$2y$10$GYaWvOFDgBF7B68If8UoVuUYYT3wXGd/3F9KLXshWutQp65E.TWGy', 2),
(13, 'mr abdelhamid', 'secretaire@example.com', '$2y$10$eUYXoE9iovJ6gYp6o3ejSOwytbEUT6ln93d9yElCnCo1IdtkU5AKK', 3),
(18, 'prosthetist1', 'prosthetist1@example.com', 'password_hash4', NULL),
(19, 'prosthetist2', 'prosthetist2@example.com', 'password_hash5', NULL),
(20, 'prosthetist3', 'prosthetist3@example.com', 'password_hash6', NULL),
(21, 'patient', 'patient@exemple.com', '$2y$10$EpoJWxFxickkpzcDLytJ/uvQeuQai/Qy6LW6AoqjfyYkk0e2P.Iae', 4),
(23, 'prothesiste', 'prothesiste@exemple.com', '$2y$10$pxuZiJ0muCYLLCk.AmvF2Oul.8h4F/PUNyu3Nfpsf3IcRy7yYE28a', 8),
(24, 'abderraouf', 'secretaireeeeee@example.com', '$2y$10$giKjc2MnCrbG8Z5bAU07GO6UeSMPGYx61kRZTinUPl3u.KLLWZ9o.', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `prostheses`
--
ALTER TABLE `prostheses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `prosthetist_id` (`prosthetist_id`);

--
-- Indexes for table `protheses`
--
ALTER TABLE `protheses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `protheses_payments`
--
ALTER TABLE `protheses_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prothese_id` (`prothese_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `prostheses`
--
ALTER TABLE `prostheses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `protheses`
--
ALTER TABLE `protheses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `protheses_payments`
--
ALTER TABLE `protheses_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `prostheses`
--
ALTER TABLE `prostheses`
  ADD CONSTRAINT `prostheses_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prostheses_ibfk_2` FOREIGN KEY (`prosthetist_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `protheses_payments`
--
ALTER TABLE `protheses_payments`
  ADD CONSTRAINT `protheses_payments_ibfk_1` FOREIGN KEY (`prothese_id`) REFERENCES `protheses` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
