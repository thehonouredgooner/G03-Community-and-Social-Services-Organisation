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

-- --- about table(s)  (owner: Hoang Nam Nguyen) -------------------------

CREATE TABLE IF NOT EXISTS `about_info` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_code` VARCHAR(10) NOT NULL,
    `group_name` VARCHAR(100) NOT NULL,
    `industry` VARCHAR(150) NOT NULL,
    `tutorial_time` VARCHAR(100) NOT NULL,
    `tutor` VARCHAR(100) NOT NULL,
    `campus` VARCHAR(100) NOT NULL,
    `group_photo` VARCHAR(255) NOT NULL,
    `photo_caption` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `about_members` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `student_id` VARCHAR(20) NOT NULL,
    `part1_contribution` TEXT NOT NULL,
    `part2_contribution` TEXT NOT NULL,
    `quote` TEXT NOT NULL,
    `quote_language` VARCHAR(10) NOT NULL,
    `quote_translation` TEXT NOT NULL,
    `first_language` VARCHAR(50) NOT NULL,
    `fun_fact1` VARCHAR(255) NOT NULL,
    `fun_fact2` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Group information
INSERT INTO `about_info`
(`group_code`, `group_name`, `industry`, `tutorial_time`, `tutor`, `campus`, `group_photo`, `photo_caption`)
VALUES
(
  'G03',
  'Group 3',
  'Community and social services (not-for-profit)',
  'Thursday, 4:30 pm - 6:30pm',
  'Rahul Raghavan',
  'Hawthorn',
  'images/group-photo.jpg',
  'left to right: Vihaan, Kevin, Nam, Lan'
);


-- Team members information
INSERT INTO `about_members`
(`name`, `student_id`, `part1_contribution`, `part2_contribution`, `quote`, `quote_language`, `quote_translation`, `first_language`, `fun_fact1`, `fun_fact2`)
VALUES
(
  'Vihaan Nambi',
  '106546607',
  'Built the home page and its CSS styling. Wrote the event and community programs content, and was responsible for the final submission.',
  'TBC',
  'No tengas miedo al fracaso. Este es el camino hacia el éxito.',
  'es',
  'Don''t be afraid of failure. This is the way to succeed.',
  'English',
  'Coaches both basketball and tennis',
  'Spends his weekends hiking'
),
(
  'Kevin Lee',
  '106523655',
  'Built the application page and its CSS styling. Set up the team Jira board.',
  'TBC',
  '터널 끝에 빛이 있다',
  'ko',
  'There is always light at the end of the tunnel.',
  'Korean',
  'His Korean name means "the first", because he is the eldest child',
  'Currently unemployed and open to offers'
),
(
  'Hoang Nam Nguyen',
  '105548039',
  'Built this page and its CSS styling, produced the organisation logo, and wrote the shared sections of the stylesheet covering the site-wide colours, navigation and footer.',
  'TBC',
  'Có công mài sắt, có ngày nên kim',
  'vi',
  'Practice makes perfect.',
  'Vietnamese',
  'Once dreamed of becoming a professional footballer',
  'Australia is the first country he visited outside Vietnam'
),
(
  'Lan Nguyen',
  '106504197',
  'Built the jobs page and its CSS styling. Set up the team GitHub repository and deployed the site using GitHub Pages.',
  'TBC',
  'Lá lành đùm lá rách',
  'vi',
  'Whole leaves shelter the torn ones. Those who have more look after those who have less.',
  'Vietnamese',
  'Born in Vietnam',
  'Has one brother'
);
