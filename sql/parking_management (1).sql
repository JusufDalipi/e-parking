-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2026 at 09:33 PM
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
-- Database: `parking_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `ip_address`, `login_time`, `success`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '::1', '2026-05-24 17:46:07', 1, 'active', '2026-05-24 15:46:07', '2026-05-24 15:46:07'),
(2, 1, '::1', '2026-05-24 19:15:19', 1, 'active', '2026-05-24 17:15:19', '2026-05-24 17:15:19'),
(3, 3, '::1', '2026-05-24 19:47:35', 1, 'active', '2026-05-24 17:47:35', '2026-05-24 17:47:35'),
(4, 1, '::1', '2026-05-24 20:01:37', 1, 'active', '2026-05-24 18:01:37', '2026-05-24 18:01:37'),
(5, 1, '::1', '2026-05-24 20:10:17', 1, 'active', '2026-05-24 18:10:17', '2026-05-24 18:10:17'),
(6, NULL, '::1', '2026-05-24 20:13:53', 0, 'active', '2026-05-24 18:13:53', '2026-05-24 18:13:53'),
(10, NULL, '::1', '2026-05-24 20:27:31', 0, 'active', '2026-05-24 18:27:31', '2026-05-24 18:27:31'),
(11, 1, '::1', '2026-05-24 20:27:40', 1, 'active', '2026-05-24 18:27:40', '2026-05-24 18:27:40'),
(12, NULL, '::1', '2026-05-24 20:37:55', 0, 'active', '2026-05-24 18:37:55', '2026-05-24 18:37:55'),
(13, NULL, '::1', '2026-05-24 20:38:08', 0, 'active', '2026-05-24 18:38:08', '2026-05-24 18:38:08'),
(14, NULL, '::1', '2026-05-24 20:38:21', 0, 'active', '2026-05-24 18:38:21', '2026-05-24 18:38:21'),
(15, 1, '::1', '2026-05-24 20:38:38', 1, 'active', '2026-05-24 18:38:38', '2026-05-24 18:38:38'),
(16, 8, '::1', '2026-05-24 20:39:10', 1, 'active', '2026-05-24 18:39:10', '2026-05-24 18:39:10'),
(17, 9, '::1', '2026-05-25 12:51:53', 1, 'active', '2026-05-25 10:51:53', '2026-05-25 10:51:53'),
(18, 3, '::1', '2026-05-25 12:53:03', 1, 'active', '2026-05-25 10:53:03', '2026-05-25 10:53:03'),
(19, 2, '::1', '2026-05-25 12:53:56', 1, 'active', '2026-05-25 10:53:56', '2026-05-25 10:53:56'),
(20, 1, '::1', '2026-05-25 12:55:41', 1, 'active', '2026-05-25 10:55:41', '2026-05-25 10:55:41'),
(21, 1, '::1', '2026-05-25 14:30:27', 1, 'active', '2026-05-25 12:30:27', '2026-05-25 12:30:27'),
(22, 9, '::1', '2026-05-25 14:30:55', 1, 'active', '2026-05-25 12:30:55', '2026-05-25 12:30:55'),
(23, 9, '::1', '2026-05-25 14:31:08', 1, 'active', '2026-05-25 12:31:08', '2026-05-25 12:31:08'),
(24, NULL, '::1', '2026-05-25 14:31:20', 0, 'active', '2026-05-25 12:31:20', '2026-05-25 12:31:20'),
(25, NULL, '::1', '2026-05-25 14:31:41', 0, 'active', '2026-05-25 12:31:41', '2026-05-25 12:31:41'),
(26, 1, '::1', '2026-05-25 14:32:31', 1, 'active', '2026-05-25 12:32:31', '2026-05-25 12:32:31'),
(27, 9, '::1', '2026-05-25 14:37:53', 1, 'active', '2026-05-25 12:37:53', '2026-05-25 12:37:53'),
(28, NULL, '::1', '2026-05-25 14:38:08', 0, 'active', '2026-05-25 12:38:08', '2026-05-25 12:38:08'),
(29, NULL, '::1', '2026-05-25 14:38:26', 0, 'active', '2026-05-25 12:38:26', '2026-05-25 12:38:26'),
(30, 9, '::1', '2026-05-25 14:38:37', 1, 'active', '2026-05-25 12:38:37', '2026-05-25 12:38:37'),
(31, 1, '::1', '2026-05-25 14:38:55', 1, 'active', '2026-05-25 12:38:55', '2026-05-25 12:38:55'),
(32, 9, '::1', '2026-05-25 14:44:59', 1, 'active', '2026-05-25 12:44:59', '2026-05-25 12:44:59'),
(33, 1, '::1', '2026-05-25 14:45:16', 1, 'active', '2026-05-25 12:45:16', '2026-05-25 12:45:16'),
(34, NULL, '::1', '2026-05-25 14:45:59', 0, 'active', '2026-05-25 12:45:59', '2026-05-25 12:45:59'),
(35, NULL, '::1', '2026-05-25 14:48:12', 0, 'active', '2026-05-25 12:48:12', '2026-05-25 12:48:12'),
(36, NULL, '::1', '2026-05-25 14:48:24', 0, 'active', '2026-05-25 12:48:24', '2026-05-25 12:48:24'),
(37, NULL, '::1', '2026-05-25 14:49:36', 0, 'active', '2026-05-25 12:49:36', '2026-05-25 12:49:36'),
(38, 1, '::1', '2026-05-25 14:49:40', 1, 'active', '2026-05-25 12:49:40', '2026-05-25 12:49:40'),
(39, 1, '::1', '2026-05-25 14:49:58', 1, 'active', '2026-05-25 12:49:58', '2026-05-25 12:49:58'),
(40, NULL, '::1', '2026-05-25 14:50:23', 0, 'active', '2026-05-25 12:50:23', '2026-05-25 12:50:23'),
(41, NULL, '::1', '2026-05-25 14:50:38', 0, 'active', '2026-05-25 12:50:38', '2026-05-25 12:50:38'),
(42, NULL, '::1', '2026-05-25 14:53:57', 0, 'active', '2026-05-25 12:53:57', '2026-05-25 12:53:57'),
(43, 1, '::1', '2026-05-25 14:54:03', 1, 'active', '2026-05-25 12:54:03', '2026-05-25 12:54:03'),
(44, 9, '::1', '2026-05-25 14:54:39', 1, 'active', '2026-05-25 12:54:39', '2026-05-25 12:54:39'),
(45, 9, '::1', '2026-05-25 14:55:16', 1, 'active', '2026-05-25 12:55:16', '2026-05-25 12:55:16'),
(46, 1, '::1', '2026-05-25 14:55:22', 1, 'active', '2026-05-25 12:55:22', '2026-05-25 12:55:22'),
(47, 8, '::1', '2026-05-25 14:55:52', 1, 'active', '2026-05-25 12:55:52', '2026-05-25 12:55:52'),
(48, 1, '::1', '2026-05-25 14:59:47', 1, 'active', '2026-05-25 12:59:47', '2026-05-25 12:59:47'),
(49, NULL, '::1', '2026-05-25 15:00:19', 0, 'active', '2026-05-25 13:00:19', '2026-05-25 13:00:19'),
(50, 1, '::1', '2026-05-25 15:03:35', 1, 'active', '2026-05-25 13:03:35', '2026-05-25 13:03:35'),
(51, 1, '::1', '2026-05-25 15:03:48', 1, 'active', '2026-05-25 13:03:48', '2026-05-25 13:03:48'),
(52, 9, '::1', '2026-05-25 15:04:19', 1, 'active', '2026-05-25 13:04:19', '2026-05-25 13:04:19'),
(53, 1, '::1', '2026-05-25 15:04:39', 1, 'active', '2026-05-25 13:04:39', '2026-05-25 13:04:39'),
(54, NULL, '::1', '2026-05-25 15:07:06', 0, 'active', '2026-05-25 13:07:06', '2026-05-25 13:07:06'),
(55, NULL, '::1', '2026-05-25 15:07:15', 0, 'active', '2026-05-25 13:07:15', '2026-05-25 13:07:15'),
(56, 9, '::1', '2026-05-25 15:07:24', 1, 'active', '2026-05-25 13:07:24', '2026-05-25 13:07:24'),
(57, 1, '::1', '2026-05-25 15:07:48', 1, 'active', '2026-05-25 13:07:48', '2026-05-25 13:07:48'),
(58, NULL, '::1', '2026-05-25 15:08:19', 0, 'active', '2026-05-25 13:08:19', '2026-05-25 13:08:19'),
(59, NULL, '::1', '2026-05-25 15:11:18', 0, 'active', '2026-05-25 13:11:18', '2026-05-25 13:11:18'),
(60, 1, '::1', '2026-05-25 15:15:14', 1, 'active', '2026-05-25 13:15:14', '2026-05-25 13:15:14'),
(61, 1, '::1', '2026-05-25 15:21:50', 1, 'active', '2026-05-25 13:21:50', '2026-05-25 13:21:50'),
(62, 9, '::1', '2026-05-25 15:32:28', 1, 'active', '2026-05-25 13:32:28', '2026-05-25 13:32:28'),
(63, 1, '::1', '2026-05-25 15:45:48', 1, 'active', '2026-05-25 13:45:48', '2026-05-25 13:45:48'),
(64, 1, '::1', '2026-05-25 17:26:22', 1, 'active', '2026-05-25 15:26:22', '2026-05-25 15:26:22'),
(65, 1, '::1', '2026-05-25 20:12:27', 1, 'active', '2026-05-25 18:12:27', '2026-05-25 18:12:27'),
(66, 1, '::1', '2026-05-26 00:31:49', 1, 'active', '2026-05-25 22:31:49', '2026-05-25 22:31:49'),
(67, NULL, '::1', '2026-05-26 01:10:34', 0, 'active', '2026-05-25 23:10:34', '2026-05-25 23:10:34'),
(68, 3, '::1', '2026-05-26 01:12:00', 1, 'active', '2026-05-25 23:12:00', '2026-05-25 23:12:00'),
(69, 1, '::1', '2026-05-26 01:13:36', 1, 'active', '2026-05-25 23:13:36', '2026-05-25 23:13:36'),
(70, 1, '::1', '2026-05-26 10:57:49', 1, 'active', '2026-05-26 08:57:49', '2026-05-26 08:57:49'),
(71, 1, '::1', '2026-05-26 18:07:34', 1, 'active', '2026-05-26 16:07:34', '2026-05-26 16:07:34'),
(72, 1, '::1', '2026-05-26 21:33:30', 1, 'active', '2026-05-26 19:33:30', '2026-05-26 19:33:30');

-- --------------------------------------------------------

--
-- Table structure for table `parking_slots`
--

CREATE TABLE `parking_slots` (
  `id` int(11) NOT NULL,
  `slot_code` varchar(30) NOT NULL,
  `floor_level` varchar(20) NOT NULL,
  `slot_type` varchar(30) NOT NULL,
  `is_occupied` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parking_slots`
--

INSERT INTO `parking_slots` (`id`, `slot_code`, `floor_level`, `slot_type`, `is_occupied`, `status`, `created_at`, `updated_at`) VALUES
(1, '12', '3', 'VIP', 0, 'inactive', '2026-05-24 15:32:40', '2026-05-24 17:37:37'),
(2, '13', '3', 'VIP', 0, 'inactive', '2026-05-24 15:32:40', '2026-05-24 17:37:37'),
(3, '14', '3', 'VIP', 0, 'inactive', '2026-05-24 15:32:40', '2026-05-24 17:37:37'),
(4, '15', '3', 'VIP', 0, 'inactive', '2026-05-24 15:32:40', '2026-05-24 17:37:37'),
(5, '16', '3', 'VIP', 0, 'inactive', '2026-05-24 15:32:40', '2026-05-24 17:37:37'),
(6, '17', '3', 'VIP', 0, 'inactive', '2026-05-24 15:32:40', '2026-05-24 17:37:37'),
(7, '18', '3', 'VIP', 0, 'inactive', '2026-05-24 15:32:40', '2026-05-24 17:37:37'),
(8, '19', '3', 'VIP', 0, 'inactive', '2026-05-24 15:32:40', '2026-05-24 17:37:37'),
(9, '20_del_1779637681_del_17796376', '1', 'standard', 0, 'deleted', '2026-05-24 15:32:40', '2026-05-24 15:48:01'),
(10, '5-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 17:40:27'),
(11, '6-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(12, '7-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(13, '8-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(14, '9-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(15, '10-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(16, '11-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(17, '12-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(18, '13-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(19, '14-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(20, '15-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(21, '16-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(22, '17-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(23, '18-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(24, '19-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(25, '20-a', '2', 'standard', 0, 'active', '2026-05-24 15:32:40', '2026-05-24 15:32:40'),
(26, '1', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-25 10:58:14'),
(27, '2', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(28, '3', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(29, '4', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(30, '5', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(31, '6', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(32, '7', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(33, '8', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(34, '9', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(35, '10', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(36, '11', '3', 'VIP', 0, 'inactive', '2026-05-24 15:34:58', '2026-05-24 17:37:37'),
(37, '1-a', '2', 'standard', 0, 'active', '2026-05-24 15:34:58', '2026-05-24 17:40:27'),
(38, '2-a', '2', 'standard', 0, 'active', '2026-05-24 15:34:58', '2026-05-24 17:40:27'),
(39, '3-a', '2', 'standard', 0, 'active', '2026-05-24 15:34:58', '2026-05-24 17:40:27'),
(40, '4-a', '2', 'standard', 0, 'active', '2026-05-24 15:34:58', '2026-05-24 17:40:27'),
(41, '21_del_1779637240_del_17796372', '1', 'Standard', 0, 'deleted', '2026-05-24 15:40:20', '2026-05-24 15:40:40'),
(42, '22_del_1779637391', '1', 'Standard', 0, 'deleted', '2026-05-24 15:40:49', '2026-05-24 15:43:11'),
(43, '21_del_1779637574_del_17796375', '1', 'Standard', 0, 'deleted', '2026-05-24 15:43:49', '2026-05-24 15:46:14'),
(44, '22_del_1779637500_del_17796375', '1', 'Standard', 0, 'deleted', '2026-05-24 15:44:07', '2026-05-24 15:45:00'),
(45, '23_del_1779637483_del_17796374', '1', 'Standard', 0, 'deleted', '2026-05-24 15:44:18', '2026-05-24 15:44:43'),
(46, '24_del_1779637476_del_17796374', '1', 'Standard', 0, 'deleted', '2026-05-24 15:44:31', '2026-05-24 15:44:36'),
(47, '20', '3', 'VIP', 0, 'inactive', '2026-05-24 15:48:10', '2026-05-24 17:37:37'),
(48, '21_del_1779644444', '1', 'Standard', 0, 'deleted', '2026-05-24 15:52:34', '2026-05-24 17:40:44'),
(50, '1', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(51, '2', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(52, '3', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(53, '4', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(54, '5', '1', 'VIP', 0, 'active', '2026-05-24 17:40:27', '2026-05-25 15:38:31'),
(55, '6', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(56, '7', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(57, '8', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(58, '9', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(59, '10', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(60, '11', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(61, '12', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(62, '13', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(63, '14', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(64, '15', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(65, '16', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(66, '17', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(67, '18', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(68, '19', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(69, '20', '1', 'standard', 0, 'active', '2026-05-24 17:40:27', '2026-05-24 17:40:27'),
(70, '1_del_1779644572', '4', 'standard', 0, 'deleted', '2026-05-24 17:42:22', '2026-05-24 17:42:52'),
(71, '2_del_1779644576', '122', 'VIP', 0, 'deleted', '2026-05-24 17:42:33', '2026-05-24 17:42:56');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(120) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `status`, `created_at`) VALUES
(1, 'kushtrim@parking.local', 'resolved', '2026-05-25 13:03:32'),
(2, 'kushtrim@parking.local', 'resolved', '2026-05-25 13:07:44');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(30) NOT NULL,
  `paid_at` datetime NOT NULL,
  `reference_no` varchar(60) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `reservation_id`, `entry_id`, `user_id`, `amount`, `payment_method`, `paid_at`, `reference_no`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 8, 1.50, 'cash', '2026-05-25 12:55:28', NULL, 'active', '2026-05-25 10:55:28', '2026-05-25 10:55:28'),
(2, NULL, 2, 9, 2.50, 'cash', '2026-05-25 13:07:10', NULL, 'active', '2026-05-25 11:07:10', '2026-05-25 11:07:10'),
(3, NULL, 3, 8, 3.50, 'cash', '2026-05-25 13:07:36', NULL, 'active', '2026-05-25 11:07:36', '2026-05-25 11:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `vehicle_plate` varchar(20) NOT NULL,
  `reserved_from` datetime NOT NULL,
  `reserved_to` datetime NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `slot_id`, `vehicle_plate`, `reserved_from`, `reserved_to`, `status`, `created_at`, `updated_at`) VALUES
(1, 9, 37, 'thfghf', '2026-05-25 12:52:00', '2026-05-27 12:52:00', 'active', '2026-05-25 10:52:38', '2026-05-25 10:52:38');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'active', '2026-05-24 15:29:34', '2026-05-24 15:29:34'),
(2, 'Roje', 'active', '2026-05-24 15:29:34', '2026-05-24 15:29:34'),
(3, 'Shofer', 'active', '2026-05-24 15:29:34', '2026-05-24 15:29:34');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_name` varchar(80) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `plan_name`, `start_date`, `end_date`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'Mujore', '2026-05-01', '2026-06-01', 200.00, 'active', '2026-05-24 17:20:39', '2026-05-24 17:20:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@parking.local', '$2y$10$dXRp0RhzPoT.CYmfD5D8qORLiRxuvLE6iSbVKvToGen9h07Ybm9IK', 'active', '2026-05-24 15:29:34', '2026-05-25 13:25:14'),
(2, 'Rojtari', 'roje@parking.local', '$2y$10$TIN2rruYgPYapgT4cxUGdOJ/etPz1gx3O9RslOniwk4Ov34XYfF6y', 'active', '2026-05-24 15:29:34', '2026-05-24 18:28:14'),
(3, 'Shofer', 'shofer@parking.local', '$2y$10$ygVlXdwIBZed96Yx0wf/z.Vnsc6nM4XcLPLNRgwaViRhwHQ6wEK/6', 'active', '2026-05-24 15:29:34', '2026-05-24 18:28:07'),
(8, 'Jusuf Dalipi', 'jusuf@parking.local', '$2y$10$UNEBFqQBi/WY0fRHxGeIFOwrR80R/TNAphkRmR/e8P9Q81oEsnzvq', 'active', '2026-05-24 18:37:32', '2026-05-25 12:55:31'),
(9, 'Kushtrim', 'kushtrim@parking.local', '$2y$10$wcZG/BpaelbcLNemZdaE8.dVO9LoE9hbIuRgYBA.dx0gGO6IcD2Uy', 'active', '2026-05-25 10:51:31', '2026-05-25 13:07:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'active', '2026-05-24 15:29:34', '2026-05-24 18:28:27'),
(2, 2, 2, 'active', '2026-05-24 15:29:34', '2026-05-24 18:28:14'),
(3, 3, 3, 'active', '2026-05-24 15:29:34', '2026-05-24 18:28:07'),
(11, 8, 3, 'active', '2026-05-24 18:37:32', '2026-05-24 18:38:54'),
(14, 9, 3, 'active', '2026-05-25 10:51:31', '2026-05-25 10:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_entries`
--

CREATE TABLE `vehicle_entries` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `vehicle_plate` varchar(20) NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_entries`
--

INSERT INTO `vehicle_entries` (`id`, `reservation_id`, `user_id`, `slot_id`, `vehicle_plate`, `entry_time`, `exit_time`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 8, 50, 'hthdfyugbkhvj', '2026-05-25 12:54:00', '2026-05-25 13:58:00', 'active', '2026-05-25 10:55:07', '2026-05-25 10:55:28'),
(2, NULL, 9, 17, '06', '2026-05-25 13:06:00', '2026-05-25 15:07:00', 'active', '2026-05-25 11:07:10', '2026-05-25 11:07:10'),
(3, NULL, 8, 15, '06', '2026-05-25 13:07:00', '2026-05-25 16:10:00', 'active', '2026-05-25 11:07:35', '2026-05-25 11:07:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_login_logs_user` (`user_id`);

--
-- Indexes for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slot_code_floor` (`slot_code`,`floor_level`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_reservation` (`reservation_id`),
  ADD KEY `fk_payments_entry` (`entry_id`),
  ADD KEY `fk_payments_user` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservations_user` (`user_id`),
  ADD KEY `fk_reservations_slot` (`slot_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subscriptions_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_role` (`user_id`,`role_id`),
  ADD KEY `fk_user_roles_role` (`role_id`);

--
-- Indexes for table `vehicle_entries`
--
ALTER TABLE `vehicle_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicle_entries_reservation` (`reservation_id`),
  ADD KEY `fk_vehicle_entries_user` (`user_id`),
  ADD KEY `fk_vehicle_entries_slot` (`slot_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `parking_slots`
--
ALTER TABLE `parking_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vehicle_entries`
--
ALTER TABLE `vehicle_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `fk_login_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_entry` FOREIGN KEY (`entry_id`) REFERENCES `vehicle_entries` (`id`),
  ADD CONSTRAINT `fk_payments_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`),
  ADD CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_slot` FOREIGN KEY (`slot_id`) REFERENCES `parking_slots` (`id`),
  ADD CONSTRAINT `fk_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_subscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `vehicle_entries`
--
ALTER TABLE `vehicle_entries`
  ADD CONSTRAINT `fk_vehicle_entries_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`),
  ADD CONSTRAINT `fk_vehicle_entries_slot` FOREIGN KEY (`slot_id`) REFERENCES `parking_slots` (`id`),
  ADD CONSTRAINT `fk_vehicle_entries_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
