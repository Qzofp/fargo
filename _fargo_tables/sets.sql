/*
SQLyog Ultimate v9.33 GA
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

/*Table structure for table `sets` */

DROP TABLE IF EXISTS `sets`;

CREATE TABLE `sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setid` int(11) DEFAULT NULL,
  `hide` tinyint(4) DEFAULT NULL,
  `refresh` smallint(6) DEFAULT NULL,
  `title` text,
  `fanart` text,
  `poster` text,
  `thumb` text,
  `playcount` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_setid` (`setid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sets` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
