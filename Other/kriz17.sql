-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 02:40 AM
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
-- Database: `kriz17`
--

-- --------------------------------------------------------

--
-- Table structure for table `troskopis_articlehistory`
--

CREATE TABLE `troskopis_articlehistory` (
  `history_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `file` varchar(150) NOT NULL,
  `category` varchar(30) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `version` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `troskopis_articles`
--

CREATE TABLE `troskopis_articles` (
  `article_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `file` varchar(150) NOT NULL,
  `category` varchar(30) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `version` int(11) NOT NULL DEFAULT 1,
  `status` enum('pending_assignment','pending_review','reviewed','editing','approved','rejected','appealed') NOT NULL,
  `assigned_reviewer` int(11) DEFAULT NULL,
  `appeal_message` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `troskopis_messages`
--

CREATE TABLE `troskopis_messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `troskopis_reviews`
--

CREATE TABLE `troskopis_reviews` (
  `review_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `article_version` int(11) NOT NULL,
  `score_relevance` int(11) NOT NULL,
  `score_originality` int(11) NOT NULL,
  `score_scientific` int(11) NOT NULL,
  `score_style` int(11) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `version` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `troskopis_users`
--

CREATE TABLE `troskopis_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` enum('admin','reviewer','author','editor','chiefeditor') NOT NULL DEFAULT 'author'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `troskopis_users`
--

INSERT INTO `troskopis_users` (`user_id`, `username`, `password`, `email`, `role`) VALUES
(2, 'recenzent', '202cb962ac59075b964b07152d234b70', 'recenzent@neco.cz', 'reviewer'),
(3, 'autor', '202cb962ac59075b964b07152d234b70', 'autor@autor.cz', 'author'),
(4, 'redaktor', '202cb962ac59075b964b07152d234b70', 'redaktor@rsp.cz', 'editor'),
(5, 'sefredaktor', '202cb962ac59075b964b07152d234b70', 'sefredaktor@rsp.cz', 'chiefeditor'),
(6, 'admin', '202cb962ac59075b964b07152d234b70', 'admin@admin.cz', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `troskopis_articlehistory`
--
ALTER TABLE `troskopis_articlehistory`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `authors` (`author_id`),
  ADD KEY `article_id_idx` (`article_id`);

--
-- Indexes for table `troskopis_articles`
--
ALTER TABLE `troskopis_articles`
  ADD PRIMARY KEY (`article_id`),
  ADD KEY `authors` (`author_id`),
  ADD KEY `fk_assigned_reviewer` (`assigned_reviewer`);

--
-- Indexes for table `troskopis_messages`
--
ALTER TABLE `troskopis_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `senders` (`sender_id`),
  ADD KEY `recipients` (`recipient_id`);

--
-- Indexes for table `troskopis_reviews`
--
ALTER TABLE `troskopis_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `articles` (`article_id`),
  ADD KEY `reviewers` (`reviewer_id`);

--
-- Indexes for table `troskopis_users`
--
ALTER TABLE `troskopis_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `troskopis_articlehistory`
--
ALTER TABLE `troskopis_articlehistory`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `troskopis_articles`
--
ALTER TABLE `troskopis_articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `troskopis_messages`
--
ALTER TABLE `troskopis_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `troskopis_reviews`
--
ALTER TABLE `troskopis_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `troskopis_users`
--
ALTER TABLE `troskopis_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `troskopis_articlehistory`
--
ALTER TABLE `troskopis_articlehistory`
  ADD CONSTRAINT `articles_history` FOREIGN KEY (`article_id`) REFERENCES `troskopis_articles` (`article_id`),
  ADD CONSTRAINT `authors_history` FOREIGN KEY (`author_id`) REFERENCES `troskopis_users` (`user_id`);

--
-- Constraints for table `troskopis_articles`
--
ALTER TABLE `troskopis_articles`
  ADD CONSTRAINT `authors` FOREIGN KEY (`author_id`) REFERENCES `troskopis_users` (`user_id`),
  ADD CONSTRAINT `fk_assigned_reviewer` FOREIGN KEY (`assigned_reviewer`) REFERENCES `troskopis_users` (`user_id`);

--
-- Constraints for table `troskopis_messages`
--
ALTER TABLE `troskopis_messages`
  ADD CONSTRAINT `recipients` FOREIGN KEY (`recipient_id`) REFERENCES `troskopis_users` (`user_id`),
  ADD CONSTRAINT `senders` FOREIGN KEY (`sender_id`) REFERENCES `troskopis_users` (`user_id`);

--
-- Constraints for table `troskopis_reviews`
--
ALTER TABLE `troskopis_reviews`
  ADD CONSTRAINT `articles` FOREIGN KEY (`article_id`) REFERENCES `troskopis_articles` (`article_id`),
  ADD CONSTRAINT `reviewers` FOREIGN KEY (`reviewer_id`) REFERENCES `troskopis_users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
