-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2025 at 07:18 AM
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
-- Database: `system`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminactionlog`
--

CREATE TABLE `adminactionlog` (
  `action_id` int(11) NOT NULL,
  `orphanage_id` int(10) UNSIGNED DEFAULT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `action` enum('approved','rejected') NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alertdismissals`
--

CREATE TABLE `alertdismissals` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `alert_key` varchar(50) NOT NULL,
  `dismissed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alertsseen`
--

CREATE TABLE `alertsseen` (
  `user_id` int(11) NOT NULL,
  `type` enum('event','appointment') NOT NULL,
  `ref_id` int(11) NOT NULL,
  `seen_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `orphanage_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `user_id`, `orphanage_id`, `date`, `status`) VALUES
(3, 1, 9, '2025-08-08', 'approved'),
(4, 1, 10, '2025-08-15', 'approved'),
(5, 1, 13, '2025-08-22', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `eventjoin`
--

CREATE TABLE `eventjoin` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `request_date` date DEFAULT NULL,
  `status` enum('pending','approved','declined') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventjoin`
--

INSERT INTO `eventjoin` (`id`, `user_id`, `post_id`, `request_date`, `status`) VALUES
(2, 1, 3, '2025-07-13', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `orphanage`
--

CREATE TABLE `orphanage` (
  `orphanage_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` text DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `status` enum('pending','verified') DEFAULT 'pending',
  `profile_pic` varchar(255) DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orphanage`
--

INSERT INTO `orphanage` (`orphanage_id`, `name`, `location`, `contact`, `status`, `profile_pic`, `user_id`, `latitude`, `longitude`) VALUES
(9, 'Nuru Orphans Center', NULL, '0683 922 816', 'verified', 'orphanage_1752421734.PNG', 13, -8.928591007494042, 33.543689562963806),
(10, 'Mkate wa Watoto Yatima Iwambi Orphanage', NULL, '399Q+P5W, Mbeya', 'verified', 'orphanage_1752422026.PNG', 14, -8.930771719160282, 33.38748690859638),
(13, 'Save Kids For Future', NULL, '0744 438 942', 'verified', 'orphanage_1752422624.PNG', 16, -8.893350494138096, 33.44784468015591);

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(10) UNSIGNED NOT NULL,
  `orphanage_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `date_posted` date DEFAULT NULL,
  `post_image` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `orphanage_id`, `title`, `content`, `date_posted`, `post_image`, `event_date`) VALUES
(3, 10, 'umoja dinner', 'joining with donors in special dinner at Flavorful Delight Mapochopocho', '2025-07-13', '1752422857_FLAVORFUL.png', '2025-08-08');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('donor','orphanage','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Paulo Gabriel Ntandu', 'ntandupaulo66@gmail.com', '$2y$10$iy6wK/TXTb61OvDOkpEyc.fTC8Z38WB9sXi5TpYd10Zg0r3NMQHaW', 'donor'),
(7, 'Admin', 'Admin', '$2y$10$3.bVu8Sp4Ii/BMJpWFwavuSnDwbVD70wG0W0U7WUYAK9QqSrShdpa', 'admin'),
(8, 'Veronika melkior shirima', 'vero@gmail.com', '$2y$10$9agWg06jDMLpsTrsbB7vXevJDRZeS4rQRQ0epvDgrP7oBfPYSxCNO', 'donor'),
(13, 'Nuru Orphans Center', 'info@nuruorphans.co.tz', '$2y$10$l1uEs.e/m8yCoXq6StIRleP9N/2.j7Os6qC/hc7jaXZj0KXdbsmr2', 'orphanage'),
(14, 'Mkate wa Watoto Yatima Iwambi Orphanage', 'info@mkatewawatoto.co.tz', '$2y$10$hPpCw8XfdTpRXCT7KhpQGO4xgVB6DCQa.D1sfPIrOvGII86Uo5kbi', 'orphanage'),
(16, 'Save Kids For Future', 'info@savekids.co.tz', '$2y$10$hHamYnKvfNhPuAwRYbqHGuSPk4J3w1N6v8FNt2PKpfNGXSJfAg/jK', 'orphanage');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminactionlog`
--
ALTER TABLE `adminactionlog`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `orphanage_id` (`orphanage_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `alertdismissals`
--
ALTER TABLE `alertdismissals`
  ADD PRIMARY KEY (`user_id`,`alert_key`);

--
-- Indexes for table `alertsseen`
--
ALTER TABLE `alertsseen`
  ADD PRIMARY KEY (`user_id`,`type`,`ref_id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `orphanage_id` (`orphanage_id`);

--
-- Indexes for table `eventjoin`
--
ALTER TABLE `eventjoin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `orphanage`
--
ALTER TABLE `orphanage`
  ADD PRIMARY KEY (`orphanage_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `orphanage_id` (`orphanage_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminactionlog`
--
ALTER TABLE `adminactionlog`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `eventjoin`
--
ALTER TABLE `eventjoin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orphanage`
--
ALTER TABLE `orphanage`
  MODIFY `orphanage_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adminactionlog`
--
ALTER TABLE `adminactionlog`
  ADD CONSTRAINT `adminactionlog_ibfk_1` FOREIGN KEY (`orphanage_id`) REFERENCES `orphanage` (`orphanage_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `adminactionlog_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `alertdismissals`
--
ALTER TABLE `alertdismissals`
  ADD CONSTRAINT `alertdismissals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`orphanage_id`) REFERENCES `orphanage` (`orphanage_id`) ON DELETE CASCADE;

--
-- Constraints for table `eventjoin`
--
ALTER TABLE `eventjoin`
  ADD CONSTRAINT `eventjoin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eventjoin_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orphanage`
--
ALTER TABLE `orphanage`
  ADD CONSTRAINT `orphanage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`orphanage_id`) REFERENCES `orphanage` (`orphanage_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
