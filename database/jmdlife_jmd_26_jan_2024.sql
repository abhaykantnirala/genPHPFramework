-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 26, 2024 at 10:43 PM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jmdlife_jmd`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female','other') NOT NULL DEFAULT 'male',
  `edate` date NOT NULL,
  `mdate` date NOT NULL,
  `enable` enum('true','false') NOT NULL DEFAULT 'false',
  `blocked` enum('true','false') NOT NULL DEFAULT 'false',
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `fname`, `lname`, `email`, `password`, `gender`, `edate`, `mdate`, `enable`, `blocked`, `address`) VALUES
(1, 'Ak', 'Sharma', 'aksharma@gmail.com', 'c982e36bc6ccd4c29ede799349a2d9d5', 'male', '2022-12-25', '2022-12-25', 'true', 'false', 'Marimata,Indore');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `plan_name` varchar(512) NOT NULL DEFAULT '',
  `plan_duration` int(11) NOT NULL DEFAULT '12' COMMENT 'in months',
  `description` text NOT NULL,
  `plan_emi_type` enum('daily','monthly','quaterly','half_yearly','yearly') NOT NULL DEFAULT 'monthly',
  `plan_amount` int(11) NOT NULL DEFAULT '0',
  `plan_emi` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statusa` int(11) NOT NULL DEFAULT '0' COMMENT 'enable = 1, disable=0',
  `statusb` int(11) NOT NULL DEFAULT '0' COMMENT 'available = 1, not available = 0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `plan_name`, `plan_duration`, `description`, `plan_emi_type`, `plan_amount`, `plan_emi`, `priority`, `datetime`, `statusa`, `statusb`) VALUES
(1, 'Gold Plan 4000', 15, '<ul>                             <li>4000 Per Month EMI</li> <li>First EMI will be 4000+500(Registration Fee)</li> <li>74000 Will be Return in 16 Month</li> <li>Surprise Gift also available for all user</li>                                              </ul>', 'monthly', 74000, 4000, 5, '2023-12-05 22:11:56', 1, 1),
(2, 'Silver Plan 4000', 20, '<ul>                             <li>4000 Per Month EMI</li> <li>First EMI will be 4000+500(Registration Fee)</li> <li>100000 Will be Return in 21 Month</li> <li>Surprise Gift also available for all user</li>                                              </ul>', 'monthly', 100000, 4000, 4, '2023-12-09 14:40:38', 1, 1),
(3, 'Platinum Plan 4000', 30, '<ul>                             <li>4000 Per Month EMI</li> <li>First EMI will be 4000+500(Registration Fee)</li> <li>150000 Will be Return in 31 Month</li> <li>Surprise Gift also available for all user</li>                                              </ul>', 'monthly', 150000, 4000, 1, '2023-12-11 17:47:15', 1, 1),
(4, 'Titanium Plan 8000', 15, '<ul>                             <li>8000 Per Month EMI</li> <li>First EMI will be 8000+500(Registration Fee)</li> <li>148000 Will be Return in 16 Month</li> <li>Surprise Gift also available for all user</li>                                              </ul>', 'monthly', 148000, 8000, 2, '2023-12-12 15:37:45', 1, 1),
(5, 'Diamond Plan 6000', 15, '<ul>                             <li>6000 Per Month EMI</li> <li>First EMI will be 6000+500(Registration Fee)</li> <li>110000 Will be Return in 16 Month</li> <li>Surprise Gift also available for all user</li>                                              </ul>', 'monthly', 111000, 6000, 3, '2023-12-12 15:50:44', 1, 1),
(6, 'Basic plan 2000', 38, '<ul>                             <li>2000 Per Month EMI</li> <li>First EMI will be 2000+500(Registration Fee)</li> <li>100000 Will be Return in 40 Month</li> <li>Surprise Gift also available for all user</li>                                              </ul>', 'monthly', 100000, 2000, 6, '2023-12-16 13:16:04', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL DEFAULT '',
  `lname` varchar(255) NOT NULL DEFAULT '',
  `aadhaar_number` varchar(20) NOT NULL DEFAULT '',
  `pan_number` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `tmppwd` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `address_1` varchar(255) NOT NULL DEFAULT '',
  `address_2` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `aadhaar_number`, `pan_number`, `password`, `tmppwd`, `country`, `state`, `city`, `address_1`, `address_2`, `phone`, `zip`, `email`, `datetime`) VALUES
(1, 'Aman', 'sharma', '', '', '35ae705c6fb3dd6ded5217946402481a', '629569', 'India', 'Datia', '', 'इंदरगढ़ दतिया mp', 'Indragarh ', '9340421890', '475675', '', '2023-12-02 13:43:18'),
(2, 'Aryan', 'Sharma', '', '', 'cf823b0c84db304f13724350ac7cd7c9', '535605', 'India', 'indore', '', 'indore', '', '9074224287', '452001', '', '2023-12-10 19:59:36'),
(6, 'Ketul', 'Tiwari ', '477901372071', 'Avept0225F', '9c70eb0f9be292d31f5f4e148d0c040a', '923896', 'India', 'Ahamdabad', '', 'Ketul B tiwari 37 vasundhara Park society Nr. Kharicut kenal road  B/H nobal school krishnanagar Ahmedabad pin. ', '', '9974747457', '382345', '', '2023-12-12 08:35:26'),
(7, 'Dharmveer ', 'Singh ', '529738677175', '', '54b0187bfa01f022b72e6e7488c5e8bc', '280828', 'India', 'Morena ', '', 'Sumavali', 'Gram Ganesh pura', '8435300563', '476221', '', '2023-12-12 20:20:14'),
(8, 'Dharm singh ', 'Sharma ', '356666764759', '', '2266bdd574454a317523a09654ea66bd', '298521', 'India', 'Morena ', '', 'Sanjay कालोनी', 'A', '9617143019', '476001', '', '2023-12-14 14:20:17'),
(9, 'Jeetendra ', 'Sharma ', '426622923575', '', 'f8b96490753049e662e1f2d36988e553', '812415', 'India', 'Morena ', '', 'Gram khutiyani har ', '', '8817052733', '476224', '', '2023-12-14 21:05:57'),
(10, 'Arun', 'Gurjar ', '593526967995', '', 'a3a072fad469e73e56809211fc1d2efe', '533982', 'India', 'Morena', '', 'Joura morena', '', '7748042917', '476224', '', '2023-12-15 11:02:19'),
(11, 'Surendra ', 'Gurjar', '557661918563', '', '4ec0a24623d2b4cfb22230ce0b96c338', '499423', 'India', 'Morena', '', 'Joura', '', '9340114504', '476224', '', '2023-12-15 11:35:32'),
(12, 'Radhamohan ', 'Sharma ', '897672944021', '', 'ed41c07d1530a95e22089434adf8f184', '609961', 'India', 'Gwalior ', '', 'EH1 DD Nagar ', '', '7747923991', '474020', '', '2023-12-15 16:33:54'),
(13, 'Pramod ', 'Sharma ', '895245005627', '', '72c4f4089fe5c0edef090dfb0d5a4848', '761798', 'India', 'Morena ', '', 'Khutiyani har ', '', '9899097914', '476224', '', '2023-12-16 17:15:49'),
(14, 'Rajveer', 'Sharma', '553699218854', '', '86e103112657dc62c7444951e7e1d9f9', '893571', 'India', 'Morena ', '', 'Beerampura ', '', '8770426948', '476001', '', '2023-12-17 18:45:28'),
(15, 'Ravindra singh', 'Gurjar', '932840373625', '', '22a580af30685d83fdeef7ff6b0f620f', '884732', 'India', 'Indragrah ', '', 'अहरौरा ', '', '9584793641', '475675', '', '2023-12-28 20:21:57'),
(16, 'Rakesh ', 'sharma', '690528283228', '', '2a0b103a53355192dfea04d0b8072106', '369335', 'India', 'Morena ', '', 'Joura morena ', '', '8770529391', '475675', '', '2024-01-02 16:15:21'),
(17, 'Ramsindur ', 'Gurjar ', '434418864765', '', 'ab9e80519195ce8eb88058e57191ccf5', '108072', 'India', 'Datia ', '', 'Ahrora ', '', '8269856447', '475675', '', '2024-01-08 17:05:03'),
(18, 'Ashok ', 'Sharma (Pinki)', '557661918525', '', 'ebce9cdc63d90a5d492157c577345b62', '771217', 'India', 'Morena ', '', 'Hadwansi ', '', '9826267554', '476221', '', '2024-01-12 20:15:42'),
(19, 'Aman ', 'Sharma ', '216666764733', '', '082e16f1ae84e43b2cf0885bfbab39d7', '958322', 'India', 'Gwalior ', '', 'Purani chhavni ', '', '8655970600', '474010', '', '2024-01-13 22:53:52'),
(20, 'Mansi ', 'Gurjar', '477901372080', '', 'ba1030f189562deb9b3a841cfe67d510', '460571', 'India', 'Gwalior ', '', 'आदित्यपुरम ', '', '6260724571', '474001', '', '2024-01-13 23:17:50'),
(21, 'Munna', 'Lal', '529738677144', '', '23a2c9a2df5ae85bb58ea724033d7451', '268091', 'India', 'Morena ', '', 'Morena', '', '6260364256', '476001', '', '2024-01-13 23:27:22'),
(22, 'Anshika ', 'Sharma', '356666764722', '', '24ac54d9cb13c59970fff2ec2ff6ab81', '459663', 'India', 'Gwalior ', '', 'Gwalior ', '', '6266884093', '474010', '', '2024-01-14 21:44:37'),
(23, 'Neeraj ', 'Gurjar ', '425466764759', '', 'f74a2f97e430a9fb7b842c9f67fca7fc', '122782', 'India', 'Morena ', '', 'Joura', '', '9425729845', '476221', '', '2024-01-20 09:57:38'),
(24, 'P. c.', 'Kushwah ', '738412725799', '', '75e2496e1ee28dd9cc6b7a5fbfdb1956', '501930', 'India', 'Morena ', '', 'केलारस', '', '9753544340', '476221', '', '2024-01-20 11:16:54'),
(25, 'Akash', 'Sharma', '741287415879', '', '21bcad492b75336e409e9fcb802e7ff9', '260075', 'India', 'Indore', '', 'Indore', '', '83700016616', '452001', '', '2024-01-22 11:29:55'),
(26, 'Nitin', 'Sharma', '741287415879', '', 'c300e459d8fe9c8767558e1facf6b80b', '340329', 'India', 'Indore', '', 'Indore', '', '7987209159', '452001', '', '2024-01-22 11:36:13'),
(27, 'Abhaykant', 'Nirala', '741287415879', '', '96e79218965eb72c92a549dd5a330112', 'updated by user', 'India', 'Indore', '', 'Indore', '', '8871991972', '452001', '', '2024-01-22 11:48:49'),
(28, 'Gautam', 'Kumar', '741287415879', '', '5fb4b3aec6d1fe7ac39d72fc3fbb2175', '110475', 'India', 'Indore', '', 'Indore', '', '8109939218', '452001', '', '2024-01-22 12:03:19'),
(29, 'Praveen', 'Rathor', '741287415879', '', '478e5e94b7f96ba4b1f5d7dfe41c4c4f', '537156', 'India', 'Indore', '', 'Indore', '', '9752931981', '452001', '', '2024-01-22 12:08:47'),
(30, 'Jhamak Lal', 'Patidar', '741287415879', '', '8820b3080cc384d52fc5e69615b61f81', '348573', 'India', 'Indore', '', 'Indore', '', '9302310917', '452001', '', '2024-01-22 12:12:23'),
(31, 'Vinod', 'Tyagi', '741287415879', '', 'f09c928f3f280f8829026a1365f2b221', '828121', 'India', 'Indore', '', 'Indore', '', '9098994591', '452001', '', '2024-01-22 12:19:35'),
(32, 'Shyam Narayan', 'Yadav', '741287415879', '', '3426ef50a363400b4abfef2c0ef993f4', '771403', 'India', 'Indore', '', 'Indore', '', '8982271053', '452001', '', '2024-01-22 12:23:15'),
(33, 'Ranvir', 'Tiwari', '741287415879', '', '2367810bfcb1884ca2a46acdab5cda8a', '933171', 'India', 'Indore', '', 'Indore', '', '9981305211', '452001', '', '2024-01-22 12:25:39'),
(34, 'Sunil', 'Panday', '741287415879', '', '0dc368f9180586aaa3893ac267704994', '410388', 'India', 'Indore', '', 'Indore', '', '9753709772', '452001', '', '2024-01-22 12:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `users_plans`
--

CREATE TABLE `users_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `plan_id` int(11) NOT NULL DEFAULT '0',
  `total_emi_received` int(11) NOT NULL DEFAULT '0',
  `statusa` int(11) NOT NULL DEFAULT '0' COMMENT 'fulfilled=1, not fulfilled = 0',
  `statusb` int(11) NOT NULL DEFAULT '0' COMMENT 'terminated = 1, not terminated = 0',
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_plans`
--

INSERT INTO `users_plans` (`id`, `user_id`, `plan_id`, `total_emi_received`, `statusa`, `statusb`, `datetime`) VALUES
(5, 6, 5, 2, 0, 0, '2023-12-12 08:38:15'),
(7, 7, 1, 2, 0, 0, '2023-12-12 20:21:50'),
(11, 13, 6, 1, 0, 0, '2023-12-16 17:16:33'),
(12, 14, 1, 1, 0, 0, '2023-12-17 18:51:13'),
(13, 11, 6, 1, 0, 0, '2023-12-17 18:53:59'),
(14, 11, 6, 0, 0, 0, '2023-12-17 18:54:31'),
(15, 15, 6, 1, 0, 0, '2023-12-28 20:33:03'),
(17, 12, 1, 1, 0, 0, '2024-01-04 18:40:15'),
(18, 17, 6, 1, 0, 0, '2024-01-08 17:17:17'),
(19, 1, 6, 1, 0, 0, '2024-01-10 22:48:14'),
(20, 2, 4, 2, 0, 0, '2024-01-10 22:54:59'),
(21, 8, 6, 1, 0, 0, '2024-01-10 23:05:03'),
(22, 9, 6, 1, 0, 0, '2024-01-10 23:06:09'),
(23, 13, 6, 0, 0, 0, '2024-01-10 23:08:46'),
(24, 16, 6, 1, 0, 0, '2024-01-10 23:09:06'),
(25, 18, 6, 1, 0, 0, '2024-01-13 22:54:48'),
(26, 19, 6, 1, 0, 0, '2024-01-13 22:59:13'),
(27, 20, 6, 1, 0, 0, '2024-01-14 09:43:27'),
(28, 25, 4, 2, 0, 0, '2024-01-22 11:37:03'),
(29, 26, 1, 2, 0, 0, '2024-01-22 11:42:30'),
(30, 27, 4, 2, 0, 0, '2024-01-22 11:58:43'),
(31, 34, 1, 2, 0, 0, '2024-01-22 12:48:38'),
(32, 33, 5, 2, 0, 0, '2024-01-22 12:50:22'),
(33, 32, 5, 2, 0, 0, '2024-01-22 14:05:26'),
(34, 31, 5, 2, 0, 0, '2024-01-22 14:06:38'),
(35, 30, 5, 2, 0, 0, '2024-01-22 14:07:41'),
(36, 29, 1, 2, 0, 0, '2024-01-22 14:09:16'),
(37, 28, 1, 2, 0, 0, '2024-01-22 14:10:27');

-- --------------------------------------------------------

--
-- Table structure for table `users_referral_code`
--

CREATE TABLE `users_referral_code` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `rf_code` varchar(10) NOT NULL COMMENT 'users_id + random chars',
  `refer_by` int(11) NOT NULL DEFAULT '0',
  `edate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_referral_code`
--

INSERT INTO `users_referral_code` (`id`, `uid`, `rf_code`, `refer_by`, `edate`) VALUES
(1, 1, '1IG3H', 0, '2023-12-02 13:43:18'),
(2, 2, '2TA0T', 0, '2023-12-10 19:59:36'),
(6, 6, '6XSDV', 0, '2023-12-12 08:35:26'),
(7, 7, '7XW4N', 0, '2023-12-12 20:20:14'),
(8, 8, '80A82', 0, '2023-12-14 14:20:17'),
(9, 9, '9UCDK', 0, '2023-12-14 21:05:57'),
(10, 10, '100L6C', 0, '2023-12-15 11:02:19'),
(11, 11, '11D863', 0, '2023-12-15 11:35:32'),
(12, 12, '1266ZZ', 0, '2023-12-15 16:33:54'),
(13, 13, '13WMK5', 0, '2023-12-16 17:15:49'),
(14, 14, '14UOK9', 1, '2023-12-17 18:45:28'),
(15, 15, '15YMX7', 0, '2023-12-28 20:21:57'),
(16, 16, '16PCLO', 0, '2024-01-02 16:15:21'),
(17, 17, '174N18', 0, '2024-01-08 17:05:03'),
(18, 18, '18SA7Z', 0, '2024-01-12 20:15:42'),
(19, 19, '19DS6M', 0, '2024-01-13 22:53:52'),
(20, 20, '20BPLV', 0, '2024-01-13 23:17:50'),
(21, 21, '21DQWI', 0, '2024-01-13 23:27:22'),
(22, 22, '22JPQK', 0, '2024-01-14 21:44:37'),
(23, 23, '23MB5N', 0, '2024-01-20 09:57:38'),
(24, 24, '24HVGU', 0, '2024-01-20 11:16:54'),
(25, 25, '25IJH6', 2, '2024-01-22 11:29:55'),
(26, 26, '26EEKP', 2, '2024-01-22 11:36:13'),
(27, 27, '2799OT', 2, '2024-01-22 11:48:49'),
(28, 28, '28UKLY', 2, '2024-01-22 12:03:19'),
(29, 29, '29Y5SJ', 2, '2024-01-22 12:08:47'),
(30, 30, '30DV0N', 2, '2024-01-22 12:12:23'),
(31, 31, '319HBI', 2, '2024-01-22 12:19:35'),
(32, 32, '32KIXF', 2, '2024-01-22 12:23:15'),
(33, 33, '33ZB1M', 2, '2024-01-22 12:25:39'),
(34, 34, '34LFF7', 2, '2024-01-22 12:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_plan_emi`
--

CREATE TABLE `user_plan_emi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_plans_id` int(11) NOT NULL DEFAULT '0',
  `emi_amount` int(11) NOT NULL DEFAULT '0',
  `late_fine` int(11) NOT NULL DEFAULT '0',
  `emi_received_method` varchar(255) NOT NULL DEFAULT 'cash',
  `comment` varchar(1024) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_plan_emi`
--

INSERT INTO `user_plan_emi` (`id`, `user_id`, `user_plans_id`, `emi_amount`, `late_fine`, `emi_received_method`, `comment`, `datetime`) VALUES
(11, 6, 5, 6000, 0, 'bank_transfer', 'Ok', '2023-12-12 08:44:56'),
(12, 7, 7, 4000, 0, 'cash', 'Ok', '2023-12-12 20:22:48'),
(13, 14, 12, 4000, 0, 'cash', 'First imi', '2023-12-17 18:52:21'),
(14, 11, 13, 2000, 0, 'cash', 'First imi', '2023-12-17 18:55:23'),
(15, 15, 15, 2000, 0, 'cash', 'First imi', '2023-12-28 20:33:44'),
(16, 12, 17, 4000, 0, 'cash', '1first emi', '2024-01-04 18:41:54'),
(17, 17, 18, 2000, 1, 'cash', 'Frist emi', '2024-01-08 17:18:10'),
(18, 1, 19, 2000, 1, 'cash', 'First imi', '2024-01-10 22:48:51'),
(19, 2, 20, 8000, 1, 'bank_transfer', 'First imi', '2023-12-15 22:56:32'),
(20, 8, 21, 2000, 1, 'cash', 'First imi', '2024-01-10 23:05:49'),
(21, 9, 22, 2000, 0, 'cash', 'First imi', '2024-01-10 23:06:45'),
(22, 16, 24, 2000, 0, 'cash', 'First imi', '2024-01-10 23:09:35'),
(23, 13, 11, 2000, 0, 'cash', 'First imi', '2024-01-10 23:10:27'),
(24, 18, 25, 2000, 0, 'cash', 'A', '2024-01-13 22:55:16'),
(25, 19, 26, 2000, 0, 'cash', '1', '2024-01-13 22:59:43'),
(26, 20, 27, 2000, 0, 'cash', '1', '2024-01-14 09:43:53'),
(27, 7, 7, 4000, 0, 'upi', '2', '2024-01-16 08:17:39'),
(28, 6, 5, 6000, 0, 'upi', '2', '2024-01-18 16:40:38'),
(29, 25, 28, 8000, 0, 'upi', 'Received', '2024-01-22 11:41:46'),
(30, 25, 28, 8000, 0, 'upi', 'Received', '2024-01-22 11:46:57'),
(31, 26, 29, 4000, 0, 'upi', 'Received', '2024-01-22 11:54:48'),
(32, 27, 30, 8000, 0, 'upi', 'Received', '2023-12-15 11:59:15'),
(33, 27, 30, 8000, 0, 'upi', 'Received', '2024-01-15 12:00:01'),
(34, 26, 29, 4000, 0, 'upi', 'Received', '2024-01-22 12:00:32'),
(35, 34, 31, 4000, 0, 'upi', 'Received', '2024-01-22 12:49:17'),
(36, 34, 31, 4000, 0, 'upi', 'Received', '2024-01-22 12:49:42'),
(37, 33, 32, 6000, 0, 'upi', 'Received', '2024-01-22 13:01:02'),
(38, 33, 32, 6000, 0, 'upi', ' Received', '2024-01-22 14:04:53'),
(39, 32, 33, 6000, 0, 'upi', ' Received', '2024-01-22 14:05:50'),
(40, 32, 33, 6000, 0, 'upi', ' Received', '2024-01-22 14:06:12'),
(41, 31, 34, 6000, 0, 'upi', ' Received', '2024-01-22 14:06:58'),
(42, 31, 34, 6000, 0, 'upi', ' Received', '2024-01-22 14:07:15'),
(43, 30, 35, 6000, 0, 'upi', ' Received', '2024-01-22 14:08:34'),
(44, 30, 35, 6000, 0, 'upi', ' Received', '2024-01-22 14:08:52'),
(45, 29, 36, 4000, 0, 'upi', ' Received', '2024-01-22 14:09:40'),
(46, 29, 36, 4000, 0, 'upi', ' Received', '2024-01-22 14:10:00'),
(47, 28, 37, 4000, 0, 'upi', ' Received', '2024-01-22 14:10:50'),
(48, 28, 37, 4000, 0, 'upi', ' Received', '2024-01-22 14:11:11'),
(51, 2, 20, 8000, 0, 'upi', '15 jan ', '2024-01-15 21:34:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_plans`
--
ALTER TABLE `users_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_referral_code`
--
ALTER TABLE `users_referral_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_plan_emi`
--
ALTER TABLE `user_plan_emi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users_plans`
--
ALTER TABLE `users_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `users_referral_code`
--
ALTER TABLE `users_referral_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user_plan_emi`
--
ALTER TABLE `user_plan_emi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
