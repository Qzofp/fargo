<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.5
 *
 * File:    settings.php
 *
 * Created on Mar 09, 2013
 * Updated on Jun 21, 2014
 *
 * Description: Fargo's settings page.
 *
 */

// Database connection settings.
define("cHOST",  "127.0.0.1");
define("cDBASE", "fargo");
define("cUSER",  "fargo_dbo");
define("cPASS",  "fargo");

// Path settings.
define("cMOVIESTHUMBS", "images/movies/thumbs");
define("cMOVIESFANART", "images/movies/fanart");
define("cSETSTHUMBS", "images/sets/thumbs");
define("cSETSFANART", "images/sets/fanart");
define("cTVSHOWSTHUMBS", "images/tvshows/thumbs");
define("cTVSHOWSFANART", "images/tvshows/fanart");
define("cSEASONSTHUMBS", "images/seasons/thumbs");
//define("cSEASONSFANART", "images/seasons/fanart");
define("cEPISODESTHUMBS", "images/episodes/thumbs");
//define("cEPISODESSFANART", "images/episodes/fanart");
define("cALBUMSTHUMBS", "images/albums/thumbs");
define("cALBUMSCOVERS", "images/albums/covers");

// URL settings.
define("cIMDB", "http://www.imdb.com/title/");
define("cYOUTUBE", "http://www.youtube.com/watch?v=");
define("cTVDB", "http://thetvdb.com/?tab=series&id=");
define("cANIDB", "http://anidb.net/a");

// Transfer (Import/Refresh) codes. Must be smaller then or equal to 0.
// Note: These codes corresponds with the codes from fargo.private.constants.js.
define("cTRANSFER_INVALID", -32602);
define("cTRANSFER_ERROR", -999);
define("cTRANSFER_DUPLICATE", -300);
define("cTRANSFER_NOT_FOUND", -200);
define("cTRANSFER_READY", -100);
define("cTRANSFER_WAIT", -1);
define("cTRANSFER_NO_MATCH", 0);

// Error codes.
define("cMYSQL_DUPLICATE_KEY_ENTRY", 1062);

// Misc settings.
define("cMediaRow", "3");
define("cMediaColumn", "5");
define("cMediaEpisodeColumn", "3");