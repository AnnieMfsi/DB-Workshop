-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 22, 2016 at 06:24 PM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Workshop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ws_employee_details`
--

CREATE TABLE IF NOT EXISTS `ws_employee_details` (
  `emp_auto_id` int(5) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `emp_fname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `emp_lname` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `emp_fk_hr_updated_by` varchar(3) CHARACTER SET latin1 NOT NULL,
  `emp_fk_hr_created_by` varchar(3) CHARACTER SET latin1 NOT NULL,
  `emp_created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `emp_updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`emp_auto_id`),
  UNIQUE KEY `emp_id` (`emp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `ws_emp_skill_list`
--

CREATE TABLE IF NOT EXISTS `ws_emp_skill_list` (
  `emp_skill_auto_id` int(3) NOT NULL AUTO_INCREMENT,
  `fk_emp_id` int(3) NOT NULL,
  `fk_skill_list_id` int(3) NOT NULL,
  PRIMARY KEY (`emp_skill_auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Table structure for table `ws_hr_list`
--

CREATE TABLE IF NOT EXISTS `ws_hr_list` (
  `hr_auto_id` int(11) NOT NULL AUTO_INCREMENT,
  `hr_name` varchar(5) NOT NULL,
  PRIMARY KEY (`hr_auto_id`),
  UNIQUE KEY `hr_name` (`hr_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `ws_skill_list`
--

CREATE TABLE IF NOT EXISTS `ws_skill_list` (
  `skill_auto_id` int(11) NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(15) NOT NULL,
  PRIMARY KEY (`skill_auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `ws_stack_detail`
--

CREATE TABLE IF NOT EXISTS `ws_stack_detail` (
  `stack_auto_id` int(3) NOT NULL AUTO_INCREMENT,
  `emp_auto_id_fk` int(11) NOT NULL,
  `stack_id` varchar(20) NOT NULL,
  `stack_name` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`stack_auto_id`),
  UNIQUE KEY `stack_id` (`stack_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
