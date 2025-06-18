-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-06-01 11:58:24
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `attendancesystem`
--

-- --------------------------------------------------------

--
-- 表的结构 `attendancerecord`
--

CREATE TABLE `attendancerecord` (
  `RecordID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `ClockInTime` time DEFAULT NULL,
  `ClockOutTime` time DEFAULT NULL,
  `OvertimeHours` decimal(5,2) DEFAULT 0.00,
  `TotalWorkHours` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `attendancerecord`
--

INSERT INTO `attendancerecord` (`RecordID`, `Name`, `EmployeeID`, `Date`, `ClockInTime`, `ClockOutTime`, `OvertimeHours`, `TotalWorkHours`) VALUES
(40, 'Ben', 1, '2025-01-01', '09:00:00', '17:00:00', 0.00, 8.00),
(41, 'Ben', 1, '2025-01-02', '08:55:00', '17:05:00', 0.00, 8.17),
(42, 'Ben', 1, '2025-01-03', '09:02:00', '18:30:00', 0.50, 9.47),
(43, 'Ben', 1, '2025-01-06', '08:58:00', '17:03:00', 0.00, 8.08),
(44, 'Ben', 1, '2025-01-07', '09:05:00', '17:10:00', 0.08, 8.08),
(45, 'Ben', 1, '2025-01-08', '08:50:00', '17:30:00', 0.33, 8.67),
(46, 'Ben', 1, '2025-01-09', '09:01:00', '17:01:00', 0.00, 8.00),
(47, 'Ben', 1, '2025-01-10', '09:03:00', '18:00:00', 0.30, 8.95),
(48, 'Ben', 1, '2025-01-13', '08:57:00', '17:02:00', 0.00, 8.08),
(49, 'Ben', 1, '2025-01-14', '09:00:00', '17:45:00', 0.75, 8.75),
(50, 'Ben', 1, '2025-01-15', '08:45:00', '17:00:00', 0.25, 8.25),
(51, 'Ben', 1, '2025-01-16', '09:05:00', '17:05:00', 0.00, 8.00),
(52, 'Ben', 1, '2025-01-17', '08:59:00', '18:15:00', 0.42, 9.27),
(53, 'Ben', 1, '2025-01-20', '09:00:00', '17:00:00', 0.00, 8.00),
(54, 'Ben', 1, '2025-01-21', '08:56:00', '17:01:00', 0.02, 8.08),
(55, 'Ben', 1, '2025-01-22', '09:03:00', '17:30:00', 0.50, 8.45),
(56, 'Ben', 1, '2025-01-23', '08:52:00', '17:02:00', 0.17, 8.17),
(57, 'Ben', 1, '2025-01-24', '09:00:00', '18:00:00', 0.67, 9.00),
(58, 'Ben', 1, '2025-01-27', '08:55:00', '17:00:00', 0.08, 8.08),
(59, 'Ben', 1, '2025-01-28', '09:01:00', '17:15:00', 0.25, 8.23),
(60, 'Ben', 1, '2025-01-29', '08:48:00', '17:03:00', 0.38, 8.25),
(61, 'Ben', 1, '2025-01-30', '09:00:00', '17:00:00', 0.00, 8.00),
(62, 'Ben', 1, '2025-01-31', '08:59:00', '17:45:00', 0.75, 8.77),
(63, 'Ben', 1, '2025-02-03', '09:02:00', '17:05:00', 0.05, 8.05),
(64, 'Ben', 1, '2025-02-04', '08:57:00', '17:10:00', 0.15, 8.18),
(65, 'Ben', 1, '2025-02-05', '09:00:00', '17:30:00', 0.50, 8.50),
(66, 'Ben', 1, '2025-02-06', '08:50:00', '17:00:00', 0.17, 8.17),
(67, 'Ben', 1, '2025-02-07', '09:01:00', '18:00:00', 0.83, 8.98),
(68, 'Ben', 1, '2025-02-10', '08:55:00', '17:02:00', 0.05, 8.12),
(69, 'Ben', 1, '2025-02-11', '09:03:00', '17:15:00', 0.20, 8.20),
(70, 'Ben', 1, '2025-02-12', '08:48:00', '17:05:00', 0.45, 8.45),
(71, 'Ben', 1, '2025-02-13', '09:00:00', '17:00:00', 0.00, 8.00),
(72, 'Ben', 1, '2025-02-14', '08:59:00', '17:45:00', 0.75, 8.77),
(73, 'Ben', 1, '2025-02-17', '09:02:00', '17:05:00', 0.05, 8.05),
(74, 'Ben', 1, '2025-02-18', '08:57:00', '17:10:00', 0.15, 8.18),
(75, 'Ben', 1, '2025-02-19', '09:00:00', '17:30:00', 0.50, 8.50),
(76, 'Ben', 1, '2025-02-20', '08:50:00', '17:00:00', 0.17, 8.17),
(77, 'Ben', 1, '2025-02-21', '09:01:00', '18:00:00', 0.83, 8.98),
(78, 'Ben', 1, '2025-02-24', '08:55:00', '17:02:00', 0.05, 8.12),
(79, 'Ben', 1, '2025-02-25', '09:03:00', '17:15:00', 0.20, 8.20),
(80, 'Ben', 1, '2025-02-26', '08:48:00', '17:05:00', 0.45, 8.45),
(81, 'Ben', 1, '2025-02-27', '09:00:00', '17:00:00', 0.00, 8.00),
(82, 'Ben', 1, '2025-02-28', '08:59:00', '17:45:00', 0.75, 8.77),
(83, 'Ben', 1, '2025-03-03', '09:02:00', '17:05:00', 0.05, 8.05),
(84, 'Ben', 1, '2025-03-04', '08:57:00', '17:10:00', 0.15, 8.18),
(85, 'Ben', 1, '2025-03-05', '09:00:00', '17:30:00', 0.50, 8.50),
(86, 'Ben', 1, '2025-03-06', '08:50:00', '17:00:00', 0.17, 8.17),
(87, 'Ben', 1, '2025-03-07', '09:01:00', '18:00:00', 0.83, 8.98),
(88, 'Ben', 1, '2025-03-10', '08:55:00', '17:02:00', 0.05, 8.12),
(89, 'Ben', 1, '2025-03-11', '09:03:00', '17:15:00', 0.20, 8.20),
(90, 'Ben', 1, '2025-03-12', '08:48:00', '17:05:00', 0.45, 8.45),
(91, 'Ben', 1, '2025-03-13', '09:00:00', '17:00:00', 0.00, 8.00),
(92, 'Ben', 1, '2025-03-14', '08:59:00', '17:45:00', 0.75, 8.77),
(93, 'Ben', 1, '2025-03-17', '09:02:00', '17:05:00', 0.05, 8.05),
(94, 'Ben', 1, '2025-03-18', '08:57:00', '17:10:00', 0.15, 8.18),
(95, 'Ben', 1, '2025-03-19', '09:00:00', '17:30:00', 0.50, 8.50),
(96, 'Ben', 1, '2025-03-20', '08:50:00', '17:00:00', 0.17, 8.17),
(97, 'Ben', 1, '2025-03-21', '09:01:00', '18:00:00', 0.83, 8.98),
(98, 'Ben', 1, '2025-03-24', '08:55:00', '17:02:00', 0.05, 8.12),
(99, 'Ben', 1, '2025-03-25', '09:03:00', '17:15:00', 0.20, 8.20),
(100, 'Ben', 1, '2025-03-26', '08:48:00', '17:05:00', 0.45, 8.45),
(101, 'Ben', 1, '2025-03-27', '09:00:00', '17:00:00', 0.00, 8.00),
(102, 'Ben', 1, '2025-03-28', '08:59:00', '17:45:00', 0.75, 8.77),
(103, 'Ben', 1, '2025-03-31', '09:02:00', '17:05:00', 0.05, 8.05),
(104, 'Ben', 1, '2025-04-01', '08:57:00', '17:10:00', 0.15, 8.18),
(105, 'Ben', 1, '2025-04-02', '09:00:00', '17:30:00', 0.50, 8.50),
(106, 'Ben', 1, '2025-04-03', '08:50:00', '17:00:00', 0.17, 8.17),
(107, 'Ben', 1, '2025-04-04', '09:01:00', '18:00:00', 0.83, 8.98),
(108, 'Ben', 1, '2025-04-07', '08:55:00', '17:02:00', 0.05, 8.12),
(109, 'Ben', 1, '2025-04-08', '09:03:00', '17:15:00', 0.20, 8.20),
(110, 'Ben', 1, '2025-04-09', '08:48:00', '17:05:00', 0.45, 8.45),
(111, 'Ben', 1, '2025-04-10', '09:00:00', '17:00:00', 0.00, 8.00),
(112, 'Ben', 1, '2025-04-11', '08:59:00', '17:45:00', 0.75, 8.77),
(113, 'Ben', 1, '2025-04-14', '09:02:00', '17:05:00', 0.05, 8.05),
(114, 'Ben', 1, '2025-04-15', '08:57:00', '17:10:00', 0.15, 8.18),
(115, 'Ben', 1, '2025-04-16', '09:00:00', '17:30:00', 0.50, 8.50),
(116, 'Ben', 1, '2025-04-17', '08:50:00', '17:00:00', 0.17, 8.17),
(117, 'Ben', 1, '2025-04-18', '09:01:00', '18:00:00', 0.83, 8.98),
(118, 'Ben', 1, '2025-04-21', '08:55:00', '17:02:00', 0.05, 8.12),
(119, 'Ben', 1, '2025-04-22', '09:03:00', '17:15:00', 0.20, 8.20),
(120, 'Ben', 1, '2025-04-23', '08:48:00', '17:05:00', 0.45, 8.45),
(121, 'Ben', 1, '2025-04-24', '09:00:00', '17:00:00', 0.00, 8.00),
(122, 'Ben', 1, '2025-04-25', '08:59:00', '17:45:00', 0.75, 8.77),
(123, 'Ben', 1, '2025-04-28', '09:02:00', '17:05:00', 0.05, 8.05),
(124, 'Ben', 1, '2025-04-29', '08:57:00', '17:10:00', 0.15, 8.18),
(125, 'Ben', 1, '2025-04-30', '09:00:00', '17:30:00', 0.50, 8.50),
(126, 'Ben', 1, '2025-05-01', '08:50:00', '17:00:00', 0.17, 8.17),
(127, 'Ben', 1, '2025-05-02', '09:01:00', '18:00:00', 0.83, 8.98),
(128, 'Ben', 1, '2025-05-05', '08:55:00', '17:02:00', 0.05, 8.12),
(129, 'Ben', 1, '2025-05-06', '09:03:00', '17:15:00', 0.20, 8.20),
(130, 'Ben', 1, '2025-05-07', '08:48:00', '17:05:00', 0.45, 8.45),
(131, 'Ben', 1, '2025-05-08', '09:00:00', '17:00:00', 0.00, 8.00),
(132, 'Ben', 1, '2025-05-09', '08:59:00', '17:45:00', 0.75, 8.77),
(133, 'Ben', 1, '2025-05-10', '09:02:00', '17:05:00', 0.05, 8.05),
(134, 'Ben', 1, '2025-05-17', '12:03:24', '12:03:42', 0.00, 0.00),
(135, 'Ben', 1, '2025-05-20', '08:10:22', '12:27:57', 0.00, 0.28),
(136, 'Ben', 1, '2025-05-22', '15:55:10', NULL, 0.00, 0.00),
(137, 'Ben', 1, '2025-05-26', '14:14:49', NULL, 0.00, 0.00),
(138, 'Ben', 1, '2025-06-01', '14:01:00', NULL, 0.00, 0.00);

-- --------------------------------------------------------

--
-- 表的结构 `cpd_program`
--

CREATE TABLE `cpd_program` (
  `id` int(11) NOT NULL,
  `programme_name` varchar(255) NOT NULL,
  `Day_Start` date DEFAULT NULL,
  `Day_End` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `cpd_program`
--

INSERT INTO `cpd_program` (`id`, `programme_name`, `Day_Start`, `Day_End`) VALUES
(1, 'GameDev Immersion: Advanced Engine Techniques', '2025-05-01', '2025-05-07'),
(2, 'Player Psychology & UX/UI Design for Engagement', '2025-06-10', '2025-06-15'),
(3, 'Narrative & World-Building Masterclass', '2025-07-05', '2025-07-10'),
(4, 'Esports & Live Service Game Operations', '2025-08-01', '2025-08-07'),
(5, 'Procedural Generation & AI in Gaming', '2025-08-08', '2025-08-14'),
(6, 'Monetization & Business Models in Gaming', '2025-09-01', '2025-09-07'),
(7, 'Cross-Platform Development & Optimization', '2025-10-01', '2025-10-07'),
(8, 'Agile Game Development & Project Management', '2025-10-08', '2025-10-15'),
(9, 'Audio Design & Interactive Soundscapes', '2025-11-01', '2025-11-07'),
(10, 'Emerging Technologies in Gaming: VR/AR/Cloud Gaming Exploration', '2025-12-01', '2025-12-07');

-- --------------------------------------------------------

--
-- 表的结构 `cpd_record`
--

CREATE TABLE `cpd_record` (
  `RecordId` int(11) NOT NULL,
  `Employee_ID` varchar(20) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `programme_name` varchar(255) NOT NULL,
  `ClockInTime` time DEFAULT NULL,
  `ClockOutTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `cpd_record`
--

INSERT INTO `cpd_record` (`RecordId`, `Employee_ID`, `Name`, `Date`, `programme_name`, `ClockInTime`, `ClockOutTime`) VALUES
(1, '1', 'Ben', '2025-06-01', 'Player Psychology & UX/UI Design for Engagement', '17:56:20', '17:57:52');

-- --------------------------------------------------------

--
-- 表的结构 `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `name`, `password`, `created_at`) VALUES
(125, '1212', 'benajmin', '$2y$10$SxWVuCpn6Pek2RYZmlXtD.en8ahNUqETdgkM2xZhnjKbTNrgsFreq', '2025-05-18 06:55:48'),
(130, '1', 'Ben', '$2y$10$OLJddRtE.2z6mbWz1QUhAO8vlPZDyzCoP0EckQtZFCnLgPnrlgm2q', '2025-05-18 07:08:20'),
(131, '222', 'happy', '$2y$10$0zMDggDG91wSFXga8/TI/eiQ4nbKMEC0BAlavbTVL/AsKSz5m9c/K', '2025-05-18 07:56:40'),
(132, '1314', 'tttttt', '13141314', '2025-05-19 01:22:37');

-- --------------------------------------------------------

--
-- 表的结构 `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `leave_type`, `start_date`, `end_date`, `reason`, `submitted_at`) VALUES
(7, 'sick', '2025-05-28', '2025-05-28', 'Got serious headache', '2025-05-26 07:17:41');

-- --------------------------------------------------------

--
-- 表的结构 `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `id` int(11) NOT NULL,
  `ot_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_hours` decimal(5,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `department` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `overtime_requests`
--

INSERT INTO `overtime_requests` (`id`, `ot_date`, `start_time`, `end_time`, `total_hours`, `reason`, `department`, `created_at`) VALUES
(1, '2025-05-14', '10:10:00', '22:10:00', 12.00, '', 'IT', '2025-05-12 11:24:12'),
(2, '2025-05-14', '10:10:00', '22:10:00', 12.00, 'overtime', 'IT', '2025-05-12 21:13:56'),
(3, '2005-04-10', '10:10:00', '22:10:00', 12.00, 's', 'IT', '2025-05-19 12:17:03');

--
-- 转储表的索引
--

--
-- 表的索引 `attendancerecord`
--
ALTER TABLE `attendancerecord`
  ADD PRIMARY KEY (`RecordID`);

--
-- 表的索引 `cpd_program`
--
ALTER TABLE `cpd_program`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cpd_record`
--
ALTER TABLE `cpd_record`
  ADD PRIMARY KEY (`RecordId`),
  ADD KEY `fk_employee_id` (`Employee_ID`);

--
-- 表的索引 `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- 表的索引 `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `attendancerecord`
--
ALTER TABLE `attendancerecord`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- 使用表AUTO_INCREMENT `cpd_program`
--
ALTER TABLE `cpd_program`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `cpd_record`
--
ALTER TABLE `cpd_record`
  MODIFY `RecordId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- 使用表AUTO_INCREMENT `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 限制导出的表
--

--
-- 限制表 `cpd_record`
--

ALTER TABLE `cpd_record`
  ADD CONSTRAINT `fk_employee_id` FOREIGN KEY (`Employee_ID`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
-- Add new tables for CPD categories and employee progress tracking
ALTER TABLE cpd_record ADD COLUMN program_id INT NULL;
ALTER TABLE cpd_record ADD FOREIGN KEY (program_id) REFERENCES cpd_program(id);
-- 1. Create CPD Categories table
CREATE TABLE `cpd_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `target_hours` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Insert CPD Categories based on your existing programs
INSERT INTO `cpd_categories` (`category_name`, `target_hours`, `description`) VALUES
('Technical Development', 10, 'Advanced technical skills including game engines, development tools, and programming'),
('User Experience & Design', 10, 'User interface design, user experience, and player psychology'),
('Creative Arts', 10, 'Narrative design, world-building, audio design, and creative content'),
('Business & Operations', 10, 'Business models, monetization, project management, and operations'),
('Emerging Technologies', 10, 'Virtual reality, augmented reality, AI, and cutting-edge gaming technologies');

-- 3. Add category_id to cpd_program table
ALTER TABLE `cpd_program` 
ADD COLUMN `category_id` int(11) DEFAULT NULL,
ADD FOREIGN KEY (`category_id`) REFERENCES `cpd_categories`(`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 4. Update existing programs with categories
UPDATE `cpd_program` SET `category_id` = 1 WHERE `id` = 1; -- GameDev Immersion: Advanced Engine Techniques
UPDATE `cpd_program` SET `category_id` = 2 WHERE `id` = 2; -- Player Psychology & UX/UI Design for Engagement
UPDATE `cpd_program` SET `category_id` = 3 WHERE `id` = 3; -- Narrative & World-Building Masterclass
UPDATE `cpd_program` SET `category_id` = 4 WHERE `id` = 4; -- Esports & Live Service Game Operations
UPDATE `cpd_program` SET `category_id` = 1 WHERE `id` = 5; -- Procedural Generation & AI in Gaming
UPDATE `cpd_program` SET `category_id` = 4 WHERE `id` = 6; -- Monetization & Business Models in Gaming
UPDATE `cpd_program` SET `category_id` = 1 WHERE `id` = 7; -- Cross-Platform Development & Optimization
UPDATE `cpd_program` SET `category_id` = 4 WHERE `id` = 8; -- Agile Game Development & Project Management
UPDATE `cpd_program` SET `category_id` = 3 WHERE `id` = 9; -- Audio Design & Interactive Soundscapes
UPDATE `cpd_program` SET `category_id` = 5 WHERE `id` = 10; -- Emerging Technologies in Gaming: VR/AR/Cloud Gaming

-- 5. Create employee CPD progress tracking table
CREATE TABLE `employee_cpd_progress` (
  `progress_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(20) NOT NULL,
  `category_id` int(11) NOT NULL,
  `completed_hours` decimal(5,2) DEFAULT 0.00,
  `target_hours` int(11) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`progress_id`),
  UNIQUE KEY `unique_employee_category` (`employee_id`, `category_id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `cpd_categories`(`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Add duration column to cpd_record table to track hours per session
ALTER TABLE `cpd_record` 
ADD COLUMN `duration_hours` decimal(5,2) DEFAULT 0.00 AFTER `ClockOutTime`;

-- 7. Create a trigger to automatically calculate duration and update progress
DELIMITER //
CREATE TRIGGER update_cpd_progress 
AFTER INSERT ON cpd_record
FOR EACH ROW
BEGIN
    DECLARE session_hours DECIMAL(5,2) DEFAULT 0.00;
    DECLARE prog_category_id INT;
    DECLARE target_hrs INT;
    
    -- Calculate session duration if both clock in and out times are provided
    IF NEW.ClockInTime IS NOT NULL AND NEW.ClockOutTime IS NOT NULL THEN
        SET session_hours = TIMESTAMPDIFF(MINUTE, 
            CONCAT(NEW.Date, ' ', NEW.ClockInTime), 
            CONCAT(NEW.Date, ' ', NEW.ClockOutTime)
        ) / 60.0;
        
        -- Update the duration in the record
        UPDATE cpd_record SET duration_hours = session_hours WHERE RecordId = NEW.RecordId;
    END IF;
    
    -- Get category_id for the program
    SELECT category_id INTO prog_category_id 
    FROM cpd_program 
    WHERE programme_name = NEW.programme_name;
    
    -- Get target hours for the category
    SELECT target_hours INTO target_hrs 
    FROM cpd_categories 
    WHERE category_id = prog_category_id;
    
    -- Insert or update employee progress
    IF prog_category_id IS NOT NULL THEN
        INSERT INTO employee_cpd_progress (employee_id, category_id, completed_hours, target_hours)
        VALUES (NEW.Employee_ID, prog_category_id, session_hours, target_hrs)
        ON DUPLICATE KEY UPDATE 
            completed_hours = completed_hours + session_hours,
            target_hours = target_hrs;
    END IF;
END//
DELIMITER ;

-- 8. Create trigger for updates to cpd_record
DELIMITER //
CREATE TRIGGER update_cpd_progress_on_update
AFTER UPDATE ON cpd_record
FOR EACH ROW
BEGIN
    DECLARE old_hours DECIMAL(5,2) DEFAULT 0.00;
    DECLARE new_hours DECIMAL(5,2) DEFAULT 0.00;
    DECLARE prog_category_id INT;
    
    -- Calculate old and new session durations
    IF OLD.ClockInTime IS NOT NULL AND OLD.ClockOutTime IS NOT NULL THEN
        SET old_hours = TIMESTAMPDIFF(MINUTE, 
            CONCAT(OLD.Date, ' ', OLD.ClockInTime), 
            CONCAT(OLD.Date, ' ', OLD.ClockOutTime)
        ) / 60.0;
    END IF;
    
    IF NEW.ClockInTime IS NOT NULL AND NEW.ClockOutTime IS NOT NULL THEN
        SET new_hours = TIMESTAMPDIFF(MINUTE, 
            CONCAT(NEW.Date, ' ', NEW.ClockInTime), 
            CONCAT(NEW.Date, ' ', NEW.ClockOutTime)
        ) / 60.0;
        
        -- Update the duration in the record
        UPDATE cpd_record SET duration_hours = new_hours WHERE RecordId = NEW.RecordId;
    END IF;
    
    -- Get category_id for the program
    SELECT category_id INTO prog_category_id 
    FROM cpd_program 
    WHERE programme_name = NEW.programme_name;
    
    -- Update employee progress (subtract old hours, add new hours)
    IF prog_category_id IS NOT NULL THEN
        UPDATE employee_cpd_progress 
        SET completed_hours = completed_hours - old_hours + new_hours
        WHERE employee_id = NEW.Employee_ID AND category_id = prog_category_id;
    END IF;
END//
DELIMITER ;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
