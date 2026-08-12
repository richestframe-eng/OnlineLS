-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Aug 12, 2026 at 06:07 PM
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
-- Database: `online_ls`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'admin@onlinels.com', '$2y$10$bWToYc2rT1JTuYUnqpcOmebX/omU9oS0BikccfAYQQACZxZW5DEgi');

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `author` (
  `author_id` int(11) NOT NULL,
  `author_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`author_id`, `author_name`) VALUES
(5, 'Andrew S. Tanenbaum'),
(2, 'Ian Sommerville'),
(4, 'Ramez Elmasri'),
(1, 'Robert C. Martin'),
(3, 'Thomas H. Cormen');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `publication_year` year(4) NOT NULL,
  `total` int(11) NOT NULL,
  `available` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `publisher_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`book_id`, `title`, `isbn`, `publication_year`, `total`, `available`, `author_id`, `publisher_id`, `category_id`, `description`) VALUES
(1, 'Clean Code', '9780132350884', '2008', 5, 5, 1, 1, 1, 'A handbook of agile software craftsmanship.'),
(2, 'Software Engineering', '9780133943030', '2015', 5, 5, 2, 1, 2, 'A comprehensive introduction to software engineering.'),
(3, 'Introduction to Algorithms', '9780262033848', '2009', 5, 5, 3, 2, 5, 'A widely used textbook on algorithms and data structures.'),
(4, 'Database System Concepts', '9780078022159', '2019', 5, 5, 4, 2, 3, 'Fundamentals of database systems.'),
(5, 'Computer Networks', '9780132126953', '2010', 5, 5, 5, 3, 4, 'Fundamentals of computer networking.'),
(6, 'Operating System Concepts', '9781119456339', '2018', 5, 5, 5, 1, 5, 'Introduction to operating system concepts.'),
(7, 'Programming Fundamentals', '9780134448237', '2020', 5, 5, 1, 1, 1, 'Fundamentals of computer programming.'),
(8, 'Web Development Basics', '9781492055759', '2021', 5, 5, 1, 4, 1, 'Introduction to modern web development.'),
(9, 'Computer Architecture', '9780134092669', '2016', 5, 5, 5, 3, 5, 'Introduction to computer architecture.'),
(10, 'Data Structures and Algorithms', '9780262046305', '2022', 5, 5, 3, 1, 5, 'Study of fundamental data structures and algorithms.');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(4, 'Computer Networks'),
(5, 'Computer Science'),
(3, 'Database'),
(1, 'Programming'),
(2, 'Software Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `issue_return`
--

CREATE TABLE `issue_return` (
  `transaction_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Issued','Returned') NOT NULL DEFAULT 'Issued',
  `fine` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

CREATE TABLE `publisher` (
  `publisher_id` int(11) NOT NULL,
  `publisher_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publisher`
--

INSERT INTO `publisher` (`publisher_id`, `publisher_name`) VALUES
(2, 'McGraw Hill'),
(4, 'O\'Reilly Media'),
(1, 'Pearson'),
(3, 'Prentice Hall');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `dob` date DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `full_name`, `address`, `phone`, `dob`, `program`, `semester`, `photo`, `email`, `password`) VALUES
(1, 'Deepak Sah', 'Jaleshwor - 07', '9800000001', '2005-02-12', 'BBA', 3, 'students/student_1786549379.jpg', 'deepak@gmail.com', '$2y$10$6lGz77U2YukxgnpDkbnnGOXVq/OssFtMJxg4Om1x3cpW7nMgrjiYu'),
(2, 'Rahul Sah', 'Mahabir Chowk - 6', '9800000002', '2004-07-12', 'B.Com', 5, 'students/student_1786549481.jpg', 'rahul@gmail.com', '$2y$10$zc12X3/A7iz5mNUDUtzDDeJvXflLZltEwUNAUhNZYNy2AqTLGQA1O'),
(3, 'Aakash Gupta', 'Sita Chowk - 11', '9800000003', '2006-06-12', 'BCA', 4, 'students/student_1786549294.jpg', 'aakash@gmail.com', '$2y$10$oBJGH/3/8rbz2UZXUk1lCurajPytO1daDmgVOYnOMUq0DGn8zH2vq');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`author_id`),
  ADD UNIQUE KEY `author_name` (`author_name`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `publisher_id` (`publisher_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `issue_return`
--
ALTER TABLE `issue_return`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `publisher`
--
ALTER TABLE `publisher`
  ADD PRIMARY KEY (`publisher_id`),
  ADD UNIQUE KEY `publisher_name` (`publisher_name`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `author`
--
ALTER TABLE `author`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `issue_return`
--
ALTER TABLE `issue_return`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publisher`
--
ALTER TABLE `publisher`
  MODIFY `publisher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `book_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`),
  ADD CONSTRAINT `book_ibfk_2` FOREIGN KEY (`publisher_id`) REFERENCES `publisher` (`publisher_id`),
  ADD CONSTRAINT `book_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `issue_return`
--
ALTER TABLE `issue_return`
  ADD CONSTRAINT `issue_return_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `issue_return_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
