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

/*Table structure for table `music` */

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_xbmcid` (`xbmcid`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
