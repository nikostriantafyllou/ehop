-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2020 at 01:16 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `userid` int(11) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(40) CHARACTER SET utf8 NOT NULL,
  `email` varchar(80) CHARACTER SET utf8 NOT NULL,
  `password` char(64) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`userid`, `firstname`, `lastname`, `email`, `password`) VALUES
(2, 'admin', 'admin', 'admin@gmail.com', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderid` int(11) NOT NULL,
  `cust_email` varchar(50) NOT NULL,
  `firstname` varchar(80) NOT NULL,
  `lastname` varchar(80) NOT NULL,
  `city` varchar(40) NOT NULL,
  `address` varchar(40) NOT NULL,
  `add_number` int(11) NOT NULL,
  `postcode` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderid`, `cust_email`, `firstname`, `lastname`, `city`, `address`, `add_number`, `postcode`, `order_date`, `active`) VALUES
(13, 'obiwan@empire.com', 'Obi Wan', 'Kenobi', 'Tatooine', 'Tatooine, 11', 85, 11111, '2020-06-07 15:16:15', 0),
(25, 'dame@gmail.com', 'Damian', 'Lilard', 'Costa Rica', 'Φραγκου', 11, 11111, '2020-06-08 09:57:14', 0),
(26, 'darthV@gmail.com', 'Darth', 'Vader', 'Death Star', 'Commander office', 31, 11111, '2020-06-08 09:57:38', 0),
(27, 'madmax@pan.com', 'Max', 'Verstappen', 'Monaco', 'Casino', 13, 11111, '2020-06-08 11:00:01', 0),
(28, 'dim@diam.com', 'Dimitris', 'Diamantidis', 'Kastoria', 'Plastira', 13, 49123, '2020-06-08 07:45:24', 1),
(31, 'obiwan@empire.com', 'Obi Wan', 'Kenobi', 'Tatooine', 'Tatooine, 11', 5, 11111, '2020-06-08 08:52:49', 1),
(32, 'air@jordan.com', 'Michael', 'Jordan', 'Σικαγο', 'Bulls Rules', 12, 12341, '2020-06-08 09:41:25', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders_content`
--

CREATE TABLE `orders_content` (
  `id` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders_content`
--

INSERT INTO `orders_content` (`id`, `orderid`, `productid`, `quantity`, `price`) VALUES
(8, 13, 98, 3, 14.3),
(28, 25, 98, 3, 14.39),
(29, 25, 102, 3, 9),
(30, 25, 185, 4, 14),
(31, 26, 102, 3, 9),
(32, 26, 185, 4, 14),
(33, 26, 127, 4, 3),
(34, 27, 127, 4, 3),
(35, 27, 120, 1, 12.5),
(36, 28, 102, 2, 6),
(37, 28, 103, 3, 9.3),
(38, 28, 127, 3, 2.25),
(41, 31, 102, 3, 9),
(42, 31, 101, 1, 8.6),
(43, 32, 99, 3, 25.5),
(44, 32, 185, 3, 10.5);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `proid` int(11) NOT NULL,
  `proname` varchar(40) CHARACTER SET utf8 NOT NULL,
  `price` double NOT NULL,
  `proimg` varchar(90) NOT NULL,
  `isactive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`proid`, `proname`, `price`, `proimg`, `isactive`) VALUES
(70, 'Γραβιέρα Πρόβεια', 5.4, 'grabiera-probeia.jpg', 1),
(90, 'Γιαούρτι Αγελ. Στραγγιστό 10% 1Kg', 6.01, 'giaoyrti-agelstraggisto-10-1kg.jpg', 1),
(95, 'Πρόβειο Γιαούρτι Πήλινο 500gr', 4.49, 'probeio-giaoyrti-phlino-500gr.jpg', 0),
(98, 'Ανωτύρι 300gr', 4.8, 'anwtyri-300-gr.jpg', 0),
(99, 'Φέτα ΠΟΠ', 8.5, 'feta-pop.jpg', 1),
(101, 'Ημίσκληρο Τυρί Φόρμα1', 8.6, 'tyri-forma.jpg', 1),
(102, 'Γραβιέρα Αγελαδινή 250gr', 3, 'grabiera-ageladinh-250gr.jpg', 1),
(103, 'Λευκό Τυρί 400gr', 3.1, 'leyko-tyri-400-gr-taper.jpg', 1),
(104, 'Ημίσκληρο Φέτες 200gr', 2.2, 'hmisklhro-fetes-200-gr-skafaki.jpg', 1),
(120, 'Πεκορίνο', 12.5, 'pekorino.jpg', 1),
(127, 'Γιαούρτι Αγελάδος 220gr', 0.75, 'giaoyrti-agelados-220gr.jpg', 0),
(185, 'Βούτυρο Αγελαδινό 250gr', 3.5, 'boytyro-ageladino-250gr-.jpg', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderid`);

--
-- Indexes for table `orders_content`
--
ALTER TABLE `orders_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`proid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `orders_content`
--
ALTER TABLE `orders_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
