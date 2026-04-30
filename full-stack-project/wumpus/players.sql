-- phpMyAdmin SQL Dump
-- version 5.2.2-1.el9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 30, 2025 at 04:14 PM
-- Server version: 9.1.0-commercial
-- PHP Version: 8.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kandap1_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `wins` int DEFAULT '0',
  `losses` int DEFAULT '0',
  `last_played` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`email`, `name`, `wins`, `losses`, `last_played`) VALUES
('alice@example.com', 'Alice Johnson', 5, 2, '2024-03-15'),
('bob@example.com', 'Bob Smith', 3, 4, '2024-03-16'),
('charlie@example.com', 'Charlie Brown', 7, 1, '2024-03-17'),
('diana@example.com', 'Diana Prince', 2, 5, '2024-03-18'),
('edward@example.com', 'Edward Cullen', 6, 3, '2024-03-19'),
('fiona@example.com', 'Fiona Apple', 4, 4, '2024-03-20'),
('george@example.com', 'George Washington', 1, 6, '2024-03-21'),
('hannah@example.com', 'Hannah Montana', 8, 2, '2024-03-22'),
('ian@example.com', 'Ian Fleming', 3, 3, '2024-03-23'),
('jane@example.com', 'Jane Doe', 5, 5, '2024-03-24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
