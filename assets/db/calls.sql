-- phpMyAdmin SQL Dump
-- version 4.0.10.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 07, 2015 at 04:31 AM
-- Server version: 5.5.41-log
-- PHP Version: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `calls`
--

-- --------------------------------------------------------

--
-- Table structure for table `T_EVENT_TYPE`
--

CREATE TABLE IF NOT EXISTS `T_EVENT_TYPE` (
  `EVENT_ID` varchar(22) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `EVENT_NAME` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `DESCRIPTION` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`EVENT_ID`),
  KEY `EVENT_NAME` (`EVENT_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_EVENT_TYPE`
--

INSERT INTO `T_EVENT_TYPE` (`EVENT_ID`, `EVENT_NAME`, `DESCRIPTION`) VALUES
('EVENT_CALL_END', 'Call End', 'Generated when one of the party cancels the call, also generated when the reciever just \r\n\r\ncancels the call.'),
('EVENT_CALL_ESTABLISHED', 'Call Established', 'Generated when the reciever answers the call.'),
('EVENT_DIAL', 'Dialling', 'Generated upon the start of the call.'),
('EVENT_HANG_UP', 'Hang-up', 'Generated when user hangs up the phone.'),
('EVENT_PICK_UP', 'Pick-up', 'Generated when user pick ups the phone.');

-- --------------------------------------------------------

--
-- Table structure for table `T_PHONE_RECORDS`
--

CREATE TABLE IF NOT EXISTS `T_PHONE_RECORDS` (
  `RECORD_ID` int(11) NOT NULL AUTO_INCREMENT,
  `RECORD_EVENT_ID` varchar(22) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `RECORD_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `CALLER` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `RECIEVER` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`RECORD_ID`),
  KEY `RECORD_EVENT_ID` (`RECORD_EVENT_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `T_PHONE_RECORDS`
--

INSERT INTO `T_PHONE_RECORDS` (`RECORD_ID`, `RECORD_EVENT_ID`, `RECORD_DATE`, `CALLER`, `RECIEVER`) VALUES
(1, 'EVENT_PICK_UP', '2015-08-07 01:27:43', '51706420', '53203033'),
(2, 'EVENT_HANG_UP', '2015-08-07 01:27:46', '51706420', ''),
(3, 'EVENT_PICK_UP', '2015-08-07 01:27:43', '52290618', '55146240'),
(4, 'EVENT_HANG_UP', '2015-08-07 01:27:46', '52290618', ''),
(5, 'EVENT_PICK_UP', '2015-08-07 01:27:43', '59311706', '59522918'),
(6, 'EVENT_DIAL', '2015-08-07 01:27:53', '59311706', '59522918'),
(7, 'EVENT_CALL_END', '2015-08-07 01:29:43', '59311706', '59522918'),
(8, 'EVENT_HANG_UP', '2015-08-07 01:42:46', '59311706', '59522918'),
(9, 'EVENT_PICK_UP', '2015-08-07 01:27:43', '55482971', '59003265'),
(10, 'EVENT_HANG_UP', '2015-08-07 01:27:46', '55482971', ''),
(11, 'EVENT_PICK_UP', '2015-08-07 01:27:43', '54658721', '52550720'),
(12, 'EVENT_HANG_UP', '2015-08-07 01:27:46', '54658721', '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `T_PHONE_RECORDS`
--
ALTER TABLE `T_PHONE_RECORDS`
  ADD CONSTRAINT `t_phone_records_ibfk_1` FOREIGN KEY (`RECORD_EVENT_ID`) REFERENCES `T_EVENT_TYPE` (`EVENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
