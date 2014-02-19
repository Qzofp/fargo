<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    search.php
 *
 * Created on Jan 28, 2014
 * Updated on Feb 18, 2014
 *
 * Description: Fargo's search page. This page is called from XBMC which push the data to Fargo.
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

// Give the damn thing cross domain access. Something XBMC won't let you do, the bastards!!!
header("Access-Control-Allow-Origin: *");  // Add "*" to settings.

require_once '../settings.php';
require_once '../tools/toolbox.php';
require_once 'common.php';

$db = OpenDatabase();

$login = CheckImportKey($db);
if($login)
{
    $aResults = ReceiveSearchResults();
    ProcessSearchResults($db, $aResults);
}
else 
{
    $aJson = LogEvent("Warning", "Unauthorized search call!");
    echo json_encode($aJson);
}

CloseDatabase($db);

/*
 * Function:	ReceiveSearchResults
 *
 * Created on Jan 28, 2014
 * Updated on Feb 01, 2014
 *
 * Description: Receive search results from XBMC. 
 *
 * In:  -
 * Out: $aData
 *
 */    
function ReceiveSearchResults()
{
    $aData = null;    
        
    $aData["id"] = null;
    if (isset($_POST["id"]) && !empty($_POST["id"]))
    {
        $aData["id"] = $_POST["id"];
    }    
    
    $aData["xbmcid"] = null;
    if (isset($_POST["xbmcid"]) && !empty($_POST["xbmcid"]))
    {
        $aData["xbmcid"] = $_POST["xbmcid"];
    }        
    
    $aData["error"] = null;
    if (isset($_POST["error"]) && !empty($_POST["error"]))
    {
        $aData["error"] = $_POST["error"];
    }  
      
    $aData["result"] = null;
    if (isset($_POST["result"]) && !empty($_POST["result"]))
    {
        $aData["result"] = $_POST["result"];
    }
    
    return $aData;
}

/*
 * Function:	ProcessSearchResults
 *
 * Created on Jan 29, 2014
 * Updated on Feb 18, 2014
 *
 * Description: Process search results from XBMC. 
 *
 * In:  $db, $aData
 * Out: -
 *
 */
function ProcessSearchResults($db, $aResults)
{
    if (!empty($aResults["result"]))
    {    
        switch($aResults["id"])
        {
            // libMovies -> library id = 1.
            case 1: UpdateSearchResults($db, $aResults, "movies", "movieid");
                    break;
            
            // libMovieSets -> library id = 2.    
            case 2: CheckAndUpdateResults($db, $aResults, "setdetails", "setid");
                    break;
            
            // libTVShows -> library id = 3.    
            case 3: UpdateSearchResults($db, $aResults, "tvshows", "tvshowid");
                    break;  
          
            // libTVShowSeasons -> library id = 4.   
            case 4: CheckAndUpdateResults($db, $aResults, "seasondetails", "seasonid");
                    break;             
            
            // libTVShowEpisodes -> library id = 5.  
            case 5: UpdateSearchResults($db, $aResults, "episodes", "episodeid");
                    break;
            
            // libAlbums -> library id = 6.
            case 6: UpdateSearchResults($db, $aResults, "music", "music");
                    break;            
        }
    }
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error.
    }
}

/*
 * Function:	UpdateSearchResults
 *
 * Created on Jan 29, 2014
 * Updated on Feb 18, 2014
 *
 * Description: Update search results for refresh purposes.
 *
 * In:  $db, $aResults, $type, $typeid
 * Out: -
 *
 */
function UpdateSearchResults($db, $aResults, $type, $typeid)
{
    if (empty($aResults["error"])) 
    {
        $status = cTRANSFER_NO_MATCH;
        if ($aResults["result"]["limits"]["total"] > 0)
        {
            for ($i = 0; $i < $aResults["result"]["limits"]["total"]; $i++) 
            {
                if ($aResults["result"][$type][$i][$typeid] > $status) {
                    $status = $aResults["result"][$type][$i][$typeid]; // Find highest id.
                }
            
                if ($aResults["xbmcid"] == $aResults["result"][$type][$i][$typeid]) 
                {
                    $status = $aResults["xbmcid"]; // Match.
                    $i = $aResults["result"]["limits"]["total"];
                }
            }
            UpdateStatus($db, "ImportStatus", $status); 
        }
        else {
            UpdateStatus($db, "ImportStatus", $status); // No match.
        }
    }
    else if ($aError["code"] == cTRANSFER_INVALID) {  // Not found.
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND);
    }
}

/*
 * Function:	CheckAndUpdateResults
 *
 * Created on Feb 03, 2014
 * Updated on Feb 18, 2014
 *
 * Description: Check if the id exists and then update the status.
 *
 * In:  $db, $aResults, $details, $typeid
 * Out: -
 *
 * Note: Sets and Seasons can't be filtered, hence the check if the id exists.
 * 
 */
function CheckAndUpdateResults($db, $aResults, $details, $typeid)
{
    if (empty($aResults["error"])) 
    {
        if (!empty($aResults["result"][$details][$typeid])) {
            UpdateStatus($db, "ImportStatus", $aResults["result"][$details][$typeid]);
        }
    }
    else if ($aError["code"] == cTRANSFER_INVALID) {  // Not found.
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND);
    }
}