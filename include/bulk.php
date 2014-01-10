<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    bulk.php
 *
 * Created on Jan 10, 2014
 * Updated on Jan 10, 2014
 *
 * Description: Fargo's bulk import page. This page is called from XBMC which push the data to Fargo.
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
    $aData = ReceiveBulkData();
    ProcessBulkData($db, $aData);
}
else 
{
    $aJson = LogEvent("Warning", "Unauthorized import call!");
    echo json_encode($aJson);
}

CloseDatabase($db);

//debug
//echo "<pre>";
//print_r($aData);
//echo "</pre></br>";

/*
 * Function:	ReceiveBulkData
 *
 * Created on Jan 10, 2014
 * Updated on Jan 10, 2014
 *
 * Description: Receive bulk data from XBMC. 
 *
 * In:  -
 * Out: $aData
 *
 */    
function ReceiveBulkData()
{
    $aData = null;    
    
    $aData["id"] = null;
    if (isset($_POST["id"]) && !empty($_POST["id"]))
    {
        $aData["id"] = $_POST["id"];
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
 * Function:	ProcessBulkData
 *
 * Created on Jan 10 15, 2013
 * Updated on Jan 10, 2014
 *
 * Description: Process bulk data from XBMC. 
 *
 * In:  $db, $aData
 * Out: -
 *
 */
function ProcessBulkData($db, $aData)
{
    switch($aData["id"])
    {
        // libMovies Import -> library id = 1.
        case 1: BulkImportMedia($db, $aData, "movies", "movieid");
                break;
            
        // libTVShowEpisode Import -> library id = 5.  
        case 5: BulkImportMedia($db, $aData, "episodes", "episodeid");
                break;
    }
}

/*
 * Function:	BulkImportMedia
 *
 * Created on Jan 10, 2013
 * Updated on Jan 10, 2014
 *
 * Description: Import bulk media. 
 *
 * In:  $db, $aData, $type, $id
 * Out: -
 *
 */
function BulkImportMedia($db, $aData, $type, $id)
{       
    if (empty($aData["error"]) && !empty($aData["result"]))
    {
        $max = count($aData["result"][$type]);
        $sql = "INSERT INTO ".$type."bulk($id, lastplayed) VALUES";
        
        for ($i = 0; $i < $max - 1; $i++) {
           $sql .= "(".$aData["result"][$type][$i][$id].", '".$aData["result"][$type][$i]['lastplayed']."'),"; 
        }

        $sql .= "(".$aData["result"][$type][$i][$id].", '".$aData["result"][$type][$i]['lastplayed']."')";       
        QueryDatabase($db, $sql);
    }
}