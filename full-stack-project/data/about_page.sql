-- phpMyAdmin SQL Dump
-- version 5.2.2-1.el9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 14, 2025 at 04:09 PM
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
-- Table structure for table `about_page`
--

CREATE TABLE `about_page` (
  `id` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `education` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `about_page`
--

INSERT INTO `about_page` (`id`, `content`, `skills`, `education`, `created_at`, `updated_at`) VALUES
(1, 'I am a web developer passionate about creating innovative solutions, you can checkout some of my projects in the projects tab.', '[\"PHP\",\"JavaScript\",\"HTML/CSS\",\"Database Management\",\"Python\",\"Java\",\"C++\",\"Haskell\"]', 'First-year Honours Computer Science student at McMaster University. Proficient in Python, Java, C++, Haskell, HTML/CSS, and Vue.js.', '2025-04-12 21:59:56', '2025-04-14 16:04:44'),
(1, 'I am a web developer passionate about creating innovative solutions, you can checkout some of my projects in the projects tab.', '[\"PHP\",\"JavaScript\",\"HTML/CSS\",\"Database Management\",\"Python\",\"Java\",\"C++\",\"Haskell\"]', 'First-year Honours Computer Science student at McMaster University. Proficient in Python, Java, C++, Haskell, HTML/CSS, and Vue.js.', '2025-04-12 21:59:56', '2025-04-14 16:04:44');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
