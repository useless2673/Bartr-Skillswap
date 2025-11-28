-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 09:37 AM
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
-- Database: `skill_swap_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `skill` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`id`, `sender_id`, `receiver_id`, `skill`, `date`, `time`, `created_at`) VALUES
(1, 2, 1, 'python', '2025-10-11', '02:28:00', '2025-10-08 00:25:27'),
(2, 2, 1, 'python', '2025-10-09', '06:32:00', '2025-10-08 00:26:36'),
(3, 2, 1, 'python', '2025-10-09', '06:32:00', '2025-10-08 00:44:51'),
(4, 1, 4, 'java', '2025-11-27', '12:45:00', '2025-11-25 09:43:35');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`, `read_at`) VALUES
(1, 2, 1, 'hi', '2025-10-08 01:22:23', NULL),
(2, 2, 1, 'byw', '2025-10-08 01:22:33', NULL),
(3, 1, 2, 'tou', '2025-10-08 01:23:09', NULL),
(4, 1, 2, 'hi', '2025-11-16 17:16:35', NULL),
(5, 1, 2, 'byeee', '2025-11-18 11:36:21', NULL),
(6, 1, 2, 'byeee', '2025-11-22 14:05:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `title`, `content`, `created_at`) VALUES
(2, 1, 'php notes', 'here are your php notes', '2025-11-15 11:42:09'),
(4, 1, 'python notes', 'here are your python notes', '2025-11-25 03:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `note_files`
--

CREATE TABLE `note_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `note_id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `note_files`
--

INSERT INTO `note_files` (`id`, `note_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_at`) VALUES
(1, 2, 'PHP notes.docx', 'storage/notes/1763206929_PHP_notes.docx', 'application/vnd.open', 16867691, '2025-11-15 11:42:09'),
(3, 4, 'Python+Handbook.pdf', 'storage/notes/1764043009_Python_Handbook.pdf', 'application/pdf', 781456, '2025-11-25 03:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `skills_have`
--

CREATE TABLE `skills_have` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `skill` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills_have`
--

INSERT INTO `skills_have` (`id`, `user_id`, `skill`) VALUES
(1, 1, 'python'),
(3, 2, 'java'),
(4, 4, 'java'),
(5, 5, 'java');

-- --------------------------------------------------------

--
-- Table structure for table `skills_want`
--

CREATE TABLE `skills_want` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `skill` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills_want`
--

INSERT INTO `skills_want` (`id`, `user_id`, `skill`) VALUES
(1, 1, 'java'),
(2, 2, 'python'),
(3, 3, 'tailwind'),
(4, 4, 'javascript'),
(5, 5, 'javascript');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password_hash`, `created_at`) VALUES
(1, 'Khushi Singh', 'ks9696086698@gmail.com', 'Khushis', '$2y$10$xqBv3S4Oz.3fXfSC6d7zbev6vj5T4w7X/2Jo.ZrpTjN87YPPCbZ4i', '2025-10-07 22:38:15'),
(2, 'Saniya', 'Saniya.Fatima@247software.com', 'Saniyaf', '$2y$10$sPodUDxXRXtpMIIOrQEhHusztppK4hfh6UChbEclAPcwoO1/YPgc6', '2025-10-07 23:27:14'),
(3, 'Siya', 'siya@example.com', 'Siya', '$2y$10$M9nbnT3u9uUK2/opcnGpw.34i7UjB72FUOH1xElgxxKW1PjyLQW1a', '2025-11-03 11:35:20'),
(4, 'Kavya Tripathi', 'tripathikavya1215@gmail.com', 'theKavya', '$2y$10$sr2pIPqjl3CFxSXy60hhkuNIn39MMiT/xmh0nyW3SCHDjTxNdVKOu', '2025-11-25 09:33:22'),
(5, 'Khushi Verma', 'khushii250604@gmail.com', 'khushiv', '$2y$10$YPScwnYlD9kMJrldJGjgIeTEvHVy/OpJ6M3zVNAouR52fsWDB/L16', '2025-11-25 09:34:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sender` (`sender_id`),
  ADD KEY `fk_receiver` (`receiver_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender_receiver` (`sender_id`,`receiver_id`),
  ADD KEY `idx_receiver_sender` (`receiver_id`,`sender_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notes_user` (`user_id`);

--
-- Indexes for table `note_files`
--
ALTER TABLE `note_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_files_note` (`note_id`);

--
-- Indexes for table `skills_have`
--
ALTER TABLE `skills_have`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `skills_want`
--
ALTER TABLE `skills_want`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `note_files`
--
ALTER TABLE `note_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `skills_have`
--
ALTER TABLE `skills_have`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `skills_want`
--
ALTER TABLE `skills_want`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `fk_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_files`
--
ALTER TABLE `note_files`
  ADD CONSTRAINT `fk_files_note` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills_have`
--
ALTER TABLE `skills_have`
  ADD CONSTRAINT `skills_have_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills_want`
--
ALTER TABLE `skills_want`
  ADD CONSTRAINT `skills_want_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
