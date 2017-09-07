-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 27, 2015 at 02:40 AM
-- Server version: 5.1.53
-- PHP Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lsat`
--

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

CREATE TABLE IF NOT EXISTS `answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `urlImage` text,
  `grade` int(11) NOT NULL,
  `textFeedback` text NOT NULL,
  `imageFeedback` text,
  `correct` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=198 ;


--
-- Table structure for table `answersinwebsincompetence`
--

CREATE TABLE IF NOT EXISTS `answersinwebsincompetence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `answerId` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `webInCompetence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=434 ;

--
-- Table structure for table `competence`
--

CREATE TABLE IF NOT EXISTS `competence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `professor` int(11) NOT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isPublished` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `competenceingroup`
--

CREATE TABLE IF NOT EXISTS `competenceingroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL,
  `competenceId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `difficulty`
--

CREATE TABLE IF NOT EXISTS `difficulty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `professor` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `urlImage` text,
  `difficulty` int(11) NOT NULL,
  `topic` int(11) NOT NULL,
  `professor` int(11) NOT NULL,
  `optionA` int(11) NOT NULL,
  `optionB` int(11) NOT NULL,
  `optionC` int(11) NOT NULL,
  `optionD` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Table structure for table `questionsforstudent`
--

CREATE TABLE IF NOT EXISTS `questionsforstudent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `answered` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=375 ;


-- --------------------------------------------------------

--
-- Table structure for table `questionsinweb`
--

CREATE TABLE IF NOT EXISTS `questionsinweb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionId` int(11) NOT NULL,
  `webId` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=108 ;

-- --------------------------------------------------------

--
-- Table structure for table `studentprogress`
--

CREATE TABLE IF NOT EXISTS `studentprogress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `startedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `finishedDate` timestamp NULL DEFAULT NULL,
  `lastAnsweredQuestion` int(11) NOT NULL,
  `lastAnswerGrade` int(11) NOT NULL DEFAULT '999',
  `firstQuestion` int(11) NOT NULL,
  `webId` int(11) NOT NULL,
  `lastQuestion` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=97 ;

-- --------------------------------------------------------

--
-- Table structure for table `studentrecord`
--

CREATE TABLE IF NOT EXISTS `studentrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `studentId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `competenceId` int(11) NOT NULL,
  `studentProgressId` int(11) NOT NULL,
  `isBlocked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

--
-- Table structure for table `studentsingroup`
--

CREATE TABLE IF NOT EXISTS `studentsingroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL,
  `studentId` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `topic`
--

CREATE TABLE IF NOT EXISTS `topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `mail` text NOT NULL,
  `idNumber` text NOT NULL,
  `salt` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL,
  `role` text NOT NULL,
  `registeredDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `mail`, `idNumber`, `salt`, `password`, `role`, `registeredDate`) VALUES
(1, 'Admin', 'lsatitesm@gmail.com', '', '|†5aw…{.Žã+eøÿ×r~€3ò¼‰€¾j‹õü', '0dc1308c05b3b518aed727aa1ad1b45cbb537a246cf8a02fe9dba629c85fc7b7', 'admin', '2015-02-20 12:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `web`
--

CREATE TABLE IF NOT EXISTS `web` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `professor` int(11) NOT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isPublished` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `websincompetence`
--

CREATE TABLE IF NOT EXISTS `websincompetence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competenceId` int(11) NOT NULL,
  `webId` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `isGraded` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

