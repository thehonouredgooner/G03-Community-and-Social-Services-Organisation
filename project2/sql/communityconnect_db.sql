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



-- --- jobs table  (owner: Vihaan Nambi) -----------------------------
CREATE TABLE IF NOT EXISTS jobs (
  job_reference CHAR(6) NOT NULL PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  salary_range VARCHAR(60) NOT NULL,
  reports_to VARCHAR(100) NOT NULL,
  responsibilities TEXT NOT NULL,            
  essential_qualifications TEXT NOT NULL,    
  preferable_qualifications TEXT NULL        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO jobs
(job_reference, title, description, salary_range, reports_to, responsibilities, essential_qualifications, preferable_qualifications)
VALUES
('CS4A7B', 'Community Support Worker',
 'Community Connect is seeking a compassionate and reliable Community Support Worker to assist individuals and families accessing community services. The role involves providing practical support, connecting people with appropriate services and helping clients participate in community activities.',
 '$60,000 – $70,000 per year', 'Community Services Manager',
 'Provide practical and emotional support to community members.\nAssist clients in accessing appropriate support services.\nMaintain accurate and confidential client records.\nSupport community activities, workshops and events.\nWork with other staff and community organisations to meet client needs.',
 'Strong communication and interpersonal skills.\nAbility to work respectfully with people from diverse backgrounds.\nGood organisation and time-management skills.\nAbility to maintain confidentiality and professional boundaries.\nCurrent Working with Children Check or willingness to obtain one.',
 'Certificate or diploma in community services or a related field.\nPrevious experience in a community services environment.\nFirst Aid certification.\nExperience working with vulnerable individuals or families.'),

('VO82C5', 'Volunteer Coordinator',
 'We are looking for an organised and enthusiastic Volunteer Coordinator to recruit, support and manage volunteers across our community programs. The successful candidate will help volunteers find suitable opportunities and ensure they have a positive and rewarding experience.',
 '$65,000 – $75,000 per year', 'Community Programs Manager',
 'Recruit and register new volunteers.\nMatch volunteers with suitable community programs and events.\nProvide volunteer induction and ongoing support.\nMaintain accurate volunteer records and schedules.\nOrganise volunteer appreciation and engagement activities.',
 'Excellent communication and people skills.\nStrong organisational and administrative abilities.\nAbility to work with people from a wide range of backgrounds.\nAbility to manage multiple tasks and deadlines.\nConfidence using common office and communication software.',
 'Previous experience coordinating volunteers.\nExperience working in a not-for-profit organisation.\nExperience organising community events.\nKnowledge of volunteer management practices.'),

('CP6D9E', 'Community Programs Officer',
 'Community Connect is seeking a Community Programs Officer to help plan, coordinate and promote programs that respond to local community needs. This role will work with staff, volunteers and community partners to deliver accessible activities and events.',
 '$68,000 – $80,000 per year', 'Programs and Services Manager',
 'Assist with planning and delivering community programs.\nCoordinate community events, workshops and information sessions.\nWork with volunteers and community partners.\nPromote programs and events to community members.\nCollect feedback and assist with evaluating program outcomes.',
 'Strong written and verbal communication skills.\nExcellent planning and organisational skills.\nAbility to build positive relationships with community members.\nAbility to work independently and as part of a team.\nCommitment to inclusion, accessibility and community wellbeing.',
 'Qualification in community development, social services or a related field.\nPrevious experience coordinating community programs.\nExperience organising public events or workshops.\nKnowledge of local community services and support networks.');

-- --- user table  (owner: Vihaan Nambi) -----------------------------

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'manager',
  failed_attempts INT NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO users (username, password_hash, role)
VALUES ('admin', '$2y$10$1a0u6FtYWpxiw8Y05lK4P.AM74zL77H2KghGaM2/XQFSmA.M8D/g2', 'manager');

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
  'Developed the jobs database and dynamic job descriptions. Implemented the login functionality.',
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
  'Created the EOI database, implemented application form processing, and developed server-side validation.',
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
  'Converted HTML pages to PHP using shared includes, configured the database connection, and developed the dynamic About page.',
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
  'Developed the HR management page and implemented EOI management functionality.',
  'Lá lành đùm lá rách',
  'vi',
  'Whole leaves shelter the torn ones. Those who have more look after those who have less.',
  'Vietnamese',
  'Born in Vietnam',
  'Has one brother'
);
