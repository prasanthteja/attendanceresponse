# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: mysql.wiu.edu (MySQL 5.0.15-log)
# Database: AttendanceTracking
# Generation Time: 2020-01-23 14:15:25 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table advisor
# ------------------------------------------------------------

DROP TABLE IF EXISTS `advisor`;

CREATE TABLE `advisor` (
  `note` longtext NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `id` varchar(50) NOT NULL,
  `noteid` int(100) NOT NULL auto_increment,
  `deleted` int(10) NOT NULL default '0',
  KEY `noteid` (`noteid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table advisorfiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `advisorfiles`;

CREATE TABLE `advisorfiles` (
  `studentid` varchar(50) default NULL,
  `location` longtext,
  `filename` longtext,
  `timestamp` timestamp NULL default NULL,
  `deleted` int(11) NOT NULL default '0',
  `fileid` int(11) NOT NULL auto_increment,
  KEY `fileid` (`fileid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table attendance
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attendance`;

CREATE TABLE `attendance` (
  `id` int(10) NOT NULL auto_increment,
  `studentEcom` varchar(15) NOT NULL,
  `StudentName` varchar(50) NOT NULL,
  `courseStar` int(10) NOT NULL,
  `attendance` varchar(15) NOT NULL default '',
  `attendedDate` date NOT NULL,
  `rank` varchar(15) default NULL,
  PRIMARY KEY  (`id`),
  KEY `courseStar` (`courseStar`),
  KEY `studentEcom` (`studentEcom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table attendance_bkup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attendance_bkup`;

CREATE TABLE `attendance_bkup` (
  `id` int(10) NOT NULL default '0',
  `studentEcom` varchar(15) NOT NULL default '',
  `StudentName` varchar(50) NOT NULL default '',
  `courseStar` int(10) NOT NULL default '0',
  `attendance` varchar(15) NOT NULL default '',
  `attendedDate` date NOT NULL default '0000-00-00',
  `rank` varchar(15) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table attendance_mar20
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attendance_mar20`;

CREATE TABLE `attendance_mar20` (
  `id` int(10) NOT NULL auto_increment,
  `studentEcom` varchar(15) NOT NULL,
  `StudentName` varchar(50) NOT NULL,
  `courseStar` int(10) NOT NULL,
  `attendance` varchar(15) NOT NULL default '',
  `attendedDate` date NOT NULL,
  `rank` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table attendance_mar22
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attendance_mar22`;

CREATE TABLE `attendance_mar22` (
  `id` int(10) NOT NULL auto_increment,
  `studentEcom` varchar(15) NOT NULL,
  `StudentName` varchar(50) NOT NULL,
  `courseStar` int(10) NOT NULL,
  `attendance` varchar(15) NOT NULL default '',
  `attendedDate` date NOT NULL,
  `rank` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table attendancedup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attendancedup`;

CREATE TABLE `attendancedup` (
  `id` int(10) NOT NULL auto_increment,
  `studentEcom` varchar(15) NOT NULL,
  `StudentName` varchar(50) NOT NULL,
  `courseStar` int(10) NOT NULL,
  `attendance` varchar(15) NOT NULL default '',
  `attendedDate` date NOT NULL,
  `rank` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table courseinfo
# ------------------------------------------------------------

DROP TABLE IF EXISTS `courseinfo`;

CREATE TABLE `courseinfo` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL,
  `star` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `cnumber` varchar(50) NOT NULL,
  `term` varchar(50) NOT NULL,
  `ecom` varchar(50) NOT NULL,
  `GA` varchar(10) default NULL,
  `department` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `star` (`star`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table courseinfo_copy
# ------------------------------------------------------------

DROP TABLE IF EXISTS `courseinfo_copy`;

CREATE TABLE `courseinfo_copy` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL,
  `star` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `cnumber` varchar(50) NOT NULL,
  `term` varchar(50) NOT NULL,
  `ecom` varchar(50) NOT NULL,
  `GA` varchar(10) default NULL,
  `department` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `star` (`star`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;



# Dump of table daily_temperatures
# ------------------------------------------------------------

DROP TABLE IF EXISTS `daily_temperatures`;

CREATE TABLE `daily_temperatures` (
  `date` date default NULL,
  `temperature` smallint(6) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table email_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email_log`;

CREATE TABLE `email_log` (
  `ID` int(10) NOT NULL auto_increment,
  `Date` datetime NOT NULL,
  `Message` text NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table excessiveabsence
# ------------------------------------------------------------

DROP TABLE IF EXISTS `excessiveabsence`;

CREATE TABLE `excessiveabsence` (
  `id` int(10) NOT NULL auto_increment,
  `studentEcom` varchar(10) default '0',
  `courseStar` varchar(10) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table handouts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `handouts`;

CREATE TABLE `handouts` (
  `id` int(10) NOT NULL auto_increment,
  `course_id` int(10) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table lab_classes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lab_classes`;

CREATE TABLE `lab_classes` (
  `course` varchar(10) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table ldap_advisors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ldap_advisors`;

CREATE TABLE `ldap_advisors` (
  `Stu_ID` int(10) NOT NULL,
  `AdvisorID` int(10) NOT NULL,
  PRIMARY KEY  (`Stu_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table LeftEarly
# ------------------------------------------------------------

DROP TABLE IF EXISTS `LeftEarly`;

CREATE TABLE `LeftEarly` (
  `id` int(10) NOT NULL,
  `LeftEarlyflag` enum('Y','N') NOT NULL default 'N',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table LeftEarly_bkp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `LeftEarly_bkp`;

CREATE TABLE `LeftEarly_bkp` (
  `id` int(10) NOT NULL,
  `LeftEarlyflag` enum('Y','N') NOT NULL default 'N',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `id` int(20) NOT NULL auto_increment,
  `note` varchar(100) default NULL,
  `studentEcom` varchar(10) NOT NULL,
  `courseStar` varchar(10) NOT NULL,
  `Date` date NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table OARS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `OARS`;

CREATE TABLE `OARS` (
  `stuID` int(10) default NULL,
  `starNo` int(10) default NULL,
  `time` timestamp NULL default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='for OARS system';



# Dump of table rank
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rank`;

CREATE TABLE `rank` (
  `rankID` int(10) default NULL,
  `rankName` varchar(15) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table shareAttendance
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shareAttendance`;

CREATE TABLE `shareAttendance` (
  `ecom` varchar(50) NOT NULL,
  `chairshare` int(10) default NULL,
  `advisorshare` int(10) default NULL,
  `courseid` int(10) NOT NULL,
  KEY `Index 1` (`ecom`),
  KEY `Index 2` (`courseid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table test_copy
# ------------------------------------------------------------

DROP TABLE IF EXISTS `test_copy`;

CREATE TABLE `test_copy` (
  `id` int(10) NOT NULL auto_increment,
  `studentEcom` varchar(15) NOT NULL,
  `StudentName` varchar(50) NOT NULL,
  `courseStar` int(10) NOT NULL,
  `attendance` varchar(15) NOT NULL default '',
  `attendedDate` date NOT NULL,
  `rank` varchar(15) default NULL,
  PRIMARY KEY  (`id`),
  KEY `courseStar` (`courseStar`),
  KEY `studentEcom` (`studentEcom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;



# Dump of table testpreference
# ------------------------------------------------------------

DROP TABLE IF EXISTS `testpreference`;

CREATE TABLE `testpreference` (
  `ID` int(10) NOT NULL auto_increment,
  `studentEcom` varchar(10) NOT NULL,
  `preferredEmail` varchar(30) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `carrier` varchar(20) NOT NULL,
  `SMSalerts` varchar(5) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table tutoring
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tutoring`;

CREATE TABLE `tutoring` (
  `name` varchar(50) NOT NULL,
  `id` int(10) NOT NULL auto_increment,
  `tutor name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `wiu id` int(11) NOT NULL,
  `course` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `issues` varchar(100) NOT NULL,
  `comments` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `wiu id` (`wiu id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table tutoring1
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tutoring1`;

CREATE TABLE `tutoring1` (
  `id` int(50) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `tutor name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `wiu id` int(20) NOT NULL,
  `course` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `issues` varchar(250) NOT NULL,
  `comments` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `wiu id` (`wiu id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table userpreference
# ------------------------------------------------------------

DROP TABLE IF EXISTS `userpreference`;

CREATE TABLE `userpreference` (
  `id` int(10) NOT NULL auto_increment,
  `courseinfo` varchar(50) NOT NULL,
  `preference` varchar(100) NOT NULL,
  `ecom` varchar(50) NOT NULL,
  `absenceLimit` varchar(10) NOT NULL,
  `studentmail` varchar(10) NOT NULL,
  `instructormail` varchar(10) NOT NULL,
  `advisormail` varchar(10) NOT NULL,
  `beforeabslimit` varchar(50) NOT NULL,
  `afterabslimit` varchar(50) NOT NULL,
  `eachabsent` varchar(50) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table userpreference2
# ------------------------------------------------------------

DROP TABLE IF EXISTS `userpreference2`;

CREATE TABLE `userpreference2` (
  `id` int(10) NOT NULL auto_increment,
  `courseinfo` varchar(50) NOT NULL,
  `preference` varchar(100) NOT NULL,
  `ecom` varchar(50) NOT NULL,
  `absenceLimit` varchar(10) NOT NULL,
  `studentmail` varchar(10) NOT NULL,
  `instructormail` varchar(10) NOT NULL,
  `advisormail` varchar(10) NOT NULL,
  `beforeabslimit` varchar(10) NOT NULL,
  `afterabslimit` varchar(10) NOT NULL,
  `sharewithchair` varchar(50) default 'true',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) NOT NULL auto_increment,
  `ecom` varchar(15) default '',
  `role` varchar(20) default '',
  `fName` varchar(50) default '',
  `lName` varchar(50) default '',
  `email` varchar(100) default '',
  `phone` varchar(15) default NULL,
  `dept` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY `usersIndex` (`ecom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table users_copy
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_copy`;

CREATE TABLE `users_copy` (
  `id` int(10) NOT NULL auto_increment,
  `ecom` varchar(15) default '',
  `role` varchar(20) default '',
  `fName` varchar(50) default '',
  `lName` varchar(50) default '',
  `email` varchar(100) default '',
  `phone` varchar(15) default NULL,
  `dept` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;



# Dump of table users_copysc
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_copysc`;

CREATE TABLE `users_copysc` (
  `id` int(10) NOT NULL auto_increment,
  `ecom` varchar(15) default '',
  `role` varchar(20) default '',
  `fName` varchar(50) default '',
  `lName` varchar(50) default '',
  `email` varchar(100) default '',
  `phone` varchar(15) default NULL,
  `dept` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;



# Dump of table usersubscriptions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `usersubscriptions`;

CREATE TABLE `usersubscriptions` (
  `ecom` varchar(50) default NULL,
  `subscribed` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
