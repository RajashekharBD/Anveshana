-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2025 at 07:31 AM
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
-- Database: `anveshana`
--

-- --------------------------------------------------------

--
-- Table structure for table `information`
--

CREATE TABLE `information` (
  `id` int(11) NOT NULL,
  `pname` varchar(100) NOT NULL,
  `pdescription` text NOT NULL,
  `pi_main` varchar(255) NOT NULL,
  `pi1` varchar(255) DEFAULT NULL,
  `pi2` varchar(255) DEFAULT NULL,
  `pi3` varchar(255) DEFAULT NULL,
  `pi4` varchar(255) DEFAULT NULL,
  `pi5` varchar(255) DEFAULT NULL,
  `pi6` varchar(255) DEFAULT NULL,
  `package` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 'Israr Khairati', 'israr@gmail.com', '$2y$10$8k2rzxX8E48gzLjQW3TD7eRU4CvHoioWnBQkXH/LPiXYFiRZ1gcqq', '2025-05-29 03:08:48'),
(2, 'omkar dani', 'omkar@gmail.com', '$2y$10$vlTN5w9oKeAq8cpNQxEFyu6d1bTcMYORYT9b/9BUb4ai6YdaN.82K', '2025-05-29 03:34:24'),
(3, 'omkar dani', 'omkar1@gmail.com', '$2y$10$ChOjlL732GCYZt5T98K9veKeHMxSBRMGRHemAf9ycHdfX9pbQHfkG', '2025-05-29 10:13:59'),
(4, 'Abcd', 'abc@gmail.com', '$2y$10$IB3TnYlP/OYG7ErrWAYzju4bre.QbOfKPGzPLjv2LAtMxB5DzWxs.', '2025-06-04 08:38:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `information`
--
ALTER TABLE `information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `information`
--
ALTER TABLE `information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
