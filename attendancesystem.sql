-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 08:58 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendancesystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendancerecord`
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
-- Dumping data for table `attendancerecord`
--

INSERT INTO `attendancerecord` (`RecordID`, `Name`, `EmployeeID`, `Date`, `ClockInTime`, `ClockOutTime`, `OvertimeHours`, `TotalWorkHours`) VALUES
(40, 'Ben', 1, '2025-01-01', '09:00:00', '17:00:00', '0.00', '8.00'),
(41, 'Ben', 1, '2025-01-02', '08:55:00', '17:05:00', '0.00', '8.17'),
(42, 'Ben', 1, '2025-01-03', '09:02:00', '18:30:00', '0.50', '9.47'),
(43, 'Ben', 1, '2025-01-06', '08:58:00', '17:03:00', '0.00', '8.08'),
(44, 'Ben', 1, '2025-01-07', '09:05:00', '17:10:00', '0.08', '8.08'),
(45, 'Ben', 1, '2025-01-08', '08:50:00', '17:30:00', '0.33', '8.67'),
(46, 'Ben', 1, '2025-01-09', '09:01:00', '17:01:00', '0.00', '8.00'),
(47, 'Ben', 1, '2025-01-10', '09:03:00', '18:00:00', '0.30', '8.95'),
(48, 'Ben', 1, '2025-01-13', '08:57:00', '17:02:00', '0.00', '8.08'),
(49, 'Ben', 1, '2025-01-14', '09:00:00', '17:45:00', '0.75', '8.75'),
(50, 'Ben', 1, '2025-01-15', '08:45:00', '17:00:00', '0.25', '8.25'),
(51, 'Ben', 1, '2025-01-16', '09:05:00', '17:05:00', '0.00', '8.00'),
(52, 'Ben', 1, '2025-01-17', '08:59:00', '18:15:00', '0.42', '9.27'),
(53, 'Ben', 1, '2025-01-20', '09:00:00', '17:00:00', '0.00', '8.00'),
(54, 'Ben', 1, '2025-01-21', '08:56:00', '17:01:00', '0.02', '8.08'),
(55, 'Ben', 1, '2025-01-22', '09:03:00', '17:30:00', '0.50', '8.45'),
(56, 'Ben', 1, '2025-01-23', '08:52:00', '17:02:00', '0.17', '8.17'),
(57, 'Ben', 1, '2025-01-24', '09:00:00', '18:00:00', '0.67', '9.00'),
(58, 'Ben', 1, '2025-01-27', '08:55:00', '17:00:00', '0.08', '8.08'),
(59, 'Ben', 1, '2025-01-28', '09:01:00', '17:15:00', '0.25', '8.23'),
(60, 'Ben', 1, '2025-01-29', '08:48:00', '17:03:00', '0.38', '8.25'),
(61, 'Ben', 1, '2025-01-30', '09:00:00', '17:00:00', '0.00', '8.00'),
(62, 'Ben', 1, '2025-01-31', '08:59:00', '17:45:00', '0.75', '8.77'),
(63, 'Ben', 1, '2025-02-03', '09:02:00', '17:05:00', '0.05', '8.05'),
(64, 'Ben', 1, '2025-02-04', '08:57:00', '17:10:00', '0.15', '8.18'),
(65, 'Ben', 1, '2025-02-05', '09:00:00', '17:30:00', '0.50', '8.50'),
(66, 'Ben', 1, '2025-02-06', '08:50:00', '17:00:00', '0.17', '8.17'),
(67, 'Ben', 1, '2025-02-07', '09:01:00', '18:00:00', '0.83', '8.98'),
(68, 'Ben', 1, '2025-02-10', '08:55:00', '17:02:00', '0.05', '8.12'),
(69, 'Ben', 1, '2025-02-11', '09:03:00', '17:15:00', '0.20', '8.20'),
(70, 'Ben', 1, '2025-02-12', '08:48:00', '17:05:00', '0.45', '8.45'),
(71, 'Ben', 1, '2025-02-13', '09:00:00', '17:00:00', '0.00', '8.00'),
(72, 'Ben', 1, '2025-02-14', '08:59:00', '17:45:00', '0.75', '8.77'),
(73, 'Ben', 1, '2025-02-17', '09:02:00', '17:05:00', '0.05', '8.05'),
(74, 'Ben', 1, '2025-02-18', '08:57:00', '17:10:00', '0.15', '8.18'),
(75, 'Ben', 1, '2025-02-19', '09:00:00', '17:30:00', '0.50', '8.50'),
(76, 'Ben', 1, '2025-02-20', '08:50:00', '17:00:00', '0.17', '8.17'),
(77, 'Ben', 1, '2025-02-21', '09:01:00', '18:00:00', '0.83', '8.98'),
(78, 'Ben', 1, '2025-02-24', '08:55:00', '17:02:00', '0.05', '8.12'),
(79, 'Ben', 1, '2025-02-25', '09:03:00', '17:15:00', '0.20', '8.20'),
(80, 'Ben', 1, '2025-02-26', '08:48:00', '17:05:00', '0.45', '8.45'),
(81, 'Ben', 1, '2025-02-27', '09:00:00', '17:00:00', '0.00', '8.00'),
(82, 'Ben', 1, '2025-02-28', '08:59:00', '17:45:00', '0.75', '8.77'),
(83, 'Ben', 1, '2025-03-03', '09:02:00', '17:05:00', '0.05', '8.05'),
(84, 'Ben', 1, '2025-03-04', '08:57:00', '17:10:00', '0.15', '8.18'),
(85, 'Ben', 1, '2025-03-05', '09:00:00', '17:30:00', '0.50', '8.50'),
(86, 'Ben', 1, '2025-03-06', '08:50:00', '17:00:00', '0.17', '8.17'),
(87, 'Ben', 1, '2025-03-07', '09:01:00', '18:00:00', '0.83', '8.98'),
(88, 'Ben', 1, '2025-03-10', '08:55:00', '17:02:00', '0.05', '8.12'),
(89, 'Ben', 1, '2025-03-11', '09:03:00', '17:15:00', '0.20', '8.20'),
(90, 'Ben', 1, '2025-03-12', '08:48:00', '17:05:00', '0.45', '8.45'),
(91, 'Ben', 1, '2025-03-13', '09:00:00', '17:00:00', '0.00', '8.00'),
(92, 'Ben', 1, '2025-03-14', '08:59:00', '17:45:00', '0.75', '8.77'),
(93, 'Ben', 1, '2025-03-17', '09:02:00', '17:05:00', '0.05', '8.05'),
(94, 'Ben', 1, '2025-03-18', '08:57:00', '17:10:00', '0.15', '8.18'),
(95, 'Ben', 1, '2025-03-19', '09:00:00', '17:30:00', '0.50', '8.50'),
(96, 'Ben', 1, '2025-03-20', '08:50:00', '17:00:00', '0.17', '8.17'),
(97, 'Ben', 1, '2025-03-21', '09:01:00', '18:00:00', '0.83', '8.98'),
(98, 'Ben', 1, '2025-03-24', '08:55:00', '17:02:00', '0.05', '8.12'),
(99, 'Ben', 1, '2025-03-25', '09:03:00', '17:15:00', '0.20', '8.20'),
(100, 'Ben', 1, '2025-03-26', '08:48:00', '17:05:00', '0.45', '8.45'),
(101, 'Ben', 1, '2025-03-27', '09:00:00', '17:00:00', '0.00', '8.00'),
(102, 'Ben', 1, '2025-03-28', '08:59:00', '17:45:00', '0.75', '8.77'),
(103, 'Ben', 1, '2025-03-31', '09:02:00', '17:05:00', '0.05', '8.05'),
(104, 'Ben', 1, '2025-04-01', '08:57:00', '17:10:00', '0.15', '8.18'),
(105, 'Ben', 1, '2025-04-02', '09:00:00', '17:30:00', '0.50', '8.50'),
(106, 'Ben', 1, '2025-04-03', '08:50:00', '17:00:00', '0.17', '8.17'),
(107, 'Ben', 1, '2025-04-04', '09:01:00', '18:00:00', '0.83', '8.98'),
(108, 'Ben', 1, '2025-04-07', '08:55:00', '17:02:00', '0.05', '8.12'),
(109, 'Ben', 1, '2025-04-08', '09:03:00', '17:15:00', '0.20', '8.20'),
(110, 'Ben', 1, '2025-04-09', '08:48:00', '17:05:00', '0.45', '8.45'),
(111, 'Ben', 1, '2025-04-10', '09:00:00', '17:00:00', '0.00', '8.00'),
(112, 'Ben', 1, '2025-04-11', '08:59:00', '17:45:00', '0.75', '8.77'),
(113, 'Ben', 1, '2025-04-14', '09:02:00', '17:05:00', '0.05', '8.05'),
(114, 'Ben', 1, '2025-04-15', '08:57:00', '17:10:00', '0.15', '8.18'),
(115, 'Ben', 1, '2025-04-16', '09:00:00', '17:30:00', '0.50', '8.50'),
(116, 'Ben', 1, '2025-04-17', '08:50:00', '17:00:00', '0.17', '8.17'),
(117, 'Ben', 1, '2025-04-18', '09:01:00', '18:00:00', '0.83', '8.98'),
(118, 'Ben', 1, '2025-04-21', '08:55:00', '17:02:00', '0.05', '8.12'),
(119, 'Ben', 1, '2025-04-22', '09:03:00', '17:15:00', '0.20', '8.20'),
(120, 'Ben', 1, '2025-04-23', '08:48:00', '17:05:00', '0.45', '8.45'),
(121, 'Ben', 1, '2025-04-24', '09:00:00', '17:00:00', '0.00', '8.00'),
(122, 'Ben', 1, '2025-04-25', '08:59:00', '17:45:00', '0.75', '8.77'),
(123, 'Ben', 1, '2025-04-28', '09:02:00', '17:05:00', '0.05', '8.05'),
(124, 'Ben', 1, '2025-04-29', '08:57:00', '17:10:00', '0.15', '8.18'),
(125, 'Ben', 1, '2025-04-30', '09:00:00', '17:30:00', '0.50', '8.50'),
(126, 'Ben', 1, '2025-05-01', '08:50:00', '17:00:00', '0.17', '8.17'),
(127, 'Ben', 1, '2025-05-02', '09:01:00', '18:00:00', '0.83', '8.98'),
(128, 'Ben', 1, '2025-05-05', '08:55:00', '17:02:00', '0.05', '8.12'),
(129, 'Ben', 1, '2025-05-06', '09:03:00', '17:15:00', '0.20', '8.20'),
(130, 'Ben', 1, '2025-05-07', '08:48:00', '17:05:00', '0.45', '8.45'),
(131, 'Ben', 1, '2025-05-08', '09:00:00', '17:00:00', '0.00', '8.00'),
(132, 'Ben', 1, '2025-05-09', '08:59:00', '17:45:00', '0.75', '8.77'),
(133, 'Ben', 1, '2025-05-10', '09:02:00', '17:05:00', '0.05', '8.05'),
(134, 'Ben', 1, '2025-05-17', '12:03:24', '12:03:42', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expire_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `name`, `password`, `created_at`, `reset_token_hash`, `reset_token_expire_at`) VALUES
(1, 'EMP001', 'John Doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-05-11 13:56:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `overtime_requests`
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
-- Dumping data for table `overtime_requests`
--

INSERT INTO `overtime_requests` (`id`, `ot_date`, `start_time`, `end_time`, `total_hours`, `reason`, `department`, `created_at`) VALUES
(1, '2025-05-14', '10:10:00', '22:10:00', '12.00', '', 'IT', '2025-05-12 19:24:12'),
(2, '2025-05-14', '10:10:00', '22:10:00', '12.00', 'overtime', 'IT', '2025-05-13 05:13:56'),
(3, '2005-04-10', '10:10:00', '22:10:00', '12.00', 's', 'IT', '2025-05-19 20:17:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendancerecord`
--
ALTER TABLE `attendancerecord`
  ADD PRIMARY KEY (`RecordID`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendancerecord`
--
ALTER TABLE `attendancerecord`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
