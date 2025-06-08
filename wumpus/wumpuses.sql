-- phpMyAdmin SQL Dump
-- version 5.2.2-1.el9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 30, 2025 at 04:15 PM
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
-- Table structure for table `wumpuses`
--

CREATE TABLE `wumpuses` (
  `id` int NOT NULL,
  `row` int NOT NULL,
  `col` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wumpuses`
--

INSERT INTO `wumpuses` (`id`, `row`, `col`) VALUES
(7, 1, 4),
(4, 1, 5),
(2, 2, 3),
(9, 2, 6),
(5, 3, 2),
(1, 4, 4),
(3, 5, 1),
(10, 5, 5),
(6, 6, 2),
(8, 7, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wumpuses`
--
ALTER TABLE `wumpuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `row` (`row`,`col`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wumpuses`
--
ALTER TABLE `wumpuses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
