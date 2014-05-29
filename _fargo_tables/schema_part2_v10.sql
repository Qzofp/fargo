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

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Data for the table `settings` */

insert  into `settings`(`id`,`name`,`value`) values (1,'Version','0.5'),(2,'Hash','Defending Our Nation. Securing The Future.'),(3,'Statistics','<table><tr><th colspan=\"2\">Movies</th></tr><tr class=\"property\"><td>Total</td><td class=\"right\">[movies]</td></tr><tr><th colspan=\"2\">TV Shows</th></tr><tr class=\"property\"><td>Total</td><td class=\"right\">[tvshows]</td></tr><tr><th colspan=\"2\">Music Albums</th></tr><tr class=\"property\"><td>Total</td><td class=\"right\">[music]</td></tr></table>'),(4,'XBMCconnection',''),(5,'XBMCport',''),(6,'XBMCusername','xbmc'),(7,'XBMCpassword',''),(8,'Timeout','1000'),(9,'Settings','<table><tr><th class=\"set\" colspan=\"2\">XBMC Settings</th></tr><tr class=\"property\"><td>Connection</td><td class=\"right\"><input type=\"text\" value=\"[connection]\"></td></tr><tr class=\"property\"><td>Port</td><td class=\"right\"><input type=\"text\" value=\"[port]\"></td></tr><tr class=\"property\"><td>Username</td><td class=\"right\"><input type=\"text\" value=\"[xbmcuser]\"></td></tr><tr class=\"property\"><td>Password</td><td class=\"right\"><input type=\"password\" value=\"[password]\"></td></tr><tr><th class=\"set\" colspan=\"2\">Fargo Settings</th></tr><tr class=\"property\"><td>Username</td><td class=\"right\"><input type=\"text\" value=\"[fargouser]\"></td></tr><tr class=\"property\"><td>Password</td><td class=\"right\"><input type=\"password\" value=\"[password]\"></td></tr><tr><th class=\"set\" colspan=\"2\">Import Setting</th></tr><tr class=\"property\"><td>Speed (300 - 3000 ms)</td><td class=\"right\"><input type=\"text\" value=\"[timeout]\"></td></tr></table>'),(10,'Library','<table><tr><th class=\"set\">Movies</th></tr><tr class=\"property\"><td>Remove library...</td></tr><tr class=\"property\"><td>Import Movies library</td></tr><tr><th class=\"set\">TV Shows</th></tr><tr class=\"property\"><td>Remove library...</td></tr><tr class=\"property\"><td>Import TV Shows library</td></tr><tr><th class=\"set\">Music</th><tr class=\"property\"><td>Remove library...</td></tr><tr class=\"property\"><td>Import Music library</td></tr></table>'),(11,'Event Log','<tr><th class=\"set\" colspan=\"3\">Events</th></tr><tr class=\"property\"><td colspan=\"3\">Remove event log...</td></tr><tr><th>Date</th><th>Type</th><th>Event</th></tr>'),(12,'Credits','<p>Every good book starts with a quote from a famous person. So I asked my good friend to deliver a nice one.</p><p><i>\"He who controls the Credits, controls the Internet! The credits must flow.\"</i></p><b>- Baron Harkonnen (Dune)</b><p>I want the thank my mother, father, my children, my wife, the neighbor\'s wife, her large breasts and nice ass. And I want to thank all the other people who didn\'t support or helped me one bit with the writing of Fargo.</p>But really the credits goes to...<ul><li>Of course myself.</li><li>The excellent XBMC, formally known as the Xbox Media Player.</li><li>The tool guys (PHP, jQuery, JavaScript, JSON, HTML5, CSS3, NetBeans, Apache, MySQL, etc.).</li><li>The code guys from which I <del>stole</del> <del>borrowed</del> got inspiration from.</li><li>jQuery Plugin writers.</li><li>And many many others...</li></ul>Thank you!<br/><b>- Qzofp</b><p></p>'),(13,'About','<h1>Fargo Version [version] (Beta 1)</h1><p><b>What does it do?</b> It imports movies, TV shows and music information from XBMC and displays it on a web page.</p><p><b>Why is it called Fargo?</b> It\'s called Fargo because it\'s a movie, a place and I liked the name. Much better than the name of a depressed, annoying and nagging hobbit who cannot bear the burden of a small round lightweight piece of plastic with some ancient looking runes on it.</p><p><b>Which version of XBMC do I need?</b> Gotham! Or if you like surprises and strange behavior then use an older version.</p><p><b>Why does it look like the XBMC\'s Confluence skin?</b> I didn\'t notice it, coincidence I guess.</p><p><b>Why doesn\'t it work in Internet Explorer 8?</b> Are you working in a museum and like silent movies? No, seriously Fargo is developed with HTML5, CSS3, jQuery and such. So it doesn\'t work with older browsers.</p><p><b>Which browser do it need for this?</b> I tested it with Firefox 21, Chrome 26, Opera 17 and Internet Explorer 10. At the moment it works better with Chrome and Firefox.</p><p><b>Has this something to do with the official XBMC team?</b> No.</p><p><b>So can I get support from my beloved XBMC team?</b> What part of the previous answer didn\'t you understand?</p><p><b>Where can I find the software?</b> On the Internet with some help from Google. Just kidding. Go to <a href=\"https://github.com/Qzofp/Fargo\">https://github.com/Qzofp/Fargo</a>.</p><p><b>Where can I download these movies for free?</b> Not from this site. I bought all my movies.</p><p><b>I English understand not. Please to my country translate you could?</b> Okay, when I\'m done learning 152 languages I\'ll translate it. But maybe it\'s faster for you to learn English!</p><p><b>Are there instructions how to install it.</b> No, not yet. It\'s still under development or something.</p><p><b>Who created it?</b> Fargo is created by Qzofp, who\'s too lazy to write an installation manual.</p><p><b>I don\'t like you!</b> I don\'t like you either!</p><p><b>Do I know who you are?</b> No... I hope not.</p>');

/*Table structure for table `status` */

DROP TABLE IF EXISTS `status`;

CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `value` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `status` */

insert  into `status`(`id`,`name`,`value`) values (1,'XbmcMoviesStart','0'),(2,'XbmcMoviesEnd','0'),(3,'XbmcSetsStart','0'),(4,'XbmcSetsEnd','0'),(5,'XbmcTVShowsStart','0'),(6,'XbmcTVShowsEnd','0'),(7,'XbmcSeasonsStart','0'),(8,'XbmcSeasonsEnd','0'),(9,'XbmcEpisodesStart','0'),(10,'XbmcEpisodesEnd','0'),(11,'XbmcAlbumsStart','0'),(12,'XbmcAlbumsEnd','0'),(13,'ImportStart','0'),(14,'ImportCounter','1'),(15,'ImportStatus','-1'),(16,'ImportKey','0'),(17,'ImportLock','-1');

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

insert  into `users`(`id`,`user`,`password`) values (1,'Fargo','2cda2f5b9a0716966d173fe3e01fb612');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
