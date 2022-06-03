-- phpMyAdmin SQL Dump
-- version 5.1.4
-- https://www.phpmyadmin.net/
--
-- Host: voron121.mysql.ukraine.com.ua
-- Generation Time: Jun 03, 2022 at 11:38 PM
-- Server version: 5.7.33-36-log
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `voron121_brain`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `categoryID` bigint(20) NOT NULL,
  `parentID` bigint(20) DEFAULT NULL,
  `realcat` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `productID` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `brief_description` text,
  `description` text,
  `country` varchar(255) DEFAULT NULL,
  `categoryID` bigint(20) DEFAULT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `actionID` int(11) DEFAULT NULL,
  `warranty` varchar(255) DEFAULT NULL,
  `is_archive` enum('yes','no') DEFAULT NULL,
  `is_exclusive` enum('yes','no') DEFAULT NULL,
  `vendorID` int(11) DEFAULT NULL,
  `articul` varchar(255) DEFAULT NULL,
  `volume` varchar(255) DEFAULT '0.00',
  `weight` varchar(255) DEFAULT NULL,
  `kbt` varchar(255) DEFAULT NULL,
  `is_price_cut` enum('yes') DEFAULT NULL,
  `is_new` enum('yes','no') DEFAULT NULL,
  `price` float DEFAULT NULL,
  `price_uah` float DEFAULT NULL,
  `recommendable_price` float DEFAULT NULL,
  `retail_price_uah` float DEFAULT NULL,
  `bonus` float DEFAULT NULL,
  `stocks` varchar(255) DEFAULT NULL,
  `stocks_expected` varchar(255) DEFAULT NULL,
  `available` varchar(255) DEFAULT NULL,
  `self_delivery` varchar(255) DEFAULT NULL,
  `full_image` varchar(255) DEFAULT NULL,
  `medium_image` varchar(255) DEFAULT NULL,
  `quantity_package_sale` int(11) DEFAULT NULL,
  `koduktved` varchar(255) DEFAULT NULL,
  `reservation_limit` int(11) DEFAULT NULL,
  `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendorID` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categoryID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendorID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
