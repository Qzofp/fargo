<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    import.php
 *
 * Created on Apr 14, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Fargo's import functions page for the XBMC media import.
 *
 */


/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	ImportMovies
 *
 * Created on Mar 11, 2013
 * Updated on Apr 15, 2013
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
    $offset  = 5;

    list($aLimits, $aMovies) = GetMoviesFromXBMC($counter, $offset);
    
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
 * Updated on Apr 19, 2013
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
    $offset  = 1;
    
    $aTVShows = GetTVShowsFromXBMC($counter, $offset);
    
    if (!empty($aTVShows)) {
        ProcessTVShows($aTVShows, $counter);
    }
    
    $aJson['counter'] = (int)GetSetting("TVShowsCounter");
    
    return $aJson;
}


/*
 * Function:	GetImportStatus
 *
 * Created on Mar 22, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Reports the status of the import TV Shows process. 
 *
 * In:  $counter, $media
 * Out: $aJson
 *
 */
function GetImportStatus($counter, $media)
{
    $aJson['id']     = 0;
    $aJson['xbmcid'] = 0; 
    $aJson['title']  = "empty";

    if (OnlineCheckXBMC())
    {
        $aJson['online'] = true; 
        $aJson['total']  = (int)GetTotalNumberOfTVShowsFromXBMC();
    }
    else {
        $aJson['online'] = false; 
        $aJson['total']  = -1;        
    }
    
    $aJson['delta'] = $aJson['total'] - $counter;
    $aJson['thumbs'] = cTVSHOWSTHUMBS;
    
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


/////////////////////////////////////////    JSON Functions    ////////////////////////////////////////////

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
        
        case "music"    : $aJson['counter'] = (int)GetSetting("MusicCounter");
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
 * Updated on Apr 20, 2013
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
        case "movies"   : 
                          break;
        
        case "music"    : 
                          break;
    
        case "tvshows"  : $counter = (int)GetSetting("TVShowsCounter");
                          $aJson = GetImportStatus($counter, $media);
                          break;
    }
    
    return $aJson;
}


/*
 * Function:	ImportMedia
 *
 * Created on Apr 19, 2013
 * Updated on Apr 19, 2013
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
        case "movies"   : 
                          break;
        
        case "music"    : 
                          break;
    
        case "tvshows"  : //echo $media;
                          $aJson = ImportTVShows();
                          break;
    }
    
    return $aJson;
}


/*
 * Function:	GetTotalNumberOfMoviesFromXBMC
 *
 * Created on Mar 18, 2013
 * Updated on Apr 15, 2013
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
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies","params": { "limits": { "start" : 0, "end": 1 }}, "id": "libMovies"}';
    $aJson = GetHttpRequest(cURL, $request);
  
    //debug
    //echo "<pre>";
    //print_r($aJson);
    //echo "</pre></br>";   
    
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

    // Get total number of TV Shows through JSON.
    // JSON: {"jsonrpc": "2.0", "method":"VideoLibrary.GetTVShows",
    //        "params": { "limits": { "start" : 0, "end": 1 }}, "id": "libTvShows"}
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows",'.
               '"params": { "limits": { "start" : 0, "end": 1 }}, "id": "libTvShows"}';
    
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
 * Updated on Apr 14, 2013
 *
 * Description: Connect to XBMC and get the Movies information.
 *
 * In:  $counter, $offset (max. number of movies received from XBMC)
 * Out:	$aLimits, $aMovies
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
    //$counter = 114;
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies","params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'}, "properties" : ["imdbnumber", "art", "thumbnail"] }, "id": "libMovies"}';
    
    $aJson = GetHttpRequest(cURL, $request);
    
    $aLimits = $aJson["result"]["limits"];
    
    //debug
    //echo "<pre>";
    //print_r($aLimits);
    //echo "</pre></br>";    
    
    if (!empty($aJson["result"]["movies"])) {
        $aMovies = $aJson["result"]["movies"];        
    }
    
    return array($aLimits, $aMovies);
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
 * Out:	$aLimits, $aMovies
 * 
 * Note: XBMC Connection is defined in constant cXBMC.
 *
 */
function GetTVShowsFromXBMC($counter, $offset)
{    
    $aTVShows = null;
    
    // Get movies through JSON.
    // JSON: {"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", 
    //        "params": { "limits": { "start": 0, "end": 75 }, 
    //                     "properties" : ["imdbnumber", "art", "thumbnail"] }, "id": "libMovies"}   
    //$counter = 114;
    $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows",'.
               '"params": {"limits": {"start": '.$counter.', "end": '.($counter+$offset).'},'.
               '"properties" : ["imdbnumber", "art", "thumbnail"] }, "id": "libTvShows"}';
    
    $aJson = GetHttpRequest(cURL, $request);
    
    //$aLimits = $aJson["result"]["limits"];
    
    //debug
    //echo "<pre>";
    //print_r($aJson);
    //echo "</pre></br>";    
    
    if (!empty($aJson["result"]["tvshows"])) {
        $aTVShows = $aJson["result"]["tvshows"];        
    }
    
    return $aTVShows;
}


/////////////////////////////////////////    Misc Functions    ////////////////////////////////////////////

/*
 * Function:	ProcessMovies
 *
 * Created on Mar 11, 2013
 * Updated on Apr 05, 2013
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
        
        // Import movie and create thumbnail locally. This cost some time.
        ResizeJpegImage($aMovie["thumb"], 100, 140, cMOVIESTHUMBS."/".$aMovie["xbmcid"].".jpg");
        
        InsertMovie($aMovie);
        
        $counter++;
        UpdateSetting("MoviesCounter", $counter);
        
        // Debug
        //sleep(1);
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
        
        // Import movie and create thumbnail locally. This cost some time.
        ResizeJpegImage($aTVShow["poster"], 100, 140, cTVSHOWSTHUMBS."/".$aTVShow["xbmcid"].".jpg");
        
        InsertTVShows($aTVShow);
        
        $counter++;
        UpdateSetting("TVShowsCounter", $counter);
    }
}


/*
 * Function:	ConvertMovie
 *
 * Created on Mar 11, 2013
 * Updated on Mar 11, 2013
 *
 * Description: Convert xbmc movie items. For instance to readably URL's.
 *
 * In:  $aXbmc_movie
 * Out: $aMovie
 *
 */
function ConvertMovie($aXbmc_movie)
{
    $aMovie["xbmcid"]  = $aXbmc_movie["movieid"];
    $aMovie["title"]   = $aXbmc_movie["label"];
    $aMovie["imdbnr" ] = $aXbmc_movie["imdbnumber"];
    $aMovie["fanart"]  = CleanImageLink($aXbmc_movie["art"]["fanart"]);
    $aMovie["poster"]  = CleanImageLink($aXbmc_movie["art"]["poster"]);
    $aMovie["thumb"]   = CleanImageLink($aXbmc_movie["thumbnail"]);        
    
    return $aMovie;
}


/*
 * Function:	ConvertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Convert xbmc TV Show items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aTVShow
 *
 */
function ConvertTVShow($aXbmc)
{
    $aTVShow["xbmcid"]  = $aXbmc["tvshowid"];
    $aTVShow["title"]   = $aXbmc["label"];
    $aTVShow["imdbnr" ] = $aXbmc["imdbnumber"];
    
    if (!empty($aXbmc["art"]["fanart"])) {
        $fanart = CleanImageLink($aXbmc["art"]["fanart"]);
    }
    else {
        $fanart = null;  
    }
    
    if (!empty($aXbmc["art"]["poster"])) {
        $poster = CleanImageLink($aXbmc["art"]["poster"]);
    }
    else {
        $poster = "images/no_poster.jpg";
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
    
    return $aTVShow;
}


/*
 * Function:    CleanImageLink
 *
 * Created on Mar 03, 2013
 * Updated on Apr 19, 2013
 *
 * Description: Cleans the image link from XBMC.
 *
 * In:  $dirty (link)
 * Out:	$clean (link)
 *
 */
function CleanImageLink($dirty)
{
    $dummy = str_replace("image://http%3a", "http:", $dirty);
    $clean = rtrim(str_replace("%2f", "/", $dummy), "/");
    
    return $clean;
}
?>
