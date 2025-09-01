-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: sdb-89.hosting.stackcp.net
-- Generation Time: Sep 01, 2025 at 09:43 AM
-- Server version: 10.11.14-MariaDB-log
-- PHP Version: 8.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quran_association-35313137dd9b`
--
CREATE DATABASE IF NOT EXISTS `quran_association-35313137dd9b` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `quran_association-35313137dd9b`;

-- --------------------------------------------------------

--
-- Table structure for table `absence_reasons`
--

CREATE TABLE `absence_reasons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reason_name` varchar(255) NOT NULL,
  `reason_description` text DEFAULT NULL,
  `reason_type` enum('medical','family','travel','emergency','personal','other') NOT NULL,
  `requires_documentation` tinyint(1) NOT NULL DEFAULT 0,
  `is_excused` tinyint(1) NOT NULL DEFAULT 1,
  `max_consecutive_days` int(11) DEFAULT NULL,
  `affects_attendance_record` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'absent',
  `points` int(11) NOT NULL DEFAULT 0,
  `memorization_points` int(11) NOT NULL DEFAULT 0,
  `final_points` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `marked_at` timestamp NULL DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `session_id`, `student_id`, `status`, `points`, `memorization_points`, `final_points`, `notes`, `marked_at`, `recorded_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'present', 6, 0, 0, NULL, '2024-05-02 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(2, 1, 2, 'present', 9, 0, 0, NULL, '2024-05-02 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(3, 1, 3, 'present', 7, 0, 0, NULL, '2024-05-02 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(4, 1, 4, 'present', 10, 0, 0, NULL, '2024-05-02 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(5, 1, 5, 'present', 5, 0, 0, NULL, '2024-05-02 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(6, 2, 1, 'present', 8, 0, 0, NULL, '2024-05-05 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(7, 2, 2, 'present', 7, 0, 0, NULL, '2024-05-05 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(8, 2, 3, 'present', 7, 0, 0, NULL, '2024-05-05 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(9, 2, 4, 'present', 10, 0, 0, NULL, '2024-05-05 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(10, 2, 5, 'present', 8, 0, 0, NULL, '2024-05-05 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(11, 3, 1, 'present', 9, 0, 0, NULL, '2024-05-07 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(12, 3, 2, 'present', 9, 0, 0, NULL, '2024-05-07 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(13, 3, 3, 'absent', 0, 0, 0, 'موعد طبي', '2024-05-07 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(14, 3, 4, 'present', 9, 0, 0, NULL, '2024-05-07 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(15, 3, 5, 'present', 5, 0, 0, NULL, '2024-05-07 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(16, 4, 1, 'present', 8, 0, 0, NULL, '2024-05-09 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(17, 4, 2, 'present', 6, 0, 0, NULL, '2024-05-09 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(18, 4, 3, 'present', 5, 0, 0, NULL, '2024-05-09 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(19, 4, 4, 'absent', 0, 0, 0, 'ظروف عائلية', '2024-05-09 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(20, 4, 5, 'present', 8, 0, 0, NULL, '2024-05-09 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(21, 5, 1, 'present', 7, 0, 0, NULL, '2024-05-12 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(22, 5, 2, 'present', 8, 0, 0, NULL, '2024-05-12 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(23, 5, 3, 'present', 6, 0, 0, NULL, '2024-05-12 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(24, 5, 4, 'present', 8, 0, 0, NULL, '2024-05-12 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(25, 5, 5, 'present', 10, 0, 0, NULL, '2024-05-12 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(26, 6, 1, 'present', 10, 0, 0, NULL, '2024-05-14 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(27, 6, 2, 'present', 9, 0, 0, NULL, '2024-05-14 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(28, 6, 3, 'present', 10, 0, 0, NULL, '2024-05-14 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(29, 6, 4, 'absent', 0, 0, 0, 'سفر مع الأسرة', '2024-05-14 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(30, 6, 5, 'present', 6, 0, 0, NULL, '2024-05-14 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(31, 7, 1, 'present', 8, 0, 0, NULL, '2024-05-16 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(32, 7, 2, 'present', 8, 0, 0, NULL, '2024-05-16 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(33, 7, 3, 'present', 6, 0, 0, NULL, '2024-05-16 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(34, 7, 4, 'absent', 0, 0, 0, 'امتحانات المدرسة', '2024-05-16 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(35, 7, 5, 'present', 9, 0, 0, NULL, '2024-05-16 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(36, 8, 1, 'present', 10, 0, 0, NULL, '2024-05-19 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(37, 8, 2, 'present', 8, 0, 0, NULL, '2024-05-19 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(38, 8, 3, 'present', 8, 0, 0, NULL, '2024-05-19 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(39, 8, 4, 'present', 5, 0, 0, NULL, '2024-05-19 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(40, 8, 5, 'present', 5, 0, 0, NULL, '2024-05-19 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(41, 9, 1, 'present', 8, 0, 0, NULL, '2024-05-21 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(42, 9, 2, 'present', 6, 0, 0, NULL, '2024-05-21 15:00:00', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(43, 9, 3, 'present', 9, 0, 0, NULL, '2024-05-21 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(44, 9, 4, 'absent', 0, 0, 0, 'موعد طبي', '2024-05-21 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(45, 9, 5, 'present', 7, 0, 0, NULL, '2024-05-21 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(46, 10, 1, 'present', 6, 0, 0, NULL, '2024-05-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(47, 10, 2, 'present', 8, 0, 0, NULL, '2024-05-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(48, 10, 3, 'present', 5, 0, 0, NULL, '2024-05-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(49, 10, 4, 'present', 5, 0, 0, NULL, '2024-05-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(50, 10, 5, 'present', 10, 0, 0, NULL, '2024-05-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(51, 11, 1, 'present', 10, 0, 0, NULL, '2024-05-26 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(52, 11, 2, 'present', 6, 0, 0, NULL, '2024-05-26 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(53, 11, 3, 'absent', 0, 0, 0, 'امتحانات المدرسة', '2024-05-26 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(54, 11, 4, 'present', 9, 0, 0, NULL, '2024-05-26 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(55, 11, 5, 'absent', 0, 0, 0, 'امتحانات المدرسة', '2024-05-26 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(56, 12, 1, 'present', 7, 0, 0, NULL, '2024-05-28 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(57, 12, 2, 'present', 6, 0, 0, NULL, '2024-05-28 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(58, 12, 3, 'present', 7, 0, 0, NULL, '2024-05-28 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(59, 12, 4, 'present', 10, 0, 0, NULL, '2024-05-28 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(60, 12, 5, 'present', 10, 0, 0, NULL, '2024-05-28 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(61, 13, 1, 'present', 9, 0, 0, NULL, '2024-05-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(62, 13, 2, 'present', 6, 0, 0, NULL, '2024-05-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(63, 13, 3, 'present', 7, 0, 0, NULL, '2024-05-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(64, 13, 4, 'present', 8, 0, 0, NULL, '2024-05-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(65, 13, 5, 'present', 8, 0, 0, NULL, '2024-05-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(66, 14, 1, 'present', 8, 0, 0, NULL, '2024-06-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(67, 14, 2, 'absent', 0, 0, 0, 'مرض', '2024-06-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(68, 14, 3, 'present', 6, 0, 0, NULL, '2024-06-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(69, 14, 4, 'present', 10, 0, 0, NULL, '2024-06-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(70, 14, 5, 'present', 7, 0, 0, NULL, '2024-06-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(71, 15, 1, 'present', 9, 0, 0, NULL, '2024-06-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(72, 15, 2, 'absent', 0, 0, 0, 'موعد طبي', '2024-06-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(73, 15, 3, 'present', 8, 0, 0, NULL, '2024-06-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(74, 15, 4, 'present', 10, 0, 0, NULL, '2024-06-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(75, 15, 5, 'present', 6, 0, 0, NULL, '2024-06-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(76, 16, 1, 'present', 8, 0, 0, NULL, '2024-06-06 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(77, 16, 2, 'absent', 0, 0, 0, 'موعد طبي', '2024-06-06 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(78, 16, 3, 'present', 7, 0, 0, NULL, '2024-06-06 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(79, 16, 4, 'absent', 0, 0, 0, 'سفر مع الأسرة', '2024-06-06 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(80, 16, 5, 'absent', 0, 0, 0, 'موعد طبي', '2024-06-06 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(81, 17, 1, 'present', 6, 0, 0, NULL, '2024-06-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(82, 17, 2, 'present', 5, 0, 0, NULL, '2024-06-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(83, 17, 3, 'present', 6, 0, 0, NULL, '2024-06-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(84, 17, 4, 'present', 10, 0, 0, NULL, '2024-06-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(85, 17, 5, 'absent', 0, 0, 0, 'ظروف عائلية', '2024-06-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(86, 18, 1, 'present', 10, 0, 0, NULL, '2024-06-11 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(87, 18, 2, 'present', 10, 0, 0, NULL, '2024-06-11 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(88, 18, 3, 'absent', 0, 0, 0, 'ظروف عائلية', '2024-06-11 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(89, 18, 4, 'present', 9, 0, 0, NULL, '2024-06-11 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(90, 18, 5, 'present', 9, 0, 0, NULL, '2024-06-11 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(91, 19, 1, 'present', 10, 0, 0, NULL, '2024-06-13 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(92, 19, 2, 'present', 9, 0, 0, NULL, '2024-06-13 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(93, 19, 3, 'present', 8, 0, 0, NULL, '2024-06-13 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(94, 19, 4, 'present', 10, 0, 0, NULL, '2024-06-13 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(95, 19, 5, 'present', 8, 0, 0, NULL, '2024-06-13 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(106, 22, 1, 'present', 7, 0, 0, NULL, '2024-06-20 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(107, 22, 2, 'present', 10, 0, 0, NULL, '2024-06-20 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(108, 22, 3, 'present', 5, 0, 0, NULL, '2024-06-20 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(109, 22, 4, 'present', 7, 0, 0, NULL, '2024-06-20 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(110, 22, 5, 'present', 5, 0, 0, NULL, '2024-06-20 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(111, 23, 1, 'present', 5, 0, 0, NULL, '2024-06-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(112, 23, 2, 'present', 7, 0, 0, NULL, '2024-06-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(113, 23, 3, 'present', 7, 0, 0, NULL, '2024-06-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(114, 23, 4, 'absent', 0, 0, 0, 'غياب بدون عذر', '2024-06-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(115, 23, 5, 'present', 10, 0, 0, NULL, '2024-06-23 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(116, 24, 1, 'present', 5, 0, 0, NULL, '2024-06-25 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(117, 24, 2, 'present', 9, 0, 0, NULL, '2024-06-25 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(118, 24, 3, 'absent', 0, 0, 0, 'موعد طبي', '2024-06-25 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(119, 24, 4, 'present', 9, 0, 0, NULL, '2024-06-25 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(120, 24, 5, 'present', 6, 0, 0, NULL, '2024-06-25 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(121, 25, 1, 'present', 10, 0, 0, NULL, '2024-06-27 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(122, 25, 2, 'present', 10, 0, 0, NULL, '2024-06-27 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(123, 25, 3, 'present', 9, 0, 0, NULL, '2024-06-27 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(124, 25, 4, 'present', 10, 0, 0, NULL, '2024-06-27 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(125, 25, 5, 'present', 7, 0, 0, NULL, '2024-06-27 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(126, 26, 1, 'present', 6, 0, 0, NULL, '2024-06-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(127, 26, 2, 'present', 6, 0, 0, NULL, '2024-06-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(128, 26, 3, 'present', 7, 0, 0, NULL, '2024-06-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(129, 26, 4, 'present', 8, 0, 0, NULL, '2024-06-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(130, 26, 5, 'present', 10, 0, 0, NULL, '2024-06-30 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(131, 27, 1, 'present', 8, 0, 0, NULL, '2024-07-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(132, 27, 2, 'present', 6, 0, 0, NULL, '2024-07-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(133, 27, 3, 'present', 5, 0, 0, NULL, '2024-07-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(134, 27, 4, 'present', 9, 0, 0, NULL, '2024-07-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(135, 27, 5, 'present', 10, 0, 0, NULL, '2024-07-02 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(136, 28, 1, 'present', 9, 0, 0, NULL, '2024-07-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(137, 28, 2, 'present', 7, 0, 0, NULL, '2024-07-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(138, 28, 3, 'absent', 0, 0, 0, 'امتحانات المدرسة', '2024-07-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(139, 28, 4, 'present', 7, 0, 0, NULL, '2024-07-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(140, 28, 5, 'present', 6, 0, 0, NULL, '2024-07-04 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(141, 29, 1, 'present', 10, 0, 0, NULL, '2024-07-07 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(142, 29, 2, 'present', 6, 0, 0, NULL, '2024-07-07 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(143, 29, 3, 'absent', 0, 0, 0, 'مرض', '2024-07-07 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(144, 29, 4, 'present', 9, 0, 0, NULL, '2024-07-07 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(145, 29, 5, 'present', 9, 0, 0, NULL, '2024-07-07 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(146, 30, 1, 'present', 5, 0, 0, NULL, '2024-07-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(147, 30, 2, 'absent', 0, 0, 0, 'موعد طبي', '2024-07-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(148, 30, 3, 'absent', 0, 0, 0, 'مرض', '2024-07-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(149, 30, 4, 'absent', 0, 0, 0, 'ظروف عائلية', '2024-07-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(150, 30, 5, 'present', 10, 0, 0, NULL, '2024-07-09 15:00:00', NULL, '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(226, 38, 1, 'absent', 0, 4, 0, 'امتحانات المدرسة', NULL, 1, '2025-08-27 19:28:29', '2025-08-27 19:28:29'),
(227, 38, 2, 'present', 5, 3, 8, NULL, NULL, 1, '2025-08-27 19:28:29', '2025-08-27 19:28:29'),
(228, 38, 3, 'present', 5, 1, 6, NULL, NULL, 1, '2025-08-27 19:28:29', '2025-08-27 19:28:29'),
(229, 38, 4, 'absent', 0, 2, 0, 'غياب بدون عذر', NULL, 1, '2025-08-27 19:28:29', '2025-08-27 19:28:29'),
(230, 38, 5, 'absent', 0, 4, 0, 'سفر مع الأسرة', NULL, 1, '2025-08-27 19:28:29', '2025-08-27 19:28:29'),
(231, 37, 1, 'present', 5, 4, 9, NULL, NULL, 1, '2025-08-27 19:29:06', '2025-08-27 19:29:06'),
(232, 37, 2, 'present', 5, 4, 9, NULL, NULL, 1, '2025-08-27 19:29:06', '2025-08-27 19:29:06'),
(233, 37, 3, 'present', 5, 4, 9, NULL, NULL, 1, '2025-08-27 19:29:06', '2025-08-27 19:29:06'),
(234, 37, 4, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-27 19:29:06', '2025-08-27 19:29:06'),
(235, 37, 5, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-27 19:29:06', '2025-08-27 19:29:06'),
(241, 20, 1, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-28 08:11:44', '2025-08-28 08:11:44'),
(242, 20, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-28 08:11:44', '2025-08-28 08:11:44'),
(243, 20, 3, 'absent', 0, 0, 0, 'غياب بدون عذر', NULL, 1, '2025-08-28 08:11:44', '2025-08-28 08:11:44'),
(244, 20, 4, 'present', 5, 4, 9, NULL, NULL, 1, '2025-08-28 08:11:44', '2025-08-28 08:11:44'),
(245, 20, 5, 'present', 5, 4, 9, NULL, NULL, 1, '2025-08-28 08:11:44', '2025-08-28 08:11:44'),
(246, 40, 1, 'present', 2, 0, 2, NULL, '2025-08-28 18:40:58', 1, '2025-08-28 18:40:58', '2025-08-28 18:40:58'),
(247, 40, 2, 'absent', 0, 0, 0, NULL, '2025-08-28 18:40:58', 1, '2025-08-28 18:40:58', '2025-08-28 18:40:58'),
(248, 40, 3, 'excused', 0, 0, 0, NULL, '2025-08-28 18:40:58', 1, '2025-08-28 18:40:58', '2025-08-28 18:40:58'),
(249, 40, 4, 'absent', 0, 0, 0, NULL, '2025-08-28 18:40:58', 1, '2025-08-28 18:40:58', '2025-08-28 18:40:58'),
(250, 40, 5, 'present', 2, 0, 2, NULL, '2025-08-28 18:40:58', 1, '2025-08-28 18:40:58', '2025-08-28 18:40:58'),
(251, 39, 1, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-28 18:42:15', '2025-08-28 18:42:15'),
(252, 39, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-28 18:42:15', '2025-08-28 18:42:15'),
(253, 39, 3, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-28 18:42:15', '2025-08-28 18:42:15'),
(254, 39, 4, 'absent', 0, 5, 0, 'مرض', NULL, 1, '2025-08-28 18:42:15', '2025-08-28 18:42:15'),
(255, 39, 5, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-28 18:42:15', '2025-08-28 18:42:15'),
(256, 36, 1, 'present', 5, 4, 9, NULL, NULL, 1, '2025-08-31 06:32:44', '2025-08-31 06:32:44'),
(257, 36, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:32:44', '2025-08-31 06:32:44'),
(258, 36, 3, 'absent', 0, 0, 0, 'موعد طبي', NULL, 1, '2025-08-31 06:32:44', '2025-08-31 06:32:44'),
(259, 36, 4, 'present', 5, 3, 8, NULL, NULL, 1, '2025-08-31 06:32:44', '2025-08-31 06:32:44'),
(260, 36, 5, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:32:44', '2025-08-31 06:32:44'),
(261, 35, 1, 'present', 5, 4, 9, NULL, NULL, 1, '2025-08-31 06:33:10', '2025-08-31 06:33:10'),
(262, 35, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:10', '2025-08-31 06:33:10'),
(263, 35, 3, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:10', '2025-08-31 06:33:10'),
(264, 35, 4, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:10', '2025-08-31 06:33:10'),
(265, 35, 5, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:10', '2025-08-31 06:33:10'),
(266, 34, 1, 'present', 5, 3, 8, NULL, NULL, 1, '2025-08-31 06:33:26', '2025-08-31 06:33:26'),
(267, 34, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:26', '2025-08-31 06:33:26'),
(268, 34, 3, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:26', '2025-08-31 06:33:26'),
(269, 34, 4, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:26', '2025-08-31 06:33:26'),
(270, 34, 5, 'absent', 0, 5, 0, 'موعد طبي', NULL, 1, '2025-08-31 06:33:26', '2025-08-31 06:33:26'),
(271, 33, 1, 'late', 3, 4, 7, NULL, NULL, 1, '2025-08-31 06:33:57', '2025-08-31 06:33:57'),
(272, 33, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:57', '2025-08-31 06:33:57'),
(273, 33, 3, 'absent', 0, 0, 0, 'موعد طبي', NULL, 1, '2025-08-31 06:33:57', '2025-08-31 06:33:57'),
(274, 33, 4, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:33:57', '2025-08-31 06:33:57'),
(275, 33, 5, 'absent', 0, 0, 0, 'موعد طبي', NULL, 1, '2025-08-31 06:33:57', '2025-08-31 06:33:57'),
(276, 32, 1, 'absent', 0, 0, 0, 'موعد طبي', NULL, 1, '2025-08-31 06:34:28', '2025-08-31 06:34:28'),
(277, 32, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:34:28', '2025-08-31 06:34:28'),
(278, 32, 3, 'absent', 0, 0, 0, 'ظروف عائلية', NULL, 1, '2025-08-31 06:34:28', '2025-08-31 06:34:28'),
(279, 32, 4, 'absent', 0, 0, 0, 'مرض', NULL, 1, '2025-08-31 06:34:28', '2025-08-31 06:34:28'),
(280, 32, 5, 'absent', 0, 0, 0, 'امتحانات المدرسة', NULL, 1, '2025-08-31 06:34:28', '2025-08-31 06:34:28'),
(281, 31, 1, 'present', 5, 3, 8, NULL, NULL, 1, '2025-08-31 06:35:02', '2025-08-31 06:35:02'),
(282, 31, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:35:02', '2025-08-31 06:35:02'),
(283, 31, 3, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:35:02', '2025-08-31 06:35:02'),
(284, 31, 4, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:35:02', '2025-08-31 06:35:02'),
(285, 31, 5, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:35:02', '2025-08-31 06:35:02'),
(286, 21, 1, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:35:29', '2025-08-31 06:35:29'),
(287, 21, 2, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:35:29', '2025-08-31 06:35:29'),
(288, 21, 3, 'absent', 0, 0, 0, 'غياب بدون عذر', NULL, 1, '2025-08-31 06:35:29', '2025-08-31 06:35:29'),
(289, 21, 4, 'absent', 0, 0, 0, 'غياب بدون عذر', NULL, 1, '2025-08-31 06:35:29', '2025-08-31 06:35:29'),
(290, 21, 5, 'present', 5, 5, 10, NULL, NULL, 1, '2025-08-31 06:35:29', '2025-08-31 06:35:29');

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `circle_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `session_type` enum('morning','evening') NOT NULL DEFAULT 'morning',
  `status` enum('present','absent','late','excused') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_reports`
--

CREATE TABLE `attendance_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_title` varchar(255) NOT NULL,
  `report_type` enum('daily','weekly','monthly','custom','session') NOT NULL,
  `report_date` date NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `circle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`report_data`)),
  `statistics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`statistics`)),
  `total_sessions` int(11) NOT NULL DEFAULT 0,
  `total_students` int(11) NOT NULL DEFAULT 0,
  `total_present` int(11) NOT NULL DEFAULT 0,
  `total_absent` int(11) NOT NULL DEFAULT 0,
  `attendance_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `summary` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `is_automated` tinyint(1) NOT NULL DEFAULT 1,
  `generated_by` bigint(20) UNSIGNED NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `recipients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recipients`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_sessions`
--

CREATE TABLE `attendance_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `circle_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `arrival_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `absence_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `parent_notified` tinyint(1) NOT NULL DEFAULT 0,
  `parent_notified_at` timestamp NULL DEFAULT NULL,
  `notification_method` enum('sms','email','app','call') DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_makeup_session` tinyint(1) NOT NULL DEFAULT 0,
  `makeup_for_session` bigint(20) UNSIGNED DEFAULT NULL,
  `participation_score` decimal(3,1) DEFAULT NULL,
  `behavior_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `circles`
--

CREATE TABLE `circles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `time` time DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 60,
  `days` varchar(255) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `max_students` int(11) NOT NULL DEFAULT 20,
  `schedule_days` varchar(255) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `circles`
--

INSERT INTO `circles` (`id`, `name`, `description`, `teacher_id`, `time`, `duration`, `days`, `level`, `max_students`, `schedule_days`, `start_time`, `end_time`, `location`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط', 'حلقة تحفيظ للمستوى المتوسط تركز على حفظ الأجزاء من الأول إلى العاشر مع التركيز على التجويد والفهم', 1, NULL, 60, NULL, 'beginner', 10, 'الأحد، الثلاثاء، الخميس', '16:00:00', '18:00:00', 'قاعة التحفيظ الرئيسية', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `circle_student`
--

CREATE TABLE `circle_student` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `circle_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_date` date NOT NULL DEFAULT '2025-08-27',
  `status` enum('active','inactive','graduated','transferred') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

CREATE TABLE `class_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_name` varchar(255) NOT NULL,
  `circle_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `recurrence_type` enum('weekly','monthly') NOT NULL DEFAULT 'weekly',
  `location` varchar(255) DEFAULT NULL,
  `max_students` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `has_multiple_days` tinyint(1) NOT NULL DEFAULT 0,
  `default_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`default_settings`)),
  `requires_attendance` tinyint(1) NOT NULL DEFAULT 1,
  `auto_create_sessions` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','inactive','completed') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`id`, `schedule_name`, `circle_id`, `start_date`, `end_date`, `start_time`, `end_time`, `recurrence_type`, `location`, `max_students`, `is_active`, `has_multiple_days`, `default_settings`, `requires_attendance`, `auto_create_sessions`, `status`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'جدولة حلقة تحفيظ القرآن الكريم - المستوى المتوسط', 1, '2025-08-27', '2025-12-31', '16:00:00', '18:00:00', 'weekly', 'قاعة التحفيظ الرئيسية', NULL, 1, 0, NULL, 1, 1, 'active', 'تجريبية', NULL, '2025-08-27 19:25:42', '2025-08-27 19:25:42');

-- --------------------------------------------------------

--
-- Table structure for table `class_sessions`
--

CREATE TABLE `class_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `circle_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `session_title` varchar(255) NOT NULL,
  `session_description` text DEFAULT NULL,
  `session_date` date NOT NULL,
  `actual_start_time` time DEFAULT NULL,
  `actual_end_time` time DEFAULT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled','missed') NOT NULL DEFAULT 'scheduled',
  `lesson_content` text DEFAULT NULL,
  `homework` text DEFAULT NULL,
  `session_notes` text DEFAULT NULL,
  `total_students` int(11) NOT NULL DEFAULT 0,
  `present_students` int(11) NOT NULL DEFAULT 0,
  `absent_students` int(11) NOT NULL DEFAULT 0,
  `attendance_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `attendance_taken` tinyint(1) NOT NULL DEFAULT 0,
  `attendance_taken_at` timestamp NULL DEFAULT NULL,
  `attendance_taken_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_sessions`
--

INSERT INTO `class_sessions` (`id`, `schedule_id`, `circle_id`, `teacher_id`, `session_title`, `session_description`, `session_date`, `actual_start_time`, `actual_end_time`, `status`, `lesson_content`, `homework`, `session_notes`, `total_students`, `present_students`, `absent_students`, `attendance_percentage`, `attendance_taken`, `attendance_taken_at`, `attendance_taken_by`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 1, 'الجلسة رقم 1', 'جلسة تحفيظ ومراجعة - 2024-05-02', '2024-05-02', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(2, NULL, 1, 1, 'الجلسة رقم 2', 'جلسة تحفيظ ومراجعة - 2024-05-05', '2024-05-05', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(3, NULL, 1, 1, 'الجلسة رقم 3', 'جلسة تحفيظ ومراجعة - 2024-05-07', '2024-05-07', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(4, NULL, 1, 1, 'الجلسة رقم 4', 'جلسة تحفيظ ومراجعة - 2024-05-09', '2024-05-09', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(5, NULL, 1, 1, 'الجلسة رقم 5', 'جلسة تحفيظ ومراجعة - 2024-05-12', '2024-05-12', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(6, NULL, 1, 1, 'الجلسة رقم 6', 'جلسة تحفيظ ومراجعة - 2024-05-14', '2024-05-14', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(7, NULL, 1, 1, 'الجلسة رقم 7', 'جلسة تحفيظ ومراجعة - 2024-05-16', '2024-05-16', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(8, NULL, 1, 1, 'الجلسة رقم 8', 'جلسة تحفيظ ومراجعة - 2024-05-19', '2024-05-19', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(9, NULL, 1, 1, 'الجلسة رقم 9', 'جلسة تحفيظ ومراجعة - 2024-05-21', '2024-05-21', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(10, NULL, 1, 1, 'الجلسة رقم 10', 'جلسة تحفيظ ومراجعة - 2024-05-23', '2024-05-23', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(11, NULL, 1, 1, 'الجلسة رقم 11', 'جلسة تحفيظ ومراجعة - 2024-05-26', '2024-05-26', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(12, NULL, 1, 1, 'الجلسة رقم 12', 'جلسة تحفيظ ومراجعة - 2024-05-28', '2024-05-28', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(13, NULL, 1, 1, 'الجلسة رقم 13', 'جلسة تحفيظ ومراجعة - 2024-05-30', '2024-05-30', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(14, NULL, 1, 1, 'الجلسة رقم 14', 'جلسة تحفيظ ومراجعة - 2024-06-02', '2024-06-02', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(15, NULL, 1, 1, 'الجلسة رقم 15', 'جلسة تحفيظ ومراجعة - 2024-06-04', '2024-06-04', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(16, NULL, 1, 1, 'الجلسة رقم 16', 'جلسة تحفيظ ومراجعة - 2024-06-06', '2024-06-06', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(17, NULL, 1, 1, 'الجلسة رقم 17', 'جلسة تحفيظ ومراجعة - 2024-06-09', '2024-06-09', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(18, NULL, 1, 1, 'الجلسة رقم 18', 'جلسة تحفيظ ومراجعة - 2024-06-11', '2024-06-11', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(19, NULL, 1, 1, 'الجلسة رقم 19', 'جلسة تحفيظ ومراجعة - 2024-06-13', '2024-06-13', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(20, NULL, 1, 1, 'الجلسة رقم 20', 'جلسة تحفيظ ومراجعة - 2024-06-16', '2024-06-16', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 4, 1, 80.00, 1, '2025-08-28 08:11:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-28 08:11:44'),
(21, NULL, 1, 1, 'الجلسة رقم 21', 'جلسة تحفيظ ومراجعة - 2024-06-18', '2024-06-18', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 3, 2, 60.00, 1, '2025-08-31 06:35:29', 1, NULL, '2025-08-27 19:22:44', '2025-08-31 06:35:29'),
(22, NULL, 1, 1, 'الجلسة رقم 22', 'جلسة تحفيظ ومراجعة - 2024-06-20', '2024-06-20', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(23, NULL, 1, 1, 'الجلسة رقم 23', 'جلسة تحفيظ ومراجعة - 2024-06-23', '2024-06-23', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(24, NULL, 1, 1, 'الجلسة رقم 24', 'جلسة تحفيظ ومراجعة - 2024-06-25', '2024-06-25', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(25, NULL, 1, 1, 'الجلسة رقم 25', 'جلسة تحفيظ ومراجعة - 2024-06-27', '2024-06-27', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(26, NULL, 1, 1, 'الجلسة رقم 26', 'جلسة تحفيظ ومراجعة - 2024-06-30', '2024-06-30', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(27, NULL, 1, 1, 'الجلسة رقم 27', 'جلسة تحفيظ ومراجعة - 2024-07-02', '2024-07-02', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(28, NULL, 1, 1, 'الجلسة رقم 28', 'جلسة تحفيظ ومراجعة - 2024-07-04', '2024-07-04', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(29, NULL, 1, 1, 'الجلسة رقم 29', 'جلسة تحفيظ ومراجعة - 2024-07-07', '2024-07-07', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(30, NULL, 1, 1, 'الجلسة رقم 30', 'جلسة تحفيظ ومراجعة - 2024-07-09', '2024-07-09', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 0, 0, 0.00, 1, '2025-08-27 19:22:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(31, NULL, 1, 1, 'الجلسة رقم 31', 'جلسة تحفيظ ومراجعة - 2024-07-11', '2024-07-11', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 5, 0, 100.00, 1, '2025-08-31 06:35:02', 1, NULL, '2025-08-27 19:22:44', '2025-08-31 06:35:02'),
(32, NULL, 1, 1, 'الجلسة رقم 32', 'جلسة تحفيظ ومراجعة - 2024-07-14', '2024-07-14', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 1, 4, 20.00, 1, '2025-08-31 06:34:28', 1, NULL, '2025-08-27 19:22:44', '2025-08-31 06:34:28'),
(33, NULL, 1, 1, 'الجلسة رقم 33', 'جلسة تحفيظ ومراجعة - 2024-07-16', '2024-07-16', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 2, 3, 40.00, 1, '2025-08-31 06:33:57', 1, NULL, '2025-08-27 19:22:44', '2025-08-31 06:33:57'),
(34, NULL, 1, 1, 'الجلسة رقم 34', 'جلسة تحفيظ ومراجعة - 2024-07-18', '2024-07-18', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 4, 1, 80.00, 1, '2025-08-31 06:33:26', 1, NULL, '2025-08-27 19:22:44', '2025-08-31 06:33:26'),
(35, NULL, 1, 1, 'الجلسة رقم 35', 'جلسة تحفيظ ومراجعة - 2024-07-21', '2024-07-21', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 5, 0, 100.00, 1, '2025-08-31 06:33:10', 1, NULL, '2025-08-27 19:22:44', '2025-08-31 06:33:10'),
(36, NULL, 1, 1, 'الجلسة رقم 36', 'جلسة تحفيظ ومراجعة - 2024-07-23', '2024-07-23', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 4, 1, 80.00, 1, '2025-08-31 06:32:44', 1, NULL, '2025-08-27 19:22:44', '2025-08-31 06:32:44'),
(37, NULL, 1, 1, 'الجلسة رقم 37', 'جلسة تحفيظ ومراجعة - 2024-07-25', '2024-07-25', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 5, 0, 100.00, 1, '2025-08-27 19:29:06', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:29:06'),
(38, NULL, 1, 1, 'الجلسة رقم 38', 'جلسة تحفيظ ومراجعة - 2024-07-28', '2024-07-28', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 2, 3, 40.00, 1, '2025-08-27 19:28:29', 1, NULL, '2025-08-27 19:22:44', '2025-08-27 19:28:29'),
(39, NULL, 1, 1, 'الجلسة رقم 39', 'جلسة تحفيظ ومراجعة - 2024-07-30', '2024-07-30', '16:00:00', '18:00:00', 'completed', 'حفظ ومراجعة أجزاء من القرآن الكريم', NULL, 'جلسة مراجعة وحفظ جديد', 5, 4, 1, 80.00, 1, '2025-08-28 18:42:15', 1, NULL, '2025-08-27 19:22:44', '2025-08-28 18:42:15'),
(40, NULL, 1, 1, 'جلسة حلقة تحفيظ القرآن الكريم - المستوى المتوسط - ٥‏/٣‏/١٤٤٧ هـ', 'gdfg', '2025-08-28', '08:00:00', '09:30:00', 'ongoing', NULL, NULL, NULL, 0, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-08-28 18:40:24', '2025-08-28 18:40:29');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

CREATE TABLE `guardians` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `national_id` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `job` varchar(255) DEFAULT NULL,
  `access_code` varchar(255) NOT NULL,
  `relationship` enum('father','mother','guardian','other') NOT NULL DEFAULT 'father',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guardians`
--

INSERT INTO `guardians` (`id`, `name`, `phone`, `email`, `national_id`, `address`, `job`, `access_code`, `relationship`, `is_active`, `notes`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'أحمد عبدالله', '0501234567', 'ahmed.parent@gmail.com', NULL, NULL, NULL, '4567', 'father', 1, NULL, '2025-08-31 18:36:47', '2025-08-27 19:22:44', '2025-08-31 18:36:47'),
(2, 'محمد حسن', '0501234568', 'mohammed.parent@gmail.com', NULL, NULL, NULL, '4568', 'father', 1, NULL, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(3, 'علي أحمد', '0501234569', 'ali.parent@gmail.com', NULL, NULL, NULL, '4569', 'father', 1, NULL, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(4, 'سالم محمد', '0501234570', 'salem.parent@gmail.com', NULL, NULL, NULL, '4570', 'father', 1, NULL, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(5, 'إبراهيم يوسف', '0501234571', 'ibrahim.parent@gmail.com', NULL, NULL, NULL, '4571', 'father', 1, NULL, NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `guardian_student`
--

CREATE TABLE `guardian_student` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `guardian_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `relationship_type` enum('father','mother','guardian','other') NOT NULL DEFAULT 'father',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guardian_student`
--

INSERT INTO `guardian_student` (`id`, `guardian_id`, `student_id`, `relationship_type`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'father', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(2, 2, 2, 'father', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(3, 3, 3, 'father', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(4, 4, 4, 'father', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(5, 5, 5, 'father', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `memorization_points`
--

CREATE TABLE `memorization_points` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `session_type` enum('morning','evening') NOT NULL,
  `points` int(10) UNSIGNED NOT NULL,
  `memorized_content` text DEFAULT NULL,
  `teacher_notes` text DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `memorization_points`
--

INSERT INTO `memorization_points` (`id`, `student_id`, `date`, `session_type`, `points`, `memorized_content`, `teacher_notes`, `recorded_by`, `recorded_at`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-05-02', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(2, 2, '2024-05-02', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(3, 3, '2024-05-02', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(4, 4, '2024-05-02', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(5, 5, '2024-05-02', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(6, 1, '2024-05-05', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(7, 2, '2024-05-05', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(8, 3, '2024-05-05', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(9, 4, '2024-05-05', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(10, 5, '2024-05-05', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(11, 1, '2024-05-07', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(12, 2, '2024-05-07', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(13, 4, '2024-05-07', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(14, 5, '2024-05-07', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(15, 1, '2024-05-09', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(16, 2, '2024-05-09', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(17, 3, '2024-05-09', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(18, 5, '2024-05-09', 'evening', 6, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(19, 1, '2024-05-12', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(20, 2, '2024-05-12', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(21, 3, '2024-05-12', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(22, 4, '2024-05-12', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(23, 5, '2024-05-12', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(24, 1, '2024-05-14', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(25, 2, '2024-05-14', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(26, 3, '2024-05-14', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(27, 5, '2024-05-14', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(28, 1, '2024-05-16', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(29, 2, '2024-05-16', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(30, 3, '2024-05-16', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(31, 5, '2024-05-16', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(32, 1, '2024-05-19', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(33, 2, '2024-05-19', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(34, 3, '2024-05-19', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(35, 4, '2024-05-19', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(36, 5, '2024-05-19', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(37, 1, '2024-05-21', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(38, 2, '2024-05-21', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:44', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(39, 3, '2024-05-21', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(40, 5, '2024-05-21', 'evening', 6, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(41, 1, '2024-05-23', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(42, 2, '2024-05-23', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(43, 3, '2024-05-23', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(44, 4, '2024-05-23', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(45, 5, '2024-05-23', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(46, 1, '2024-05-26', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(47, 2, '2024-05-26', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(48, 4, '2024-05-26', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(49, 1, '2024-05-28', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(50, 2, '2024-05-28', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(51, 3, '2024-05-28', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(52, 4, '2024-05-28', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(53, 5, '2024-05-28', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(54, 1, '2024-05-30', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(55, 2, '2024-05-30', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(56, 3, '2024-05-30', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(57, 4, '2024-05-30', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(58, 5, '2024-05-30', 'evening', 6, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(59, 1, '2024-06-02', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(60, 3, '2024-06-02', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(61, 4, '2024-06-02', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(62, 5, '2024-06-02', 'evening', 6, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(63, 1, '2024-06-04', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(64, 3, '2024-06-04', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(65, 4, '2024-06-04', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(66, 5, '2024-06-04', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(67, 1, '2024-06-06', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(68, 3, '2024-06-06', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(69, 1, '2024-06-09', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(70, 2, '2024-06-09', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(71, 3, '2024-06-09', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(72, 4, '2024-06-09', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(73, 1, '2024-06-11', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(74, 2, '2024-06-11', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(75, 4, '2024-06-11', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(76, 5, '2024-06-11', 'evening', 6, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(77, 1, '2024-06-13', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(78, 2, '2024-06-13', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(79, 3, '2024-06-13', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(80, 4, '2024-06-13', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(81, 5, '2024-06-13', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(82, 1, '2024-06-16', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(83, 2, '2024-06-16', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(84, 4, '2024-06-16', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(85, 5, '2024-06-16', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(86, 1, '2024-06-18', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(87, 2, '2024-06-18', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(88, 5, '2024-06-18', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(89, 1, '2024-06-20', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(90, 2, '2024-06-20', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(91, 3, '2024-06-20', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(92, 4, '2024-06-20', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(93, 5, '2024-06-20', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(94, 1, '2024-06-23', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(95, 2, '2024-06-23', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(96, 3, '2024-06-23', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(97, 5, '2024-06-23', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(98, 1, '2024-06-25', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(99, 2, '2024-06-25', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(100, 4, '2024-06-25', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(101, 5, '2024-06-25', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(102, 1, '2024-06-27', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(103, 2, '2024-06-27', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(104, 3, '2024-06-27', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(105, 4, '2024-06-27', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(106, 5, '2024-06-27', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(107, 1, '2024-06-30', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(108, 2, '2024-06-30', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(109, 3, '2024-06-30', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(110, 4, '2024-06-30', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(111, 5, '2024-06-30', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(112, 1, '2024-07-02', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(113, 2, '2024-07-02', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(114, 3, '2024-07-02', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(115, 4, '2024-07-02', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(116, 5, '2024-07-02', 'evening', 6, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(117, 1, '2024-07-04', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(118, 2, '2024-07-04', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(119, 4, '2024-07-04', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(120, 5, '2024-07-04', 'evening', 6, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(121, 1, '2024-07-07', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(122, 2, '2024-07-07', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(123, 4, '2024-07-07', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(124, 5, '2024-07-07', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(125, 1, '2024-07-09', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(126, 5, '2024-07-09', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(127, 1, '2024-07-11', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(128, 2, '2024-07-11', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(129, 3, '2024-07-11', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(130, 4, '2024-07-11', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(131, 5, '2024-07-11', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(132, 2, '2024-07-14', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(133, 1, '2024-07-16', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(134, 2, '2024-07-16', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(135, 4, '2024-07-16', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(136, 1, '2024-07-18', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(137, 2, '2024-07-18', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(138, 3, '2024-07-18', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(139, 4, '2024-07-18', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(140, 1, '2024-07-21', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(141, 2, '2024-07-21', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(142, 3, '2024-07-21', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(143, 4, '2024-07-21', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(144, 5, '2024-07-21', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(145, 1, '2024-07-23', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(146, 2, '2024-07-23', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(147, 4, '2024-07-23', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(148, 5, '2024-07-23', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(149, 1, '2024-07-25', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(150, 2, '2024-07-25', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(151, 3, '2024-07-25', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(152, 4, '2024-07-25', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(153, 5, '2024-07-25', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(154, 2, '2024-07-28', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(155, 3, '2024-07-28', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(156, 1, '2024-07-30', 'evening', 9, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(157, 2, '2024-07-30', 'evening', 7, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(158, 3, '2024-07-30', 'evening', 10, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45'),
(159, 5, '2024-07-30', 'evening', 8, 'حفظ وتلاوة مع مراجعة الأجزاء السابقة', 'أداء ممتاز، استمر على هذا المستوى', 1, '2025-08-27 19:22:45', '2025-08-27 19:22:45', '2025-08-27 19:22:45');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_07_09_095058_create_teachers_table', 1),
(6, '2025_07_09_095059_create_circles_table', 1),
(7, '2025_07_09_095060_create_students_table', 1),
(8, '2025_07_09_095062_create_attendances_table', 1),
(9, '2025_07_09_095063_create_student_progress_table', 1),
(10, '2025_07_09_095065_create_news_table', 1),
(11, '2025_07_09_104012_create_memorization_points_table', 1),
(12, '2025_07_09_104012_create_notifications_table', 1),
(13, '2025_07_09_104012_create_users_table_new', 1),
(14, '2025_07_09_104013_create_notification_settings_table', 1),
(15, '2025_07_09_104013_create_notification_templates_table', 1),
(16, '2025_07_09_104124_update_students_table_add_parent_id', 1),
(17, '2025_07_09_104734_merge_users_tables', 1),
(18, '2025_07_09_115655_create_class_schedules_table', 1),
(19, '2025_07_09_115713_create_class_sessions_table', 1),
(20, '2025_07_09_115731_create_attendance_sessions_table', 1),
(21, '2025_07_09_115752_create_absence_reasons_table', 1),
(22, '2025_07_09_115809_create_attendance_reports_table', 1),
(23, '2025_07_09_131332_update_circles_table_add_new_fields', 1),
(24, '2025_07_09_153342_update_teachers_table_add_advanced_fields', 1),
(25, '2025_07_09_160000_add_soft_deletes_to_teachers_table', 1),
(26, '2025_07_09_170000_create_teacher_ratings_table', 1),
(27, '2025_07_09_170500_add_level_to_circles_table', 1),
(28, '2025_07_09_171000_add_session_type_to_attendances_table', 1),
(29, '2025_07_13_061119_create_schedule_days_table', 1),
(30, '2025_07_17_120000_create_sessions_table', 1),
(31, '2025_07_17_180000_create_circle_student_table', 1),
(32, '2025_08_11_190238_create_guardians_table', 1),
(33, '2025_08_11_190255_create_guardian_student_table', 1),
(34, '2025_08_11_191037_add_last_login_at_to_guardians_table', 1),
(35, '2025_08_11_200218_update_guardians_relationship_values', 1),
(36, '2025_08_11_221944_fix_guardians_table_structure', 1),
(37, '2025_08_11_224343_add_password_to_teachers_table', 1),
(38, '2025_08_11_231038_create_attendance_table', 1),
(39, '2025_08_11_231251_update_sessions_table_structure', 1),
(40, '2025_08_11_231702_fix_sessions_table_schedule_id', 1),
(41, '2025_08_11_234702_add_recorded_by_to_attendance_table', 1),
(42, '2025_08_12_003556_add_memorization_points_to_attendance_table', 1),
(43, '2025_08_12_120940_fix_attendance_session_foreign_key', 1),
(44, '2025_08_12_203531_create_student_circles_table', 1),
(45, '2025_08_17_100500_modify_class_sessions_schedule_id_nullable', 1),
(46, '2025_08_19_103800_add_missed_status_to_class_sessions', 1);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `publish_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('attendance','memorization','report','reminder','announcement') NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `status` enum('pending','sent','delivered','read','failed') NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `fcm_message_id` varchar(255) DEFAULT NULL,
  `priority` enum('low','normal','high') NOT NULL DEFAULT 'normal',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_settings`
--

CREATE TABLE `notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `notification_type` enum('attendance','memorization','report','reminder','announcement') NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `delivery_method` enum('push','sms','email') NOT NULL DEFAULT 'push',
  `quiet_hours_start` time NOT NULL DEFAULT '22:00:00',
  `quiet_hours_end` time NOT NULL DEFAULT '06:00:00',
  `frequency` enum('immediate','daily_digest','weekly_digest') NOT NULL DEFAULT 'immediate',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('attendance','memorization','report','reminder','announcement') NOT NULL,
  `title_template` varchar(255) NOT NULL,
  `body_template` text NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_days`
--

CREATE TABLE `schedule_days` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` enum('sunday','monday','tuesday','wednesday','thursday','friday','saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `session_type` enum('morning','afternoon','evening') DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `circle_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `session_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `max_students` int(11) DEFAULT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled','postponed') NOT NULL DEFAULT 'scheduled',
  `requires_attendance` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `lesson_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`lesson_content`)),
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `age` int(11) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `parent_phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `education_level` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `total_attendance_points` int(11) NOT NULL DEFAULT 0,
  `total_memorization_points` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','inactive','transferred','graduated') NOT NULL DEFAULT 'active',
  `circle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `parent_id`, `age`, `phone`, `parent_phone`, `address`, `birth_date`, `gender`, `education_level`, `notes`, `total_attendance_points`, `total_memorization_points`, `is_active`, `status`, `circle_id`, `created_at`, `updated_at`) VALUES
(1, 'عبدالرحمن أحمد', NULL, 12, '0501111111', '0501234567', 'الرياض، المملكة العربية السعودية', '2012-03-15', 'male', 'ابتدائي', 'طالب متميز في الحفظ والتلاوة', 0, 0, 1, 'active', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(2, 'فاطمة محمد', NULL, 11, '0501111112', '0501234568', 'الرياض، المملكة العربية السعودية', '2013-07-22', 'female', 'ابتدائي', 'طالبة مجتهدة ومنتظمة في الحضور', 0, 0, 1, 'active', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(3, 'محمد علي', NULL, 13, '0501111113', '0501234569', 'الرياض، المملكة العربية السعودية', '2011-11-08', 'male', 'ابتدائي', 'طالب نشط ومتفاعل في الحلقة', 0, 0, 1, 'active', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(4, 'عائشة سالم', NULL, 10, '0501111114', '0501234570', 'الرياض، المملكة العربية السعودية', '2014-01-30', 'female', 'ابتدائي', 'طالبة بحاجة لمزيد من التشجيع', 0, 0, 1, 'active', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(5, 'يوسف إبراهيم', NULL, 14, '0501111115', '0501234571', 'الرياض، المملكة العربية السعودية', '2010-09-12', 'male', 'ابتدائي', 'طالب ذكي لكن يحتاج لمزيد من الانتظام', 0, 0, 1, 'active', NULL, '2025-08-27 19:22:44', '2025-08-27 19:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `student_circles`
--

CREATE TABLE `student_circles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `circle_id` bigint(20) UNSIGNED NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `left_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_circles`
--

INSERT INTO `student_circles` (`id`, `student_id`, `circle_id`, `enrolled_at`, `left_at`, `is_active`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-04-30 23:00:00', NULL, 1, 'انضم في بداية الفصل الدراسي', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(2, 2, 1, '2024-04-30 23:00:00', NULL, 1, 'انضم في بداية الفصل الدراسي', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(3, 3, 1, '2024-04-30 23:00:00', NULL, 1, 'انضم في بداية الفصل الدراسي', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(4, 4, 1, '2024-04-30 23:00:00', NULL, 1, 'انضم في بداية الفصل الدراسي', '2025-08-27 19:22:44', '2025-08-27 19:22:44'),
(5, 5, 1, '2024-04-30 23:00:00', NULL, 1, 'انضم في بداية الفصل الدراسي', '2025-08-27 19:22:44', '2025-08-27 19:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `student_progress`
--

CREATE TABLE `student_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `surah_name` varchar(255) NOT NULL,
  `from_verse` int(11) NOT NULL,
  `to_verse` int(11) NOT NULL,
  `status` enum('memorizing','reviewing','completed') NOT NULL,
  `grade` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(4) DEFAULT NULL COMMENT 'آخر 4 أرقام من الهاتف',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `national_id` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `specialization` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `can_receive_notifications` tinyint(1) NOT NULL DEFAULT 1,
  `max_students` int(11) NOT NULL DEFAULT 20,
  `salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `phone`, `password`, `last_login_at`, `email`, `national_id`, `address`, `birth_date`, `gender`, `qualification`, `experience_years`, `hire_date`, `bio`, `skills`, `photo`, `experience`, `specialization`, `is_active`, `can_receive_notifications`, `max_students`, `salary`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'أحمد محمد الأستاذ', '0501234888', '4888', '2025-08-28 18:39:00', 'ahmed.teacher@quran.com', NULL, 'الرياض، المملكة العربية السعودية', '1990-08-28', 'male', 'بكالوريوس الشريعة', NULL, NULL, NULL, NULL, NULL, '8', 'تحفيظ القرآن الكريم', 1, 1, 20, NULL, '2025-08-27 19:22:44', '2025-08-28 18:39:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_ratings`
--

CREATE TABLE `teacher_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` int(10) UNSIGNED NOT NULL COMMENT 'التقييم من 1 إلى 5',
  `comment` text DEFAULT NULL COMMENT 'تعليق على التقييم',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `fcm_token` varchar(255) DEFAULT NULL,
  `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_preferences`)),
  `password` varchar(255) NOT NULL,
  `role` enum('admin','parent') NOT NULL DEFAULT 'parent',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `phone_verified_at`, `last_login_at`, `fcm_token`, `notification_preferences`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'مدير النظام', 'admin@quran.com', '0501234000', '2025-08-27 19:22:44', NULL, NULL, NULL, NULL, '$2y$12$yXyUJQmuU6ij762hlYMxM.FDykxaLJR5sEz00pllinDb.BBl4CKOm', 'admin', 1, 'qZ08lVJJuZpVfk8Votc7ztGqb94YQy9fDp9eizOwYbY8xhtc4yBBZnWmTs6X', '2025-08-27 19:22:44', '2025-08-27 19:22:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absence_reasons`
--
ALTER TABLE `absence_reasons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absence_reasons_reason_type_index` (`reason_type`),
  ADD KEY `absence_reasons_is_active_index` (`is_active`),
  ADD KEY `absence_reasons_is_excused_index` (`is_excused`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_session_id_student_id_unique` (`session_id`,`student_id`),
  ADD KEY `attendance_student_id_foreign` (`student_id`),
  ADD KEY `attendance_recorded_by_foreign` (`recorded_by`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_student_id_foreign` (`student_id`),
  ADD KEY `attendances_circle_id_foreign` (`circle_id`);

--
-- Indexes for table `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_reports_generated_by_foreign` (`generated_by`),
  ADD KEY `attendance_reports_report_type_report_date_index` (`report_type`,`report_date`),
  ADD KEY `attendance_reports_circle_id_period_start_period_end_index` (`circle_id`,`period_start`,`period_end`),
  ADD KEY `attendance_reports_teacher_id_period_start_period_end_index` (`teacher_id`,`period_start`,`period_end`),
  ADD KEY `attendance_reports_student_id_period_start_period_end_index` (`student_id`,`period_start`,`period_end`),
  ADD KEY `attendance_reports_is_automated_index` (`is_automated`),
  ADD KEY `attendance_reports_is_sent_index` (`is_sent`);

--
-- Indexes for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_sessions_session_id_student_id_unique` (`session_id`,`student_id`),
  ADD KEY `attendance_sessions_recorded_by_foreign` (`recorded_by`),
  ADD KEY `attendance_sessions_makeup_for_session_foreign` (`makeup_for_session`),
  ADD KEY `attendance_sessions_session_id_status_index` (`session_id`,`status`),
  ADD KEY `attendance_sessions_student_id_status_index` (`student_id`,`status`),
  ADD KEY `attendance_sessions_circle_id_status_index` (`circle_id`,`status`),
  ADD KEY `attendance_sessions_parent_notified_index` (`parent_notified`),
  ADD KEY `attendance_sessions_recorded_at_index` (`recorded_at`);

--
-- Indexes for table `circles`
--
ALTER TABLE `circles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `circles_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `circle_student`
--
ALTER TABLE `circle_student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `circle_student_circle_id_student_id_unique` (`circle_id`,`student_id`),
  ADD KEY `circle_student_circle_id_status_index` (`circle_id`,`status`),
  ADD KEY `circle_student_student_id_status_index` (`student_id`,`status`);

--
-- Indexes for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_schedules_circle_id_foreign` (`circle_id`),
  ADD KEY `idx_schedule_dates` (`start_date`,`end_date`),
  ADD KEY `idx_schedule_status` (`status`),
  ADD KEY `idx_schedule_active` (`is_active`);

--
-- Indexes for table `class_sessions`
--
ALTER TABLE `class_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_sessions_attendance_taken_by_foreign` (`attendance_taken_by`),
  ADD KEY `class_sessions_circle_id_session_date_index` (`circle_id`,`session_date`),
  ADD KEY `class_sessions_teacher_id_session_date_index` (`teacher_id`,`session_date`),
  ADD KEY `class_sessions_session_date_status_index` (`session_date`,`status`),
  ADD KEY `class_sessions_attendance_taken_index` (`attendance_taken`),
  ADD KEY `class_sessions_schedule_id_foreign` (`schedule_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `guardians`
--
ALTER TABLE `guardians`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guardians_phone_unique` (`phone`);

--
-- Indexes for table `guardian_student`
--
ALTER TABLE `guardian_student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guardian_student_guardian_id_student_id_unique` (`guardian_id`,`student_id`),
  ADD KEY `guardian_student_student_id_foreign` (`student_id`);

--
-- Indexes for table `memorization_points`
--
ALTER TABLE `memorization_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_points` (`student_id`,`date`,`session_type`),
  ADD KEY `memorization_points_recorded_by_foreign` (`recorded_by`),
  ADD KEY `memorization_points_date_index` (`date`),
  ADD KEY `memorization_points_student_id_date_index` (`student_id`,`date`),
  ADD KEY `memorization_points_points_index` (`points`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_status_index` (`user_id`,`status`),
  ADD KEY `notifications_type_index` (`type`),
  ADD KEY `notifications_sent_at_index` (`sent_at`),
  ADD KEY `notifications_scheduled_at_index` (`scheduled_at`);

--
-- Indexes for table `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_type` (`user_id`,`notification_type`),
  ADD KEY `notification_settings_user_id_index` (`user_id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_templates_name_unique` (`name`),
  ADD KEY `notification_templates_type_index` (`type`),
  ADD KEY `notification_templates_is_active_index` (`is_active`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `schedule_days`
--
ALTER TABLE `schedule_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schedule_days_schedule_id_day_of_week_start_time_unique` (`schedule_id`,`day_of_week`,`start_time`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_circle_id_foreign` (`circle_id`),
  ADD KEY `idx_session_date` (`session_date`),
  ADD KEY `idx_session_status` (`status`),
  ADD KEY `idx_schedule_session_date` (`schedule_id`,`session_date`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `students_circle_id_foreign` (`circle_id`),
  ADD KEY `students_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `student_circles`
--
ALTER TABLE `student_circles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_circles_student_id_circle_id_unique` (`student_id`,`circle_id`),
  ADD KEY `student_circles_student_id_is_active_index` (`student_id`,`is_active`),
  ADD KEY `student_circles_circle_id_is_active_index` (`circle_id`,`is_active`);

--
-- Indexes for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_progress_student_id_foreign` (`student_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teachers_email_unique` (`email`),
  ADD UNIQUE KEY `teachers_national_id_unique` (`national_id`);

--
-- Indexes for table `teacher_ratings`
--
ALTER TABLE `teacher_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_ratings_teacher_id_student_id_unique` (`teacher_id`,`student_id`),
  ADD KEY `teacher_ratings_student_id_foreign` (`student_id`),
  ADD KEY `teacher_ratings_teacher_id_rating_index` (`teacher_id`,`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_is_active_index` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absence_reasons`
--
ALTER TABLE `absence_reasons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_reports`
--
ALTER TABLE `attendance_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `circles`
--
ALTER TABLE `circles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `circle_student`
--
ALTER TABLE `circle_student`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `class_sessions`
--
ALTER TABLE `class_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guardians`
--
ALTER TABLE `guardians`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `guardian_student`
--
ALTER TABLE `guardian_student`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `memorization_points`
--
ALTER TABLE `memorization_points`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_settings`
--
ALTER TABLE `notification_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule_days`
--
ALTER TABLE `schedule_days`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_circles`
--
ALTER TABLE `student_circles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teacher_ratings`
--
ALTER TABLE `teacher_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `teachers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `class_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD CONSTRAINT `attendance_reports_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_reports_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_reports_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD CONSTRAINT `attendance_sessions_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_sessions_makeup_for_session_foreign` FOREIGN KEY (`makeup_for_session`) REFERENCES `class_sessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_sessions_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_sessions_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `class_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_sessions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `circles`
--
ALTER TABLE `circles`
  ADD CONSTRAINT `circles_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `circle_student`
--
ALTER TABLE `circle_student`
  ADD CONSTRAINT `circle_student_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `circle_student_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `class_schedules_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_sessions`
--
ALTER TABLE `class_sessions`
  ADD CONSTRAINT `class_sessions_attendance_taken_by_foreign` FOREIGN KEY (`attendance_taken_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `class_sessions_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_sessions_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `class_sessions_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guardian_student`
--
ALTER TABLE `guardian_student`
  ADD CONSTRAINT `guardian_student_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guardian_student_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `memorization_points`
--
ALTER TABLE `memorization_points`
  ADD CONSTRAINT `memorization_points_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `memorization_points_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD CONSTRAINT `notification_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule_days`
--
ALTER TABLE `schedule_days`
  ADD CONSTRAINT `schedule_days_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_circles`
--
ALTER TABLE `student_circles`
  ADD CONSTRAINT `student_circles_circle_id_foreign` FOREIGN KEY (`circle_id`) REFERENCES `circles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_circles_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD CONSTRAINT `student_progress_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_ratings`
--
ALTER TABLE `teacher_ratings`
  ADD CONSTRAINT `teacher_ratings_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_ratings_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
