-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2025 at 08:51 AM
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
-- Database: `plane`
--

-- --------------------------------------------------------

--
-- Table structure for table `flight_refund_t`
--

CREATE TABLE `flight_refund_t` (
  `f_refund_id` char(20) NOT NULL,
  `f_book_id` char(20) NOT NULL,
  `refund_amt` decimal(10,2) NOT NULL,
  `refund_method` varchar(100) NOT NULL,
  `refund_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flight_refund_t`
--

INSERT INTO `flight_refund_t` (`f_refund_id`, `f_book_id`, `refund_amt`, `refund_method`, `refund_date`, `status`) VALUES
('REF187658', 'BK2844637404', 199.84, 'Apple Pay', '2025-07-07 00:00:00', 'complete'),
('REF525634', 'BK2083882917', 207.25, 'Apple Pay', '2025-07-03 00:00:00', 'complete'),
('REF528597', 'BK4933454975', 2691.73, 'Paypal', '2025-07-07 00:00:00', 'complete'),
('REF582543', 'BK1904311170', 330.45, 'Apple Pay', '2025-07-03 00:00:00', 'complete'),
('REF599253', 'BK1040552567', 455.66, 'Google Pay', '2025-07-03 00:00:00', 'complete'),
('REF605670', 'BK3738310410', 2245.82, 'Paypal', '2025-07-07 00:00:00', 'complete'),
('REF633031', 'BK4722235178', 3168.69, 'Amazon Pay', '2025-07-07 00:00:00', 'complete'),
('REF687460', 'BK1694659248', 137.00, 'Debit/Credit Card', '2025-07-03 00:00:00', 'complete'),
('REF922371', 'BK2944032024', 165.33, 'Apple Pay', '2025-07-07 00:00:00', 'complete'),
('REF973572', 'BK1306063556', 114.99, 'Debit/Credit Card', '2025-07-03 00:00:00', 'complete');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `flight_refund_t`
--
ALTER TABLE `flight_refund_t`
  ADD PRIMARY KEY (`f_refund_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
