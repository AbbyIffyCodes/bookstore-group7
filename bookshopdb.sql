-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2026 at 12:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookshopdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `BookID` int(11) NOT NULL,
  `CustomBookID` varchar(20) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Stock` int(11) NOT NULL DEFAULT 0,
  `Price` decimal(10,2) NOT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `Category` varchar(50) NOT NULL,
  `Image` varchar(255) DEFAULT 'images/default_cover.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`BookID`, `CustomBookID`, `Title`, `Stock`, `Price`, `CreatedAt`, `Category`, `Image`) VALUES
(1, 'TXP1001', 'THE ALCHEMIST', 14, 450.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXP1001.jpg'),
(2, 'TXP1002', 'ATOMIC HABITS', 27, 520.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXP1002.jpg'),
(3, 'TXP1003', 'A SONG OF ICE AND FIRE', 32, 2225.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXP1003.jpg'),
(4, 'TXP1004', 'RICH DAD POOR DAD', 8, 570.00, '2026-09-02 11:07:50', 'Business', 'images/TXP1004.jpg'),
(5, 'TXP1005', 'CLEAN CODE', 5, 1250.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1005.jpg'),
(6, 'TXP1006', 'THE PRAGMATIC PROGRAMMER', 18, 1400.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1006.jpg'),
(7, 'TXP1007', 'DESIGN PATTERNS', 8, 1650.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1007.jpg'),
(8, 'TXP1008', 'INTRODUCTION TO ALGORITHMS', 10, 2100.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1008.jpg'),
(9, 'TXP1009', 'YOU DONT KNOW JS YET', 25, 850.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1009.jpg'),
(10, 'TXP1010', 'PYTHON CRASH COURSE', 30, 950.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1010.jpg'),
(11, 'TXP1011', 'HEAD FIRST JAVA', 14, 1100.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1011.jpg'),
(12, 'TXP1012', 'LEARNING SQL', 20, 780.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1012.jpg'),
(13, 'TXP1013', 'PHP AND MYSQL WEB DEVELOPMENT', 16, 990.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1013.jpg'),
(14, 'TXP1014', 'GO PROGRAMMING LANGUAGE', 11, 1350.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1014.jpg'),
(15, 'TXP1015', 'REFACTORING', 7, 1500.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1015.jpg'),
(16, 'TXP1016', 'SYSTEM DESIGN INTERVIEW', 22, 1800.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1016.jpg'),
(17, 'TXP1017', 'STRUCTURE AND INTERPRETATION OF COMPUTER PROGRAMS', 5, 2400.00, '2026-09-02 11:07:50', 'Programming', 'images/TXP1017.jpg'),
(18, 'TXF1001', 'TO KILL A MOCKINGBIRD', 20, 480.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1001.jpg'),
(19, 'TXF1002', '1984', 40, 420.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1002.jpg'),
(20, 'TXF1003', 'THE GREAT GATSBY', 18, 390.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1003.jpg'),
(21, 'TXF1004', 'PRIDE AND PREJUDICE', 25, 450.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1004.jpg'),
(22, 'TXF1005', 'THE HOBBIT', 30, 650.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1005.jpg'),
(23, 'TXF1006', 'HARRY POTTER AND THE PHILOSOPHERS STONE', 50, 750.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1006.jpg'),
(24, 'TXF1007', 'THE CATCHER IN THE RYE', 15, 410.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1007.jpg'),
(25, 'TXF1008', 'LORD OF THE RINGS FELLOWSHIP', 22, 890.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1008.jpg'),
(26, 'TXF1009', 'CRIME AND PUNISHMENT', 12, 580.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1009.jpg'),
(27, 'TXF1010', 'ONE HUNDRED YEARS OF SOLITUDE', 14, 620.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1010.jpg'),
(28, 'TXF1011', 'THE KITE RUNNER', 19, 520.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1011.jpg'),
(29, 'TXF1012', 'BRAVE NEW WORLD', 21, 460.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1012.jpg'),
(30, 'TXF1013', 'THE COUNT OF MONTE CRISTO', 9, 820.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1013.jpg'),
(31, 'TXF1014', 'Fahrenheit 451', 16, 430.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1014.jpg'),
(32, 'TXF1015', 'THE BOOK THIEF', 28, 550.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1015.jpg'),
(33, 'TXF1016', 'LIFE OF PI', 24, 490.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1016.jpg'),
(34, 'TXF1017', 'THE SHADOW OF THE WIND', 13, 610.00, '2026-09-02 11:07:50', 'Fiction', 'images/TXF1017.jpg'),
(35, 'TXB1001', 'THE LEAN STARTUP', 22, 720.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1001.jpg'),
(36, 'TXB1002', 'ZERO TO ONE', 35, 680.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1002.jpg'),
(37, 'TXB1003', 'GOOD TO GREAT', 18, 850.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1003.jpg'),
(38, 'TXB1004', 'THE INTELLIGENT INVESTOR', 15, 950.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1004.jpg'),
(39, 'TXB1005', 'SHOEDOG', 26, 780.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1005.jpg'),
(40, 'TXB1006', 'THE PERSONAL MBA', 20, 890.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1006.jpg'),
(41, 'TXB1007', 'BLUE OCEAN STRATEGY', 12, 820.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1007.jpg'),
(42, 'TXB1008', 'THINK AND GROW RICH', 45, 380.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1008.jpg'),
(43, 'TXB1009', 'THE HARD THING ABOUT HARD THINGS', 16, 760.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1009.jpg'),
(44, 'TXB1010', 'PRINCIPLES BY RAY DALIO', 10, 1200.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1010.jpg'),
(45, 'TXB1011', 'THE E-MYTH REVISITED', 14, 640.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1011.jpg'),
(46, 'TXB1012', 'CAPITAL IN THE TWENTY FIRST CENTURY', 8, 1450.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1012.jpg'),
(47, 'TXB1013', 'START WITH WHY', 30, 690.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1013.jpg'),
(48, 'TXB1014', 'THE INNOVATORS DILEMMA', 11, 880.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1014.jpg'),
(49, 'TXB1015', 'PROFIT FIRST', 19, 650.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1015.jpg'),
(50, 'TXB1016', 'BUILD TO LAST', 13, 810.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1016.jpg'),
(51, 'TXB1017', 'MEASURE WHAT MATTERS', 17, 730.00, '2026-09-02 11:07:50', 'Business', 'images/TXB1017.jpg'),
(52, 'TXS1001', 'A BRIEF HISTORY OF TIME', 25, 580.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1001.jpg'),
(53, 'TXS1002', 'COSMOS BY CARL SAGAN', 20, 690.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1002.jpg'),
(54, 'TXS1003', 'THE SELFISH GENE', 15, 750.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1003.jpg'),
(55, 'TXS1004', 'ASTROPHYSICS FOR PEOPLE IN A HURRY', 32, 490.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1004.jpg'),
(56, 'TXS1005', 'THE GENE AN INTIMATE HISTORY', 11, 890.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1005.jpg'),
(57, 'TXS1006', 'WHAT IF BY RANDALL MUNROE', 18, 620.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1006.jpg'),
(58, 'TXS1007', 'THE EMPEROR OF ALL MALADIES', 9, 920.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1007.jpg'),
(59, 'TXS1008', 'QUANTUM MECHANICS THE THEORETICAL MINIMUM', 14, 830.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1008.jpg'),
(60, 'TXS1009', 'THE ELEGANT UNIVERSE', 12, 710.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1009.jpg'),
(61, 'TXS1010', 'SIX EASY PIECES', 22, 450.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1010.jpg'),
(62, 'TXS1011', 'THE MAN WHO MISTOOK HIS WIFE FOR A HAT', 16, 540.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1011.jpg'),
(63, 'TXS1012', 'THE DEMON HAUNTED WORLD', 19, 670.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1012.jpg'),
(64, 'TXS1013', 'INCOGNITO THE SECRET LIVES OF THE BRAIN', 21, 590.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1013.jpg'),
(65, 'TXS1014', 'THE ORDER OF TIME', 13, 630.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1014.jpg'),
(66, 'TXS1015', 'GUT BY GIULIA ENDERS', 17, 510.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1015.jpg'),
(67, 'TXS1016', 'PARASITE REX', 10, 560.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1016.jpg'),
(68, 'TXS1017', 'THE BOD Y A GUIDE FOR OCCUPANTS', 23, 780.00, '2026-09-02 11:07:50', 'Science', 'images/TXS1017.jpg'),
(69, 'TXH1001', 'SAPIENS A BRIEF HISTORY OF HUMANKIND', 40, 850.00, '2026-09-02 11:07:50', 'History', 'images/TXH1001.jpg'),
(70, 'TXH1002', 'GUNS GERMS AND STEEL', 18, 790.00, '2026-09-02 11:07:50', 'History', 'images/TXH1002.jpg'),
(71, 'TXH1003', 'THE SILK ROADS', 15, 880.00, '2026-09-02 11:07:50', 'History', 'images/TXH1003.jpg'),
(72, 'TXH1004', 'HOMO DEUS A BRIEF HISTORY OF TOMORROW', 22, 820.00, '2026-09-02 11:07:50', 'History', 'images/TXH1004.jpg'),
(73, 'TXH1005', 'THE RISE AND FALL OF THE THIRD REICH', 8, 1350.00, '2026-09-02 11:07:50', 'History', 'images/TXH1005.jpg'),
(74, 'TXH1006', 'A PEOPLE HISTORY OF THE UNITED STATES', 12, 920.00, '2026-09-02 11:07:50', 'History', 'images/TXH1006.jpg'),
(75, 'TXH1007', 'THE PLANTAGENETS', 10, 780.00, '2026-09-02 11:07:50', 'History', 'images/TXH1007.jpg'),
(76, 'TXH1008', 'SPQR A HISTORY OF ANCIENT ROME', 14, 840.00, '2026-09-02 11:07:50', 'History', 'images/TXH1008.jpg'),
(77, 'TXH1009', 'THE ANARCHY BY WILLIAM DALRYMPLE', 16, 910.00, '2026-09-02 11:07:50', 'History', 'images/TXH1009.jpg'),
(78, 'TXH1010', 'POSTWAR HISTORY OF EUROPE SINCE 1945', 7, 1100.00, '2026-09-02 11:07:50', 'History', 'images/TXH1010.jpg'),
(79, 'TXH1011', 'THE CRUSADES BY THOMAS ASBRIDGE', 11, 870.00, '2026-09-02 11:07:50', 'History', 'images/TXH1011.jpg'),
(80, 'TXH1012', 'GENGHIS KHAN AND THE MAKING OF THE MODERN WORLD', 20, 680.00, '2026-09-02 11:07:50', 'History', 'images/TXH1012.jpg'),
(81, 'TXH1013', 'BATTLING THE GODS ANCIENT ATHEISM', 9, 620.00, '2026-09-02 11:07:50', 'History', 'images/TXH1013.jpg'),
(82, 'TXH1014', 'THE LESSONS OF HISTORY', 25, 410.00, '2026-09-02 11:07:50', 'History', 'images/TXH1014.jpg'),
(83, 'TXH1015', 'THE OTTOMANS KINGS AND EMPIRES', 13, 760.00, '2026-09-02 11:07:50', 'History', 'images/TXH1015.jpg'),
(84, 'TXH1016', 'CHERNOBYL HISTORY OF A TRAGEDY', 17, 720.00, '2026-09-02 11:07:50', 'History', 'images/TXH1016.jpg'),
(85, 'TXSH1001', 'THE 7 HABITS OF HIGHLY EFFECTIVE PEOPLE', 30, 620.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1001.jpg'),
(86, 'TXSH1002', 'HOW TO WIN FRIENDS AND INFLUENCE PEOPLE', 45, 450.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1002.jpg'),
(87, 'TXSH1003', 'DEEP WORK', 28, 670.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1003.jpg'),
(89, 'TXSH1005', 'CAN T HURT ME', 20, 780.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1005.jpg'),
(90, 'TXSH1006', 'THINKING FAST AND SLOW', 18, 820.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1006.jpg'),
(91, 'TXSH1007', 'THE POWER OF HABIT', 24, 610.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1007.jpg'),
(92, 'TXSH1008', 'PSYCHOLOGY OF MONEY', 40, 520.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1008.jpg'),
(93, 'TXSH1009', 'FOUR THOUSAND WEEKS', 15, 690.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1009.jpg'),
(94, 'TXSH1010', 'EGO IS THE ENEMY', 22, 580.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1010.jpg'),
(95, 'TXSH1011', 'THE OBSTACLE IS THE WAY', 21, 570.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1011.jpg'),
(96, 'TXSH1012', 'MAKE YOUR BED', 32, 390.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1012.jpg'),
(97, 'TXSH1013', 'GETTING THINGS DONE', 16, 640.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1013.jpg'),
(98, 'TXSH1014', 'MINDSET BY CAROL DWECK', 25, 590.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1014.jpg'),
(99, 'TXSH1015', 'ESSENTIALISM', 19, 630.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1015.jpg'),
(100, 'TXSH1016', 'THE 5 AM CLUB', 27, 540.00, '2026-09-02 11:07:50', 'Self-Help', 'images/TXSH1016.jpg'),
(110, 'TXS1018', 'ENCYCLOPEDIA OF MAMMALS', 12, 120.00, '2026-09-08 15:35:09', 'Science', 'images/TXS1018_1788860109.jpg'),
(111, 'TXP1018', 'TESTING MVC', 1, 101.00, '2026-09-11 15:02:06', 'Programming', '../public/images/TXP1018_1789117326.png');

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `OrderItemID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `BookID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `UnitPrice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`OrderItemID`, `OrderID`, `BookID`, `Quantity`, `UnitPrice`) VALUES
(1, 1, 1, 1, 450.00),
(2, 1, 2, 1, 520.00),
(3, 2, 5, 5, 1250.00),
(4, 2, 3, 1, 2225.00),
(5, 3, 1, 1, 450.00),
(6, 3, 2, 1, 520.00),
(7, 3, 3, 1, 2225.00),
(8, 4, 5, 1, 1250.00),
(9, 5, 3, 1, 2225.00),
(10, 6, 5, 1, 1250.00),
(11, 6, 3, 1, 2225.00),
(12, 6, 4, 1, 570.00),
(13, 6, 1, 1, 450.00),
(14, 7, 11, 2, 1100.00),
(15, 7, 12, 3, 780.00),
(16, 7, 10, 5, 950.00),
(17, 7, 14, 1, 1350.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `OrderNumber` varchar(20) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `OrderType` varchar(20) NOT NULL,
  `OrderDate` datetime DEFAULT current_timestamp(),
  `Status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `OrderNumber`, `UserID`, `TotalAmount`, `OrderType`, `OrderDate`, `Status`) VALUES
(1, 'ORD1038', 4, 1030.00, 'Online', '2026-09-03 00:19:04', 'Pending'),
(2, 'ORD-1788373651', 2, 8475.00, 'In Store', '2026-09-03 00:27:31', 'Completed'),
(3, 'ORD6618', 4, 3255.00, 'Online', '2026-09-09 13:31:41', 'Pending'),
(4, 'ORD-1789097690', 2, 1250.00, 'In Store', '2026-09-11 09:34:50', 'Completed'),
(5, 'ORD-1789097754', 2, 2225.00, 'In Store', '2026-09-11 09:35:54', 'Completed'),
(6, 'ORD-1789120125', 2, 4495.00, 'In Store', '2026-09-11 15:48:45', 'Completed'),
(7, 'ORD2243', 4, 10700.00, 'Online', '2026-09-11 16:04:11', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `CustomUserID` varchar(10) DEFAULT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` varchar(20) NOT NULL,
  `Status` varchar(10) NOT NULL DEFAULT 'Active',
  `ShippingAddress` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `CustomUserID`, `FullName`, `Email`, `Phone`, `PasswordHash`, `Role`, `Status`, `ShippingAddress`, `CreatedAt`) VALUES
(1, 'T001', 'ABTAHEE', 'admin@bookshop.com', '01929722066', '$2y$10$5EIkOJw2vgK6VjgqIvC.iuqD4QNVZ5p9hBPPTerfbFbDYFtPe6guq', 'ADMIN', 'Active', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', '2026-09-02 11:00:10'),
(2, 'T002', 'MAYESHA', 'employee@bookshop.com', '01920936124', '$2y$10$LbR2y7V89FE/Zmr7/f9yLeQoJfp8hC9AxnbF9uHvqlDRkgIP1W69K', 'EMPLOYEE', 'Active', NULL, '2026-09-02 11:00:10'),
(3, 'T003', 'TAHMID', 'tahmid01@bookshop.com', '01941831102', '$2y$10$4FzqE1fSJVAYrTjGqnD.y.PZDuCbUpJF1kfzrAABbMyccVu7oXMe.', 'CUSTOMER', 'Active', 'House 19, Road 18, Road 15, Dhanmondi, Dhaka', '2026-09-02 11:00:10'),
(4, 'T004', 'ZOHRA', 'customer@bookshop.com', '01924701696', '$2y$10$2SpYhWw9YWVBl4ROspxJx.XhbMuaUGE28bRWJkSlxx/5QxERzDTpq', 'CUSTOMER', 'Active', 'House 222, Road 122, Block E, MIRPUR, Dhaka', '2026-09-02 11:00:10'),
(5, 'T005', 'RAHIM UDDIN', 'rahim_admin@bookshop.com', '01939733695', '$2y$10$AtvjCcNgxNLk1E8u0iucReEiBjZ7GlMyeFfUCPrH1uw0epaEBsTL2', 'ADMIN', 'ACTIVE', NULL, '2026-09-02 11:00:10'),
(6, 'T006', 'SULTANA KAMAL', 'sultana_admin@bookshop.com', '01935860105', '$2y$10$cZ6C3xSZfHed.GITXHWcyOCAwxzYbyiqkhFWHNDQgw1MQW7UEs9P.', 'ADMIN', 'ACTIVE', NULL, '2026-09-02 11:00:10'),
(7, 'T007', 'TANVIR HASSAN', 'tanvir_employee@bookshop.com', '01950099446', '$2y$10$g4YjVzjVSdZytHsGHDNhdOJMA4UpnuF1kLucgfL82IWxqjdOp4uza', 'EMPLOYEE', 'ACTIVE', NULL, '2026-09-02 11:00:10'),
(8, 'T008', 'NUSHAT CHOWDHURY', 'nushat_employee@bookshop.com', '01942916916', '$2y$10$IBtDhuqmyGmCN1Y8hwz97eWsQr1C73y8xWvUE5EWvWfQIUvk68eFe', 'EMPLOYEE', 'Active', NULL, '2026-09-02 11:00:10'),
(9, 'T009', 'AREEB AHMED', 'areeb_employee@bookshop.com', '01954286246', '$2y$10$84a1r.zHaEaSOV4CpGljReZzlFlSHHCKC7/rbk9FrLwbNRV/TTjlG', 'EMPLOYEE', 'Active', NULL, '2026-09-02 11:00:10'),
(10, 'T010', 'MEHREEN ISLAM', 'mehreen_employee@bookshop.com', '01942680487', '$2y$10$yP8sC.6718fy1gT7sE/7j../O7yOtlCJE9qIjTCcmj8obid5xDQve', 'EMPLOYEE', 'Active', NULL, '2026-09-02 11:00:10'),
(11, 'T011', 'SAKIB REZA', 'sakib_employee@bookshop.com', '01940543700', '$2y$10$lo8SKLz6uDY8tVYveV9JauRys/28KP0iPYAAiavQ7Tlf8SiSkzt9W', 'EMPLOYEE', 'Active', NULL, '2026-09-02 11:00:10'),
(12, 'T012', 'FARHANA YEASMIN', 'farhana_employee@bookshop.com', '01964677040', '$2y$10$ONcvEfcxTlgFm28wi4QXYePD6Ef2E0J1AMyCNaqPdM6U/n0TujLCO', 'EMPLOYEE', 'Active', NULL, '2026-09-02 11:00:10'),
(13, 'T013', 'ZUBAYER ALAM', 'zubayer_employee@bookshop.com', '01911754102', '$2y$10$ju2n1ELL6amLTpkkvs4dDOTupmqWS7BKtdZ9oHBctJ8HzQZICgGOa', 'EMPLOYEE', 'Active', NULL, '2026-09-02 11:00:10'),
(14, 'T014', 'SAMIHA KHAN', 'samiha_employee@bookshop.com', '01934739396', '$2y$10$75J85kMivo2VbbrF0fPzx.eOXhTP/aJlYFpqVI6WeagNRMpROY/AW', 'EMPLOYEE', 'ACTIVE', NULL, '2026-09-02 11:00:10'),
(15, 'T015', 'ARIF KABIR', 'arif_customer@bookshop.com', '01910131844', '$2y$10$h78grHZ1f6zQ7Wjv/G6PVedVW8xnVKzygWUljzVDDhvFzm1k1Bfm.', 'CUSTOMER', 'ACTIVE', 'House 65, Road 5, Sector 14, Uttara, Dhaka', '2026-09-02 11:00:10'),
(16, 'T016', 'NILA HOQUE', 'nila_customer@bookshop.com', '01951947431', '$2y$10$U00HDpRbGIincVvLwKjg8ukRVUu32goFSFhFNorO9tC23CaLSabfC', 'CUSTOMER', 'ACTIVE', 'House 44, Road 16, Gulshan, Dhaka', '2026-09-02 11:00:10'),
(17, 'T017', 'MAHMUDUL HASAN', 'mahmudul_customer@bookshop.com', '01982343079', '$2y$10$.ONxkJmR0uDfASh02D9Y/OnIlVfJTG/2ak6StnefsYHGXM2j5sY4W', 'CUSTOMER', 'ACTIVE', 'House 12, Road 4, Block D, Banani, Dhaka', '2026-09-02 11:00:10'),
(18, 'T018', 'SABRINA JAHAN', 'sabrina_customer@bookshop.com', '01936922297', '$2y$10$sY2RIHZOU9CRr3I0X6yhEeZ0IBFw/Rg.gHfYSaJt4wrRjBGxyIUMy', 'CUSTOMER', 'ACTIVE', 'House 64, Road 6, Block B, Mirpur, Dhaka', '2026-09-02 11:00:10'),
(19, 'T019', 'FAYSAL AHMED', 'faysal_customer@bookshop.com', '01991024423', '$2y$10$kdaQ/8U64LtI5GOFR6nvdOHNQQhdP.L0FjIW6acko6jcnssECHz9q', 'CUSTOMER', 'ACTIVE', 'House 85, Road 11, Sector 10, Uttara, Dhaka', '2026-09-02 11:00:10'),
(20, 'T020', 'TASNEEM ZAHRA', 'tasneem_customer@bookshop.com', '01928909519', '$2y$10$roSz/Lz9XqXClbzXEcdlbeSZsju463VXVsa2xy2p55zgsYUA5EdSq', 'CUSTOMER', 'ACTIVE', 'House 5, Road 13, Road 22, Dhanmondi, Dhaka', '2026-09-02 11:00:10'),
(21, 'T021', 'IMTIAZ MAHMOOD', 'imtiaz_customer@bookshop.com', '01922609304', '$2y$10$nJ6JXweod7dzpuvROvv5pes.VCFh6kVn6/HNqZg6isdKSLwQEMQGm', 'CUSTOMER', 'ACTIVE', 'House 36, Road 7, Gulshan, Dhaka', '2026-09-02 11:00:10'),
(22, 'T022', 'NOWSHIN SHARMA', 'nowshin_customer@bookshop.com', '01918152234', '$2y$10$z.Q5V4uPzYgEf/39/jnVKuJsBcesMzYBOkQW6TBp0xjEHbGYHidxW', 'CUSTOMER', 'ACTIVE', 'House 60, Road 15, Gulshan, Dhaka', '2026-09-02 11:00:10'),
(23, 'T023', 'KAZI RAFIQ', 'kazi_customer@bookshop.com', '01974863323', '$2y$10$sjKAajh9FtYNFNxDY7i/7uTACweU.P81aQVIg.EZwebTB3..jdrCe', 'CUSTOMER', 'Active', 'House 29, Road 6, Block D, Banani, Dhaka', '2026-09-02 11:00:10'),
(24, 'T024', 'LAMIYA KHANAM', 'lamiya_customer@bookshop.com', '01972653547', '$2y$10$fPdA3.Ic/DpMWbTkC5eC2e2H8F0.3sK0deu3LVO2LV/tXSlmUTlRy', 'CUSTOMER', 'Active', 'House 89, Road 8, Sector 6, Uttara, Dhaka', '2026-09-02 11:00:10'),
(26, 'T026', 'FOYSAL AHMED', 'foysal_admin@bookshop.com', '01929722000', '$2y$10$jb6STveGZyuXgXXDrOrzXOAJxGTXrmiaZFGdunndXzAQS6WHep8uW', 'ADMIN', 'Active', 'PARIS ROAD,MIRPUR-10', '2026-09-02 16:03:14'),
(27, 'T027', 'SHIKDAR', 'shikdar_customer@bookshop.com', NULL, '$2y$10$ZiPQku18rE7y45jCJGoHveMb6qNUYOxYdpigc0MWnxxcP0fktczua', 'CUSTOMER', 'Active', NULL, '2026-09-02 22:43:02'),
(28, 'T028', 'JOHN DOE', 'doe_employee@bookshop.com', NULL, '$2y$10$s/TlgoQ8EnSPbtnDweSn/ebKkY2RgdfiEwgrIMKYXbMPewDbXm3wW', 'EMPLOYEE', 'Active', NULL, '2026-09-02 23:04:09'),
(29, 'T029', 'RAHIDUL ISLAM', 'rahidul_customer@bookshop.com', NULL, '$2y$10$tpVdLpWxiCvZpraOaWEnQOEmEaA0uLEDp4VDifpAb4r/PT2rrLCc.', 'CUSTOMER', 'Active', NULL, '2026-09-08 13:37:41'),
(30, 'T029', 'TESTING101', 'testing_customer@bookshop.com', NULL, '$2y$10$cXhmxkGXMiOEkRhBFrUpn.uUKMJcSbsDUSIHiF/PfvZDIvXH6SIHm', 'CUSTOMER', 'Active', NULL, '2026-09-08 14:29:41'),
(31, 'T031', 'SIGNUP TESTING PROP', 'singuptesting_customer@bookshop.com', '01929722101', '$2y$10$yEJRSz8PFMRhn7WdF4veTu94v5bL3MBXVfLWMQ3qwTDghB47KDusS', 'CUSTOMER', 'Active', NULL, '2026-09-08 14:32:22'),
(32, 'T032', 'Muhammad Tahmid Abtahee', 'muhammad_admin@bookshop.com', NULL, '$2y$10$JsyR9bk7hGjmkEfUUm/YU.6UMxlnJXFuEps6pGli8ihsdGUAsQZTq', 'ADMIN', 'Active', NULL, '2026-09-11 15:18:06'),
(33, 'T033', 'xyz', 'xyz_customer@bookshop.com', NULL, '$2y$10$kYCkbbFoXJP9HsR2s0qYbe2.8zFe/pPj1IxK7VlgHtfj88I9/EZgq', 'CUSTOMER', 'Active', NULL, '2026-09-11 15:19:12'),
(34, 'T034', 'test101user', 'test101_customer@bookshop.com', NULL, '$2y$10$mwsjxxfzLQtA101/qFGQJuE.ZKzBaTyqEQOvd96rfk/LpYsDPRE12', 'CUSTOMER', 'Active', NULL, '2026-09-11 15:42:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`BookID`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `BookID` (`BookID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `OrderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
