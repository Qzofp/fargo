/*
SQLyog Ultimate v11.11 (32 bit)
MySQL - 5.5.34 : Database - fargo
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

/*Table structure for table `albums` */

DROP TABLE IF EXISTS `albums`;

CREATE TABLE `albums` (
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
  `hash` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_xbmcid` (`xbmcid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

/*Table structure for table `albumsmeta` */

DROP TABLE IF EXISTS `albumsmeta`;

CREATE TABLE `albumsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `albumid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_albumid` (`albumid`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

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
  `hash` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_episodeid` (`episodeid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_tvshowid` (`tvshowid`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8;

/*Table structure for table `episodesmeta` */

DROP TABLE IF EXISTS `episodesmeta`;

CREATE TABLE `episodesmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `episodeid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_episodeid` (`episodeid`)
) ENGINE=InnoDB AUTO_INCREMENT=1542 DEFAULT CHARSET=utf8;

/*Table structure for table `genres` */

DROP TABLE IF EXISTS `genres`;

CREATE TABLE `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media` varchar(8) DEFAULT NULL,
  `genre` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_genre` (`media`,`genre`)
) ENGINE=InnoDB AUTO_INCREMENT=20207 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=314 DEFAULT CHARSET=utf8;

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
  `hash` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_xbmcid` (`xbmcid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`),
  KEY `ix_setid` (`setid`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;

/*Table structure for table `moviesmeta` */

DROP TABLE IF EXISTS `moviesmeta`;

CREATE TABLE `moviesmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movieid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_movieid` (`movieid`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;

/*Table structure for table `seasons` */

DROP TABLE IF EXISTS `seasons`;

CREATE TABLE `seasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seasonid` int(11) DEFAULT NULL,
  `hide` tinyint(4) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `tvshowid` int(11) NOT NULL,
  `showtitle` text,
  `playcount` smallint(6) DEFAULT NULL,
  `season` smallint(6) DEFAULT NULL,
  `episode` smallint(6) DEFAULT NULL,
  `watchedepisodes` smallint(6) DEFAULT NULL,
  `hash` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_seasonid` (`seasonid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_tvshowid` (`tvshowid`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

/*Table structure for table `seasonsmeta` */

DROP TABLE IF EXISTS `seasonsmeta`;

CREATE TABLE `seasonsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seasonid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_seasonid` (`seasonid`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

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
  `hash` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_setid` (`setid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

/*Table structure for table `setsmeta` */

DROP TABLE IF EXISTS `setsmeta`;

CREATE TABLE `setsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_setid` (`setid`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

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
  `hash` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_xbmcid` (`xbmcid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

/*Table structure for table `tvshowsmeta` */

DROP TABLE IF EXISTS `tvshowsmeta`;

CREATE TABLE `tvshowsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tvshowid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_tvshowid` (`tvshowid`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
