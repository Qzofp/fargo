/*
SQLyog Ultimate v11.11 (32 bit)
MySQL - 5.5.27 : Database - fargo
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`fargo` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `fargo`;

/*Table structure for table `episodes` */

DROP TABLE IF EXISTS `episodes`;

CREATE TABLE `episodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `episodeid` int(11) DEFAULT NULL,
  `tvshowid` int(11) DEFAULT NULL,
  `hide` tinyint(4) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `originaltitle` text,
  `rating` decimal(10,2) DEFAULT NULL,
  `writer` text,
  `director` text,
  `cast` text,
  `plot` text,
  `playcount` smallint(6) DEFAULT NULL,
  `episode` smallint(6) DEFAULT NULL,
  `firstaired` date DEFAULT NULL,
  `lastplayed` datetime DEFAULT NULL,
  `dateadded` datetime DEFAULT NULL,
  `votes` int(11) DEFAULT NULL,
  `file` text,
  `showtitle` text,
  `season` smallint(6) DEFAULT NULL,
  `audio` text,
  `video` text,
  `runtime` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_episodeid` (`episodeid`),
  KEY `ix_tvshowid` (`tvshowid`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8;

/*Table structure for table `genres` */

DROP TABLE IF EXISTS `genres`;

CREATE TABLE `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media` varchar(8) DEFAULT NULL,
  `genre` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_genre` (`media`,`genre`)
) ENGINE=InnoDB AUTO_INCREMENT=4357 DEFAULT CHARSET=utf8;

/*Table structure for table `genretomovie` */

DROP TABLE IF EXISTS `genretomovie`;

CREATE TABLE `genretomovie` (
  `genreid` int(11) NOT NULL,
  `movieid` int(11) NOT NULL,
  UNIQUE KEY `ix_genreid` (`genreid`,`movieid`),
  UNIQUE KEY `ix_movieid` (`movieid`,`genreid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `genretomusic` */

DROP TABLE IF EXISTS `genretomusic`;

CREATE TABLE `genretomusic` (
  `genreid` int(11) DEFAULT NULL,
  `musicid` int(11) DEFAULT NULL,
  UNIQUE KEY `ix_genreid` (`genreid`,`musicid`),
  UNIQUE KEY `ix_albumid` (`musicid`,`genreid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `genretotvshow` */

DROP TABLE IF EXISTS `genretotvshow`;

CREATE TABLE `genretotvshow` (
  `genreid` int(11) DEFAULT NULL,
  `tvshowid` int(11) DEFAULT NULL,
  UNIQUE KEY `ix_genreid` (`genreid`,`tvshowid`),
  UNIQUE KEY `ix_tvshowid` (`tvshowid`,`genreid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `log` */

DROP TABLE IF EXISTS `log`;

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `type` varchar(16) NOT NULL,
  `event` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=768 DEFAULT CHARSET=utf8;

/*Table structure for table `movies` */

DROP TABLE IF EXISTS `movies`;

CREATE TABLE `movies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xbmcid` int(11) NOT NULL,
  `hide` tinyint(1) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `imdbnr` text,
  `sorttitle` text,
  `originaltitle` text,
  `year` smallint(6) DEFAULT NULL,
  `rating` decimal(10,2) DEFAULT NULL,
  `director` text,
  `trailer` text,
  `tagline` text,
  `plot` text,
  `plotoutline` text,
  `lastplayed` datetime DEFAULT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `writer` text,
  `studio` text,
  `mpaa` text,
  `cast` text,
  `country` text,
  `runtime` smallint(6) DEFAULT NULL,
  `set` text,
  `setid` int(11) DEFAULT NULL,
  `audio` text,
  `video` text,
  `votes` int(11) DEFAULT NULL,
  `file` text,
  `dateadded` datetime DEFAULT NULL,
  `genre` text,
  PRIMARY KEY (`id`),
  KEY `ix_xbmcid` (`xbmcid`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`),
  KEY `ix_setid` (`setid`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

/*Table structure for table `music` */

DROP TABLE IF EXISTS `music`;

CREATE TABLE `music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xbmcid` int(11) NOT NULL,
  `hide` tinyint(1) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `artist` text,
  `description` text,
  `genre` text,
  `theme` text,
  `mood` text,
  `style` text,
  `type` text,
  `albumlabel` text,
  `rating` decimal(10,0) DEFAULT NULL,
  `year` smallint(6) DEFAULT NULL,
  `mbalbumid` text,
  `mbalbumartistid` text,
  `playcount` smallint(6) DEFAULT NULL,
  `genreid` int(11) DEFAULT NULL,
  `artistid` int(11) DEFAULT NULL,
  `displayartist` text,
  `sorttitle` text,
  PRIMARY KEY (`id`),
  KEY `ix_xbmcid` (`xbmcid`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

/*Table structure for table `seasons` */

DROP TABLE IF EXISTS `seasons`;

CREATE TABLE `seasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tvshowid` int(11) DEFAULT NULL,
  `hide` tinyint(4) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `showtitle` text,
  `playcount` smallint(6) DEFAULT NULL,
  `season` smallint(6) DEFAULT NULL,
  `episode` smallint(6) DEFAULT NULL,
  `watchedepisodes` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_tvshowid` (`tvshowid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Table structure for table `sets` */

DROP TABLE IF EXISTS `sets`;

CREATE TABLE `sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setid` int(11) DEFAULT NULL,
  `hide` tinyint(4) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `sorttitle` text,
  `playcount` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_setid` (`setid`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Table structure for table `status` */

DROP TABLE IF EXISTS `status`;

CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `value` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

/*Table structure for table `tvshows` */

DROP TABLE IF EXISTS `tvshows`;

CREATE TABLE `tvshows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xbmcid` int(11) NOT NULL,
  `hide` tinyint(1) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `imdbnr` text,
  `genre` text,
  `year` smallint(6) DEFAULT NULL,
  `rating` decimal(10,2) DEFAULT NULL,
  `plot` text,
  `studio` text,
  `mpaa` text,
  `cast` text,
  `playcount` smallint(6) DEFAULT NULL,
  `episode` smallint(6) DEFAULT NULL,
  `premiered` date DEFAULT NULL,
  `votes` int(11) DEFAULT NULL,
  `lastplayed` datetime DEFAULT NULL,
  `file` text,
  `originaltitle` text,
  `sorttitle` text,
  `season` smallint(6) DEFAULT NULL,
  `episodeguide` text,
  `watchedepisodes` smallint(6) DEFAULT NULL,
  `dateadded` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_xbmcid` (`xbmcid`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(64) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
