/*
SQLyog Ultimate v9.33 GA
MySQL - 5.5.27 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `seasons` (
	`id` int (11),
	`tvshowid` int (11),
	`hide` tinyint (4),
	`refresh` smallint (6),
	`title` blob ,
	`showtitle` blob ,
	`thumb` blob ,
	`playcount` smallint (6),
	`season` smallint (6),
	`episode` smallint (6),
	`watchedepisodes` smallint (6)
); 
