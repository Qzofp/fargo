<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    meta.php
 *
 * Created on Jan 10, 2014
 * Updated on Jan 27, 2014
 *
 * Description: Fargo's meta data import page. This page is called from XBMC which push the data to Fargo.
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
    $aData = ReceiveMetaData();
    ProcessMetaData($db, $aData);
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
 * Function:	ReceiveMetaData
 *
 * Created on Jan 10, 2014
 * Updated on Jan 11, 2014
 *
 * Description: Receive meta data from XBMC. 
 *
 * In:  -
 * Out: $aData
 *
 */    
function ReceiveMetaData()
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
 * Function:	ProcessMetaData
 *
 * Created on Jan 10, 2014
 * Updated on Jan 20, 2014
 *
 * Description: Process bulk data from XBMC. 
 *
 * In:  $db, $aData
 * Out: -
 *
 */
function ProcessMetaData($db, $aData)
{
    switch($aData["id"])
    {
        // libMovies -> library id = 1.
        case 1: ImportMediaMeta($db, $aData, "movies", "movieid");
                break;
            
         // libMovieSets -> library id = 2.    
        case 2: ImportMediaMeta($db, $aData, "sets", "setid");
                break;
            
         // libTVShows -> library id = 3.    
        case 3: ImportMediaMeta($db, $aData, "tvshows", "tvshowid");
                break;  
          
         // libTVShowSeasons -> library id = 4.   
        case 4: ImportSeasonsMeta($db, $aData);
                break;             
            
        // libTVShowEpisodes -> library id = 5.  
        case 5: ImportMediaMeta($db, $aData, "episodes", "episodeid");
                break;
            
        // libAlbums -> library id = 6.
        case 6: ImportMediaMeta($db, $aData, "albums", "albumid");
                break;            
    }
}

/*
 * Function:	ImportMediaMeta
 *
 * Created on Jan 10, 2014
 * Updated on Jan 11, 2014
 *
 * Description: Import media meta data. 
 *
 * In:  $db, $aData, $type, $id
 * Out: -
 *
 */
function ImportMediaMeta($db, $aData, $type, $id)
{       
    if (empty($aData["error"]) && !empty($aData["result"]))
    {
        $max = count($aData["result"][$type]);
        $sql = "INSERT INTO ".$type."meta($id, playcount) VALUES";
                
        for ($i = 0; $i < $max - 1; $i++) {
           $sql .= "(".$aData["result"][$type][$i][$id].", '".$aData["result"][$type][$i]['playcount']."'),"; 
        }

        $sql .= "(".$aData["result"][$type][$i][$id].", '".$aData["result"][$type][$i]['playcount']."')";       
        QueryDatabase($db, $sql);
    }
}

/*
 * Function:	ImportSeasonsMeta
 *
 * Created on Jan 10, 2014
 * Updated on Jan 27, 2014
 *
 * Description: Import media meta data. 
 *
 * In:  $db, $aData
 * Out: -
 *
 */
function ImportSeasonsMeta($db, $aData)
{       
    if (empty($aData["error"]) && !empty($aData["result"]))
    {
        $max = count($aData["result"]["seasons"]);
        $sql = "INSERT INTO seasonsmeta(seasonid, playcount) VALUES";
                
        for ($i = 0; $i < $max - 1; $i++) {
           $sql .= "(".$aData["result"]["seasons"][$i]["seasonid"].", '".$aData["result"]["seasons"][$i]['playcount']."'),"; 
        }

        $sql .= "(".$aData["result"]["seasons"][$i]["seasonid"].", '".$aData["result"]["seasons"][$i]['playcount']."')"; 
        QueryDatabase($db, $sql);
        
        // Update number of seasons (row count).
        UpdateStatus($db, "XbmcSeasonsEnd", mysqli_insert_id($db));
    }
}