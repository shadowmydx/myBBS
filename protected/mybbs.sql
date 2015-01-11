-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-01-11 08:27:28
-- 服务器版本： 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mybbs`
--

-- --------------------------------------------------------

--
-- 表的结构 `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `sub_id` int(11) NOT NULL DEFAULT '0',
  `issue_id` int(11) NOT NULL DEFAULT '0',
  `whichpart` int(11) DEFAULT NULL,
  PRIMARY KEY (`sub_id`,`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `content`
--

INSERT INTO `content` (`sub_id`, `issue_id`, `whichpart`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1);

-- --------------------------------------------------------

--
-- 表的结构 `issue`
--

CREATE TABLE IF NOT EXISTS `issue` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `pubdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`issue_id`),
  KEY `user_id` (`user_id`),
  KEY `sub_id` (`sub_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `issue`
--

INSERT INTO `issue` (`issue_id`, `sub_id`, `content`, `pubdate`, `user_id`) VALUES
(1, 1, 'æ¶ˆç­ä¸‡æ¶å…±åŒª', '2015-01-09 15:02:10', 1),
(2, 2, 'ä¸€å®šè¦åæ”»å¤§é™†', '2015-01-09 15:02:10', 1),
(3, 3, 'å“¦å“¦ï¼Œå‘å¸ƒä¼¼ä¹Žè¦æˆåŠŸï¼Ÿ', '2015-01-09 15:47:39', 1),
(4, 4, 'æ¶ˆç­ä¸‡æ¶å…±åŒª', '2015-01-11 06:40:07', 1);

-- --------------------------------------------------------

--
-- 表的结构 `subject`
--

CREATE TABLE IF NOT EXISTS `subject` (
  `sub_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(400) NOT NULL,
  `pubdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `totalpart` int(11) DEFAULT NULL,
  PRIMARY KEY (`sub_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `subject`
--

INSERT INTO `subject` (`sub_id`, `name`, `pubdate`, `user_id`, `totalpart`) VALUES
(1, 'æˆ¡ä¹±å»ºå›½', '2015-01-09 15:02:10', 1, 1),
(2, 'æ¯‹å¿˜åœ¨èŽ’', '2015-01-09 15:02:10', 1, 1),
(3, 'ä»Šå¤©ä¹Ÿè¾›è‹¦äº†', '2015-01-09 15:47:39', 1, 1),
(4, 'åæ”»å¤§é™†', '2015-01-11 06:40:07', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `totalsubject`
--

CREATE TABLE IF NOT EXISTS `totalsubject` (
  `totalsub` int(11) NOT NULL,
  PRIMARY KEY (`totalsub`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `totalsubject`
--

INSERT INTO `totalsubject` (`totalsub`) VALUES
(4);

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`) VALUES
(1, 'wmydx', '123');

--
-- 限制导出的表
--

--
-- 限制表 `issue`
--
ALTER TABLE `issue`
  ADD CONSTRAINT `issue_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `issue_ibfk_2` FOREIGN KEY (`sub_id`) REFERENCES `subject` (`sub_id`);

--
-- 限制表 `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
