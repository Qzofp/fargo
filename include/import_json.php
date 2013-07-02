<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    import_json.php
 *
 * Created on Jul 02, 2013
 * Updated on Jul 02, 2013
 *
 * Description: The XBMC import json functions page. 
 * 
 * Note: This page contains json functions for importing media from XBMC (used by import.php).
 *
 */

/////////////////////////////////////////    JSON Functions    ////////////////////////////////////////////

/*
 * Function:	OnlineCheckXBMC
 *
 * Created on Mar 11, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Check with JSON if XBMC is online.
 *
 * In:  -
 * Out:	$online (true|false)
 *
 * Note: XBMC Connection is defined in constant cXBMC.
 * 
 */
function OnlineCheckXBMC()
{
    $online = true;    
    $url = GetXbmcConnecting();
    
    // Check if JSON response.
    // JSON: {"jsonrpc": "2.0", "method": "JSONRPC.Ping", "id": 1}
    $request = '{"jsonrpc": "2.0", "method": "JSONRPC.Ping", "id": 1}';
    $aJson = GetHttpRequest($url, $request);
    
    if ($aJson["result"] != "pong") {
        $online = -1;
    }
    return $online;
}

/*
 * Function:	GetXbmcConnection
 *
 * Created on Jul 02, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Get XBMC connection url for JSON.
 *
 * In:  -
 * Out:	$
 * 
 */
function GetXbmcConnecting()
{
    $conn = GetSetting("XBMCconnection");
    $port = GetSetting("XBMCport");
    
    // Note: No user and password yet!
    
    $url = "http://$conn:$port/jsonrpc";    
    
    return $url;
}

/*
 * Function:	GetMediaCounterFromXBMC
 *
 * Created on Mar 18, 2013
 * Updated on May 19, 2013
 *
 * Description: Connect to XBMC and get the media counter.
 *
 * In:  $media
 * Out:	$aJson
 * 
 * Note: XBMC Connection is defined in constant cXBMC.
 *
 */
function GetMediaCounterFromXBMC($media)
{
    $aJson = null;
    
    switch ($media)    
    {   
        case "movies"   : $aJson['counter'] = GetTotalNumberOfMoviesFromXBMC();
                          break;
    
        case "tvshows"  : $aJson['counter'] = GetTotalNumberOfTVShowsFromXBMC();
                          break;
                      
        case "music"    : $aJson['counter'] = GetTotalNumberOfAlbumsFromXBMC();
                          break;                      
    }
    
    $aJson['online'] = true;
    if ($aJson['counter'] == -1) {
        $aJson['online'] = -1;
    }
    
    return $aJson;
}

/*
 * Function:	GetTotalNumberOfMoviesFromXBMC
 *
 * Created on Mar 18, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Connect to XBMC and get the total number of movies.
 *
 * In:  -
 * Out:	$total
 * 
 * Note: XBMC Connection is defined in constant cXBMC.
 *
 */
function GetTotalNumberOfMoviesFromXBMC()
{ 
    $total = -1;
    $url = GetXbmcConnecting();

    // Get total number of movies through JSON.
    // JSON: {"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", 
    //        "params": { "limits": { "start" : 0, "end": 1 }}, "id": "libMovies"} 
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies",'.
               '"params": { "limits": { "start" : 0, "end": 1 }}, "id": "libMovies"}';
    
    $aJson = GetHttpRequest($url, $request); 
    
    if (!empty($aJson)) {
        $total = $aJson["result"]["limits"]["total"];
    }
    
    return $total;
}    

/*
 * Function:	GetTotalNumberOfTVShowsFromXBMC
 *
 * Created on Apr 14, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Connect to XBMC and get the total number of TV Shows.
 *
 * In:  -
 * Out:	$total
 * 
 * Note: XBMC Connection is defined in constant cXBMC.
 *
 */
function GetTotalNumberOfTVShowsFromXBMC()
{ 
    $total = -1;   
    $url = GetXbmcConnecting();

    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows",'.
               '"params": { "limits": { "start" : 0, "end": 1 }}, "id": "libTvShows"}';
    
    $aJson = GetHttpRequest($url, $request);
    
    if (!empty($aJson)) {
        $total = $aJson["result"]["limits"]["total"];
    }
    
    return $total;
} 


/*
 * Function:	GetTotalNumberOfAlbumsFromXBMC
 *
 * Created on Apr 20, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Connect to XBMC and get the total number of Albums.
 *
 * In:  -
 * Out:	$total
 * 
 * Note: XBMC Connection is defined in constant cXBMC.
 *
 */
function GetTotalNumberOfAlbumsFromXBMC()
{ 
    $total = -1;
    $url = GetXbmcConnecting();
    
    $request = '{"jsonrpc": "2.0", "method": "AudioLibrary.GetAlbums",'.
               '"params": { "limits": { "start" : 0, "end": 1 }}, "id": "libAlbums"}';
    
    $aJson = GetHttpRequest($url, $request);
    
    if (!empty($aJson)) {
        $total = $aJson["result"]["limits"]["total"];
    }
    
    return $total;
} 

/*
 * Function:	GetMoviesFromXBMC
 *
 * Created on Mar 03, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Connect to XBMC and get the Movies information.
 *
 * In:  $counter, $offset (max. number of movies received from XBMC)
 * Out:	$aMovies
 * 
 * Note: XBMC Connection is defined in constant cXBMC.
 *
 */
function GetMoviesFromXBMC($counter, $offset)
{    
    $aMovies = null;
    $url = GetXbmcConnecting();
    
    // Get movies through JSON.
    // JSON: {"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", 
    //        "params": { "limits": { "start": 0, "end": 75 }, 
    //                     "properties" : ["imdbnumber", "art", "thumbnail"] }, "id": "libMovies"}   
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies",'.
               '"params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'},'.
               '"properties": ["title","genre","year","rating","director","trailer","tagline","plot",'.
               '"plotoutline","originaltitle","lastplayed","playcount","writer","studio","mpaa","cast",'.
               '"country","imdbnumber","runtime","set","showlink","streamdetails","top250","votes",'.
               '"fanart","thumbnail","file","sorttitle","resume","setid","dateadded","tag","art"'.
               ']}, "id": "libMovies"}';
    
    $aJson = GetHttpRequest($url, $request);
    
    //$aLimits = $aJson["result"]["limits"];
    
    //debug
    //echo "<pre>";
    //print_r($aLimits);
    //echo "</pre></br>";    
    
    if (!empty($aJson["result"]["movies"])) {
        $aMovies = $aJson["result"]["movies"];        
    }

    //debug
    //echo "<pre>";
    //print_r($aMovies);
    //echo "</pre></br>";      
       
    return $aMovies;
}

/*
 * Function:	GetTVShowsFromXBMC
 *
 * Created on Apr 19, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Connect to XBMC and get the TV Shows information.
 *
 * In:  $counter, $offset (max. number of TV Shows received from XBMC)
 * Out:	$aTVShows
 *
 */
function GetTVShowsFromXBMC($counter, $offset)
{    
    $aTVShows = null;
    $url = GetXbmcConnecting();
   
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows",'.
               '"params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'},'.
               '"properties": ["title", "genre", "year", "rating", "plot", "studio", "mpaa", "cast", "playcount",'. 
               '"episode", "imdbnumber", "premiered", "votes", "lastplayed", "fanart", "thumbnail",'.
               '"file", "originaltitle", "sorttitle", "episodeguide", "season", "watchedepisodes",'.
               '"dateadded", "tag", "art"] }, "id": "libTvShows"}';
        
    /*
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows",'.
               '"params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'},'.
               '"properties": ["imdbnumber", "art", "thumbnail"] }, "id": "libTvShows"}';
    */
    
    $aJson = GetHttpRequest($url, $request);
    
    //debug
    //echo "<pre>";
    //print_r($aJson);
    //echo "</pre></br>";    
    
    if (!empty($aJson["result"]["tvshows"])) {
        $aTVShows = $aJson["result"]["tvshows"];        
    }
    
    return $aTVShows;
}

/*
 * Function:	GetAlbumsFromXBMC
 *
 * Created on Apr 20, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Connect to XBMC and get the Albums information.
 *
 * In:  $counter, $offset (max. number of Albums received from XBMC)
 * Out:	$aAlbums
 *
 */
function GetAlbumsFromXBMC($counter, $offset)
{    
    $aAlbums = null;
    $url = GetXbmcConnecting();

    $request = '{"jsonrpc": "2.0", "method": "AudioLibrary.GetAlbums",'.
               '"params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'},'.
               '"properties": ["title", "description", "artist", "genre", "theme", "mood", "style","type",'.
               '"albumlabel", "rating", "year", "musicbrainzalbumid", "musicbrainzalbumartistid", "fanart",'.
               '"thumbnail","playcount", "genreid", "artistid", "displayartist"] }, "id": "libAlbums"}';    
    
    $aJson = GetHttpRequest($url, $request);
    
    //debug
    //echo "<pre>";
    //print_r($aJson);
    //echo "</pre></br>";    
    
    if (!empty($aJson["result"]["albums"])) {
        $aAlbums = $aJson["result"]["albums"];        
    }
    
    return $aAlbums;
}
?>

