<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    import.php
 *
 * Created on Apr 14, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Fargo's import functions page for the XBMC media import.
 *
 */


/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	ImportMovies
 *
 * Created on Mar 11, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Import the movies. 
 *
 * In:  -
 * Out: $aJson
 *
 */
function ImportMovies()
{
    $counter = (int)GetSetting("MoviesCounter");
    $offset  = 3;

    $aMovies = GetMoviesFromXBMC($counter, $offset);
    
    if (!empty($aMovies)) {
        ProcessMovies($aMovies, $counter);
    }
    
    $aJson['counter'] = (int)GetSetting("MoviesCounter");
    
    return $aJson;
}


/*
 * Function:	ImportTVShows
 *
 * Created on Apr 19, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Import the tv shows. 
 *
 * In:  -
 * Out: $aJson
 *
 */
function ImportTVShows()
{
    $counter = (int)GetSetting("TVShowsCounter");
    $offset  = 3;
    
    $aTVShows = GetTVShowsFromXBMC($counter, $offset);
    
    if (!empty($aTVShows)) {
        ProcessTVShows($aTVShows, $counter);
    }
    
    $aJson['counter'] = (int)GetSetting("TVShowsCounter");
    
    return $aJson;
}


/*
 * Function:	ImportAlbums
 *
 * Created on Apr 20, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Import the music albums. 
 *
 * In:  -
 * Out: $aJson
 *
 */
function ImportAlbums()
{
    $counter = (int)GetSetting("AlbumsCounter");
    $offset  = 3;
    
    $aAlbums = GetAlbumsFromXBMC($counter, $offset);
    
    if (!empty($aAlbums)) {
        ProcessAlbums($aAlbums, $counter);
    }
    
    $aJson['counter'] = (int)GetSetting("AlbumsCounter");
    
    return $aJson;
}


/*
 * Function:	GetMediaCounter
 *
 * Created on Apr 17, 2013
 * Updated on Apr 17, 2013
 *
 * Description: Get the media counter.
 *
 * In:  $media
 * Out:	$aJson
 * 
 */
function GetMediaCounter($media)
{
    $aJson = null;
    
    switch ($media)    
    {   
        case "movies"   : $aJson['counter'] = (int)GetSetting("MoviesCounter");
                          break;
        
        case "music"    : $aJson['counter'] = (int)GetSetting("AlbumsCounter");
                          break;
    
        case "tvshows"  : $aJson['counter'] = (int)GetSetting("TVShowsCounter");
                          break;
    }
    
    return $aJson;
}

/*
 * Function:	GetMediaStatus
 *
 * Created on Mar 22, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  -
 * Out: $aJson
 *
 */
function GetMediaStatus($media)
{
    $aJson = null;
    
    switch ($media)    
    {   
        case "movies"   : $counter = (int)GetSetting("MoviesCounter");
                          $total   = (int)GetTotalNumberOfMoviesFromXBMC();
                          $aJson   = GetImportStatus($media, $counter, $total, cMOVIESPOSTERS);
                          break;
        
        case "music"    : $counter = (int)GetSetting("AlbumsCounter");
                          $total   = (int)GetTotalNumberOfAlbumsFromXBMC();
                          $aJson   = GetImportStatus($media, $counter, $total, cALBUMSCOVERS);
                          break;
    
        case "tvshows"  : $counter = (int)GetSetting("TVShowsCounter");
                          $total   = (int)GetTotalNumberOfTVShowsFromXBMC();
                          $aJson   = GetImportStatus($media, $counter, $total, cTVSHOWSPOSTERS);
                          break;
    }
    
    return $aJson;
}


/*
 * Function:	ImportMedia
 *
 * Created on Apr 19, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  $media
 * Out: $aJson
 *
 */
function ImportMedia($media)
{
    $aJson = null;
    
    switch ($media)    
    {   
        case "movies"   : $aJson = ImportMovies();
                          break;
        
        case "music"    : $aJson = ImportAlbums();
                          break;
    
        case "tvshows"  : $aJson = ImportTVShows();
                          break;
    }
    
    return $aJson;
}


/////////////////////////////////////////    JSON Functions    ////////////////////////////////////////////

/*
 * Function:	GetTotalNumberOfMoviesFromXBMC
 *
 * Created on Mar 18, 2013
 * Updated on Apr 20, 2013
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
    $total = 0;  

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
 * Updated on Apr 17, 2013
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
    $total = 0;    

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
 * Updated on Apr 20, 2013
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
    $total = 0;
    
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


////////////////////////////////////////    Database Functions    /////////////////////////////////////////

/*
 * Function:	GetImportStatus
 *
 * Created on Mar 22, 2013
 * Updated on Apr 21, 2013
 *
 * Description: Reports the status of the import TV Shows process. 
 *
 * In:  $media, $counter, $total, $thumbs
 * Out: $aJson
 *
 */
function GetImportStatus($media, $counter, $total, $thumbs)
{
    $aJson['id']     = 0;
    $aJson['xbmcid'] = 0; 
    $aJson['title']  = "empty";

    if (OnlineCheckXBMC())
    {
        $aJson['online'] = true; 
        $aJson['total']  = $total;
    }
    else {
        $aJson['online'] = false; 
        $aJson['total']  = -1;        
    }
    
    $aJson['delta'] = $aJson['total'] - $counter;
    $aJson['thumbs'] = $thumbs;
    
    if ($counter > 0)
    {   
        $db = OpenDatabase();

        $id     = 0;
        $xbmcid = 0;
        $title  = null;

        $sql = "SELECT id, xbmcid, title ".
               "FROM $media ".
               "WHERE id = $counter";
        
        $stmt = $db->prepare($sql);
        if($stmt)
        {
            if($stmt->execute())
            {
                // Get number of rows.
                $stmt->store_result();
                $rows = $stmt->num_rows;

                if ($rows != 0)
                {              
                    $stmt->bind_result($id, $xbmcid, $title);
                    while($stmt->fetch())
                    {                
                        $aJson['id']     = $id;
                        $aJson['xbmcid'] = $xbmcid;  
                        $aJson['title']  = $title;
                    }                  
                }
            }
            else
            {
                die('Ececution query failed: '.mysqli_error($db));
            }
            $stmt->close();
        }
        else
        {
            die('Invalid query: '.mysqli_error($db));
        } 

        CloseDatabase($db);
    }
    
    return $aJson;
}


/////////////////////////////////////////    Misc Functions    ////////////////////////////////////////////

/*
 * Function:	ProcessMovies
 *
 * Created on Mar 11, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Process the movies. 
 *
 * In:  $aMovies, $counter
 * Out: -
 *
 */
function ProcessMovies($aMovies, $counter)
{  
    foreach ($aMovies as $aMovie)
    {            
        $aMovie = ConvertMovie($aMovie);
        
        InsertMovie($aMovie);
        
        $counter++;
        UpdateSetting("MoviesCounter", $counter);
    }
}

/*
 * Function:	ProcessTVShows
 *
 * Created on Apr 19, 2013
 * Updated on Apr 20, 2013
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
        
        $counter++;
        UpdateSetting("TVShowsCounter", $counter);
    }
}


/*
 * Function:	ProcessAlbums
 *
 * Created on Apr 19, 2013
 * Updated on Apr 20, 2013
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
        
        $counter++;
        UpdateSetting("AlbumsCounter", $counter);
    }
}


/*
 * Function:	ConvertMovie
 *
 * Created on Mar 11, 2013
 * Updated on Apr 22, 2013
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
    DeleteFile(cTEMPPOSTERS."/*");
    
    return $aMovie;
}


/*
 * Function:	ConvertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Apr 22, 2013
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
    DeleteFile(cTEMPPOSTERS."/*");    
    
    return $aTVShow;
}


/*
 * Function:	ConvertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on Apr 22, 2013
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
    DeleteFile(cTEMPPOSTERS."/*");   
    
    return $aAlbum;
}
?>
