-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el9.remi
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 21, 2024 at 12:57 PM
-- Server version: 8.0.36
-- PHP Version: 8.2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kriz17`
--

-- --------------------------------------------------------

--
-- Table structure for table `troskopis_articlehistory`
--

CREATE TABLE `troskopis_articlehistory` (
  `article_id` int NOT NULL,
  `author_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `file` varchar(150) NOT NULL,
  `category` varchar(30) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `version` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `troskopis_articlehistory`
--
ALTER TABLE `troskopis_articlehistory`
  ADD PRIMARY KEY (`article_id`),
  ADD KEY `authors` (`author_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `troskopis_articlehistory`
--
ALTER TABLE `troskopis_articlehistory`
  ADD CONSTRAINT `authors_history` FOREIGN KEY (`author_id`) REFERENCES `troskopis_users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
