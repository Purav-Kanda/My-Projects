-- phpMyAdmin SQL Dump
-- version 5.2.2-1.el9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 14, 2025 at 04:08 PM
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
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int NOT NULL,
  `question` text COLLATE utf8mb4_general_ci NOT NULL,
  `option1` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option2` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option3` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option4` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `correct_option` int NOT NULL,
  `explanation` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `question`, `option1`, `option2`, `option3`, `option4`, `correct_option`, `explanation`) VALUES
(6, 'What\'s my favourite movie ever?', 'The Lego Batman Movie', 'Avengers: Age of Ultron', 'Iron Man 3', 'Man of Steel', 1, 'I love lego'),
(7, 'What is my dream job?', 'Software Developer', 'UX developer', 'Mobile App Developer', 'AI Engineer', 4, 'I love AI in order to learn how to use advances in AI technology to automate and perform tedious tasks'),
(9, 'what is my most favourite coding language?', 'React', 'Haskell', 'C', 'Python', 4, 'It\'s easy to learn, easy to understand, incrementally adoptable, and beginner friendly.'),
(10, 'What is my favourite color?', 'red', 'green', 'blue', 'yellow', 2, '\"Green\" which represents grass.'),
(11, 'What is my age?', '19', '18', '22', '21', 1, 'i was born in 2006');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
