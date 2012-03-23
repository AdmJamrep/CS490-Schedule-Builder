-- Author: John Fostek
-- phpMyAdmin SQL Dump
-- version 3.2.2
-- http://www.phpmyadmin.net
--
-- Host: sql.njit.edu
-- Generation Time: Mar 09, 2012 at 01:45 PM
-- Server version: 5.0.91
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `jjf6_proj`
--

-- --------------------------------------------------------

--
-- Table structure for table `class_sections`
--

CREATE TABLE IF NOT EXISTS `class_sections` (
  `section_number` varchar(3) NOT NULL,
  `call_number` int(5) NOT NULL,
  `instructor` varchar(30) NOT NULL,
  `status` varchar(10) NOT NULL,
  `comments` text NOT NULL,
  `max_size` int(100) NOT NULL,
  `current_size` int(100) NOT NULL,
  `course_number` varchar(4) NOT NULL,
  `abbreviation` varchar(10) NOT NULL,
  `semester` varchar(6) NOT NULL,
  `year_offered` year(4) NOT NULL,
  PRIMARY KEY  (`call_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `course_number` varchar(4) NOT NULL,
  `description` text NOT NULL,
  `name` varchar(60) NOT NULL,
  `credits` float NOT NULL,
  `pre-requisites` text NOT NULL,
  `abbreviation` varchar(10) NOT NULL,
  PRIMARY KEY  (`course_number`,`abbreviation`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `saved_schedules`
--

CREATE TABLE IF NOT EXISTS `saved_schedules` (
  `name` varchar(30) NOT NULL,
  `num_identifier` int(11) NOT NULL auto_increment,
  `creation_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `saved_flag` int(2) NOT NULL,
  PRIMARY KEY  (`num_identifier`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `saved_schedule_classes`
--

CREATE TABLE IF NOT EXISTS `saved_schedule_classes` (
  `num_identifier` int(10) NOT NULL,
  `call_number` int(5) NOT NULL,
  PRIMARY KEY  (`num_identifier`,`call_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE IF NOT EXISTS `subjects` (
  `heading` varchar(80) NOT NULL,
  `name` varchar(120) NOT NULL,
  `abbreviation` varchar(10) NOT NULL,
  PRIMARY KEY  (`abbreviation`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `time_and_location`
--

CREATE TABLE IF NOT EXISTS `time_and_location` (
  `day` varchar(4) NOT NULL,
  `start_time` time NOT NULL default '00:00:00',
  `end_time` time NOT NULL default '00:00:00',
  `room` varchar(10) NOT NULL,
  `call_number` int(5) NOT NULL,
  PRIMARY KEY  (`call_number`,`day`,`start_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
