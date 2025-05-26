-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 12:32 PM
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
-- Database: `fitness`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `title` varchar(80) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `room` varchar(30) DEFAULT NULL,
  `capacity` smallint(5) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `title`, `trainer_id`, `room`, `capacity`, `description`) VALUES
(8, 'Weight Loss', 10, NULL, NULL, 'A dynamic weight loss session.'),
(9, 'Cardio', 10, NULL, NULL, 'An energetic cardio workout.'),
(10, 'Yoga', 11, NULL, NULL, 'A relaxing and strengthening yoga session.'),
(11, 'Fitness', 12, NULL, NULL, 'A comprehensive fitness training class.'),
(12, 'Boxing', 13, NULL, NULL, 'A high-intensity boxing workout.'),
(13, 'Body Building', 14, NULL, NULL, 'A class focused on strength and muscle building.'),
(14, 'Karate', 15, NULL, NULL, 'A traditional karate training session.');

-- --------------------------------------------------------

--
-- Table structure for table `class_timetable`
--

CREATE TABLE `class_timetable` (
  `class_id` int(11) NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `time_of_day` enum('6.00am - 8.00am','10.00am - 12.00am','5.00pm - 7.00pm','7.00pm - 9.00pm') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_timetable`
--

INSERT INTO `class_timetable` (`class_id`, `day_of_week`, `time_of_day`) VALUES
(8, 'monday', '6.00am - 8.00am'),
(8, 'wednesday', '10.00am - 12.00am'),
(8, 'sunday', '7.00pm - 9.00pm'),
(9, 'monday', '7.00pm - 9.00pm'),
(9, 'tuesday', '6.00am - 8.00am'),
(9, 'thursday', '10.00am - 12.00am'),
(9, 'saturday', '5.00pm - 7.00pm'),
(10, 'wednesday', '6.00am - 8.00am'),
(10, 'thursday', '7.00pm - 9.00pm'),
(10, 'friday', '5.00pm - 7.00pm'),
(11, 'monday', '10.00am - 12.00am'),
(11, 'friday', '6.00am - 8.00am'),
(11, 'sunday', '5.00pm - 7.00pm'),
(12, 'monday', '5.00pm - 7.00pm'),
(12, 'wednesday', '7.00pm - 9.00pm'),
(12, 'saturday', '6.00am - 8.00am'),
(12, 'saturday', '7.00pm - 9.00pm'),
(13, 'wednesday', '5.00pm - 7.00pm'),
(13, 'friday', '10.00am - 12.00am'),
(13, 'sunday', '6.00am - 8.00am'),
(14, 'tuesday', '5.00pm - 7.00pm'),
(14, 'friday', '7.00pm - 9.00pm'),
(14, 'saturday', '10.00am - 12.00am');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `email`, `name`, `message`) VALUES
(1, 'usarac3@gmail.com', 'Umbut', 'hey uhh i think ts awesome');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `duration_weeks` tinyint(3) UNSIGNED NOT NULL,
  `features` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `price`, `duration_weeks`, `features`, `is_active`) VALUES
(1, 'Class drop-in', 500.00, 8, 'Pick your class,Access to equipments,Specialist trainer,Once every two days\r\n', 1),
(2, '6-Month Unlimited', 1200.00, 26, 'Access to all classes,Free cycle rides,Unlimited access equipment,Personal trainer,Weight loss dietary\r\n\r\n\r\n', 1),
(3, '12-Month Unlimited', 2000.00, 52, 'Access to all classes,Unlimited access equipment,Personal trainer,Weight loss dietary\r\n\r\n\r\n', 1);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `cover_img` varchar(255) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress_logs`
--

CREATE TABLE `progress_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL,
  `body_fat` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `quote` varchar(255) DEFAULT NULL,
  `photo_before` varchar(255) DEFAULT NULL,
  `photo_after` varchar(255) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `quote`, `photo_before`, `photo_after`, `rating`, `status`, `created_at`) VALUES
(3, 'Umut Saraç', 'This fitness website is great!', NULL, NULL, 5, 'approved', NULL),
(4, 'Um td', 'ts stuc', NULL, NULL, 4, 'approved', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('member','trainer','admin') NOT NULL DEFAULT 'member',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `username` varchar(255) NOT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `membership_plan_id` int(11) DEFAULT NULL,
  `membership_start_date` date DEFAULT NULL,
  `membership_end_date` date DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `age` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`, `is_active`, `username`, `phone_number`, `address`, `date_of_birth`, `profile_picture`, `membership_plan_id`, `membership_start_date`, `membership_end_date`, `updated_at`, `age`, `height`, `weight`) VALUES
(5, 'Umut Saraç', 'umut.sarac@tedu.edu.tr', '$2y$10$7.G3Fg/0nQXFC3iNC0a5Iu0wdIBC/.lGUCSMm.TRkiv6D9yO2pNaK', 'admin', '2025-05-24 09:05:47', 1, 'umutsarac', '+905359627717', 'everywhere bby', '2005-04-14', 'user_5_683163bd41d52.jpg', NULL, NULL, NULL, '2025-05-24 07:12:52', 0, 0, 0),
(10, 'RLefew D. Loee', 'rlefew.d.loee@example.com', 'yourPlaintextPassword123', 'trainer', '2025-05-26 04:46:08', 1, 'rlefewdloee', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:46:08', 30, 170, 70),
(11, 'Keaf Shen', 'keaf.shen@example.com', 'yourPlaintextPassword123', 'trainer', '2025-05-26 04:46:08', 1, 'keafshen', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:46:08', 30, 170, 70),
(12, 'Kimberly Stone', 'kimberly.stone@example.com', 'yourPlaintextPassword123', 'trainer', '2025-05-26 04:46:08', 1, 'kimberlystone', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:46:08', 30, 170, 70),
(13, 'Rachel Adam', 'rachel.adam@example.com', 'yourPlaintextPassword123', 'trainer', '2025-05-26 04:46:08', 1, 'racheladam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:46:08', 30, 170, 70),
(14, 'Robert Cage', 'robert.cage@example.com', 'yourPlaintextPassword123', 'trainer', '2025-05-26 04:46:08', 1, 'robertcage', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:46:08', 30, 170, 70),
(15, 'Donald Grey', 'donald.grey@example.com', 'yourPlaintextPassword123', 'trainer', '2025-05-26 04:46:08', 1, 'donaldgrey', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:46:08', 30, 170, 70),
(16, 'meme', 'no@u.cum', '$2y$10$zVBpx4loAs7zmirrj.z1J.7G59wlry.CV7w9cuPVXHvhOwYzEZ1fC', 'member', '2025-05-26 08:14:18', 1, 'me', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-26 05:14:18', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `class_timetable`
--
ALTER TABLE `class_timetable`
  ADD PRIMARY KEY (`day_of_week`,`time_of_day`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UC_message` (`email`,`name`,`message`) USING HASH;

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `progress_logs`
--
ALTER TABLE `progress_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `progress_logs`
--
ALTER TABLE `progress_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_timetable`
--
ALTER TABLE `class_timetable`
  ADD CONSTRAINT `class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress_logs`
--
ALTER TABLE `progress_logs`
  ADD CONSTRAINT `progress_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
