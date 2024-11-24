-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2024 at 04:30 AM
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
-- Database: `admindirectory`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterAdmin` (IN `p_first_name` VARCHAR(50), IN `p_last_name` VARCHAR(50), IN `p_middle_name` VARCHAR(50), IN `p_age` INT, IN `p_gender` VARCHAR(10), IN `p_birthday` DATE, IN `p_contact` VARCHAR(15), IN `p_email` VARCHAR(100), IN `p_username` VARCHAR(50), IN `p_password` VARCHAR(255))   BEGIN
    
    IF (SELECT COUNT(*) FROM AdminDirectory WHERE username = p_username OR email = p_email) > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Username or email already exists.';
    
    ELSEIF (p_first_name = p_last_name AND p_first_name = p_middle_name) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'First name, last name, and middle name cannot be the same.';
    ELSE
        
        INSERT INTO AdminDirectory (
            first_name, last_name, middle_name, age, gender, birthday,
            contact, email, username, password, role
        ) VALUES (
            p_first_name, p_last_name, p_middle_name, p_age, p_gender, p_birthday,
            p_contact, p_email, p_username, p_password, 'Admin'  
        );
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admindirectory`
--

CREATE TABLE `admindirectory` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('male','female','Male','Female','MALE','FEMALE') DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin') DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admindirectory`
--

INSERT INTO `admindirectory` (`id`, `first_name`, `last_name`, `middle_name`, `age`, `gender`, `birthday`, `contact`, `email`, `username`, `password`, `role`) VALUES
(1, 'marc', 'bryant', 'peralta', 23, '', '2003-09-24', '9089809090989', 'marcbryantp@gmail.com', 'marc', '$2y$10$Ked4wWOiI6DKzJRIzqR1WuAPQYImORukS1AnboTHWo.H.u5cBdiM2', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admindirectory`
--
ALTER TABLE `admindirectory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admindirectory`
--
ALTER TABLE `admindirectory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
