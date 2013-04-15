<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    import.php
 *
 * Created on Apr 14, 2013
 * Updated on Apr 15, 2013
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
 * Out: -
 *
 */
function ImportMovies()
{
    $counter = (int)GetSetting("MovieCounter");
    $offset  = 1;

    list($aLimits, $aMovies) = GetMoviesFromXBMC($counter, $offset);
    
    if (!empty($aMovies)) {
        ProcessMovies($aMovies, $counter);
    }
}


/*
 * Function:	ImportMoviesStatus
 *
 * Created on Mar 22, 2013
 * Updated on Mar 25, 2013
 *
 * Description: Reports the status of the import movies process. 
 *
 * In:  -
 * Out: $aJson
 *
 */
function ImportMoviesStatus()
{
    $aJson['id']     = 0;
    $aJson['xbmcid'] = 0; 
    $aJson['title']  = "empty";

    $aJson['online'] = OnlineCheckXBMC();
    
    $total = 0; 
    if ($aJson['online']) {
        $total = GetTotalNumberOfMoviesFromXBMC();
    }
    
    $counter = (int)GetSetting("MovieCounter");
    $aJson['delta']   = $total - $counter;
    $aJson['total']   = $total;
    $aJson['counter'] = $counter;

    if ($counter > 0)
    {   
        $db = OpenDatabase();

        $id     = 0;
        $xbmcid = 0;
        $title  = null;

        $sql = "SELECT id, xbmcid, title ".
               "FROM movies ".
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
 * Function:	GetXbmcValues
 *
 * Created on Apr 14, 2013
 * Updated on Apr 14, 2013
 *
 * Description: Get the initial values for XBMC.
 *
 * In:  $media
 * Out:	$aJson
 *
 * Note: XBMC Connection is defined in constant cXBMC.
 * 
 */
function GetXbmcValues($media)
{
    switch ($media)    
    {   
        case "movies"   : break;
        
        case "music"    : break;
    
        case "tvshows"  : $aJson = GetXbmcValuesTVShows();
                          break;
    }
    
    return $aJson;
}

/*
 * Function:	GetXbmcValuesTVShows
 *
 * Created on Apr 14, 2013
 * Updated on Apr 14, 2013
 *
 * Description: Get the initial values for XBMC TV Shows.
 *
 * In:  -
 * Out:	$aJson
 *
 * Note: XBMC Connection is defined in constant cXBMC.
 * 
 */
function GetXbmcValuesTVShows()
{
    if (OnlineCheckXBMC())
    {
        $aJson['online'] = true; 
        $aJson['total']  = (int)GetTotalNumberOfTVShowsFromXBMC();
    }
    else {
        $aJson['online'] = false; 
        $aJson['total']  = -1;        
    }
    
    $aJson['counter'] = (int)GetSetting("TVShowsCounter");
    
    return $aJson;
}


/*
 * Function:	OnlineCheckXBMC
 *
 * Created on Mar 11, 2013
 * Updated on Apr 15, 2013
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
    
    // Check if JSON response.
    // JSON: {"jsonrpc": "2.0", "method": "JSONRPC.Ping", "id": 1}
    $request = '{"jsonrpc": "2.0", "method": "JSONRPC.Ping", "id": 1}';
    $aJson = GetHttpRequest(cURL, $request);    
    
    if ($aJson["result"] != "pong") {
        $online = false;
    }
    return $online;
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
        $total =  $aJson["result"]["limits"]["total"];
    }
    
    return $total;
}    

/*
 * Function:	GetTotalNumberOfTVShowsFromXBMC
 *
 * Created on Apr 14, 2013
 * Updated on Apr 14, 2013
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
    
    try {
        $rpc = new XBMC_RPC_HTTPClient(cXBMC);
    } 
    catch (XBMC_RPC_ConnectionException $e) {
        die($e->getMessage());
    }

    // Get total number of TV Shows through JSON.
    // JSON: {"jsonrpc": "2.0", "method":"VideoLibrary.GetTVShows",
    //        "params": { "limits": { "start" : 0, "end": 1 }}, "id": "libTvShows"}
    try {
        $aJson = $rpc->VideoLibrary->GetTVShows(array("limits" => array("start"=>0, "end"=>1)));        
    }
    catch (XBMC_RPC_Exception $e) {
        die($e->getMessage());
    }
  
    if (!empty($aJson)) {
        $total = $aJson["limits"]["total"];
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
        UpdateSetting("MovieCounter", $counter);
        
        // Debug
        //sleep(1);
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
 * Function:    CleanImageLink
 *
 * Created on Mar 03, 2013
 * Updated on Mar 04, 2013
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
    $clean = str_replace("%2f", "/", $dummy);
    
    return $clean;
}
?>
