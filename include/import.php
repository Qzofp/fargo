<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    import.php
 *
 * Created on Apr 14, 2013
 * Updated on May 15, 2013
 *
 * Description: Fargo's import functions page for the XBMC media import.
 *
 */


/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	ImportMedia
 *
 * Created on Apr 19, 2013
 * Updated on May 13, 2013
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  $start, $media
 * Out: -
 *
 */
function ImportMedia($start, $media)
{
    switch ($media)    
    {   
        case "movies"   : ImportMovies($start);
                          break;
    
        case "tvshows"  : ImportTVShows($start);
                          break;
                      
        case "music"    : ImportAlbums($start);
                          break;                      
    }
}

/*
 * Function:	ImportMovies
 *
 * Created on Mar 11, 2013
 * Updated on May 15, 2013
 *
 * Description: Import the movies. 
 *
 * In:  $start
 * Out: -
 *
 */
function ImportMovies($start)
{
    $offset  = 3;
    $aMovies = GetMoviesFromXBMC($start, $offset);
    
    if (!empty($aMovies)) {
        ProcessMovies($aMovies);
    }
}

/*
 * Function:	ImportTVShows
 *
 * Created on Apr 19, 2013
 * Updated on May 15, 2013
 *
 * Description: Import the tv shows. 
 *
 * In:  $start
 * Out: -
 *
 */
function ImportTVShows($start)
{
    $offset  = 3;  
    $aTVShows = GetTVShowsFromXBMC($start, $offset);
    
    if (!empty($aTVShows)) {
        ProcessTVShows($aTVShows, $start);
    }
}

/*
 * Function:	ImportAlbums
 *
 * Created on Apr 20, 2013
 * Updated on May 15, 2013
 *
 * Description: Import the music albums. 
 *
 * In:  $start
 * Out: -
 *
 */
function ImportAlbums($start)
{
    $offset  = 3;   
    $aAlbums = GetAlbumsFromXBMC($start, $offset);
    
    if (!empty($aAlbums)) {
        ProcessAlbums($aAlbums, $start);
    }
}

/*
 * Function:	GetMediaStatus
 *
 * Created on Mar 22, 2013
 * Updated on May 13, 2013
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  -
 * Out: $aJson
 *
 */
/*function GetMediaStatus($media)
{
    $aJson = null;   
    switch ($media)    
    {   
        case "movies"   : $total = (int)GetTotalNumberOfMoviesFromXBMC();
                          $aJson = GetImportStatus($media, $total, cMOVIESPOSTERS);
                          break;
        
        case "music"    : $total = (int)GetTotalNumberOfAlbumsFromXBMC();
                          $aJson = GetImportStatus($media, $total, cALBUMSCOVERS);
                          break;
    
        case "tvshows"  : $total = (int)GetTotalNumberOfTVShowsFromXBMC();
                          $aJson = GetImportStatus($media, $total, cTVSHOWSPOSTERS);
                          break;
    }    
    return $aJson;
}*/

/////////////////////////////////////////    JSON Functions    ////////////////////////////////////////////

/*
 * Function:	GetMediaCounterFromXBMC
 *
 * Created on Mar 18, 2013
 * Updated on Apr 20, 2013
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
    
    return $aJson;
}

/*
 * Function:	GetTotalNumberOfMoviesFromXBMC
 *
 * Created on Mar 18, 2013
 * Updated on May 15, 2013
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

    // Get total number of movies through JSON.
    // JSON: {"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", 
    //        "params": { "limits": { "start" : 0, "end": 1 }}, "id": "libMovies"} 
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies",'.
               '"params": { "limits": { "start" : 0, "end": 1 }}, "id": "libMovies"}';
    
    $aJson = GetHttpRequest(cURL, $request); 
    
    if (!empty($aJson)) {
        $total = $aJson["result"]["limits"]["total"];
    }
    
    return $total;
}    

/*
 * Function:	GetTotalNumberOfTVShowsFromXBMC
 *
 * Created on Apr 14, 2013
 * Updated on May 15, 2013
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

    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows",'.
               '"params": { "limits": { "start" : 0, "end": 1 }}, "id": "libTvShows"}';
    
    $aJson = GetHttpRequest(cURL, $request);
    
    if (!empty($aJson)) {
        $total = $aJson["result"]["limits"]["total"];
    }
    
    return $total;
} 


/*
 * Function:	GetTotalNumberOfAlbumsFromXBMC
 *
 * Created on Apr 20, 2013
 * Updated on May 15, 2013
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
    
    $request = '{"jsonrpc": "2.0", "method": "AudioLibrary.GetAlbums",'.
               '"params": { "limits": { "start" : 0, "end": 1 }}, "id": "libAlbums"}';
    
    $aJson = GetHttpRequest(cURL, $request);
    
    if (!empty($aJson)) {
        $total = $aJson["result"]["limits"]["total"];
    }
    
    return $total;
} 

/*
 * Function:	GetMoviesFromXBMC
 *
 * Created on Mar 03, 2013
 * Updated on Apr 22, 2013
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
    
    // Get movies through JSON.
    // JSON: {"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", 
    //        "params": { "limits": { "start": 0, "end": 75 }, 
    //                     "properties" : ["imdbnumber", "art", "thumbnail"] }, "id": "libMovies"}   
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies",'.
               '"params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'},'.
               '"properties": ["imdbnumber", "art", "thumbnail"] }, "id": "libMovies"}';
    
    $aJson = GetHttpRequest(cURL, $request);
    
    //$aLimits = $aJson["result"]["limits"];
    
    //debug
    //echo "<pre>";
    //print_r($aLimits);
    //echo "</pre></br>";    
    
    if (!empty($aJson["result"]["movies"])) {
        $aMovies = $aJson["result"]["movies"];        
    }
    
    return $aMovies;
}


/*
 * Function:	GetTVShowsFromXBMC
 *
 * Created on Apr 19, 2013
 * Updated on Apr 19, 2013
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
    
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows",'.
               '"params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'},'.
               '"properties": ["imdbnumber", "art", "thumbnail"] }, "id": "libTvShows"}';
    
    $aJson = GetHttpRequest(cURL, $request);
    
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
 * Updated on Apr 20, 2013
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

    $request = '{"jsonrpc": "2.0", "method": "AudioLibrary.GetAlbums",'.
               '"params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'},'.
               '"properties": ["artist", "thumbnail"] }, "id": "libAlbums"}';
    
    $aJson = GetHttpRequest(cURL, $request);
    
    //debug
    //echo "<pre>";
    //print_r($aJson);
    //echo "</pre></br>";    
    
    if (!empty($aJson["result"]["albums"])) {
        $aAlbums = $aJson["result"]["albums"];        
    }
    
    return $aAlbums;
}

/////////////////////////////////////////    Misc Functions    ////////////////////////////////////////////

/*
 * Function:	ProcessMovies
 *
 * Created on Mar 11, 2013
 * Updated on May 13, 2013
 *
 * Description: Process the movies. 
 *
 * In:  $aMovies
 * Out: -
 *
 */
function ProcessMovies($aMovies)
{  
    foreach ($aMovies as $aMovie)
    {            
        $aMovie = ConvertMovie($aMovie);        
        InsertMovie($aMovie);
    }
}

/*
 * Function:	ProcessTVShows
 *
 * Created on Apr 19, 2013
 * Updated on May 13, 2013
 *
 * Description: Process the TV Shows. 
 *
 * In:  $aTVShows, $counter
 * Out: -
 *
 */
function ProcessTVShows($aTVShows, $counter)
{  
    foreach ($aTVShows as $aTVShow)
    {            
        $aTVShow = ConvertTVShow($aTVShow);
        
        InsertTVShow($aTVShow);
    }
}


/*
 * Function:	ProcessAlbums
 *
 * Created on Apr 19, 2013
 * Updated on May 13, 2013
 *
 * Description: Process the music albums. 
 *
 * In:  $aAlbums, $counter
 * Out: -
 *
 */
function ProcessAlbums($aAlbums, $counter)
{  
    foreach ($aAlbums as $aAlbum)
    {            
        $aAlbum = ConvertAlbum($aAlbum);
        
        InsertAlbum($aAlbum);
    }
}


/*
 * Function:	ConvertMovie
 *
 * Created on Mar 11, 2013
 * Updated on Apr 27, 2013
 *
 * Description: Convert xbmc movie items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aMovie
 *
 */
function ConvertMovie($aXbmc)
{
    $poster = "images/no_poster.jpg";
    
    $aMovie["xbmcid"]  = $aXbmc["movieid"];
    $aMovie["title"]   = $aXbmc["label"];
    $aMovie["imdbnr" ] = $aXbmc["imdbnumber"];
    
    if (!empty($aXbmc["art"]["fanart"])) {
        $fanart = CleanImageLink($aXbmc["art"]["fanart"]);
    }
    else {
        $fanart = null;  
    }
    
    if (!empty($aXbmc["art"]["poster"])) 
    {
        $poster = CleanImageLink($aXbmc["art"]["poster"]);
        
        // Download the poster to a temporary folder.
        DownloadFile($poster, cTEMPPOSTERS."/".$aMovie["xbmcid"].".jpg");
        $tmp = cTEMPPOSTERS."/".$aMovie["xbmcid"].".jpg";
    }
    else { 
        $tmp = $poster;
    }
    
    if (!empty($aXbmc["thumbnail"])) {
        $thumb = CleanImageLink($aXbmc["thumbnail"]);
    }
    else {
        $thumb = null;  
    }     
    
    $aMovie["fanart"]  = $fanart;
    $aMovie["poster"]  = $poster;
    $aMovie["thumb"]   = $thumb;        
    
    // Create thumbnail locally.
    ResizeJpegImage($tmp, 100, 140, cMOVIESPOSTERS."/".$aMovie["xbmcid"].".jpg");
    
    // Delete the temporary poster.
    DeleteFile(cTEMPPOSTERS."/*.j*");
    DeleteFile(cTEMPPOSTERS."/*.p*"); 
    
    return $aMovie;
}


/*
 * Function:	ConvertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Apr 27, 2013
 *
 * Description: Convert xbmc TV Show items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aTVShow
 *
 */
function ConvertTVShow($aXbmc)
{
    $poster = "images/no_poster.jpg";
            
    $aTVShow["xbmcid"]  = $aXbmc["tvshowid"];
    $aTVShow["title"]   = $aXbmc["label"];
    $aTVShow["imdbnr" ] = $aXbmc["imdbnumber"];
    
    if (!empty($aXbmc["art"]["fanart"])) {
        $fanart = CleanImageLink($aXbmc["art"]["fanart"]);
    }
    else {
        $fanart = null;  
    }
    
    if (!empty($aXbmc["art"]["poster"])) 
    {
        $poster = CleanImageLink($aXbmc["art"]["poster"]);
        
        // Download the poster to a temporary folder.
        DownloadFile($poster, cTEMPPOSTERS."/".$aTVShow["xbmcid"].".jpg");
        $tmp = cTEMPPOSTERS."/".$aTVShow["xbmcid"].".jpg";
    }
    else {
        $tmp = $poster;
    }
    
    if (!empty($aXbmc["thumbnail"])) {
        $thumb = CleanImageLink($aXbmc["thumbnail"]);
    }
    else {
        $thumb = null;  
    }    
    
    $aTVShow["fanart"]  = $fanart;
    $aTVShow["poster"]  = $poster;
    $aTVShow["thumb"]   = $thumb;       
    
    // Create thumbnail locally.
    ResizeJpegImage($tmp, 100, 140, cTVSHOWSPOSTERS."/".$aTVShow["xbmcid"].".jpg");
    
    // Delete the temporary poster.
    DeleteFile(cTEMPPOSTERS."/*.j*");
    DeleteFile(cTEMPPOSTERS."/*.p*");    
    
    return $aTVShow;
}


/*
 * Function:	ConvertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on Apr 27, 2013
 *
 * Description: Convert XBMC album items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aAlbum
 *
 */
function ConvertAlbum($aXbmc)
{
    $no_cover = "images/no_cover.jpg";
    $aAlbum["xbmcid"] = $aXbmc["albumid"];
    $aAlbum["title"]  = addcslashes($aXbmc["label"], "'");
    $aAlbum["artist"] = addcslashes($aXbmc["artist"][0], "'");
    
    if (!empty($aXbmc["thumbnail"])) 
    {
        $cover = CleanImageLink($aXbmc["thumbnail"]);  
        $http = parse_url($cover, PHP_URL_SCHEME);
        if ($http == 'http') 
        {
            $ext = strtolower(pathinfo($cover, PATHINFO_EXTENSION));
            
            // Download the poster to a temporary folder.
            DownloadFile($cover, cTEMPPOSTERS."/".$aAlbum["xbmcid"].".".$ext);
            $tmp = cTEMPPOSTERS."/".$aAlbum["xbmcid"].".".$ext;     
        }
        else {
            $tmp = $no_cover;
        }
    }
    else 
    {
        $cover = $no_cover;
        $tmp = $no_cover;
    }    
    
    $aAlbum["cover"] = $cover;
    
    // Create thumbnail locally. This cost some time.
    ResizeJpegImage($tmp, 100, 100, cALBUMSCOVERS."/".$aAlbum["xbmcid"].".jpg");
    
    // Delete the temporary poster.
    DeleteFile(cTEMPPOSTERS."/*.j*");
    DeleteFile(cTEMPPOSTERS."/*.p*");  
    
    return $aAlbum;
}
?>
