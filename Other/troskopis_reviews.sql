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
-- Table structure for table `troskopis_reviews`
--

CREATE TABLE `troskopis_reviews` (
  `review_id` int NOT NULL,
  `article_id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `article_version` int NOT NULL,
  `score_relevance` int NOT NULL,
  `score_originality` int NOT NULL,
  `score_scientific` int NOT NULL,
  `score_style` int NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `troskopis_reviews`
--
ALTER TABLE `troskopis_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `articles` (`article_id`),
  ADD KEY `reviewers` (`reviewer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `troskopis_reviews`
--
ALTER TABLE `troskopis_reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `troskopis_reviews`
--
ALTER TABLE `troskopis_reviews`
  ADD CONSTRAINT `articles` FOREIGN KEY (`article_id`) REFERENCES `troskopis_articles` (`article_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `reviewers` FOREIGN KEY (`reviewer_id`) REFERENCES `troskopis_users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
