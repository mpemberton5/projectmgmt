-- phpMyAdmin SQL Dump
-- version 3.1.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 30, 2011 at 04:29 PM
-- Server version: 5.1.35
-- PHP Version: 5.2.9-2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `projectmgmt-prod`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `client_ID` int(11) NOT NULL AUTO_INCREMENT,
  `client_full_name` varchar(150) NOT NULL,
  `client_abbr_name` varchar(30) NOT NULL,
  PRIMARY KEY (`client_ID`),
  KEY `client_name` (`client_full_name`),
  KEY `client_abbr_name` (`client_abbr_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE IF NOT EXISTS `departments` (
  `department_ID` int(20) NOT NULL AUTO_INCREMENT,
  `Dept_Name` varchar(50) NOT NULL,
  PRIMARY KEY (`department_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE IF NOT EXISTS `employees` (
  `employee_ID` int(20) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `Department_ID` int(20) DEFAULT NULL,
  `MedCtrLogin` varchar(20) NOT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `EMail` varchar(40) DEFAULT NULL,
  `JobTitle` varchar(40) DEFAULT NULL,
  `Phone` varchar(12) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  `Level_ID` int(20) DEFAULT NULL,
  `pm_SiteAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `mgmt` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`employee_ID`),
  UNIQUE KEY `MedCtrLogin` (`MedCtrLogin`),
  KEY `Full Name` (`LastName`,`FirstName`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Employee Table Comment' AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT NULL,
  `folder_id` int(11) NOT NULL DEFAULT '75',
  `token` varchar(32) DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploaded_date` timestamp NULL DEFAULT NULL,
  `uploaded_by` int(10) unsigned NOT NULL DEFAULT '0',
  `mime` varchar(50) DEFAULT NULL,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `content` longtext,
  PRIMARY KEY (`file_id`),
  KEY `token` (`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE IF NOT EXISTS `levels` (
  `level_ID` int(4) NOT NULL AUTO_INCREMENT,
  `Parent_ID` int(4) NOT NULL,
  `LevelName` varchar(20) NOT NULL,
  PRIMARY KEY (`level_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_data`
--

CREATE TABLE IF NOT EXISTS `list_data` (
  `list_data_ID` int(11) NOT NULL AUTO_INCREMENT,
  `list_type` int(11) NOT NULL,
  `item_ID` int(11) NOT NULL,
  `item_text` varchar(50) NOT NULL,
  PRIMARY KEY (`list_data_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE IF NOT EXISTS `logins` (
  `login_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `session_key` varchar(25) NOT NULL,
  `ip` varchar(25) NOT NULL,
  `lastaccess` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`login_id`),
  UNIQUE KEY `session_key` (`session_key`(10),`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=674 ;

-- --------------------------------------------------------

--
-- Table structure for table `portlets`
--

CREATE TABLE IF NOT EXISTS `portlets` (
  `portlet_ID` int(11) NOT NULL AUTO_INCREMENT,
  `portlet_type` varchar(20) NOT NULL,
  `portlet_title` varchar(50) NOT NULL,
  `portlet_key` varchar(20) NOT NULL,
  `portlet_description` text NOT NULL,
  PRIMARY KEY (`portlet_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `project_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Project_Name` varchar(50) NOT NULL,
  `Owner_ID` int(4) NOT NULL COMMENT 'Links to Employee ID',
  `Client_ID` int(11) NOT NULL DEFAULT '0',
  `EntityID` int(11) NOT NULL,
  `Category` int(4) DEFAULT NULL COMMENT 'Link to Category table',
  `Priority` varchar(15) NOT NULL COMMENT 'Low,Normal,High',
  `Status` varchar(15) DEFAULT NULL COMMENT 'Planning,Active,On Hold,Archived,Cancelled,Complete',
  `CreationDate` date DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `LastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Description` text NOT NULL,
  `Impact` varchar(15) NOT NULL COMMENT 'Low,Normal,High',
  `CE` tinyint(1) DEFAULT NULL,
  `Managed` tinyint(1) DEFAULT NULL,
  `Contingency` int(2) DEFAULT NULL,
  `milestones` tinyint(4) NOT NULL DEFAULT '0',
  `PercentComplete` int(11) NOT NULL DEFAULT '0',
  `ClientContact` varchar(50) NOT NULL,
  PRIMARY KEY (`project_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=167 ;

-- --------------------------------------------------------

--
-- Table structure for table `proj_templates`
--

CREATE TABLE IF NOT EXISTS `proj_templates` (
  `pt_id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_name` varchar(50) NOT NULL,
  PRIMARY KEY (`pt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `proj_template_details`
--

CREATE TABLE IF NOT EXISTS `proj_template_details` (
  `ptd_id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_id` int(11) NOT NULL COMMENT 'Project Template ID',
  `ptd_parent_id` int(11) NOT NULL,
  `ptd_name` varchar(50) NOT NULL,
  `order_num` int(11) NOT NULL,
  PRIMARY KEY (`ptd_id`),
  KEY `ptd_parent_id` (`ptd_parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `task_ID` int(11) NOT NULL AUTO_INCREMENT,
  `parent_task_ID` int(11) NOT NULL DEFAULT '0',
  `Curr_Task_ID` int(11) DEFAULT '0',
  `order_num` int(11) NOT NULL,
  `Project_ID` int(11) NOT NULL COMMENT 'Link to ID in Projects',
  `ParentProjectLink_ID` int(11) NOT NULL DEFAULT '0',
  `task_name` varchar(150) NOT NULL,
  `Priority` varchar(15) NOT NULL COMMENT 'Low,Normal,High',
  `Status` varchar(15) DEFAULT NULL,
  `weight` int(3) DEFAULT '1',
  `PercentComplete` int(3) NOT NULL COMMENT 'Updated from Task Activity',
  `ipctcmp` int(10) unsigned DEFAULT '0' COMMENT 'Internal pct complete - weighted percent.',
  `Assigned_To_ID` int(11) DEFAULT NULL COMMENT 'Link to Employees',
  `Description` text NOT NULL,
  `Start_Date` date DEFAULT NULL,
  `End_Date` date DEFAULT NULL,
  `LastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`task_ID`),
  KEY `ParentProjectLink_ID` (`ParentProjectLink_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1629 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_notes`
--

CREATE TABLE IF NOT EXISTS `task_notes` (
  `note_ID` int(11) NOT NULL AUTO_INCREMENT,
  `task_ID` int(11) NOT NULL COMMENT 'Link to Task ID',
  `project_ID` int(11) NOT NULL,
  `user_ID` int(11) NOT NULL,
  `Note` text NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `PercentComplete` int(3) NOT NULL DEFAULT '0' COMMENT 'On EVERY update - update parent task % complete',
  PRIMARY KEY (`note_ID`),
  KEY `project-task` (`project_ID`,`task_ID`,`TimeStamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=981 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_prefs`
--

CREATE TABLE IF NOT EXISTS `user_prefs` (
  `user_prefs_ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_ID` int(11) NOT NULL,
  `pref_type` varchar(20) NOT NULL,
  `value1` varchar(50) DEFAULT NULL,
  `value2` varchar(50) DEFAULT NULL,
  `value3` varchar(50) DEFAULT NULL,
  `value4` varchar(50) DEFAULT NULL,
  `value5` varchar(50) DEFAULT NULL,
  `value6` varchar(50) DEFAULT NULL,
  `value7` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_prefs_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;
