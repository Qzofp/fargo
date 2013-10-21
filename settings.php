<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    settings.php
 *
 * Created on Mar 09, 2013
 * Updated on Oct 20, 2013
 *
 * Description: Fargo's settings page.
 *
 */

// Database connection settings.
define("cHOST",  "localhost");
define("cDBASE", "fargo");
define("cUSER",  "fargo_dbo");
define("cPASS",  "Mime1276"); 

// Path settings.
define("cMOVIESTHUMBS", "images/movies/thumbs");
define("cMOVIESFANART", "images/movies/fanart");
define("cSETSTHUMBS", "images/sets/thumbs");
define("cSETSFANART", "images/sets/fanart");
define("cTVSHOWSTHUMBS", "images/tvshows/thumbs");
define("cTVSHOWSFANART", "images/tvshows/fanart");
define("cSEASONSTHUMBS", "images/seasons/thumbs");
//define("cSEASONSFANART", "images/seasons/fanart");
define("cALBUMSTHUMBS", "images/music/thumbs");
define("cALBUMSCOVERS", "images/music/covers");

// URL settings.
define("cIMDB", "http://www.imdb.com/title/");
define("cYOUTUBE", "http://www.youtube.com/watch?v=");
define("cTVDB", "http://thetvdb.com/?tab=series&id=");
define("cANIDB", "http://anidb.net/a");

// Misc settings.
define("cMediaRow", "3");
define("cMediaColumn", "5");
?>
