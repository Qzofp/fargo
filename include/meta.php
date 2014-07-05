<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    meta.php
 *
 * Created on Jan 10, 2014
 * Updated on Jul 04, 2014
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
    $aJson = LogEvent("Warning", "Unauthorized meta import call!");
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
 * Updated on Jun 27, 2014
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
        case 1: $aHash = CreateMediaHash($aData, "movies");
                ImportMediaMeta($db, $aData, $aHash, "movies", "movieid");
                break;
            
        // libMovieSets -> library id = 2.    
        case 2: $aHash = CreateMediaHash($aData, "sets");
                ImportMediaMeta($db, $aData, $aHash, "sets", "setid");
                break;
            
        // libTVShows -> library id = 3.    
        case 3: $aHash = CreateMediaHash($aData, "tvshows");
                ImportMediaMeta($db, $aData, $aHash, "tvshows", "tvshowid");
                break;  
          
        // libTVShowSeasons -> library id = 4.   
        case 4: $aHash = CreateMediaHash($aData, "seasons");
                ImportMediaMeta($db, $aData, $aHash, "seasons", "seasonid");
                //ImportSeasonsMeta($db, $aData);
                break;             
            
        // libTVShowEpisodes -> library id = 5.  
        case 5: $aHash = CreateMediaHash($aData, "episodes");
                ImportMediaMeta($db, $aData, $aHash, "episodes", "episodeid");
                break;
            
        // libAlbums -> library id = 6.
        case 6: $aHash = CreateMediaHash($aData, "albums");
                ImportMediaMeta($db, $aData, $aHash, "albums", "albumid");
                break;
            
        // libSongs -> library id = 7.
        case 7: $aHash = CreateMediaHash($aData, "songs");
                ImportMediaMeta($db, $aData, $aHash, "songs", "songid");
                break;              
    }
}

/*
 * Function:	CreateMediaHash
 *
 * Created on Jun 09, 2014
 * Updated on Jul 04, 2014
 *
 * Description: Create media hash. 
 *
 * In:  $aData, $type
 * Out: $aHash
 *
 * Note: Hash is used as an unique db entry.
 * 
 */
function CreateMediaHash($aData, $type)
{
    $aHash = null;
    
    if (empty($aData["error"]) && !empty($aData["result"]))
    {
        $max = count($aData["result"][$type]);
        for ($i = 0; $i < $max; $i++) 
        {
            switch ($type)
            {
                case "movies"   : $file      = !empty($aData["result"][$type][$i]["file"])?$aData["result"][$type][$i]["file"]:null; 
                                  $aHash[$i] = hash("md5", $aData["result"][$type][$i]["label"].$file);
                                  break;
                          
                case "sets"     : $aHash[$i] = hash("md5", $aData["result"][$type][$i]["label"]);
                                  break;
                
                case "tvshows"  : $file      = !empty($aData["result"][$type][$i]["file"])?$aData["result"][$type][$i]["file"]:null; 
                                  $aHash[$i] = hash("md5", $aData["result"][$type][$i]["label"].$file);
                                  break;
                              
                case "seasons"  : $showtitle = !empty($aData["result"][$type][$i]["showtitle"])?$aData["result"][$type][$i]["showtitle"]:null;
                                  $aHash[$i] = hash("md5", $aData["result"][$type][$i]["label"].$showtitle);
                                  break;
                
                case "episodes" : $episode   = !empty($aData["result"][$type][$i]["episode"])?$aData["result"][$type][$i]["episode"]:0;
                                  $file      = !empty($aData["result"][$type][$i]["file"])?$aData["result"][$type][$i]["file"]:null; 
                                  $aHash[$i] = hash("md5", $episode.$file);
                                  break;
                              
                case "albums"   : $artist    = !empty($aData["result"][$type][$i]["artist"])?implode("|", $aData["result"][$type][$i]["artist"]):null;
                                  $year      = !empty($aData["result"][$type][$i]["year"])?$aData["result"][$type][$i]["year"]:0;
                                  $aHash[$i] = hash("md5", $aData["result"][$type][$i]["label"].$artist.$year);
                                  break;
                              
                case "songs"    : $track     = !empty($aData["result"][$type][$i]["track"])?$aData["result"][$type][$i]["track"]:0;
                                  $file      = !empty($aData["result"][$type][$i]["file"])?$aData["result"][$type][$i]["file"]:null; 
                                  $aHash[$i] = hash("md5", $track.$aData["result"][$type][$i]["label"].$file);
                                  break;                              
            }     
        }
    }    
    
    return $aHash;
}

/*
 * Function:	ImportMediaMeta
 *
 * Created on Jan 10, 2014
 * Updated on Jun 16, 2014
 *
 * Description: Import media meta data. 
 *
 * In:  $db, $aData, $aHash, $type, $id
 * Out: -
 *
 */
function ImportMediaMeta($db, $aData, $aHash, $type, $id)
{       
    if (empty($aData["error"]) && !empty($aData["result"]))
    {
        $max = count($aData["result"][$type]);
        $sql = "INSERT INTO ".$type."meta($id, playcount, hash) VALUES";
                
        for ($i = 0; $i < $max - 1; $i++) {
           $sql .= "(".$aData["result"][$type][$i][$id].", '".$aData["result"][$type][$i]['playcount']."', unhex('$aHash[$i]')),"; 
        }

        $sql .= "(".$aData["result"][$type][$i][$id].", '".$aData["result"][$type][$i]['playcount']."', unhex('$aHash[$i]'))";
        
        QueryDatabase($db, $sql);
    }
    
    if ($type == "seasons") {
        UpdateStatus($db, "ImportEnd", CountRows($db, "seasonsmeta"));
    }
}