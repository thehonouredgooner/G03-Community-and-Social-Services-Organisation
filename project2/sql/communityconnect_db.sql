-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2026 at 02:31 PM
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
-- Database: `communityconnect_db`
--
CREATE DATABASE IF NOT EXISTS `communityconnect_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `communityconnect_db`;


-- ==========================================================
-- communityconnect_db.sql — Group G03
-- Each person adds their own tables below. Please only edit
-- your own section.
-- ==========================================================

-- --- eoi table  (owner: Kevin) ----------------------------

CREATE TABLE `eoi` (
  `EOInumber` int(11) NOT NULL,
  `job_reference` char(6) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` varchar(12) NOT NULL,
  `street_address` varchar(40) NOT NULL,
  `suburb` varchar(40) NOT NULL,
  `state` varchar(3) NOT NULL,
  `postcode` char(4) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `skill_maintenance` tinyint(1) DEFAULT 0,
  `skill_event_planning` tinyint(1) DEFAULT 0,
  `skill_advertising` tinyint(1) DEFAULT 0,
  `skill_customer_service` tinyint(1) DEFAULT 0,
  `other_skills` text DEFAULT NULL,
  `status` enum('New','Current','Final') NOT NULL DEFAULT 'New'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eoi`
--
ALTER TABLE `eoi`
  ADD PRIMARY KEY (`EOInumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eoi`
--
ALTER TABLE `eoi`
  MODIFY `EOInumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



-- --- jobs table  (owner: TBA) -----------------------------

-- --- user table  (owner: TBA) -----------------------------

-- --- about table(s)  (owner: TBA) -------------------------