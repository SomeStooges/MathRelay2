-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 17, 2015 at 10:22 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mathrelay2`
--

-- --------------------------------------------------------

--
-- Table structure for table `answer_table`
--

CREATE TABLE IF NOT EXISTS `answer_table` (
  `SeriesNumber` int(11) NOT NULL,
  `LevelNumber` int(11) NOT NULL,
  `Answer` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `answer_table`
--

INSERT INTO `answer_table` (`SeriesNumber`, `LevelNumber`, `Answer`) VALUES
(12, 3, 'A'),
(12, 2, 'B'),
(12, 1, 'C'),
(1, 3, 'A'),
(1, 2, 'B'),
(1, 1, 'C'),
(2, 3, 'A'),
(2, 2, 'B'),
(2, 1, 'C'),
(3, 3, 'A'),
(3, 2, 'B'),
(3, 1, 'C'),
(4, 3, 'A'),
(4, 2, 'B'),
(4, 1, 'C'),
(5, 3, 'A'),
(5, 2, 'B'),
(5, 1, 'C'),
(13, 3, '3Î /2'),
(14, 3, '3âˆš2'),
(14, 2, '6Î '),
(14, 1, 'âˆž'),
(13, 3, '5Î '),
(15, 3, '1'),
(15, 2, '2'),
(15, 1, '3'),
(15, 3, '15');

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `TeamID` int(32) NOT NULL,
  `Series` int(11) NOT NULL,
  `Award3` int(2) NOT NULL,
  `Answer3` varchar(32) NOT NULL,
  `Award2` int(2) NOT NULL,
  `Answer2` varchar(32) NOT NULL,
  `Award1` int(2) NOT NULL,
  `Answer1` varchar(32) NOT NULL,
  `Timestamp` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `relay_options`
--

CREATE TABLE IF NOT EXISTS `relay_options` (
  `Name` varchar(128) NOT NULL,
  `Value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `relay_options`
--

INSERT INTO `relay_options` (`Name`, `Value`) VALUES
('currentEvent', 'none'),
('numberOfTeams', '50'),
('adminPassword', 'admin'),
('cleanupParagraph', '[type new text here]'),
('displayNumber', '10'),
('displayOptions', '1;1;1;1;1;1'),
('rankingStyle', 'rankingStyle1');

-- --------------------------------------------------------

--
-- Table structure for table `team_data`
--

CREATE TABLE IF NOT EXISTS `team_data` (
  `TeamID` int(10) NOT NULL,
  `TeamNickname` varchar(128) NOT NULL,
  `Password` varchar(128) NOT NULL,
  `Points3` int(10) NOT NULL,
  `Points2` int(10) NOT NULL,
  `Points1` int(10) NOT NULL,
  `Rank` int(10) NOT NULL,
  `FreetimeRank` int(10) NOT NULL,
  `lastCheckTime` int(20) DEFAULT '0',
  `lastPointTime` int(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `team_data`
--

INSERT INTO `team_data` (`TeamID`, `TeamNickname`, `Password`, `Points3`, `Points2`, `Points1`, `Rank`, `FreetimeRank`, `lastCheckTime`, `lastPointTime`) VALUES
(1, '', '723RN', 0, 0, 0, 50, 0, 0, 0),
(2, '', 'BF12RZ', 0, 0, 0, 36, 0, 0, 0),
(3, '', '4L3NM4', 0, 0, 0, 35, 0, 0, 0),
(4, '', 'NWMQCQ', 0, 0, 0, 34, 0, 0, 0),
(5, '', 'R8HJ2S', 0, 0, 0, 33, 0, 0, 0),
(6, '', 'ZD554P', 0, 0, 0, 32, 0, 0, 0),
(7, '', '4HQFKL', 0, 0, 0, 31, 0, 0, 0),
(8, '', '64KNL', 0, 0, 0, 30, 0, 0, 0),
(9, '', 'RDDF71', 0, 0, 0, 29, 0, 0, 0),
(10, '', 'DMG8ML', 0, 0, 0, 28, 0, 0, 0),
(11, '', 'PWFV', 0, 0, 0, 27, 0, 0, 0),
(12, '', 'J8V8FA', 0, 0, 0, 37, 0, 0, 0),
(13, '', 'G3X3EL', 0, 0, 0, 38, 0, 0, 0),
(14, '', 'UFKJX', 0, 0, 0, 39, 0, 0, 0),
(15, '', '56UFK5', 0, 0, 0, 49, 0, 0, 0),
(16, '', '4HQN63', 0, 0, 0, 48, 0, 0, 0),
(17, '', '6GCLZT', 0, 0, 0, 47, 0, 0, 0),
(18, '', 'K2SD4X', 0, 0, 0, 46, 0, 0, 0),
(19, '', 'MET66L', 0, 0, 0, 45, 0, 0, 0),
(20, '', '28E7M5', 0, 0, 0, 44, 0, 0, 0),
(21, '', '2WY5W', 0, 0, 0, 43, 0, 0, 0),
(22, '', 'TQ9YJU', 0, 0, 0, 42, 0, 0, 0),
(23, '', 'G2NEL', 0, 0, 0, 41, 0, 0, 0),
(24, '', 'MM845R', 0, 0, 0, 40, 0, 0, 0),
(25, '', 'EJXE44', 0, 0, 0, 26, 0, 0, 0),
(26, '', 'TPWH6Y', 0, 0, 0, 25, 0, 0, 0),
(27, '', 'W1T8PA', 0, 0, 0, 11, 0, 0, 0),
(28, '', '5ZBMX6', 0, 0, 0, 10, 0, 0, 0),
(29, '', 'GNJ97N', 0, 0, 0, 9, 0, 0, 0),
(30, '', 'U72B6G', 0, 0, 0, 8, 0, 0, 0),
(31, '', '6VC452', 0, 0, 0, 7, 0, 0, 0),
(32, '', 'RG218M', 0, 0, 0, 6, 0, 0, 0),
(33, '', 'HJ3ZLU', 0, 0, 0, 5, 0, 0, 0),
(34, '', '6HWMUQ', 0, 0, 0, 4, 0, 0, 0),
(35, '', 'H4XJ3Z', 0, 0, 0, 3, 0, 0, 0),
(36, '', 'TELJD', 0, 0, 0, 2, 0, 0, 0),
(37, '', 'REQWKW', 0, 0, 0, 12, 0, 0, 0),
(38, '', 'ZWPNCV', 0, 0, 0, 13, 0, 0, 0),
(39, '', '8XRRPF', 0, 0, 0, 14, 0, 0, 0),
(40, '', 'G39DCR', 0, 0, 0, 24, 0, 0, 0),
(41, '', 'FJT9D3', 0, 0, 0, 23, 0, 0, 0),
(42, '', 'KLQP37', 0, 0, 0, 22, 0, 0, 0),
(43, '', 'GM52M', 0, 0, 0, 21, 0, 0, 0),
(44, '', '3M7WRV', 0, 0, 0, 20, 0, 0, 0),
(45, '', 'V6HBL', 0, 0, 0, 19, 0, 0, 0),
(46, '', '8U8Y7Y', 0, 0, 0, 18, 0, 0, 0),
(47, '', 'LF9M3U', 0, 0, 0, 17, 0, 0, 0),
(48, '', 'TCRM3', 0, 0, 0, 16, 0, 0, 0),
(49, '', '8H59KR', 0, 0, 0, 15, 0, 0, 0),
(50, '', '4VDQXD', 0, 0, 0, 1, 0, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
