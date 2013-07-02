<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    settings.php
 *
 * Created on Mar 09, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Fargo's settings page.
 *
 */

// Database connection settings.
define("cHOST",  "localhost");
define("cDBASE", "fargo");
define("cUSER",  "fargo_dbo");
define("cPASS",  "Mime1276"); 

// XBMC connection setting.
//define("cXBMC", "xbmc:xbmc@localhost:8080");
//define("cURL", "http://localhost:8080/jsonrpc");

// Path settings.
define("cMOVIESPOSTERS", "images/movies/posters");
define("cMOVIESFANART", "images/movies/fanart");
define("cTVSHOWSPOSTERS", "images/tvshows/posters");
define("cTVSHOWSFANART", "images/tvshows/fanart");
define("cALBUMSCOVERS", "images/music/covers");
define("cTEMPPOSTERS", "images/temp");

// Misc settings.
define("cMediaRow", "3");
define("cMediaColumn", "5");


?>
