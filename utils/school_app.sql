-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2024 at 01:08 AM
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
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance` enum('present','absent') NOT NULL,
  `teacher_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `attendance_date`, `attendance`, `teacher_id`) VALUES
(6, 15, '2024-12-16', 'absent', NULL),
(8, 16, '2024-12-16', 'present', NULL),
(9, 17, '2024-12-16', 'absent', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `subject_id` int(11) NOT NULL,
  `quarter` int(11) NOT NULL,
  `written_test` decimal(5,2) NOT NULL DEFAULT 0.00,
  `performance_task` decimal(5,2) NOT NULL DEFAULT 0.00,
  `exm` decimal(5,2) NOT NULL DEFAULT 0.00,
  `s_grade` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `quarter`, `written_test`, `performance_task`, `exm`, `s_grade`, `created_at`) VALUES
(33, 15, 1, 1, 25.00, 29.00, 20.00, 74.00, '2024-12-15 05:50:23'),
(34, 15, 2, 1, 25.00, 29.00, 20.00, 74.00, '2024-12-15 05:50:23'),
(35, 15, 3, 1, 25.00, 29.00, 20.00, 74.00, '2024-12-15 05:50:23'),
(36, 15, 4, 1, 27.00, 32.00, 20.00, 79.00, '2024-12-15 05:50:23'),
(37, 15, 5, 1, 30.00, 29.00, 20.00, 79.00, '2024-12-15 05:50:23'),
(38, 15, 6, 1, 27.00, 25.00, 20.00, 72.00, '2024-12-15 05:50:23'),
(39, 15, 7, 1, 27.00, 28.00, 20.00, 75.00, '2024-12-15 05:50:23'),
(40, 15, 8, 1, 25.00, 29.00, 20.00, 74.00, '2024-12-15 05:50:23'),
(73, 16, 1, 1, 25.00, 45.00, 15.00, 85.00, '2024-12-15 11:12:12'),
(74, 16, 2, 1, 25.00, 45.00, 15.00, 85.00, '2024-12-15 11:12:13'),
(75, 16, 3, 1, 26.00, 45.00, 12.00, 83.00, '2024-12-15 11:12:13'),
(76, 16, 4, 1, 27.00, 40.00, 17.00, 84.00, '2024-12-15 11:12:13'),
(77, 16, 5, 1, 24.00, 50.00, 14.00, 88.00, '2024-12-15 11:12:13'),
(78, 16, 6, 1, 15.00, 50.00, 20.00, 85.00, '2024-12-15 11:12:13'),
(79, 16, 7, 1, 14.00, 45.00, 20.00, 79.00, '2024-12-15 11:12:13'),
(80, 16, 8, 1, 25.00, 42.00, 13.00, 80.00, '2024-12-15 11:12:13'),
(81, 17, 1, 1, 12.00, 50.00, 20.00, 82.00, '2024-12-16 22:26:53'),
(82, 17, 2, 1, 30.00, 23.00, 20.00, 73.00, '2024-12-16 22:26:53'),
(83, 17, 3, 1, 30.00, 50.00, 20.00, 100.00, '2024-12-16 22:26:53'),
(84, 17, 4, 1, 30.00, 50.00, 20.00, 100.00, '2024-12-16 22:26:53'),
(85, 17, 5, 1, 30.00, 50.00, 20.00, 100.00, '2024-12-16 22:26:53'),
(86, 17, 6, 1, 30.00, 34.00, 20.00, 84.00, '2024-12-16 22:26:53'),
(87, 17, 7, 1, 30.00, 50.00, 20.00, 100.00, '2024-12-16 22:26:53'),
(88, 17, 8, 1, 30.00, 24.00, 20.00, 74.00, '2024-12-16 22:26:53');

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
(15, '117739160005', 'Tiongco, Jude Ledesma', '2024-12-11', 'Male', 'Catholic', 'Polopangyan', 'Old Sagay', 'Sagay', 'Negros Occidental', '09454413739', '', '', '', '', '', 'Grade 8', 'A', 'Face-to-Face', '', '2024-12-10 17:10:39', '2024-12-10 17:10:39'),
(16, '117739160026', 'BOCALANGCO, JHON ISABELO', '2024-12-15', 'Male', 'Catholic', '123 street', 'CAMPO SANTIAGO', 'Sagay', 'Negros Occidental', '94555511236', '', 'BOCALANGCO,ELSIE,DELGADO,', '', 'Mother', '', 'Grade 8', 'A', 'Face-to-Face', '', '2024-12-15 09:56:31', '2024-12-15 09:56:31'),
(17, '117696070022', 'Penaflor, Lovely Middle', '2024-12-17', 'Female', 'Roman Catholic', 'Sewahon', 'Barangay 1', 'Sewahon', 'Negros Occidental', '09454413739', 'Random Father', 'Random Mother', 'Secret', 'Secret', '09874561242', 'Grade 9', 'Prudence', 'Online', 'adfas', '2024-12-16 20:09:36', '2024-12-16 20:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `student_card`
--

CREATE TABLE `student_card` (
  `id` int(11) NOT NULL,
  `student_id` int(11) UNSIGNED NOT NULL,
  `subject_id` int(11) NOT NULL,
  `1st_quarter` decimal(5,2) DEFAULT NULL,
  `2nd_quarter` decimal(5,2) DEFAULT NULL,
  `3rd_quarter` decimal(5,2) DEFAULT NULL,
  `4th_quarter` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_card`
--

INSERT INTO `student_card` (`id`, `student_id`, `subject_id`, `1st_quarter`, `2nd_quarter`, `3rd_quarter`, `4th_quarter`, `created_at`) VALUES
(1, 15, 1, 90.00, 86.00, 74.00, NULL, '2024-12-15 05:50:23'),
(2, 15, 2, 91.00, 91.00, 74.00, NULL, '2024-12-15 05:50:23'),
(3, 15, 3, 58.00, 86.00, 74.00, NULL, '2024-12-15 05:50:23'),
(4, 15, 4, 53.00, 86.00, 79.00, NULL, '2024-12-15 05:50:23'),
(5, 15, 5, 45.00, 86.00, 79.00, NULL, '2024-12-15 05:50:23'),
(6, 15, 6, 45.00, 86.00, 72.00, NULL, '2024-12-15 05:50:23'),
(7, 15, 7, 45.00, 86.00, 75.00, NULL, '2024-12-15 05:50:23'),
(8, 15, 8, 45.00, 86.00, 74.00, NULL, '2024-12-15 05:50:23'),
(9, 16, 1, 85.00, NULL, NULL, NULL, '2024-12-15 11:12:13'),
(10, 16, 2, 85.00, NULL, NULL, NULL, '2024-12-15 11:12:13'),
(11, 16, 3, 83.00, NULL, NULL, NULL, '2024-12-15 11:12:13'),
(12, 16, 4, 84.00, NULL, NULL, NULL, '2024-12-15 11:12:13'),
(13, 16, 5, 88.00, NULL, NULL, NULL, '2024-12-15 11:12:13'),
(14, 16, 6, 85.00, NULL, NULL, NULL, '2024-12-15 11:12:13'),
(15, 16, 7, 79.00, NULL, NULL, NULL, '2024-12-15 11:12:13'),
(16, 16, 8, 80.00, NULL, NULL, NULL, '2024-12-15 11:12:13'),
(17, 17, 1, 82.00, NULL, NULL, NULL, '2024-12-16 22:26:53'),
(18, 17, 2, 73.00, NULL, NULL, NULL, '2024-12-16 22:26:53'),
(19, 17, 3, 100.00, NULL, NULL, NULL, '2024-12-16 22:26:53'),
(20, 17, 4, 100.00, NULL, NULL, NULL, '2024-12-16 22:26:53'),
(21, 17, 5, 100.00, NULL, NULL, NULL, '2024-12-16 22:26:53'),
(22, 17, 6, 84.00, NULL, NULL, NULL, '2024-12-16 22:26:53'),
(23, 17, 7, 100.00, NULL, NULL, NULL, '2024-12-16 22:26:53'),
(24, 17, 8, 74.00, NULL, NULL, NULL, '2024-12-16 22:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `student_subject`
--

CREATE TABLE `student_subject` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_subject`
--

INSERT INTO `student_subject` (`student_id`, `subject_id`) VALUES
(15, 1),
(15, 2),
(15, 3),
(15, 4),
(15, 5),
(15, 6),
(15, 7),
(15, 8),
(16, 1),
(16, 2),
(16, 3),
(16, 4),
(16, 5),
(16, 6),
(16, 7),
(16, 8),
(17, 1),
(17, 2),
(17, 3),
(17, 4),
(17, 5),
(17, 6),
(17, 7),
(17, 8);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(50) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `created_at`, `updated_at`) VALUES
(1, 'Filipino', '2024-12-11', '2024-12-11'),
(2, 'English', '2024-12-11', '2024-12-11'),
(3, 'Math', '2024-12-11', '2024-12-11'),
(4, 'Science', '2024-12-11', '2024-12-11'),
(5, 'Aral-Pan', '2024-12-11', '2024-12-11'),
(6, 'MAPEH', '2024-12-11', '2024-12-11'),
(7, 'ESP', '2024-12-11', '2024-12-11'),
(8, 'T.L.E', '2024-12-11', '2024-12-11');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) UNSIGNED NOT NULL,
  `teacher_id_num` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `teacher_id_num`, `full_name`, `birth_date`, `sex`, `religion`, `street`, `barangay`, `municipality`, `province`, `contact_number`, `grade`, `section`, `student_id`) VALUES
(6, 'tch-12659', 'Penfiel, Lovely Middle', '2023-06-24', 'Female', 'Roman Catholic', 'Sewahon', 'Barangay 1', 'Sewahon', 'Negros Occidental', '09123456789', 'Grade 8', 'A', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_archive`
--

CREATE TABLE `teacher_archive` (
  `id` int(11) NOT NULL,
  `teacher_id_num` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `barangay` varchar(100) NOT NULL,
  `municipality` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `grade` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `teacher_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_student_assignments`
--

CREATE TABLE `teacher_student_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `teacher_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_student_assignments`
--

INSERT INTO `teacher_student_assignments` (`id`, `teacher_id`, `student_id`) VALUES
(6, 4, 15),
(7, 4, 16),
(8, 5, 17),
(9, 6, 15),
(10, 6, 16);

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
  `student_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `user_role`, `full_name`, `email`, `contact_number`, `is_active`, `created_at`, `student_id`, `teacher_id`) VALUES
(2, 'admin001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Administrator', 'admin@sewahon.edu.ph', '09123456789', 1, '2024-12-02 13:11:48', NULL, NULL),
(20, '117696071155', '$2y$10$jgOxz3GMwZUp2Q8a7Zmmc.D3bw0tjxLc4.Pa7UzOxMIs7TGHSskMy', 'student', 'Vergara, Colet Florenosos', 'colet@bini.com', '09987654321', 1, '2024-12-04 12:27:40', 13, NULL),
(24, '117739160005', '$2y$10$2MM2lzgRoJ4ZXopjruLgbuNur/xNFsK3z4JdwCDHx9hHGLURR/RQq', 'student', 'Tiongco, Jude Ledesma', 'ledesma@gmail.com', '09454413739', 1, '2024-12-10 17:10:40', 15, NULL),
(25, '117739160026', '$2y$10$/59Om6KRMi9kkr78BY5Ng.xeiPiGMubbi2Xpkg55xSl2VOJjAIYVG', 'student', 'BOCALANGCO, JHON ISABELO', 'isabelo@email.com', '94555511236', 1, '2024-12-15 09:56:31', 16, NULL),
(30, 'tch-95740', '$2y$10$5LVKqM4ZTfpOn1P4aQYhxeneoSn537I0bs6EybpapDL7rtaC6QAX.', 'teacher', 'Penfiel, Lovely Middle', 'lovely@email.com', '94555511236', 1, '2024-12-16 20:51:45', NULL, NULL),
(33, 'tch-12659', '$2y$10$IkFOha2H66eHNRBfG20NV.65seLf86ZAopUrneFR.FHPIUV1t20Ni', 'teacher', 'Penfiel, Lovely Middle', NULL, '09123456789', 1, '2024-12-17 00:03:53', NULL, 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_student_assignments_ibfk_1` (`teacher_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`subject_id`),
  ADD KEY `grades_ibfk_2` (`subject_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lrn` (`lrn`);

--
-- Indexes for table `student_card`
--
ALTER TABLE `student_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subject_id` (`subject_id`),
  ADD KEY `fk_student_id` (`student_id`);

--
-- Indexes for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD PRIMARY KEY (`student_id`,`subject_id`),
  ADD KEY `student_subject_ibfk_2` (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_teachers_id` (`id`),
  ADD KEY `fk_student` (`student_id`);

--
-- Indexes for table `teacher_student_assignments`
--
ALTER TABLE `teacher_student_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_teacher_user` (`teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_card`
--
ALTER TABLE `student_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teacher_student_assignments`
--
ALTER TABLE `teacher_student_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_teacher_student` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `teacher_student_assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_card`
--
ALTER TABLE `student_card`
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `fk_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD CONSTRAINT `student_subject_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_subject_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `teacher_student_assignments`
--
ALTER TABLE `teacher_student_assignments`
  ADD CONSTRAINT `teacher_student_assignments_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_teacher_user` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

