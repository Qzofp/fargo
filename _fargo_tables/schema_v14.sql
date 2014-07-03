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
  `poster` binary(4) DEFAULT NULL,
  `year` smallint(6) DEFAULT NULL,
  `mbalbumid` text,
  `mbalbumartistid` text,
  `playcount` smallint(6) DEFAULT NULL,
  `displayartist` text,
  `sorttitle` text,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_xbmcid` (`xbmcid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `albums` */

/*Table structure for table `albumsmeta` */

DROP TABLE IF EXISTS `albumsmeta`;

CREATE TABLE `albumsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `albumid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_albumid` (`albumid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `albumsmeta` */

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
  `poster` binary(4) DEFAULT NULL,
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
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_episodeid` (`episodeid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_tvshowid` (`tvshowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `episodes` */

/*Table structure for table `episodesmeta` */

DROP TABLE IF EXISTS `episodesmeta`;

CREATE TABLE `episodesmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `episodeid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_episodeid` (`episodeid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `episodesmeta` */

/*Table structure for table `genres` */

DROP TABLE IF EXISTS `genres`;

CREATE TABLE `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media` varchar(8) DEFAULT NULL,
  `genre` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_genre` (`media`,`genre`)
) ENGINE=InnoDB AUTO_INCREMENT=3080 DEFAULT CHARSET=utf8;

/*Data for the table `genres` */

/*Table structure for table `genretomovie` */

DROP TABLE IF EXISTS `genretomovie`;

CREATE TABLE `genretomovie` (
  `genreid` int(11) NOT NULL,
  `movieid` int(11) NOT NULL,
  UNIQUE KEY `ix_genreid` (`genreid`,`movieid`),
  UNIQUE KEY `ix_movieid` (`movieid`,`genreid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `genretomovie` */

/*Table structure for table `genretomusic` */

DROP TABLE IF EXISTS `genretomusic`;

CREATE TABLE `genretomusic` (
  `genreid` int(11) DEFAULT NULL,
  `musicid` int(11) DEFAULT NULL,
  UNIQUE KEY `ix_genreid` (`genreid`,`musicid`),
  UNIQUE KEY `ix_albumid` (`musicid`,`genreid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `genretomusic` */

/*Table structure for table `genretotvshow` */

DROP TABLE IF EXISTS `genretotvshow`;

CREATE TABLE `genretotvshow` (
  `genreid` int(11) DEFAULT NULL,
  `tvshowid` int(11) DEFAULT NULL,
  UNIQUE KEY `ix_genreid` (`genreid`,`tvshowid`),
  UNIQUE KEY `ix_tvshowid` (`tvshowid`,`genreid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `genretotvshow` */

/*Table structure for table `log` */

DROP TABLE IF EXISTS `log`;

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `type` varchar(16) NOT NULL,
  `event` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `log` */

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
  `poster` binary(4) DEFAULT NULL,
  `fanart` binary(4) DEFAULT NULL,
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
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_xbmcid` (`xbmcid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`),
  KEY `ix_setid` (`setid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `movies` */

/*Table structure for table `moviesmeta` */

DROP TABLE IF EXISTS `moviesmeta`;

CREATE TABLE `moviesmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movieid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_movieid` (`movieid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `moviesmeta` */

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
  `poster` binary(4) DEFAULT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `season` smallint(6) DEFAULT NULL,
  `episode` smallint(6) DEFAULT NULL,
  `watchedepisodes` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_seasonid` (`seasonid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_tvshowid` (`tvshowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `seasons` */

/*Table structure for table `seasonsmeta` */

DROP TABLE IF EXISTS `seasonsmeta`;

CREATE TABLE `seasonsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seasonid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_seasonid` (`seasonid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `seasonsmeta` */

/*Table structure for table `sets` */

DROP TABLE IF EXISTS `sets`;

CREATE TABLE `sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setid` int(11) DEFAULT NULL,
  `hide` tinyint(4) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `sorttitle` text,
  `poster` binary(4) DEFAULT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_setid` (`setid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sets` */

/*Table structure for table `setsmeta` */

DROP TABLE IF EXISTS `setsmeta`;

CREATE TABLE `setsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_setid` (`setid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `setsmeta` */

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Data for the table `settings` */

insert  into `settings`(`id`,`name`,`value`) values (1,'Version','0.6'),(2,'Hash','Defending Our Nation. Securing The Future.'),(3,'Statistics','<table><tr><th colspan=\"2\">Movies</th></tr><tr class=\"property\"><td>Titels</td><td class=\"right\">[movies]</td></tr><tr class=\"property\"><td>Sets</td><td class=\"right\">[sets]</td></tr><tr><th colspan=\"2\">TV Shows</th></tr><tr class=\"property\"><td>Titels</td><td class=\"right\">[tvshows]</td></tr><tr class=\"property\"><td>Seasons</td><td class=\"right\">[seasons]</td></tr><tr class=\"property\"><td>Episodes</td><td class=\"right\">[episodes]</td></tr><tr><th colspan=\"2\">Music</th></tr><tr class=\"property\"><td>Albums</td><td class=\"right\">[albums]</td></tr><tr class=\"property\"><td>Songs</td><td class=\"right\">[songs]</td></tr></table>'),(4,'XBMCconnection','192.168.198.129'),(5,'XBMCport','8080'),(6,'XBMCusername','xbmc'),(7,'XBMCpassword',''),(8,'Timeout','800'),(9,'Settings','<table><tr><th class=\"set\" colspan=\"2\">XBMC Settings</th></tr><tr class=\"property\"><td>Connection</td><td class=\"right\"><input type=\"text\" value=\"[connection]\"></td></tr><tr class=\"property\"><td>Port</td><td class=\"right\"><input type=\"text\" value=\"[port]\"></td></tr><tr class=\"property\"><td>Username</td><td class=\"right\"><input type=\"text\" value=\"[xbmcuser]\"></td></tr><tr class=\"property\"><td>Password</td><td class=\"right\"><input type=\"password\" value=\"[password]\"></td></tr><tr><th class=\"set\" colspan=\"2\">Fargo Settings</th></tr><tr class=\"property\"><td>Username</td><td class=\"right\"><input type=\"text\" value=\"[fargouser]\"></td></tr><tr class=\"property\"><td>Password</td><td class=\"right\"><input type=\"password\" value=\"[password]\"></td></tr><tr><th class=\"set\" colspan=\"2\">Import Setting</th></tr><tr class=\"property\"><td>Speed (300 - 3000 ms)</td><td class=\"right\"><input type=\"text\" value=\"[timeout]\"></td></tr></table>'),(10,'Library','<table><tr><th class=\"set\">Movies</th></tr><tr class=\"property\"><td>Remove library...</td></tr><tr class=\"property\"><td>Import Movies library</td></tr><tr><th class=\"set\">TV Shows</th></tr><tr class=\"property\"><td>Remove library...</td></tr><tr class=\"property\"><td>Import TV Shows library</td></tr><tr><th class=\"set\">Music</th><tr class=\"property\"><td>Remove library...</td></tr><tr class=\"property\"><td>Import Music library</td></tr></table>'),(11,'Event Log','<tr><th class=\"set\" colspan=\"3\">Events</th></tr><tr class=\"property\"><td colspan=\"3\">Remove event log...</td></tr><tr><th>Date</th><th>Type</th><th>Event</th></tr>'),(12,'Credits','<p>Every good book starts with a quote from a famous person. So I asked my good friend to deliver a nice one.</p><p><i>\"He who controls the Credits, controls the Internet! The credits must flow.\"</i></p><b>- Baron Harkonnen (Dune)</b><p>I want the thank my mother, father, my children, my wife, the neighbor\'s wife, her large breasts and nice ass. And I want to thank all the other people who didn\'t support or helped me one bit with the writing of Fargo.</p>But really the credits goes to...<ul><li>Of course myself.</li><li>The excellent XBMC, formally known as the Xbox Media Player.</li><li>The tool guys (PHP, jQuery, JavaScript, JSON, HTML5, CSS3, NetBeans, Apache, MySQL, etc.).</li><li>The code guys from which I <del>stole</del> <del>borrowed</del> got inspiration from.</li><li>jQuery Plugin writers.</li><li>And many many others...</li></ul>Thank you!<br/><b>- Qzofp</b><p></p>'),(13,'About','<h1>Fargo Version [version] (Beta 2)</h1><p><b>What does it do?</b> It imports movies, TV shows and music information from XBMC and displays it on a web page.</p><p><b>Why is it called Fargo?</b> It\'s called Fargo because it\'s a movie, a place and I liked the name. Much better than the name of a depressed, annoying and nagging hobbit who cannot bear the burden of a small round lightweight piece of plastic with some ancient looking runes on it.</p><p><b>Which version of XBMC do I need?</b> Gotham! Or if you like surprises and strange behavior then use an older version.</p><p><b>Why does it look like the XBMC\'s Confluence skin?</b> I didn\'t notice it, coincidence I guess.</p><p><b>Why doesn\'t it work in Internet Explorer 8?</b> Are you working in a museum and like silent movies? No, seriously Fargo is developed with HTML5, CSS3, jQuery and such. So it doesn\'t work with older browsers.</p><p><b>Which browser do it need for this?</b> I tested it with Firefox 30, Chrome 35, Opera 22 and Internet Explorer 11. At the moment it works better with Chrome and Firefox.</p><p><b>Has this something to do with the official XBMC team?</b> No.</p><p><b>So can I get support from my beloved XBMC team?</b> What part of the previous answer didn\'t you understand?</p><p><b>Where can I find the software?</b> On the Internet with some help from Google. Just kidding. Go to <a href=\"https://github.com/Qzofp/Fargo\">https://github.com/Qzofp/Fargo</a>.</p><p><b>Where can I download these movies for free?</b> Not from this site. I bought all my movies.</p><p><b>I English understand not. Please to my country translate you could?</b> Okay, when I\'m done learning 152 languages I\'ll translate it. But maybe it\'s faster for you to learn English!</p><p><b>Are there instructions how to install it.</b> No, not yet. It\'s still under development or something.</p><p><b>Who created it?</b> Fargo is created by Qzofp, who\'s too lazy to write an installation manual.</p><p><b>I don\'t like you!</b> I don\'t like you either!</p><p><b>Do I know who you are?</b> No... I hope not.</p>');

/*Table structure for table `songs` */

DROP TABLE IF EXISTS `songs`;

CREATE TABLE `songs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `songid` int(11) NOT NULL,
  `hide` tinyint(1) DEFAULT '0',
  `refresh` smallint(6) DEFAULT '0',
  `title` text,
  `artist` text,
  `albumid` int(11) DEFAULT NULL,
  `album` text,
  `albumartist` text,
  `genre` text,
  `poster` binary(4) DEFAULT NULL,
  `year` smallint(6) DEFAULT NULL,
  `rating` decimal(10,0) DEFAULT NULL,
  `track` smallint(6) DEFAULT NULL,
  `duration` smallint(6) DEFAULT NULL,
  `comment` text,
  `lyrics` text,
  `mbtrackid` text,
  `mbartistid` text,
  `mbalbumid` text,
  `mbalbumartistid` text,
  `playcount` smallint(6) DEFAULT NULL,
  `file` text,
  `lastplayed` datetime DEFAULT NULL,
  `disc` smallint(6) DEFAULT NULL,
  `displayartist` text,
  `sorttitle` text,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_songid` (`songid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_albumid` (`albumid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `songs` */

/*Table structure for table `songsmeta` */

DROP TABLE IF EXISTS `songsmeta`;

CREATE TABLE `songsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `songid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_songid` (`songid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `songsmeta` */

/*Table structure for table `status` */

DROP TABLE IF EXISTS `status`;

CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `status` */

insert  into `status`(`id`,`name`,`value`) values (1,'ImportStart','1'),(2,'ImportEnd','1'),(3,'ImportCounter','1'),(4,'ImportStatus','-1'),(5,'ImportKey','ac85444af5a91b419e6a3321ee9bff22a075a2a137af416ff938a55f62984bc7'),(6,'ImportLock','-1');

/*Table structure for table `tmp_import` */

DROP TABLE IF EXISTS `tmp_import`;

CREATE TABLE `tmp_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mediaid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_mediaid` (`mediaid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tmp_import` */

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
  `poster` binary(4) DEFAULT NULL,
  `fanart` binary(4) DEFAULT NULL,
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
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_xbmcid` (`xbmcid`),
  UNIQUE KEY `ix_hash` (`hash`),
  KEY `ix_sorttitle` (`sorttitle`(10)),
  KEY `ix_year` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tvshows` */

/*Table structure for table `tvshowsmeta` */

DROP TABLE IF EXISTS `tvshowsmeta`;

CREATE TABLE `tvshowsmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tvshowid` int(11) NOT NULL,
  `playcount` smallint(6) DEFAULT NULL,
  `hash` binary(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_tvshowid` (`tvshowid`),
  UNIQUE KEY `ix_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tvshowsmeta` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(64) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ix_user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`id`,`user`,`password`) values (1,'Fargo','b66ab4207498f3ec4e1f01c70dcccd85de7463a875196bac065b32c4dafebc14');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
