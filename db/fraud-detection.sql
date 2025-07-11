-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2025 at 12:23 AM
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
-- Database: `fraud-detection`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(125) NOT NULL,
  `role` varchar(15) NOT NULL,
  `operation` text DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `role`, `operation`, `ip_address`, `created_at`) VALUES
(4020, '4', 'customer', 'order a product on : 2025-07-07 18:56:34', '::1', '2025-07-07 19:56:34'),
(4021, '4', 'customer', 'order a product on : 2025-07-07 19:00:06', '::1', '2025-07-07 20:00:06'),
(4022, '4', 'customer', 'order a product on : 2025-07-07 19:14:10', '::1', '2025-07-07 20:14:10'),
(4023, '4', 'customer', 'order a product on : 2025-07-07 19:44:26', '::1', '2025-07-07 20:44:26'),
(4024, '5', 'Admin', 'logged in as Admin on: 2025-07-10 10:56:14', '::1', '2025-07-10 11:56:14'),
(4025, '5', 'Admin', 'logged in as Admin on: 2025-07-10 10:56:45', '::1', '2025-07-10 11:56:45'),
(4026, '5', 'Admin', 'logged in as Admin on: 2025-07-10 12:52:12', '::1', '2025-07-10 13:52:12'),
(4027, '5', 'Admin', 'logged in as Admin on: 2025-07-10 12:55:00', '::1', '2025-07-10 13:55:00'),
(4028, '5', 'Admin', 'Black-list user on 2025-07-10 13:12:49', '::1', '2025-07-10 14:12:49'),
(4029, '5', 'Remove user fro', NULL, '::1', '2025-07-10 14:21:28'),
(4030, '5', 'Admin', 'Black-List user on 2025-07-10 13:22:09', '::1', '2025-07-10 14:22:09'),
(4031, '5', 'Remove user fro', NULL, '::1', '2025-07-10 14:22:16'),
(4032, '4', 'customer', 'logged in as customer on: 2025-07-10 22:13:55', '::1', '2025-07-10 23:13:55'),
(4033, '5', 'Admin', 'logged in as Admin on: 2025-07-11 09:08:23', '::1', '2025-07-11 10:08:23'),
(4034, '4', 'customer', 'logged in as customer on: 2025-07-11 11:33:29', '::1', '2025-07-11 12:33:29'),
(4035, '4', 'customer', 'order a product on : 2025-07-11 12:20:00', '::1', '2025-07-11 13:20:00'),
(4036, '4', 'customer', 'order a product on : 2025-07-11 12:21:31', '::1', '2025-07-11 13:21:31'),
(4037, '4', 'customer', 'order a product on : 2025-07-11 12:26:54', '::1', '2025-07-11 13:26:54'),
(4038, '4', 'customer', 'order a product on : 2025-07-11 12:51:37', '::1', '2025-07-11 13:51:37'),
(4039, '4', 'customer', 'order a product on : 2025-07-11 12:53:44', '::1', '2025-07-11 13:53:44'),
(4040, '4', 'customer', 'order a product on : 2025-07-11 13:17:26', '::1', '2025-07-11 14:17:26'),
(4041, '4', 'customer', 'order a product on : 2025-07-11 13:17:47', '::1', '2025-07-11 14:17:47'),
(4042, '4', 'customer', 'order a product on : 2025-07-11 13:17:59', '::1', '2025-07-11 14:17:59'),
(4043, '4', 'customer', 'order a product on : 2025-07-11 13:31:15', '::1', '2025-07-11 14:31:15'),
(4044, '4', 'customer', 'order a product on : 2025-07-11 13:32:08', '::1', '2025-07-11 14:32:08'),
(4045, '4', 'customer', 'order a product on : 2025-07-11 13:32:25', '::1', '2025-07-11 14:32:25'),
(4046, '4', 'customer', 'order a product on : 2025-07-11 13:32:39', '::1', '2025-07-11 14:32:39'),
(4047, '4', 'customer', 'order a product on : 2025-07-11 13:32:56', '::1', '2025-07-11 14:32:56'),
(4048, '4', 'customer', 'order a product on : 2025-07-11 13:35:05', '::1', '2025-07-11 14:35:05'),
(4049, '4', 'customer', 'order a product on : 2025-07-11 13:35:20', '::1', '2025-07-11 14:35:20'),
(4050, '4', 'customer', 'order a product on : 2025-07-11 13:35:58', '::1', '2025-07-11 14:35:58'),
(4051, '4', 'customer', 'order a product on : 2025-07-11 14:00:13', '::1', '2025-07-11 15:00:13'),
(4052, '4', 'customer', 'order a product on : 2025-07-11 20:27:19', '::1', '2025-07-11 21:27:19'),
(4053, '4', 'customer', 'order a product on : 2025-07-11 20:28:49', '::1', '2025-07-11 21:28:49'),
(4054, '4', 'customer', 'order a product on : 2025-07-11 20:33:47', '::1', '2025-07-11 21:33:47'),
(4055, '4', 'customer', 'order a product on : 2025-07-11 20:37:34', '::1', '2025-07-11 21:37:34'),
(4056, '4', 'customer', 'order a product on : 2025-07-11 20:38:11', '::1', '2025-07-11 21:38:11'),
(4057, '4', 'customer', 'order a product on : 2025-07-11 20:49:44', '::1', '2025-07-11 21:49:44'),
(4058, '4', 'customer', 'order a product on : 2025-07-11 20:50:09', '::1', '2025-07-11 21:50:09'),
(4059, '4', 'customer', 'order a product on : 2025-07-11 21:01:23', '::1', '2025-07-11 22:01:23'),
(4060, '4', 'customer', 'order a product on : 2025-07-11 21:15:37', '::1', '2025-07-11 22:15:37'),
(4061, '4', 'customer', 'order a product on : 2025-07-11 21:16:01', '::1', '2025-07-11 22:16:01'),
(4062, '4', 'customer', 'order a product on : 2025-07-11 21:16:12', '::1', '2025-07-11 22:16:12'),
(4063, '4', 'customer', 'order a product on : 2025-07-11 21:16:25', '::1', '2025-07-11 22:16:25'),
(4064, '4', 'customer', 'order a product on : 2025-07-11 21:18:53', '::1', '2025-07-11 22:18:53'),
(4065, '4', 'customer', 'order a product on : 2025-07-11 21:44:10', '::1', '2025-07-11 22:44:10'),
(4066, '4', 'customer', 'order a product on : 2025-07-11 21:45:23', '::1', '2025-07-11 22:45:23'),
(4067, '5', 'Admin', 'logged in as Admin on: 2025-07-11 22:06:24', '::1', '2025-07-11 23:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(50) DEFAULT 'shipping',
  `line1` varchar(255) NOT NULL,
  `city` varchar(80) NOT NULL,
  `state` varchar(80) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `label`, `line1`, `city`, `state`, `postal_code`, `country`, `created_at`) VALUES
(14, 4, 'shipping', '1b Ikono Rd, Ikot Ekpene, Uyo Rd, Ikot Ekpene', '1b Ikono Rd, Ikot Ekpene, Uyo Rd, Ikot Ekpene', '1b Ikono Rd, Ikot Ekpene, Uyo Rd, Ikot Ekpene', '', 'Nigeria', '2025-07-11 20:27:14');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('open','converted','abandoned') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 4, 'converted', '2025-07-07 18:43:04', '2025-07-07 18:43:04'),
(3, 4, 'converted', '2025-07-07 18:52:47', '2025-07-07 18:52:47'),
(4, 4, 'converted', '2025-07-07 18:56:34', '2025-07-07 18:56:34'),
(5, 4, 'converted', '2025-07-07 19:00:06', '2025-07-07 19:00:06'),
(6, 4, 'converted', '2025-07-07 19:14:10', '2025-07-07 19:14:10'),
(7, 4, 'converted', '2025-07-07 19:44:26', '2025-07-07 19:44:26'),
(8, 4, 'converted', '2025-07-11 12:19:54', '2025-07-11 12:19:54'),
(9, 4, 'converted', '2025-07-11 12:21:24', '2025-07-11 12:21:24'),
(10, 4, 'converted', '2025-07-11 12:26:47', '2025-07-11 12:26:47'),
(11, 4, 'converted', '2025-07-11 12:51:33', '2025-07-11 12:51:33'),
(12, 4, 'converted', '2025-07-11 12:53:40', '2025-07-11 12:53:40'),
(13, 4, 'converted', '2025-07-11 13:17:22', '2025-07-11 13:17:22'),
(14, 4, 'converted', '2025-07-11 13:17:44', '2025-07-11 13:17:44'),
(15, 4, 'converted', '2025-07-11 13:17:56', '2025-07-11 13:17:56'),
(16, 4, 'converted', '2025-07-11 13:31:11', '2025-07-11 13:31:11'),
(17, 4, 'converted', '2025-07-11 13:32:05', '2025-07-11 13:32:05'),
(18, 4, 'converted', '2025-07-11 13:32:22', '2025-07-11 13:32:22'),
(19, 4, 'converted', '2025-07-11 13:32:36', '2025-07-11 13:32:36'),
(20, 4, 'converted', '2025-07-11 13:32:53', '2025-07-11 13:32:53'),
(21, 4, 'converted', '2025-07-11 13:35:01', '2025-07-11 13:35:01'),
(22, 4, 'converted', '2025-07-11 13:35:16', '2025-07-11 13:35:16'),
(23, 4, 'converted', '2025-07-11 13:35:53', '2025-07-11 13:35:53'),
(24, 4, 'converted', '2025-07-11 14:00:09', '2025-07-11 14:00:09'),
(26, 4, 'converted', '2025-07-11 20:27:14', '2025-07-11 20:27:14'),
(27, 4, 'converted', '2025-07-11 20:28:45', '2025-07-11 20:28:45'),
(28, 4, 'converted', '2025-07-11 20:33:43', '2025-07-11 20:33:43'),
(29, 4, 'converted', '2025-07-11 20:37:28', '2025-07-11 20:37:28'),
(30, 4, 'converted', '2025-07-11 20:38:05', '2025-07-11 20:38:05'),
(31, 4, 'converted', '2025-07-11 20:49:41', '2025-07-11 20:49:41'),
(32, 4, 'converted', '2025-07-11 20:50:05', '2025-07-11 20:50:05'),
(33, 4, 'converted', '2025-07-11 21:01:19', '2025-07-11 21:01:19'),
(35, 4, 'converted', '2025-07-11 21:15:33', '2025-07-11 21:15:33'),
(36, 4, 'converted', '2025-07-11 21:15:57', '2025-07-11 21:15:57'),
(37, 4, 'converted', '2025-07-11 21:16:08', '2025-07-11 21:16:08'),
(38, 4, 'converted', '2025-07-11 21:16:20', '2025-07-11 21:16:20'),
(40, 4, 'converted', '2025-07-11 21:18:50', '2025-07-11 21:18:50'),
(47, 4, 'converted', '2025-07-11 21:44:07', '2025-07-11 21:44:07'),
(48, 4, 'converted', '2025-07-11 21:45:20', '2025-07-11 21:45:20'),
(49, 4, 'converted', '2025-07-11 21:53:33', '2025-07-11 21:53:33'),
(50, 4, 'converted', '2025-07-11 22:01:06', '2025-07-11 22:01:06'),
(51, 4, 'converted', '2025-07-11 22:02:02', '2025-07-11 22:02:02');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `qty` smallint(5) UNSIGNED NOT NULL,
  `price_each` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `qty`, `price_each`) VALUES
(1, 2, 3, 1, 250),
(2, 2, 2, 1, 5300),
(3, 2, 1, 1, 200000),
(4, 3, 1, 1, 200000),
(5, 3, 2, 1, 5300),
(6, 4, 1, 1, 200000),
(7, 4, 2, 1, 5300),
(8, 5, 3, 1, 250),
(9, 5, 5, 1, 340000),
(10, 6, 1, 1, 200000),
(11, 6, 2, 1, 5300),
(12, 6, 3, 1, 250),
(13, 7, 3, 2, 250),
(14, 7, 5, 2, 340000),
(15, 8, 3, 1, 250),
(16, 8, 5, 1, 340000),
(17, 8, 2, 1, 5300),
(18, 9, 5, 2, 340000),
(19, 10, 1, 1, 200000),
(20, 11, 1, 1, 200000),
(21, 11, 5, 1, 340000),
(22, 11, 9, 1, 100000),
(23, 12, 3, 1, 250),
(24, 12, 2, 1, 5300),
(25, 13, 3, 1, 250),
(26, 13, 2, 1, 5300),
(27, 14, 3, 1, 250),
(28, 14, 2, 1, 5300),
(29, 15, 3, 1, 250),
(30, 15, 2, 1, 5300),
(31, 16, 10, 1, 19000),
(32, 16, 9, 1, 100000),
(33, 16, 5, 1, 340000),
(34, 16, 1, 1, 200000),
(35, 17, 10, 1, 19000),
(36, 17, 9, 1, 100000),
(37, 17, 5, 1, 340000),
(38, 17, 1, 1, 200000),
(39, 18, 10, 1, 19000),
(40, 18, 9, 1, 100000),
(41, 18, 5, 1, 340000),
(42, 18, 1, 1, 200000),
(43, 19, 10, 1, 19000),
(44, 19, 9, 1, 100000),
(45, 19, 5, 1, 340000),
(46, 19, 1, 1, 200000),
(47, 20, 10, 1, 19000),
(48, 20, 9, 1, 100000),
(49, 20, 5, 1, 340000),
(50, 20, 1, 1, 200000),
(51, 21, 10, 1, 19000),
(52, 21, 9, 1, 100000),
(53, 21, 5, 1, 340000),
(54, 21, 1, 1, 200000),
(55, 22, 10, 1, 19000),
(56, 22, 9, 1, 100000),
(57, 22, 5, 1, 340000),
(58, 22, 1, 1, 200000),
(59, 23, 5, 1, 340000),
(60, 24, 2, 1, 5300),
(61, 24, 5, 1, 340000),
(63, 26, 5, 2, 340000),
(64, 27, 5, 2, 340000),
(65, 28, 5, 2, 340000),
(66, 29, 5, 2, 340000),
(67, 30, 5, 2, 340000),
(68, 31, 3, 1, 250),
(69, 31, 5, 1, 340000),
(70, 32, 3, 1, 250),
(71, 32, 5, 1, 340000),
(72, 33, 3, 1, 250),
(73, 33, 5, 1, 340000),
(76, 35, 5, 1, 340000),
(77, 35, 3, 1, 250),
(78, 36, 5, 1, 340000),
(79, 36, 3, 1, 250),
(80, 37, 5, 1, 340000),
(81, 37, 3, 1, 250),
(82, 38, 5, 1, 340000),
(83, 38, 3, 1, 250),
(85, 40, 5, 1, 340000),
(92, 47, 5, 2, 340000),
(93, 48, 5, 2, 340000),
(94, 49, 3, 1, 250),
(95, 49, 5, 1, 340000),
(96, 50, 5, 1, 340000),
(97, 50, 8, 1, 34000),
(98, 51, 5, 1, 340000),
(99, 51, 13, 2, 1000000);

-- --------------------------------------------------------

--
-- Table structure for table `chargebacks`
--

CREATE TABLE `chargebacks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `reason_code` varchar(40) DEFAULT NULL,
  `status` enum('received','won','lost','pending') DEFAULT 'received',
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_fingerprints`
--

CREATE TABLE `device_fingerprints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `device_hash` char(64) NOT NULL,
  `ip_address` varbinary(16) DEFAULT NULL,
  `first_seen` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fraud_cases`
--

CREATE TABLE `fraud_cases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scope` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `risk_score` int(11) NOT NULL,
  `status` enum('open','investigating','confirmed_fraud','cleared') DEFAULT 'open',
  `analyst_notes` text DEFAULT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fraud_cases`
--

INSERT INTO `fraud_cases` (`id`, `scope`, `user_id`, `risk_score`, `status`, `analyst_notes`, `opened_at`, `closed_at`) VALUES
(4, 'login', 4, 100, 'open', NULL, '2025-07-11 09:05:47', NULL),
(24, 'checkout', 4, 54, 'open', NULL, '2025-07-11 21:53:36', NULL),
(25, 'checkout', 4, 54, 'open', NULL, '2025-07-11 22:01:09', NULL),
(26, 'checkout', 4, 74, 'open', NULL, '2025-07-11 22:02:06', NULL),
(27, 'login', 4, 130, 'open', NULL, '2025-07-11 22:05:13', NULL),
(28, 'login', 4, 100, 'open', NULL, '2025-07-11 22:07:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fraud_rules`
--

CREATE TABLE `fraud_rules` (
  `id` int(10) UNSIGNED NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `scope` enum('checkout','login','account') NOT NULL,
  `expression` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`expression`)),
  `signal_type` varchar(60) NOT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `weight` smallint(6) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fraud_rules`
--

INSERT INTO `fraud_rules` (`id`, `rule_name`, `scope`, `expression`, `signal_type`, `default_value`, `weight`, `is_active`, `updated_at`) VALUES
(1, 'Order > ₦500,000', 'checkout', '{\"and\":[{\">\":[{\"var\":\"cart.total_cents\"},50000000]}]}', 'high_ticket', NULL, 10, 1, '2025-07-11 11:51:51'),
(2, 'In correct Password', 'login', '{\"!=\":[{\"var\":\"ip.country_iso2\"},{\"var\":\"billing.country_iso2\"}]}', 'bad_password', NULL, 30, 1, '2025-07-11 09:14:47'),
(3, 'User Not Found', 'login', '{\"==\":[{\"var\":\"user.first_time_device\"},true]}', 'Unknown_user_role', NULL, 15, 1, '2025-07-11 09:14:58'),
(4, 'In active User', 'login', '{\"and\":[{\">\":[{\"var\":\"user.shared_address_count\"},5]}]}', 'no_user_or_inactive', NULL, 10, 1, '2025-07-11 09:15:21'),
(5, 'Frequent login Failures under 1 minutes', 'login', '{\"and\":[{\">\":[{\"var\":\"card.reuse_count\"},2]}]}', 'frequent_login_failures', NULL, 40, 1, '2025-07-11 11:53:42'),
(6, 'Ip address   ≠ Country', 'checkout', '{\"!=\":[{\"var\":\"card.bin_country_iso2\"},{\"var\":\"billing.country_iso2\"}]}', 'ip_mismatch', NULL, 20, 1, '2025-07-11 11:53:16'),
(7, 'Shipping to risky country', 'checkout', '{\"in\":[{\"var\":\"shipping.country_iso2\"}, [\"NG\",\"RU\",\"PK\",\"ID\"]]}', 'high_risk_country', NULL, 10, 1, '2025-07-11 08:47:51'),
(10, 'Frequent orders under 1 minutes', 'checkout', '3433434', 'frequent_checkout', NULL, 20, 1, '2025-07-11 21:05:16');

-- --------------------------------------------------------

--
-- Table structure for table `ip_blacklist`
--

CREATE TABLE `ip_blacklist` (
  `ip_address` varbinary(16) NOT NULL,
  `reason` varchar(120) DEFAULT NULL,
  `banned_until` timestamp NULL DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email_submitted` varchar(255) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `failure_reason` varchar(60) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `user_id`, `email_submitted`, `user_agent`, `ip_address`, `success`, `failure_reason`, `created_at`) VALUES
(38, 4, 'newleastpaysolution@gmail.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 0, 'bad_password', '2025-07-11 22:05:13'),
(39, 5, 'newleastpaysolution@yahoo.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 1, NULL, '2025-07-11 22:06:24'),
(40, 4, 'newleastpaysolution@gmail.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 0, 'bad_password', '2025-07-11 22:07:16'),
(41, 4, 'newleastpaysolution@gmail.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 0, 'frequent_login_failures', '2025-07-11 22:07:43');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `address_id` bigint(20) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','paid','cancelled','shipped','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `cart_id`, `user_id`, `address_id`, `total`, `status`, `created_at`) VALUES
(24, 26, 4, 14, 680000, 'paid', '2025-07-11 20:27:14'),
(25, 27, 4, 14, 680000, 'paid', '2025-07-11 20:28:45'),
(26, 28, 4, 14, 680000, 'paid', '2025-07-11 20:33:43'),
(27, 29, 4, 14, 680000, 'paid', '2025-07-11 20:37:28'),
(28, 30, 4, 14, 680000, 'paid', '2025-07-11 20:38:05'),
(29, 31, 4, 14, 340250, 'paid', '2025-07-11 20:49:41'),
(30, 32, 4, 14, 340250, 'paid', '2025-07-11 20:50:05'),
(31, 33, 4, 14, 340250, 'paid', '2025-07-11 21:01:19'),
(33, 35, 4, 14, 340250, 'paid', '2025-07-11 21:15:33'),
(34, 36, 4, 14, 340250, 'paid', '2025-07-11 21:15:57'),
(35, 37, 4, 14, 340250, 'paid', '2025-07-11 21:16:08'),
(36, 38, 4, 14, 340250, 'paid', '2025-07-11 21:16:20'),
(38, 40, 4, 14, 340000, 'paid', '2025-07-11 21:18:50'),
(45, 47, 4, 14, 680000, 'paid', '2025-07-11 21:44:07'),
(46, 48, 4, 14, 680000, 'paid', '2025-07-11 21:45:20'),
(47, 49, 4, 14, 340250, 'paid', '2025-07-11 21:53:33'),
(48, 50, 4, 14, 374000, 'paid', '2025-07-11 22:01:06'),
(49, 51, 4, 14, 2340000, 'paid', '2025-07-11 22:02:02');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(40) NOT NULL,
  `provider_txn_id` varchar(100) NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `status` enum('initiated','authorized','captured','failed','refunded') DEFAULT 'initiated',
  `card_last4` char(4) DEFAULT NULL,
  `card_brand` varchar(20) DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `provider`, `provider_txn_id`, `amount`, `status`, `card_last4`, `card_brand`, `processed_at`) VALUES
(1, 1, 'demo', 'demo_686c1538b8c015.45350852', 205550, 'captured', '3443', 'VISA', '2025-07-07 18:43:04'),
(2, 2, 'Demo', 'demo_686c177fa02148.99241733', 205300, 'captured', '3232', 'VISA', '2025-07-07 18:52:47'),
(3, 3, 'Demo', 'demo_686c1862c17ef4.90307465', 205300, 'captured', '3232', 'VISA', '2025-07-07 18:56:34'),
(4, 4, 'Demo', 'demo_686c19367421a7.25926757', 340250, 'captured', '3567', 'VISA', '2025-07-07 19:00:06'),
(5, 5, 'Demo', 'demo_686c1c82a9c0d7.18335292', 205550, 'captured', '6777', 'VISA', '2025-07-07 19:14:10'),
(6, 6, 'Demo', 'demo_686c239aa9eba3.26980876', 680500, 'captured', '7666', 'VISA', '2025-07-07 19:44:26'),
(7, 7, 'Demo', 'demo_6871017066d863.63612704', 345550, 'captured', '2121', 'VISA', '2025-07-11 12:20:00'),
(8, 8, 'Demo', 'demo_687101cbd24e03.15899884', 680000, 'captured', '4242', 'VISA', '2025-07-11 12:21:31'),
(9, 9, 'Demo', 'demo_6871030e1ceb61.28837007', 200000, 'captured', '2323', 'VISA', '2025-07-11 12:26:54'),
(10, 10, 'Demo', 'demo_687108d9367b30.07880966', 640000, 'captured', '3232', 'VISA', '2025-07-11 12:51:37'),
(11, 11, 'Demo', 'demo_687109583b7464.56743708', 5550, 'captured', '8786', 'VISA', '2025-07-11 12:53:44'),
(12, 12, 'Demo', 'demo_68710ee6909206.77216633', 5550, 'captured', '8786', 'VISA', '2025-07-11 13:17:26'),
(13, 13, 'Demo', 'demo_68710efb779a79.23574183', 5550, 'captured', '8786', 'VISA', '2025-07-11 13:17:47'),
(14, 14, 'Demo', 'demo_68710f075d5031.36872889', 5550, 'captured', '8786', 'VISA', '2025-07-11 13:17:59'),
(15, 15, 'Demo', 'demo_68711223bf3df2.97609719', 659000, 'captured', '7654', 'VISA', '2025-07-11 13:31:15'),
(16, 16, 'Demo', 'demo_6871125858a8c1.90596086', 659000, 'captured', '7654', 'VISA', '2025-07-11 13:32:08'),
(17, 17, 'Demo', 'demo_6871126953dc83.31130632', 659000, 'captured', '7654', 'VISA', '2025-07-11 13:32:25'),
(18, 18, 'Demo', 'demo_68711277a98001.04658556', 659000, 'captured', '7654', 'VISA', '2025-07-11 13:32:39'),
(19, 19, 'Demo', 'demo_6871128879cf37.68816469', 659000, 'captured', '7654', 'VISA', '2025-07-11 13:32:56'),
(20, 20, 'Demo', 'demo_6871130963b2b2.90203015', 659000, 'captured', '7654', 'VISA', '2025-07-11 13:35:05'),
(21, 21, 'Demo', 'demo_687113186eb986.62232385', 659000, 'captured', '7654', 'VISA', '2025-07-11 13:35:20'),
(22, 22, 'Demo', 'demo_6871133e1042a3.07698493', 340000, 'captured', '2334', 'VISA', '2025-07-11 13:35:58'),
(23, 23, 'Demo', 'demo_687118ed5609d0.11977433', 345300, 'captured', '5447', 'VISA', '2025-07-11 14:00:13'),
(24, 24, 'Demo', 'demo_687173a743d689.31225843', 680000, 'captured', '6756', 'VISA', '2025-07-11 20:27:19'),
(25, 25, 'Demo', 'demo_68717401aabf36.54132671', 680000, 'captured', '6756', 'VISA', '2025-07-11 20:28:49'),
(26, 26, 'Demo', 'demo_6871752bdd0284.46869728', 680000, 'captured', '6756', 'VISA', '2025-07-11 20:33:47'),
(27, 27, 'Demo', 'demo_6871760ef06f00.64057958', 680000, 'captured', '6756', 'VISA', '2025-07-11 20:37:34'),
(28, 28, 'Demo', 'demo_68717633324ac3.78130849', 680000, 'captured', '6756', 'VISA', '2025-07-11 20:38:11'),
(29, 29, 'Demo', 'demo_687178e86d48a5.59003171', 340250, 'captured', '3333', 'VISA', '2025-07-11 20:49:44'),
(30, 30, 'Demo', 'demo_687179011c95c2.79591881', 340250, 'captured', '3333', 'VISA', '2025-07-11 20:50:09'),
(31, 31, 'Demo', 'demo_68717ba338b652.36480435', 340250, 'captured', '3333', 'VISA', '2025-07-11 21:01:23'),
(32, 33, 'Demo', 'demo_68717ef90af944.36860066', 340250, 'captured', '7654', 'VISA', '2025-07-11 21:15:37'),
(33, 34, 'Demo', 'demo_68717f111d8084.39739172', 340250, 'captured', '7654', 'VISA', '2025-07-11 21:16:01'),
(34, 35, 'Demo', 'demo_68717f1c2b0026.91891667', 340250, 'captured', '7654', 'VISA', '2025-07-11 21:16:12'),
(35, 36, 'Demo', 'demo_68717f2911cfc2.51039731', 340250, 'captured', '7654', 'VISA', '2025-07-11 21:16:25'),
(36, 38, 'Demo', 'demo_68717fbd99af36.74203648', 340000, 'captured', '1212', 'VISA', '2025-07-11 21:18:53'),
(37, 45, 'Demo', 'demo_687185aa271305.69405552', 680000, 'captured', '5678', 'VISA', '2025-07-11 21:44:10'),
(38, 46, 'Demo', 'demo_687185f3c502a1.94176234', 680000, 'captured', '7887', 'VISA', '2025-07-11 21:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `image` text NOT NULL,
  `price` int(10) UNSIGNED NOT NULL,
  `stock_qty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image`, `price`, `stock_qty`, `created_at`, `updated_at`) VALUES
(1, 'computer system', 'full set with printer', 'uploadImage/products/prod_686bee977be051.07493362.jfif', 200000, 21, '2025-07-07 15:58:15', '2025-07-11 13:35:16'),
(2, 'CD Drive', '', 'uploadImage/products/prod_686beebf2107e9.61351280.jfif', 5300, 11, '2025-07-07 15:58:55', '2025-07-11 14:00:09'),
(3, 'CD-ROM', 'sony', 'uploadImage/products/prod_686beedc5d9ab6.29898817.jfif', 250, 182, '2025-07-07 15:59:24', '2025-07-11 21:53:33'),
(5, 'Electric Adjustable Computer Table', 'Key Features\nElectric Height Adjustment\n', 'uploadImage/products/prod_686bef2aefc8d3.47142352.jfif', 340000, 13, '2025-07-07 16:00:42', '2025-07-11 22:02:02'),
(7, 'Monitor', 'Fairly used Samsung monitor', 'uploadImage/products/prod_686bef9e403274.34405447.jfif', 45000, 43, '2025-07-07 16:02:38', '2025-07-07 16:02:38'),
(8, 'System Unit', 'Dell', 'uploadImage/products/prod_686befb4909103.75998428.jfif', 34000, 77, '2025-07-07 16:03:00', '2025-07-11 22:01:06'),
(9, 'Router', '', 'uploadImage/products/prod_686bf032b70bf7.76535503.jfif', 100000, 69, '2025-07-07 16:05:06', '2025-07-11 13:35:16'),
(10, 'Modem', '3GM Universal Modem', 'uploadImage/products/prod_686bf05ae73c23.64981790.jfif', 19000, 37, '2025-07-07 16:05:46', '2025-07-11 13:35:16'),
(11, 'Joystiick', '', 'uploadImage/products/prod_686bf07793c290.77950718.jfif', 23590, 76, '2025-07-07 16:06:15', '2025-07-07 16:06:15'),
(12, 'MacBook Air', '', 'uploadImage/products/prod_686bf095aa8732.95292316.webp', 1250000, 45, '2025-07-07 16:06:45', '2025-07-07 16:06:45'),
(13, 'Macbook Laptop', '', 'uploadImage/products/prod_686bf0b0465473.95909188.webp', 1000000, 19, '2025-07-07 16:07:12', '2025-07-11 22:02:02');

-- --------------------------------------------------------

--
-- Table structure for table `risk_signals`
--

CREATE TABLE `risk_signals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `signal_type` varchar(60) NOT NULL,
  `signal_value` varchar(255) DEFAULT NULL,
  `weight` smallint(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risk_signals`
--

INSERT INTO `risk_signals` (`id`, `order_id`, `user_id`, `signal_type`, `signal_value`, `weight`, `created_at`) VALUES
(34, 36, 4, 'frequent_checkout', 'More than 3 orders in 2 minutes', 20, '2025-07-11 21:16:25'),
(37, 38, 4, 'high_risk_country', 'Nigeria', 4, '2025-07-11 21:18:53'),
(46, 45, 4, 'high_ticket', '₦680000', 10, '2025-07-11 21:44:10'),
(47, 45, 4, 'high_risk_country', 'Nigeria', 10, '2025-07-11 21:44:10'),
(48, 46, 4, 'high_ticket', '₦680000', 10, '2025-07-11 21:45:23'),
(49, 49, 4, 'high_ticket', '₦2340000', 10, '2025-07-11 22:02:06'),
(50, 49, 4, 'high_risk_country', 'Nigeria', 10, '2025-07-11 22:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` char(60) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role` varchar(15) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `phone`, `role`, `status`, `created_at`, `updated_at`) VALUES
(4, 'newleastpaysolution@gmail.com', '$2y$10$St13e5IpnU1bG5pvx//GV.BdcipkL4M/TVxyOujfzyLxU5ltZt4bW', 'Mmakamba Okorie', '08067361023', 'customer', 1, '2025-07-06 21:48:58', '2025-07-11 22:08:00'),
(5, 'newleastpaysolution@yahoo.com', '$2y$10$1.c2KKmCGlha3csy9IBIKetTD4xzYnaXHd/lxB7v9dBkf82oCZhh2', 'Ndueso Walter', '08067361023', 'Admin', 1, '2025-07-07 11:17:50', '2025-07-10 13:21:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_cart_product` (`cart_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `chargebacks`
--
ALTER TABLE `chargebacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `device_fingerprints`
--
ALTER TABLE `device_fingerprints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_device` (`user_id`,`device_hash`);

--
-- Indexes for table `fraud_cases`
--
ALTER TABLE `fraud_cases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `fraud_rules`
--
ALTER TABLE `fraud_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_scope_active` (`scope`,`is_active`);

--
-- Indexes for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  ADD PRIMARY KEY (`ip_address`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_login_ip_time` (`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_txn_id` (`provider_txn_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `risk_signals`
--
ALTER TABLE `risk_signals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_signal_user_type` (`user_id`,`signal_type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4068;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `chargebacks`
--
ALTER TABLE `chargebacks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_fingerprints`
--
ALTER TABLE `device_fingerprints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fraud_cases`
--
ALTER TABLE `fraud_cases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `fraud_rules`
--
ALTER TABLE `fraud_rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `risk_signals`
--
ALTER TABLE `risk_signals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `chargebacks`
--
ALTER TABLE `chargebacks`
  ADD CONSTRAINT `chargebacks_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `device_fingerprints`
--
ALTER TABLE `device_fingerprints`
  ADD CONSTRAINT `device_fingerprints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fraud_cases`
--
ALTER TABLE `fraud_cases`
  ADD CONSTRAINT `fraud_cases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD CONSTRAINT `login_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risk_signals`
--
ALTER TABLE `risk_signals`
  ADD CONSTRAINT `risk_signals_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `risk_signals_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
