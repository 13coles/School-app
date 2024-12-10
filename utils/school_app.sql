-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2024 at 01:50 PM
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
-- Database: `school_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `lrn` varchar(12) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `guardian_contact` varchar(15) DEFAULT NULL,
  `grade` varchar(20) DEFAULT NULL,
  `section` varchar(100) DEFAULT NULL,
  `learning_modality` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `lrn`, `full_name`, `birth_date`, `sex`, `religion`, `street`, `barangay`, `municipality`, `province`, `contact_number`, `father_name`, `mother_name`, `guardian_name`, `relationship`, `guardian_contact`, `grade`, `section`, `learning_modality`, `remarks`, `created_at`, `updated_at`) VALUES
(13, '117696071155', 'Vergara, Colet Florenosos', '2005-03-04', 'Female', 'Catholic', 'Commonwealth Street', 'Barangay 7', 'Quezon City', 'Manila', '09987654321', 'Secret', 'Secret', 'Secret', 'Secret', '', 'Grade 10', 'Fortitude', 'Face-to-Face', 'bini member', '2024-12-04 12:27:40', '2024-12-04 12:33:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` enum('admin','teacher','student') NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `user_role`, `full_name`, `email`, `contact_number`, `is_active`, `created_at`, `student_id`) VALUES
(2, 'admin001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Administrator', 'admin@sewahon.edu.ph', '09123456789', 1, '2024-12-02 13:11:48', NULL),
(5, 'tch-07703', '$2y$10$hLDjfpvqiGRbTjd0EAnWqu8J/CcrTGEvEbOkthTgtZAChjosibHJ6', 'teacher', 'Irene Cartaciano', 'irene@email.com', '09112233445', 1, '2024-12-02 16:39:32', NULL),
(20, '117696071155', '$2y$10$jgOxz3GMwZUp2Q8a7Zmmc.D3bw0tjxLc4.Pa7UzOxMIs7TGHSskMy', 'student', 'Vergara, Colet Florenosos', 'colet@bini.com', '09987654321', 1, '2024-12-04 12:27:40', 13),
(21, 'tch-09464', '$2y$10$PcL1XkJWRNvFFgXbKZ/4qerj5S.Z9rj.mmouOUAnzTbSAOyMpphIO', 'teacher', 'Mikhaela Janna Jimenea Lim', 'mikha@bini.com', '09111234567', 1, '2024-12-04 12:30:30', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lrn` (`lrn`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
