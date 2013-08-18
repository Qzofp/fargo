<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    settings.php
 *
 * Created on Mar 09, 2013
 * Updated on Aug 17, 2013
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
define("cMOVIESPOSTERS", "images/movies/posters");
define("cMOVIESFANART", "images/movies/fanart");
define("cTVSHOWSTHUMBS", "images/tvshows/thumbs");
define("cTVSHOWSPOSTERS", "images/tvshows/posters");
define("cTVSHOWSFANART", "images/tvshows/fanart");
define("cALBUMSTHUMBS", "images/music/thumbs");
define("cALBUMSCOVERS", "images/music/covers");
define("cTEMPPOSTERS", "images/temp"); // Soon obsolete.

// URL settings.
define("cIMDB", "http://www.imdb.com/title/");
define("cYOUTUBE", "http://www.youtube.com/watch?v=");
define("cTVDB", "http://thetvdb.com/?tab=series&id=");
define("cANIDB", "http://anidb.net/a");

// Misc settings.
define("cMediaRow", "3");
define("cMediaColumn", "5");
?>
