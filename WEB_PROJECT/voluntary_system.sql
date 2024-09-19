-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: 127.0.0.1
-- Χρόνος δημιουργίας: 07 Σεπ 2024 στις 15:38:26
-- Έκδοση διακομιστή: 10.4.32-MariaDB
-- Έκδοση PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `voluntary_system`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `add_category`
--

CREATE TABLE `add_category` (
  `category_id` int(11) NOT NULL,
  `category_items` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `category_date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `add_category`
--

INSERT INTO `add_category` (`category_id`, `category_items`, `category_date_added`) VALUES
(1, '-----', '2024-09-06 17:55:57'),
(2, '2d hacker', '2024-09-06 17:55:57'),
(3, 'Animal Food', '2024-09-06 17:55:57'),
(4, 'Baby Essentials', '2024-09-06 17:55:57'),
(5, 'Beverages', '2024-09-06 17:55:57'),
(6, 'Cleaning Supplies', '2024-09-06 17:55:57'),
(7, 'Cleaning Supplies.', '2024-09-06 17:55:57'),
(8, 'Clothing', '2024-09-06 17:55:57'),
(9, 'Cold weather', '2024-09-06 17:55:57'),
(10, 'Electronic Devices', '2024-09-06 17:55:57'),
(11, 'Financial support', '2024-09-06 17:55:57'),
(12, 'Flood', '2024-09-06 17:55:57'),
(13, 'Food', '2024-09-06 17:55:57'),
(14, 'Hacker of class', '2024-09-06 17:55:57'),
(15, 'Hot Weather', '2024-09-06 17:55:57'),
(16, 'Insect Repellents', '2024-09-06 17:55:57'),
(17, 'Kitchen Supplies', '2024-09-06 17:55:57'),
(18, 'Medical Supplies', '2024-09-06 17:55:57'),
(19, 'new cat', '2024-09-06 17:55:57'),
(20, 'Personal Hygiene ', '2024-09-06 17:55:57'),
(21, 'Shoes', '2024-09-06 17:55:57'),
(22, 'Test', '2024-09-06 17:55:57'),
(23, 'Tools', '2024-09-06 17:55:57'),
(48, 'Clothes', '2024-09-06 18:02:58'),
(76, 'Electronics', '2024-09-07 11:15:08');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `admin_announcements`
--

CREATE TABLE `admin_announcements` (
  `ann_id` int(11) NOT NULL,
  `ann_announcement` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `admin_announcements`
--

INSERT INTO `admin_announcements` (`ann_id`, `ann_announcement`) VALUES
(8, 'Ζητούνται 7 Coca Cola\'s'),
(9, 'Ζητούνται 5 παυσίπονα'),
(10, 'Ζητούνται 5 κουβέρτες'),
(11, 'Ζητούνται 6 Ιβουπροφένες'),
(12, 'Ζητούνται 7 ζευγάρια παπούτσια'),
(13, 'Ζητούνται 5 ρολά χαρτιού υγείας');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `allusers`
--

CREATE TABLE `allusers` (
  `user_id` int(11) NOT NULL,
  `user_username` varchar(20) NOT NULL,
  `user_password` varchar(20) NOT NULL,
  `user_role` enum('admin','rescuer','citizen') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `allusers`
--

INSERT INTO `allusers` (`user_id`, `user_username`, `user_password`, `user_role`) VALUES
(1, 'Bill', 'Bill1', 'admin'),
(2, 'Fanis', 'Fanis1', 'rescuer'),
(3, 'Sakis', 'Sakis1', 'rescuer'),
(4, 'Giota', 'Giota1', 'rescuer'),
(5, 'Tasos', 'Tasos1', 'citizen'),
(6, 'Lucas', 'Lucas1', 'citizen'),
(7, 'Giorgos', 'Giorgos1', 'citizen'),
(8, 'Nikos', 'Nikos1', 'citizen'),
(9, 'Maria', 'Maria1', 'citizen'),
(10, 'Takis', 'Takis1', 'rescuer');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `base`
--

CREATE TABLE `base` (
  `base_id` int(11) NOT NULL,
  `base_latitude` float DEFAULT NULL,
  `base_longitude` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `base`
--

INSERT INTO `base` (`base_id`, `base_latitude`, `base_longitude`) VALUES
(1, 38.2516, 21.742);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `citizen_offers`
--

CREATE TABLE `citizen_offers` (
  `citoff_id` int(11) NOT NULL,
  `citoff_citizen_id` int(11) DEFAULT NULL,
  `citoff_stuff` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `citoff_quantity` int(11) NOT NULL,
  `citoff_date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `citoff_state` enum('ACCEPTED','NOT ACCEPTED') NOT NULL DEFAULT 'NOT ACCEPTED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `citizen_offers`
--

INSERT INTO `citizen_offers` (`citoff_id`, `citoff_citizen_id`, `citoff_stuff`, `citoff_quantity`, `citoff_date_added`, `citoff_state`) VALUES
(19, 9, 'Coca Cola', 10, '2024-09-07 13:28:01', 'NOT ACCEPTED'),
(20, 9, 'Painkillers', 6, '2024-09-07 13:29:25', 'NOT ACCEPTED'),
(21, 9, 'Ibuprofen', 6, '2024-09-07 13:30:18', 'NOT ACCEPTED'),
(22, 8, 'Blanket', 5, '2024-09-07 13:33:04', 'NOT ACCEPTED'),
(23, 8, 'Shoes', 7, '2024-09-07 13:33:38', 'NOT ACCEPTED'),
(24, 8, 'Toilet Paper', 8, '2024-09-07 13:33:54', 'NOT ACCEPTED');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `citizen_registration`
--

CREATE TABLE `citizen_registration` (
  `citizen_id` int(11) NOT NULL,
  `citizen_username` varchar(20) NOT NULL,
  `citizen_password` varchar(20) NOT NULL,
  `citizen_name` varchar(100) NOT NULL,
  `citizen_phone` bigint(20) NOT NULL,
  `citizen_latitude` decimal(10,8) DEFAULT NULL,
  `citizen_longitude` decimal(10,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `citizen_registration`
--

INSERT INTO `citizen_registration` (`citizen_id`, `citizen_username`, `citizen_password`, `citizen_name`, `citizen_phone`, `citizen_latitude`, `citizen_longitude`) VALUES
(5, 'Tasos', 'Tasos1', 'Tasos Kontogiorgos', 2610333777, 38.24658800, 21.73100424),
(6, 'Lucas', 'Lucas1', 'Lucas Triantafyllopoulos', 2310521010, 38.24809117, 21.74282312),
(7, 'Giorgos', 'Giorgos1', 'Giorgos Georgiou', 2694061111, 38.24155080, 21.74898148),
(8, 'Nikos', 'Nikos1', 'Nikos Daskalopoulos', 2610666666, 38.24055984, 21.73951435),
(9, 'Maria', 'Maria1', 'Maria Margaritopoulou', 2614123456, 38.24288555, 21.74020958);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `citizen_requests`
--

CREATE TABLE `citizen_requests` (
  `citres_id` int(11) NOT NULL,
  `citres_citizen_id` int(11) DEFAULT NULL,
  `citres_stuff` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `citres_people` int(11) NOT NULL,
  `citres_date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `citres_state` enum('ACCEPTED','NOT ACCEPTED') NOT NULL DEFAULT 'NOT ACCEPTED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `citizen_requests`
--

INSERT INTO `citizen_requests` (`citres_id`, `citres_citizen_id`, `citres_stuff`, `citres_people`, `citres_date_added`, `citres_state`) VALUES
(15, 7, 'Water', 5, '2024-09-07 12:47:19', 'NOT ACCEPTED'),
(16, 7, 'Banana', 13, '2024-09-07 12:47:16', 'NOT ACCEPTED'),
(17, 7, 'Coca Cola', 18, '2024-09-07 12:47:14', 'NOT ACCEPTED'),
(18, 5, 'Tomatoes', 10, '2024-09-07 13:10:54', 'NOT ACCEPTED'),
(19, 5, 'Painkillers', 4, '2024-09-07 13:11:50', 'NOT ACCEPTED'),
(20, 5, 'Toilet Paper', 6, '2024-09-07 13:12:09', 'NOT ACCEPTED'),
(21, 5, 'Blanket', 3, '2024-09-07 13:12:25', 'NOT ACCEPTED'),
(22, 6, 'Multivitamines', 9, '2024-09-07 13:13:37', 'NOT ACCEPTED'),
(23, 6, 'Paracetamol', 3, '2024-09-07 13:13:52', 'NOT ACCEPTED'),
(24, 6, 'Ibuprofen', 10, '2024-09-07 13:15:21', 'NOT ACCEPTED'),
(25, 6, 'Cleaning rag', 3, '2024-09-07 13:15:36', 'NOT ACCEPTED'),
(26, 7, 'Shoes', 7, '2024-09-07 13:16:48', 'NOT ACCEPTED');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `current_tasks`
--

CREATE TABLE `current_tasks` (
  `task_id` int(11) NOT NULL,
  `task_rescuer_id` int(11) DEFAULT NULL,
  `citizen_id` int(11) DEFAULT NULL,
  `citizen_fullname` varchar(100) NOT NULL,
  `citizen_telephone` bigint(20) NOT NULL,
  `offer_request_date_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `task_date_received` timestamp NOT NULL DEFAULT current_timestamp(),
  `task_date_completed` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_stuff` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `item_quantity` int(11) DEFAULT NULL,
  `task_latitude` decimal(10,8) DEFAULT NULL,
  `task_longitude` decimal(10,8) DEFAULT NULL,
  `task_type` enum('request','offer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `current_tasks`
--

INSERT INTO `current_tasks` (`task_id`, `task_rescuer_id`, `citizen_id`, `citizen_fullname`, `citizen_telephone`, `offer_request_date_added`, `task_date_received`, `task_date_completed`, `item_stuff`, `item_quantity`, `task_latitude`, `task_longitude`, `task_type`) VALUES
(25, 10, 6, 'Lucas Triantafyllopoulos', 2310521010, '2024-09-07 12:35:49', '2024-09-07 12:37:40', '2024-09-07 12:37:40', 'Water', 5, 38.24609273, 21.74219166, 'offer');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_stuff` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `item_quantity` int(11) DEFAULT NULL,
  `item_category_id` int(11) DEFAULT NULL,
  `item_category` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `items`
--

INSERT INTO `items` (`item_id`, `item_stuff`, `item_quantity`, `item_category_id`, `item_category`) VALUES
(6, 'Chocolate', 27, 5, 'Food'),
(7, 'Men Sneakers', 35, 7, 'Clothing'),
(8, 'Test Product', 4, 9, '2d hacker'),
(9, 'Test Val', 19, 14, 'Flood'),
(11, 'Croissant', 10, 5, 'Food'),
(12, '', 31, 10, ''),
(14, 'Bandages', 15, 16, 'Medical Supplies'),
(15, 'Disposable gloves', 19, 16, 'Medical Supplies'),
(16, 'Gauze', 9, 16, 'Medical Supplies'),
(17, 'Antiseptic', 25, 16, 'Medical Supplies'),
(18, 'First Aid Kit', 31, 16, 'Medical Supplies'),
(19, 'Painkillers', 38, 16, 'Medical Supplies'),
(20, 'Blanket', 12, 7, 'Clothing'),
(21, 'Fakes', 17, 5, 'Food'),
(22, 'Menstrual Pads', 4, 21, 'Personal Hygiene '),
(23, 'Tampon', 29, 21, 'Personal Hygiene '),
(24, 'Toilet Paper', 20, 21, 'Personal Hygiene '),
(25, 'Baby wipes', 17, 21, 'Personal Hygiene '),
(26, 'Toothbrush', 27, 21, 'Personal Hygiene '),
(27, 'Toothpaste', 25, 21, 'Personal Hygiene '),
(28, 'Vitamin C', 33, 16, 'Medical Supplies'),
(29, 'Multivitamines', 39, 16, 'Medical Supplies'),
(30, 'Paracetamol', 15, 16, 'Medical Supplies'),
(31, 'Ibuprofen', 39, 16, 'Medical Supplies'),
(35, 'Cleaning rag', 14, 22, 'Cleaning Supplies'),
(36, 'Detergent', 7, 22, 'Cleaning Supplies'),
(37, 'Disinfectant', 31, 22, 'Cleaning Supplies'),
(38, 'Mop', 16, 22, 'Cleaning Supplies'),
(39, 'Plastic bucket', 27, 22, 'Cleaning Supplies'),
(40, 'Scrub brush', 5, 22, 'Cleaning Supplies'),
(41, 'Dust mask', 26, 22, 'Cleaning Supplies'),
(42, 'Broom', 31, 22, 'Cleaning Supplies'),
(43, 'Hammer', 37, 23, 'Tools'),
(44, 'Skillsaw', 14, 23, 'Tools'),
(45, 'Prybar', 36, 23, 'Tools'),
(46, 'Shovel', 18, 23, 'Tools'),
(47, 'Flashlight', 22, 23, 'Tools'),
(48, 'Duct tape', 14, 23, 'Tools'),
(49, 'Underwear', 3, 7, 'Clothing'),
(50, 'Socks', 12, 7, 'Clothing'),
(51, 'Warm Jacket', 13, 7, 'Clothing'),
(52, 'Raincoat', 27, 7, 'Clothing'),
(53, 'Gloves', 5, 7, 'Clothing'),
(54, 'Pants', 2, 7, 'Clothing'),
(55, 'Boots', 39, 7, 'Clothing'),
(67, 't22', 29, 9, '2d hacker'),
(68, 'water ', 11, 6, 'Beverages'),
(69, 'Coca Cola', 6, 6, 'Beverages'),
(74, 'Condensed milk', 35, 5, 'Food'),
(75, 'Cereal bar', 23, 5, 'Food'),
(76, 'Pocket Knife', 10, 23, 'Tools'),
(77, 'Water Disinfection Tablets', 18, 16, 'Medical Supplies'),
(79, 'Kitchen appliances', 17, 14, 'Flood'),
(84, 'Tea', 19, 6, 'Beverages'),
(87, 'Canned', 24, 5, 'Food'),
(88, 'Chlorine', 32, 22, 'Cleaning Supplies'),
(89, 'Medical gloves', 5, 22, 'Cleaning Supplies'),
(90, 'T-Shirt', 9, 7, 'Clothing'),
(93, 'Whistle', 8, 23, 'Tools'),
(98, 'Thermometer', 13, 16, 'Medical Supplies'),
(101, 'Towels', 13, 22, 'Cleaning Supplies'),
(102, 'Wet Wipes', 15, 22, 'Cleaning Supplies'),
(103, 'Fire Extinguisher', 38, 23, 'Tools'),
(104, 'Water', 26, 6, 'Beverages'),
(105, 'Orange juice', 6, 6, 'Beverages'),
(106, 'Sardines', 7, 5, 'Food'),
(107, 'Canned corn', 16, 5, 'Food'),
(108, 'Bread', 16, 5, 'Food'),
(207, 'Banana', 32, NULL, 'Food'),
(208, 'Apple', 1, NULL, 'Food'),
(211, 'Canned Food', 28, NULL, 'Food'),
(213, 'Meat', 53, NULL, 'Food'),
(214, 'Spaggheti', 1, NULL, 'Food'),
(215, 'Tomatoes', 134, NULL, 'Food'),
(216, 'Strawberries', 29, NULL, 'Food'),
(217, 'Short Sleeve', 39, NULL, 'Clothes'),
(218, 'Sweater', 26, NULL, 'Clothes'),
(221, 'Long Sleeve', 36, NULL, 'Clothes'),
(222, 'T-shirt', 34, NULL, 'Clothes'),
(223, 'Outerwear', 19, NULL, 'Clothes'),
(224, 'Shoes', 33, NULL, 'Clothes'),
(225, 'Room Shoes', 28, NULL, 'Clothes'),
(226, 'Hoodie', 2, NULL, 'Clothes'),
(227, 'Pain-Killers', 6, NULL, 'Medical Supplies'),
(228, 'Vitamins', 22, NULL, 'Medical Supplies'),
(229, 'Pressure Pills', 10, NULL, 'Medical Supplies'),
(230, 'XanaX', 24, NULL, 'Medical Supplies'),
(231, 'Syringes', 11, NULL, 'Medical Supplies'),
(234, 'Neck Collars', 31, NULL, 'Medical Supplies'),
(235, 'Antibiotics', 9, NULL, 'Medical Supplies'),
(236, 'Stitches', 32, NULL, 'Medical Supplies'),
(246, 'Spaghetti', 17, 5, 'Food'),
(343, 'Rice', 51, 1, 'Food'),
(345, 'Biscuits', 12, 1, 'Food'),
(370, 'Pumpers', 11, NULL, 'Baby Essentials'),
(372, 'Lithium Batteries', 13, NULL, 'Electronics');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `old_offers`
--

CREATE TABLE `old_offers` (
  `oldoff_id` int(11) NOT NULL,
  `oldoff_citizen_id` int(11) DEFAULT NULL,
  `oldoff_stuff` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `oldoff_quantity` int(11) NOT NULL,
  `oldoff_date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `old_offers`
--

INSERT INTO `old_offers` (`oldoff_id`, `oldoff_citizen_id`, `oldoff_stuff`, `oldoff_quantity`, `oldoff_date_added`) VALUES
(1, 6, 'Baby wipes', 4, '2024-09-06 19:04:05'),
(2, 6, 'Toothpaste', 10, '2024-09-06 19:31:49'),
(3, 5, 'Water', 5, '2024-09-07 12:42:19'),
(4, 5, 'Meat', 20, '2024-09-07 12:55:12'),
(5, 5, 'Toilet Paper', 8, '2024-09-07 12:58:15'),
(6, 6, 'Banana', 13, '2024-09-07 13:02:57');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `old_requests`
--

CREATE TABLE `old_requests` (
  `oldres_id` int(11) NOT NULL,
  `oldres_citizen_id` int(11) DEFAULT NULL,
  `oldres_stuff` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `oldres_people` int(11) NOT NULL,
  `oldres_date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `old_requests`
--

INSERT INTO `old_requests` (`oldres_id`, `oldres_citizen_id`, `oldres_stuff`, `oldres_people`, `oldres_date_added`) VALUES
(1, 7, 'Chocolate', 6, '2024-09-07 12:23:09'),
(2, 6, 'Tomatoes', 35, '2024-09-07 12:33:11'),
(3, 5, 'Tomatoes', 12, '2024-09-07 12:56:49'),
(4, 5, 'Gloves', 12, '2024-09-07 13:01:21'),
(5, 5, 'Fakes', 6, '2024-09-07 13:01:32'),
(6, 5, 'Rice', 8, '2024-09-07 13:01:41');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `rescuers`
--

CREATE TABLE `rescuers` (
  `rescuer_id` int(11) NOT NULL,
  `rescuer_username` varchar(20) NOT NULL,
  `rescuer_vehicle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `rescuers`
--

INSERT INTO `rescuers` (`rescuer_id`, `rescuer_username`, `rescuer_vehicle`) VALUES
(2, 'Fanis', 'Vehicle of Fanis'),
(3, 'Sakis', 'Vehicle of Sakis'),
(4, 'Giota', 'Vehicle of Giota'),
(10, 'Takis', 'Vehicle of Takis');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `task_connections`
--

CREATE TABLE `task_connections` (
  `id` int(11) NOT NULL,
  `rescuer_id` int(11) DEFAULT NULL,
  `task_latitude` decimal(10,8) DEFAULT NULL,
  `task_longitude` decimal(10,8) DEFAULT NULL,
  `vehicle_latitude` double DEFAULT NULL,
  `vehicle_longitude` double DEFAULT NULL,
  `task_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `task_connections`
--

INSERT INTO `task_connections` (`id`, `rescuer_id`, `task_latitude`, `task_longitude`, `vehicle_latitude`, `vehicle_longitude`, `task_type`) VALUES
(17, 10, 38.24609273, 21.74219166, 38.25541297881957, 21.738656212152012, 'offer');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_rescuer_id` int(11) DEFAULT NULL,
  `vehicle_name` varchar(50) NOT NULL,
  `vehicle_tasks` int(11) DEFAULT 0,
  `vehicle_latitude` float DEFAULT NULL,
  `vehicle_longitude` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `vehicles`
--

INSERT INTO `vehicles` (`vehicle_rescuer_id`, `vehicle_name`, `vehicle_tasks`, `vehicle_latitude`, `vehicle_longitude`) VALUES
(2, 'Vehicle of Fanis', 0, 38.2457, 21.733),
(3, 'Vehicle of Sakis', 0, 38.2509, 21.7421),
(4, 'Vehicle of Giota', 0, 38.2746, 21.7323),
(10, 'Vehicle of Takis', 1, 38.2554, 21.7387);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `vehicle_storage`
--

CREATE TABLE `vehicle_storage` (
  `vehicle_rescuer_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `item_quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `vehicle_storage`
--

INSERT INTO `vehicle_storage` (`vehicle_rescuer_id`, `item_id`, `item_name`, `item_quantity`) VALUES
(2, 21, 'Fakes', 1),
(2, 343, 'Rice', 2),
(4, 17, 'Antiseptic', 3),
(4, 18, 'First Aid Kit', 2),
(10, 7, 'Men Sneakers', 4);

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `add_category`
--
ALTER TABLE `add_category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_items` (`category_items`);

--
-- Ευρετήρια για πίνακα `admin_announcements`
--
ALTER TABLE `admin_announcements`
  ADD PRIMARY KEY (`ann_id`);

--
-- Ευρετήρια για πίνακα `allusers`
--
ALTER TABLE `allusers`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_username` (`user_username`);

--
-- Ευρετήρια για πίνακα `base`
--
ALTER TABLE `base`
  ADD PRIMARY KEY (`base_id`);

--
-- Ευρετήρια για πίνακα `citizen_offers`
--
ALTER TABLE `citizen_offers`
  ADD PRIMARY KEY (`citoff_id`),
  ADD KEY `CTZOFFCTZ` (`citoff_citizen_id`),
  ADD KEY `CTZOFFSTF` (`citoff_stuff`);

--
-- Ευρετήρια για πίνακα `citizen_registration`
--
ALTER TABLE `citizen_registration`
  ADD PRIMARY KEY (`citizen_id`),
  ADD UNIQUE KEY `citizen_username` (`citizen_username`);

--
-- Ευρετήρια για πίνακα `citizen_requests`
--
ALTER TABLE `citizen_requests`
  ADD PRIMARY KEY (`citres_id`),
  ADD KEY `CITRESCIT` (`citres_citizen_id`),
  ADD KEY `CTZSTFF` (`citres_stuff`);

--
-- Ευρετήρια για πίνακα `current_tasks`
--
ALTER TABLE `current_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `CURRTSKRESC` (`task_rescuer_id`);

--
-- Ευρετήρια για πίνακα `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`,`item_stuff`),
  ADD UNIQUE KEY `item_stuff` (`item_stuff`),
  ADD KEY `ITEMCATID` (`item_category_id`);

--
-- Ευρετήρια για πίνακα `old_offers`
--
ALTER TABLE `old_offers`
  ADD PRIMARY KEY (`oldoff_id`);

--
-- Ευρετήρια για πίνακα `old_requests`
--
ALTER TABLE `old_requests`
  ADD PRIMARY KEY (`oldres_id`);

--
-- Ευρετήρια για πίνακα `rescuers`
--
ALTER TABLE `rescuers`
  ADD PRIMARY KEY (`rescuer_id`);

--
-- Ευρετήρια για πίνακα `task_connections`
--
ALTER TABLE `task_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rescuer_id` (`rescuer_id`);

--
-- Ευρετήρια για πίνακα `vehicles`
--
ALTER TABLE `vehicles`
  ADD KEY `VEHRESID` (`vehicle_rescuer_id`);

--
-- Ευρετήρια για πίνακα `vehicle_storage`
--
ALTER TABLE `vehicle_storage`
  ADD PRIMARY KEY (`vehicle_rescuer_id`,`item_id`),
  ADD KEY `VEHITNAM` (`item_id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `add_category`
--
ALTER TABLE `add_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT για πίνακα `admin_announcements`
--
ALTER TABLE `admin_announcements`
  MODIFY `ann_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT για πίνακα `allusers`
--
ALTER TABLE `allusers`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT για πίνακα `citizen_offers`
--
ALTER TABLE `citizen_offers`
  MODIFY `citoff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT για πίνακα `citizen_registration`
--
ALTER TABLE `citizen_registration`
  MODIFY `citizen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT για πίνακα `citizen_requests`
--
ALTER TABLE `citizen_requests`
  MODIFY `citres_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT για πίνακα `current_tasks`
--
ALTER TABLE `current_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT για πίνακα `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=373;

--
-- AUTO_INCREMENT για πίνακα `old_offers`
--
ALTER TABLE `old_offers`
  MODIFY `oldoff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT για πίνακα `old_requests`
--
ALTER TABLE `old_requests`
  MODIFY `oldres_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT για πίνακα `task_connections`
--
ALTER TABLE `task_connections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `citizen_offers`
--
ALTER TABLE `citizen_offers`
  ADD CONSTRAINT `CTZOFFCTZ` FOREIGN KEY (`citoff_citizen_id`) REFERENCES `citizen_registration` (`citizen_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CTZOFFSTF` FOREIGN KEY (`citoff_stuff`) REFERENCES `items` (`item_stuff`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `citizen_registration`
--
ALTER TABLE `citizen_registration`
  ADD CONSTRAINT `CITZID` FOREIGN KEY (`citizen_id`) REFERENCES `allusers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `citizen_requests`
--
ALTER TABLE `citizen_requests`
  ADD CONSTRAINT `CITRESCIT` FOREIGN KEY (`citres_citizen_id`) REFERENCES `citizen_registration` (`citizen_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CTZSTFF` FOREIGN KEY (`citres_stuff`) REFERENCES `items` (`item_stuff`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `current_tasks`
--
ALTER TABLE `current_tasks`
  ADD CONSTRAINT `CURRTSKRESC` FOREIGN KEY (`task_rescuer_id`) REFERENCES `rescuers` (`rescuer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `ITEMCATID` FOREIGN KEY (`item_category_id`) REFERENCES `add_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `rescuers`
--
ALTER TABLE `rescuers`
  ADD CONSTRAINT `RESCID` FOREIGN KEY (`rescuer_id`) REFERENCES `allusers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `task_connections`
--
ALTER TABLE `task_connections`
  ADD CONSTRAINT `task_connections_ibfk_1` FOREIGN KEY (`rescuer_id`) REFERENCES `rescuers` (`rescuer_id`);

--
-- Περιορισμοί για πίνακα `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `VEHRESID` FOREIGN KEY (`vehicle_rescuer_id`) REFERENCES `rescuers` (`rescuer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `vehicle_storage`
--
ALTER TABLE `vehicle_storage`
  ADD CONSTRAINT `VEHIRESID` FOREIGN KEY (`vehicle_rescuer_id`) REFERENCES `rescuers` (`rescuer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `VEHITNAM` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
