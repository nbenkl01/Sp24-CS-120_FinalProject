-- phpMyAdmin SQL Dump
-- version 5.1.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 10, 2024 at 05:02 PM
-- Server version: 5.7.44-48-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "-05:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbnggjboof0sqv`
--

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

CREATE TABLE `Comments` (
  `comment_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_text` text,
  `comment_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Comments`
--

INSERT INTO `Comments` (`comment_id`, `item_id`, `user_id`, `comment_text`, `comment_timestamp`) VALUES
(1, 1, 2, 'This item looks great!', '2024-04-10 17:00:36'),
(2, 1, 3, 'I\'m interested in purchasing this.', '2024-04-10 17:00:36'),
(3, 2, 1, 'Is this item still available?', '2024-04-10 17:00:36'),
(4, 2, 3, 'Yes, it is still available.', '2024-04-10 17:00:36'),
(5, 3, 2, 'What is the condition of this item?', '2024-04-10 17:00:36'),
(6, 3, 1, 'It\'s brand new, never been used.', '2024-04-10 17:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `Items`
--

CREATE TABLE `Items` (
  `item_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `category` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `credit_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Items`
--

INSERT INTO `Items` (`item_id`, `name`, `description`, `category`, `item_condition`, `owner_id`, `credit_value`) VALUES
(1, 'Item 1', 'Description for Item 1', 'Category A', 'New', 1, 50),
(2, 'Item 2', 'Description for Item 2', 'Category B', 'Used', 2, 75),
(3, 'Item 3', 'Description for Item 3', 'Category C', 'New', 3, 100),
(4, 'Item 4', 'Description for Item 4', 'Category A', 'Used', 1, 80),
(5, 'Item 5', 'Description for Item 5', 'Category B', 'New', 2, 60),
(6, 'Item 6', 'Description for Item 6', 'Category C', 'Used', 3, 90),
(7, 'Item 7', 'Description for Item 7', 'Category A', 'New', 1, 70),
(8, 'Item 8', 'Description for Item 8', 'Category B', 'Used', 2, 85),
(9, 'Item 9', 'Description for Item 9', 'Category C', 'New', 3, 110),
(10, 'Item 10', 'Description for Item 10', 'Category A', 'Used', 1, 95),
(11, 'Item 11', 'Description for Item 11', 'Category B', 'New', 2, 55),
(12, 'Item 12', 'Description for Item 12', 'Category C', 'Used', 3, 120),
(13, 'Item 13', 'Description for Item 13', 'Category A', 'New', 1, 65),
(14, 'Item 14', 'Description for Item 14', 'Category B', 'Used', 2, 90),
(15, 'Item 15', 'Description for Item 15', 'Category C', 'New', 3, 105),
(16, 'Item 16', 'Description for Item 16', 'Category A', 'Used', 1, 75),
(17, 'Item 17', 'Description for Item 17', 'Category B', 'New', 2, 65),
(18, 'Item 18', 'Description for Item 18', 'Category C', 'Used', 3, 115),
(19, 'Item 19', 'Description for Item 19', 'Category A', 'New', 1, 85),
(20, 'Item 20', 'Description for Item 20', 'Category B', 'Used', 2, 95);

-- --------------------------------------------------------

--
-- Table structure for table `Transactions`
--

CREATE TABLE `Transactions` (
  `transaction_id` int(11) NOT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `transaction_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Transactions`
--

INSERT INTO `Transactions` (`transaction_id`, `buyer_id`, `seller_id`, `item_id`, `transaction_timestamp`, `status`) VALUES
(1, 2, 1, 7, '2024-04-04 16:30:00', 'Completed'),
(2, 3, 2, 8, '2024-04-03 11:25:00', 'Completed'),
(3, 1, 3, 9, '2024-04-02 18:40:00', 'Pending'),
(4, 2, 1, 10, '2024-04-01 20:15:00', 'Completed'),
(5, 3, 2, 11, '2024-03-31 09:50:00', 'Completed'),
(6, 1, 3, 12, '2024-03-30 14:05:00', 'Pending'),
(7, 2, 1, 13, '2024-03-29 17:20:00', 'Completed'),
(8, 3, 2, 14, '2024-03-28 21:35:00', 'Completed'),
(9, 1, 3, 15, '2024-03-27 08:45:00', 'Pending'),
(10, 2, 1, 16, '2024-03-26 12:55:00', 'Completed'),
(11, 3, 2, 17, '2024-03-25 19:10:00', 'Completed'),
(12, 1, 3, 18, '2024-03-24 13:00:00', 'Pending'),
(13, 2, 1, 19, '2024-03-23 10:30:00', 'Completed'),
(14, 3, 2, 20, '2024-03-22 15:20:00', 'Completed'),
(15, 1, 3, 1, '2024-03-21 17:40:00', 'Pending'),
(16, 2, 1, 2, '2024-03-20 18:55:00', 'Completed'),
(17, 3, 2, 3, '2024-03-19 22:25:00', 'Completed'),
(18, 1, 3, 4, '2024-03-18 11:30:00', 'Pending'),
(19, 2, 1, 5, '2024-03-17 14:45:00', 'Completed'),
(20, 3, 2, 6, '2024-03-16 16:20:00', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `credits_balance` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `username`, `email`, `password`, `credits_balance`) VALUES
(1, 'nbenkler', 'Noam.Benkler@tufts.edu', 'nb', 100),
(2, 'cowen', 'Casey.Owen@tufts.edu', 'co', 200),
(3, 'jgan', 'Jin.Gan@tufts.edu', 'jg', 150);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Comments`
--
ALTER TABLE `Comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Items`
--
ALTER TABLE `Items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Comments`
--
ALTER TABLE `Comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Items`
--
ALTER TABLE `Items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Transactions`
--
ALTER TABLE `Transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Comments`
--
ALTER TABLE `Comments`
  ADD CONSTRAINT `Comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `Items` (`item_id`),
  ADD CONSTRAINT `Comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

--
-- Constraints for table `Items`
--
ALTER TABLE `Items`
  ADD CONSTRAINT `Items_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `Users` (`user_id`);

--
-- Constraints for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD CONSTRAINT `Transactions_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `Transactions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `Transactions_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `Items` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
