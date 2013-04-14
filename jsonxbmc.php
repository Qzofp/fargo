<?php
/*
 * Title:   AXMC (Working title)
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    xbmc.php
 *
 * Created on Mar 22, 2013
 * Updated on Apr 14, 2013
 *
 * Description: The main XBMC functions page. 
 * 
 * Note: This page contains functions for importing media information, XBMC online and status checks. 
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'rpc/HTTPClient.php';
require_once 'include/common.php';

$aJson = null;
$action = GetPageValue('action');

switch ($action) 
{    
    case "init"    : $media = GetPageValue('media');
                     $aJson = GetXbmcValues($media);
                     break;
    
    case "online"  : $aJson['online'] = OnlineCheckXBMC();
                     break;
                
    case "import"  : ImportMovies();
                     break;
                
    case "tvshows" : $aJson = GetTotalNumberOfTVShowsFromXBMC();
                     break;
    
    case "status"  : $aJson = ImportMoviesStatus();
                     break;
    
    case "test"    : break;
                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson);
}


/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	ImportMovies
 *
 * Created on Mar 11, 2013
 * Updated on Mar 26, 2013
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
    ProcessMovies($aMovies, $counter);
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
 * Updated on Mar 11, 2013
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
    $msg = null;
    $online = true;
 
    try {
        new XBMC_RPC_HTTPClient(cXBMC);
    } 
    catch (XBMC_RPC_ConnectionException $msg) {
        //die($msg->getMessage());
        $online = false;
    }
       
    return $online;
}

/*
 * Function:	GetTotalNumberOfMoviesFromXBMC
 *
 * Created on Mar 18, 2013
 * Updated on Mar 25, 2013
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
    
    try {
        $rpc = new XBMC_RPC_HTTPClient(cXBMC);
    } 
    catch (XBMC_RPC_ConnectionException $e) {
        die($e->getMessage());
    }

    // Get total number of movies through JSON.
    // JSON: {"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", 
    //        "params": { "limits": { "start" : 0, "end": 1 }}, "id": "libMovies"}
    try {
        $aJson = $rpc->VideoLibrary->GetMovies(array("limits" => array("start"=>0, "end"=>1)));        
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

    // Get total number of movies through JSON.
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
 * Updated on Mar 20, 2013
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
    
    try {
        $rpc = new XBMC_RPC_HTTPClient(cXBMC);
    } 
    catch (XBMC_RPC_ConnectionException $e) {
        die($e->getMessage());
    }

    // Get movies through JSON.
    // JSON: {"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", 
    //        "params": {{ "limits": { "start": 0, "end": 75 } 
    //                     "properties" : ["imdbnumber", "art", "thumbnail"] }, "id": "libMovies"}
    try {
        $aJson = $rpc->VideoLibrary->GetMovies(array("limits" => array("start"=>$counter, "end"=>$counter+$offset),
                                                     "properties" =>(array("imdbnumber", "art", "thumbnail"))));        
    }
    catch (XBMC_RPC_Exception $e) {
        die($e->getMessage());
    }
    
    $aLimits = $aJson["limits"];
    if (!empty($aJson["movies"])) {
        $aMovies = $aJson["movies"];
    }
    
    return array($aLimits, $aMovies);
}
?>
