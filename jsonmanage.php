<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    jsonmanage.php
 *
 * Created on Nov 20, 2013
 * Updated on Jul 28, 2014
 *
 * Description: The main Json Manage page.
 * 
 * Note: This page contains the management functions that returns Json data for Jquery code.
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

session_start();
if(!isset($_SESSION['LOGIN'])) {
    $login = false;
}
else {
    $login = true;
}

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

$aJson = null;
$action = GetPageValue('action');

switch($action)
{                                
    case "hide"     : if($login)
                      {
                         $media = GetPageValue('media');
                         $id    = GetPageValue('id');
                         $value = GetPageValue('value');
                         $aJson = HideOrShowMediaInFargo($media, $id, $value);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized hide action call!");
                      }                     
                      break;
                   
    case "delete"   : if($login)
                      {
                         $media  = GetPageValue('media'); 
                         $id     = GetPageValue('id');
                         $aJson  = RemoveMediaFromFargo($media, $id);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized delete action call!");
                      }
                      break;                  
             
    case "reset"    : if($login) 
                      {
                         $media   = GetPageValue('media');
                         $aJson   = ResetStatus($media);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized reset action call!");
                      }
                      break;
                    
    case "counter"  : if($login)
                      {
                         $media = GetPageValue('media');
                         $aJson = GetCountersStatus($media);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized counter action call!");
                      }                     
                      break;
                      
    case "init"     : if($login){
                          $aJson = InitStatus();
                      }
                      else {
                          $aJson = LogEvent("Warning", "Unauthorized init action call!");
                      }
                      break;    
                          
    case "import"   : if($login)
                      {
                          $mode  = GetPageValue('mode');
                          $aJson = ProcessImportMode($mode);
                      }
                      else {
                          $aJson = LogEvent("Warning", "Unauthorized import action call!");
                      }
                      break;
                      
    case "status"   : if($login)
                      {
                         $media  = GetPageValue('media');
                         $id     = GetPageValue('id');
                         $xbmcid = GetPageValue('xbmcid');
                        
                         $aJson = GetMediaStatus($media, $id, $xbmcid);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized status action call!");
                      }                        
                      break;
                      
    case "initmeta" : if($login)
                      { 
                        $type  = GetPageValue('type');
                        $aJson = InitMeta($type);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized init meta action call!");
                      }                    
                      break;
                      
    case "chkmeta"  : if($login)
                      { 
                        $type  = GetPageValue('type');
                        $aJson = CheckMeta($type);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized check meta action call!");
                      }                    
                      break;                      
                      
    case "tvshowids": // Get TV Shows id's to retrieve the Seasons meta data.
                      if($login)
                      { 
                         $start  = GetPageValue('start');
                         $offset = GetPageValue('offset');
                         $aJson  = GetTVShowsIds($start, $offset);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized series (meta data) action call!");
                      }                    
                      break;  
                                            
    case "property" : if($login)
                      {
                         $option = GetPageValue('option');
                         $number = GetPageValue('number');
                         $value  = GetPageValue('value');
                         $aJson  = SetSystemProperty($option, $number, $value);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized property action call!");
                      }                       
                      break; 
                    
    case "log"     : if($login)
                     { 
                        $type  = GetPageValue('type');
                        $event = GetPageValue('event');
                        $aJson = LogEvent($type, $event);
                     }
                     else {
                        $aJson = LogEvent("Warning", "Unauthorized log action call!");
                     }                    
                     break;                  
}                     

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson);
}

///////////////////////////////////////    Management Functions    ////////////////////////////////////////

/*
 * Function:	HideOrShowMediaInFargo
 *
 * Created on Nov 20, 2013
 * Updated on Jul 19, 2014
 *
 * Description: Hide or show media.
 *
 * In:  $media, $id, $value
 * Out:	$aJson
 * 
 */
function HideOrShowMediaInFargo($media, $id, $value)
{
    $aJson = null;
    $db = OpenDatabase();
    
    switch ($media)
    {
        case "titles"   : $aJson = HideOrShowMedia($db, "movies", $id, $value);
                          break;
        
        case "sets"     : $aJson = HideOrShowMedia($db, "sets", $id, $value);
                          break;
                      
        case "movieset" : $aJson = HideOrShowMedia($db, "movies", $id, $value);
                          break;                      
                    
        case "tvtitles" : $aJson = HideOrShowMedia($db, "tvshows", $id, $value);
                          break;
                      
        case "series"   : $aItems = explode("_", $id);
                          $aJson = HideOrShowMedia($db, "tvshows", $aItems[0], $value);
                          break;                      

        case "seasons"  : $aItems = explode("_", $id);
                          $aJson = HideOrShowMedia($db, "seasons", $aItems[0], $value);
                          break;
                      
        case "episodes" : $aJson = HideOrShowMedia($db, "episodes", $id, $value);
                          break;
                      
        case "albums"   : $aJson = HideOrShowMedia($db, "albums", $id, $value);
                          break; 
                      
        case "songs"    : $aJson = HideOrShowMedia($db, "albums", $id, $value);
                          break;  
                      
        case "tracks"   : $aJson = HideOrShowMedia($db, "songs", $id, $value);
                          break;                        
    }
    
    CloseDatabase($db);     
    return $aJson;
}

/*
 * Function:	HideOrShowMedia
 *
 * Created on Sep 23, 2013
 * Updated on Jul 04, 2014
 *
 * Description: Update hide media hide column.
 *
 * In:  $db, $media, $id, $value
 * Out:	Update hide column
 * 
 */
function HideOrShowMedia($db, $table, $id, $value)
{
    $aJson = null;
    
    $sql = "UPDATE $table ".
           "SET hide = $value ".
           "WHERE id = $id";
      
    QueryDatabase($db, $sql);
    
    $aJson["ready"] = true;
    return $aJson;
}

/*
 * Function:	RemoveMediaFromFargo
 *
 * Created on Nov 22, 2013
 * Updated on Jul 03, 2014
 *
 * Description: Delete media from Fargo database.
 *
 * In:  $media, $id
 * Out:	Deleted media
 * 
 */
function RemoveMediaFromFargo($media, $id)
{   
    $aJson = null;
    $db = OpenDatabase();    
    
    switch ($media)
    {
        case "titles"   : $aJson = DeleteMedia($db, "movies", $id);
                          break;
        
        case "sets"     : $aJson = DeleteMedia($db, "sets", $id);
                          break;
                    
        case "movieset" : $aJson = DeleteMedia($db, "movies", $id);
                          break;
                      
        case "tvtitles" : $aJson = DeleteMedia($db, "tvshows", $id); // TV Show + Seasons + Episodes.
                          break;
                      
        case "series"   : $aJson = DeleteMedia($db, "seasons", $id); // Seasons + Episodes.
                          break;  
                      
        case "seasons"  : $aJson = DeleteMedia($db, "seasons", $id); // Season + Episodes.
                          break;                      
                      
        case "episodes" : $aJson = DeleteMedia($db, "episodes", $id);
                          break;
                      
        case "albums"   : $aJson = DeleteMedia($db, "albums", $id); // Album + Songs.
                          break;
                      
        case "songs"    : $aJson = DeleteMedia($db, "songs", $id);
                          break; 
                      
        case "tracks"   : $aJson = DeleteMedia($db, "songs", $id);
                          break;                    
    }
    
    CloseDatabase($db);      
    return $aJson;   
}

/*
 * Function:	DeleteMedia
 *
 * Created on Oct 05, 2013
 * Updated on Jul 19, 2014
 *
 * Description: Delete media from Fargo database.
 *
 * In:  $db, $media, $id
 * Out:	Deleted media
 * 
 */
function DeleteMedia($db, $media, $id)
{
    $aJson = null;
    
    switch ($media)
    {
        case "movies"   : DeleteMediaQuery($db, "movies", $id);
                          DeleteMediaGenreQuery($db, "movie", $id);
                          break;
                     
        case "sets"     : // Won't delete the movies in the set. Maybe in the future releases.
                          DeleteMediaQuery($db, "sets", $id);
                          break;
                            
       case "tvshows"  : // Delete episodes.                     
                          $sql = "DELETE FROM episodes WHERE tvshowid = $id";
                          QueryDatabase($db, $sql);
                          
                          // Delete seasons.                        
                          $sql = "DELETE FROM seasons WHERE tvshowid = $id";
                          QueryDatabase($db, $sql);
                          
                          // Delete TV show.
                          DeleteMediaQuery($db, "tvshows", $id);
                          DeleteMediaGenreQuery($db, "tvshow", $id);
                          break;
                     
        case "seasons"  : // Delete episodes.                
                          $sql = "DELETE FROM episodes ".
                                 "WHERE tvshowid = (SELECT tvshowid FROM seasons WHERE id = $id) ".
                                 "AND season = (SELECT season FROM seasons WHERE id = $id)";                                  
                          QueryDatabase($db, $sql);
                         
                          // Delete seasons.
                          DeleteMediaQuery($db, "seasons", $id);  
                          break;
                     
        case "episodes" : DeleteMediaQuery($db, "episodes", $id);
                          break;                     
                            
        case "albums"   : // Delete songs.                    
                          $sql = "DELETE FROM songs WHERE albumid = $id";
                          QueryDatabase($db, $sql);

                          // Delete Album.
                          DeleteMediaQuery($db, "albums", $id);
                          DeleteMediaGenreQuery($db, "music", $id);
                          break;  
                      
        case "songs"    : DeleteMediaQuery($db, "songs", $id);
                          DeleteMediaGenreQuery($db, "music", $id);
                          break;              
    } 
                          
    $aJson["ready"] = true;
    return $aJson;    
}

/*
 * Function:	DeleteMediaQuery
 *
 * Created on Nov 22, 2013
 * Updated on Jul 01, 2014
 *
 * Description: Delete media from the Fargo database.
 *
 * In:  $db, $table, $id
 * Out:	Deleted media
 * 
 */
function DeleteMediaQuery($db, $table, $id)
{
    $sql = "DELETE FROM $table ".
           "WHERE id = $id";
    
    QueryDatabase($db, $sql);
}

/*
 * Function:	DeleteMediaGenreQuery
 *
 * Created on Nov 22, 2013
 * Updated on Jul 07, 2014
 *
 * Description: Delete media genre from Fargo database.
 *
 * In:  $db, $name, $id
 * Out:	Deleted media genre
 * 
 */
function DeleteMediaGenreQuery($db, $name, $id)
{
    $sql = "DELETE FROM genreto$name ".
           "WHERE ".$name."id = $id";
    
    QueryDatabase($db, $sql);
}

/////////////////////////////////////////    Status Functions    //////////////////////////////////////////

/*
 * Function:	ResetStatus
 *
 * Created on Jul 22, 2013
 * Updated on Jul 28, 2014
 *
 * Description: Reset the status. 
 *
 * In:  $media
 * Out: $aJson
 *
 */
function ResetStatus($media)
{       
    $aJson = null;
    
    $db = OpenDatabase();
    
    if ($media == "tvseasons") {
        $media = "seasons";
    }
    
    UpdateStatus($db, "ImportStatus", -1); // Needed for refresh media.   
    UpdateStatus($db, "ImportCounter", 1);   
    UpdateStatus($db, "ImportStart", 1);
    
    // Fill json.
    $aJson["connection"] = GetSetting($db, "XBMCconnection");
    $aJson["port"]       = GetSetting($db, "XBMCport");
    $aJson["username"]   = GetSetting($db, "XBMCusername");
    $aJson["password"]   = GetSetting($db, "XBMCpassword"); 
    $aJson["timeout"]    = GetSetting($db, "Timeout");
    $aJson["key"]        = GenerateKey($db);    
    $aJson["status"]     = "reset";  
    
    CloseDatabase($db);
    
    return $aJson;
}

/*
 * Function:	GetCountersStatus
 *
 * Created on Jan 03, 2014
 * Updated on Jun 27, 2014
 *
 * Description: Get status counter
 *
 * In:  $media
 * Out: $aJson
 *
 */
function GetCountersStatus($media)
{
    $aJson = null;    
    $db = OpenDatabase(); 
    
    $aJson['xbmc']['start'] = GetStatus($db, "ImportStart");
    
    if ($media == "tvseasons") {
        $aJson['xbmc']['end'] = CountRows($db, "tvshows");
    }
    else {
        $aJson['xbmc']['end'] = GetStatus($db, "ImportEnd");
    }
     
    $aJson['import'] = GetStatus($db, "ImportCounter");
    
    CloseDatabase($db); 
    return $aJson;    
}

/*
 * Function:	CreateMetaStartQuery
 *
 * Created on May 12, 2014
 * Updated on May 12, 2014
 *
 * Description: Create query to get te meta start value.
 *
 * In:  $db, $media
 * Out: $sql
 *
 */
/*function CreateMetaStartQuery($db, $media) // Obsolete
{
    
    switch ($media)
    {
        case "movies"   : $meta = "moviesmeta";
                          $name = "movieid";
                          break;
                      
        case "sets"     : $meta = "setsmeta";
                          $name = "setid";
                          break;
   
        case "tvshows"  : $meta = "tvshowsmeta";
                          $name = "tvshowid";
                          break;
                     
        case "seasons"  : $meta = "seasonsmeta";
                          $name = "seasonid";
                          break;
                     
        case "episodes" : $meta = "episodesmeta";
                          $name = "episodeid";
                          break;
                      
        case "albums"   : $meta = "albumsmeta";
                          $name = "albumid";
                          break;                         
                      
        case "music"    : $meta = "albumsmeta";
                          $name = "albumid";
                          break;                
    }
    
    // Get meta import start value.
    $xbmcid = GetStatus($db, "Xbmc".$media."Start");
    $sql = "SELECT COUNT(*)+1 AS id FROM $meta WHERE $name <= $xbmcid";
    
    return $sql;
}*/

/*
 * Function:	InitStatus
 *
 * Created on Jan 03, 2014
 * Updated on May 12, 2014
 *
 * Description: Initialize import ready status.
 *
 * In:  -
 * Out: $aJson
 *
 */
function InitStatus()
{        
    $aJson = null;
    $db = OpenDatabase();
    
    $aJson['ready'] = GetStatus($db, "ImportLock");
    if ($aJson['ready'] >= 0) {
        UpdateStatus($db, "ImportLock", -1);
    }
    
    CloseDatabase($db);
    return $aJson;
}
                          
/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	ProcessImportMode
 *
 * Created on Dec 13, 2013
 * Updated on May 12, 2013
 *
 * Description: Process the import mode (check, lock or unlock import). 
 *
 * In:  $mode
 * Out: $aJson
 *
 */
function ProcessImportMode($mode)
{
    $aJson = null;
    $db = OpenDatabase();
    
    switch($mode)
    {
        case "check"  : $aJson["check"] = GetStatus($db, "ImportLock");
                         break;
                    
        case "lock"   : UpdateStatus($db, "ImportLock", 0); // 0 = false.
                        $aJson["check"] = 0;
                        break;
                   
        case "unlock" : UpdateStatus($db, "ImportLock", 1); // 1 = true. 
                        $aJson["check"] = 1;
                        break;                   
    }

    CloseDatabase($db);    
    return $aJson;
}

/*
 * Function:	GetMediaStatus
 *
 * Created on May 18, 2013
 * Updated on JuL 02, 2014
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  $media, $id, $xbmcid
 * Out: $aJson
 *
 */
function GetMediaStatus($media, $id, $xbmcid)
{
    $aJson = null;   
    $db = OpenDatabase();
    
    switch ($media)    
    {   
        case "movies"   : $aJson = GetImportStatus($db, "movies", "movieid", "xbmcid", $id, $xbmcid, cMOVIESART);
                          break;
                      
        case "sets"     : $aJson = GetImportStatus($db, "sets", "setid", "setid", $id, $xbmcid, cMOVIESART);
                          break;                      
    
        case "tvshows"  : $aJson = GetImportStatus($db, "tvshows", "tvshowid", "xbmcid", $id, $xbmcid, cTVSHOWSART);
                          break;
                                            
        case "seasons"  : $aJson = GetSeriesImportStatus($db, "seasons", "seasonid", $id, $xbmcid, cTVSHOWSART);
                           break;
                      
        case "episodes" : $aJson = GetSeriesImportStatus($db, "episodes", "episodeid", $id, $xbmcid, cTVSHOWSART);
                          break;                      
                      
        case "albums"   : $aJson = GetImportStatus($db, "albums", "albumid", "xbmcid", $id, $xbmcid, cMUSICART);
                          break;
                            
        case "songs"    : $aJson = GetSongsImportStatus($db, $id, $xbmcid, cMUSICART);
                          break;                            
    }      
 
    CloseDatabase($db);
    return $aJson;    
}    

/*
 * Function:	GetImportStatus
 *
 * Created on May 18, 2013
 * Updated on Jul 12, 2014
 *
 * Description: Reports the status of the import process.
 *
 * In:  $db, $table, $typeid, $nameid, $id, $xbmcid, $thumbs
 * Out: $aJson
 *
 */
function GetImportStatus($db, $table, $typeid, $nameid, $id, $xbmcid, $thumbs)
{
    $aJson = null;
       
    // Get mediaid from temporary import table.
    $sql = "SELECT mediaid FROM tmp_import ".
           "WHERE id = $id"; 
    $aJson['xbmcid'] = GetItemFromDatabase($db, $typeid, $sql);  
    
    // Get title, sub and poster from media table.
    $sql = "SELECT title, NULL, HEX(poster) AS poster FROM $table ".
           "WHERE $nameid = $xbmcid";
    $aJson = GetStatusItems($db, $sql, $aJson);
        
    // Get the import status.
    $aJson['status']  = GetStatus($db, "ImportStatus");
    if ($aJson['status'] == cTRANSFER_READY) {
        UpdateStatus($db, "ImportStatus", cTRANSFER_WAIT);
    }
    
    $aJson['thumbs'] = $thumbs;
    $aJson['counter'] = GetStatus($db, "ImportStart");
    
    return $aJson;
}

/*
 * Function:	GetSeriesImportStatus
 *
 * Created on Jan 20, 2014
 * Updated on Jul 12, 2014
 *
 * Description: Reports the status of the series (seasons, episodes) import process.
 *
 * In:  $db, $table, $typeid, $id, $xbmcid, $thumbs
 * Out: $aJson
 *
 */
function GetSeriesImportStatus($db, $table, $typeid, $id, $xbmcid, $thumbs)
{
    $aJson = null;
   
    // Get mediaid from temporary import table.
    $sql = "SELECT mediaid FROM tmp_import ".
           "WHERE id = $id"; 
    $aJson['xbmcid'] = GetItemFromDatabase($db, $typeid, $sql);
    
    // Get title, sub and poster from media table.
    $sub = "title";
    if ($table == "episodes") {
        $sub = "CONCAT(episode, '. ', title) AS title";
    }      
    
    $sql = "SELECT showtitle, $sub, HEX(poster) AS poster FROM $table ".
           "WHERE $typeid = $xbmcid";
    $aJson = GetStatusItems($db, $sql, $aJson);    
    
    // Get the import status.
    $aJson['status']  = GetStatus($db, "ImportStatus");
    if ($aJson['status'] == cTRANSFER_READY) {
        UpdateStatus($db, "ImportStatus", cTRANSFER_WAIT);
    }    
    
    $aJson['thumbs'] = $thumbs;
    $aJson['counter'] =  GetStatus($db, "ImportStart");
    
    return $aJson;
}

/*
 * Function:	GetSongsImportStatus
 *
 * Created on Jun 28, 2014
 * Updated on Jul 12, 2014
 *
 * Description: Reports the status of the songs import process.
 *
 * In:  $db, $id, $xbmcid, $thumbs
 * Out: $aJson
 *
 */
function GetSongsImportStatus($db, $id, $xbmcid, $thumbs)
{
    $aJson = null;
   
    // Get mediaid from temporary import table.      
    $sql = "SELECT mediaid FROM tmp_import ".
           "WHERE id = $id";  
    $aJson['xbmcid'] = GetItemFromDatabase($db, "songid", $sql);
    
    // Get title, sub and poster from media table.
    $sql = "SELECT album, CONCAT(track, '. ', title) AS sub, HEX(poster) AS poster FROM songs ".
           "WHERE songid = $xbmcid";
    $aJson = GetStatusItems($db, $sql, $aJson);    
        
    // Get the import status.
    $aJson['status']  = GetStatus($db, "ImportStatus");
    if ($aJson['status'] == cTRANSFER_READY) {
        UpdateStatus($db, "ImportStatus", cTRANSFER_WAIT);
    }
    
    $aJson['thumbs'] = $thumbs;
    $aJson['counter'] = GetStatus($db, "ImportStart");
    
    return $aJson;
}

/*
 * Function:	GetStatusItems
 *
 * Created on Jul 04, 2014
 * Updated on Jul 07, 2014
 *
 * Description: Convert TV show id's to season id for serie (season 1) refresh. 
 *
 * In:  $db, $sql, $aStatus
 * Out: $aStatus
 *
 */
function GetStatusItems($db, $sql, $aStatus)
{    
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($title, $sub, $poster);
            $stmt->fetch();
            
            $aStatus["title"]  = stripslashes($title);
            $aStatus["sub"]    = !empty($sub)?stripslashes($sub):"&nbsp;";
            $aStatus["poster"] = !empty($poster)?strtolower($poster[0]."/".$poster):0;
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
    
    return $aStatus;
}        

//////////////////////////////////////////    Meta Functions    ///////////////////////////////////////////

/*
 * Function:	InitMeta
 *
 * Created on Jan 27, 2014
 * Updated on Jul 04, 2014
 *
 * Description: Initialize meta data (empty meta tables).
 *
 * In:  $type
 * Out: $aJson
 *
 */
function InitMeta($type)
{
    $aJson = null;
    $db = OpenDatabase();
    
    // Empty the temporary import table.
    EmptyTable($db, "tmp_import");
    EmptyTable($db, $type."meta");

    $aJson['status'] = "ready";
    
    CloseDatabase($db); 
    return $aJson;
}

/*
 * Function:	CheckMeta
 *
 * Created on Feb 12, 2014
 * Updated on Jun 24, 2014
 *
 * Description: Initialize meta data (empty meta tables).
 *
 * In:  $type
 * Out: $aJson
 *
 */
function CheckMeta($type)
{
    $aJson = null;
    $db = OpenDatabase();

    $aJson['total'] = CountRows($db, $type."meta");    
    $end = GetStatus($db, "ImportEnd");
    
    $aJson['check'] = false;
    if ($aJson['total'] == $end || $type == "seasons") // There is no seasons check!
    {
        $aJson['import'] = CompareAndPrepareImport($db, $type);
        UpdateStatus($db, "ImportEnd", $aJson['import']);
        $aJson['check'] = true;
    }
    
    CloseDatabase($db); 
    return $aJson;
}

/*
 * Function:	CompareAndPrepareImport($type)
 *
 * Created on Jun 15, 2014
 * Updated on Jun 16, 2014
 *
 * Description: 
 *
 * In:  $type
 * Out: Compare media meta data with existing media and place the missing media meta data in the import table.
 *
 */
function CompareAndPrepareImport($db, $type)
{
    $typeid = rtrim($type, 's')."id";

    if ($type == "albums" || $type == "movies" || $type == "tvshows") {
        $mid = "xbmcid";
    }
    else {
        $mid = $typeid;
    }
        
    // Insert new found id's in the temporary import table.
    $sql = "INSERT tmp_import(mediaid) ".
           "  SELECT me.$typeid FROM ".$type."meta me ".
           "  LEFT JOIN $type m ON me.$typeid = m.$mid AND me.hash = m.hash ".
           "  WHERE m.title IS NULL";   
   
    mysqli_query($db, $sql);
    
    return mysqli_affected_rows($db);
}

/*
 * Function:	GetTVShowsIds
 *
 * Created on Jan 20, 2014
 * Updated on Jun 16, 2014
 *
 * Description: Get TV Shows id's to retrieve the Seasons meta data.
 *
 * In:  $start, $offset
 * Out: $aJson
 *
 */
function GetTVShowsIds($start, $offset)
{
    $aJson = null;
    $db = OpenDatabase();
    
    // Init meta for seasons.
    EmptyTable($db, "seasonsmeta");
    EmptyTable($db, "tmp_import");
    
    $sql = "SELECT tvshowid FROM tvshowsmeta ".
           "ORDER BY tvshowid ". 
           "LIMIT $start, $offset";
    
    $aJson["tvshowids"] = GetItemsFromDatabase($db, $sql);
    
    CloseDatabase($db); 
    return $aJson;
}

/////////////////////////////////////////    System Functions    //////////////////////////////////////////

/*
 * Function:	SetSystemProperty
 *
 * Created on May 27, 2013
 * Updated on Jul 05, 2014
 *
 * Description: Set the system property. 
 *
 * In:  $option, $number, $value
 * Out: $aJson
 *
 */
function SetSystemProperty($option, $number, $value)
{
    $aJson = null;
    
    switch(strtolower($option))
    {
        case "settings"  : $aJson = SetSettingProperty($number, $value);            
                           break;
                    
        case "library"   : $aJson = CleanLibrary($number, $value);
                           break;
                      
        case "event log" : $aJson = CleanEventLog();            
        
        default : break;
    }
    
    return $aJson;
}

/*
 * Function:	SetSettingProperty
 *
 * Created on May 27, 2013
 * Updated on Jul 06, 2014
 *
 * Description: Set the setting property. 
 *
 * In:  $number, $value
 * Out: $aJson
 *
 */
function SetSettingProperty($number, $value)
{
    $aJson = null;
    $aJson['counter'] = 0;
    $db = OpenDatabase();
    
    switch($number)
    {
        case 1 : // Set XBMC Connection
                 UpdateSetting($db, "XBMCconnection", $value);
                 UpdateHeaderFile($db); 
                 break;
             
        case 2 : // Set XBMC Port
                 UpdateSetting($db, "XBMCport", $value);
                 UpdateHeaderFile($db);
                 break;
             
        case 3 : // Set XBMC Username
                 UpdateSetting($db, "XBMCusername", $value);
                 break;
             
        case 4 : // Set XBMC Password
                 UpdateSetting($db, "XBMCpassword", $value);
                 break; 
             
        case 6 : // Set Fargo Username
                 UpdateUser($db, 1, $value);
                 break;
             
        case 7 : // Set Fargo Password
                 UpdatePassword($db, 1, $value);
                 break; 
             
        case 9 : // Set Timer
                 UpdateSetting($db, "Timeout", $value);
                 break;              
    }
    
    CloseDatabase($db);
    return $aJson;
}

/*
 * Function:	UpdateHeaderFile
 *
 * Created on Jul 06, 2014
 * Updated on Jul 06, 2014
 *
 * Description: Update the header file with the XBMC connection (ip-address) and port.
 *
 * In:  $db
 * Out: Updated Access-Control-Allow-Origin header.
 * 
 * Note: The header.php file is updated. It gives only the XBMC ip-address (with port) cross domain access.
 *
 */
function UpdateHeaderFile($db)
{
    $ip   = GetSetting($db, "XBMCconnection");
    $port = GetSetting($db, "XBMCport");
    
    $header = '<?php header("Access-Control-Allow-Origin: http://'.$ip.':'.$port.'");';
    
    $fp = fopen("include/header.php","wb");
    fwrite($fp, $header);
    fclose($fp);
}

/*
 * Function:	CleanLibrary
 *
 * Created on Jun 10, 2013
 * Updated on Jul 19, 2014
 *
 * Description: Clean the media library. 
 *
 * In:  $number
 * Out: $aJson
 *
 */
function CleanLibrary($number, $value)
{
    $aJson = null;
    $db = OpenDatabase();
    
    switch($number)
    {
        case 1 : $aJson['name']     = "movies";
                 $aJson['counter']  = CountRows($db, "movies");
                 $aJson['counter'] += CountRows($db, "sets");
                 EmptyTable($db, "movies");
                 EmptyTable($db, "moviesmeta");
                 EmptyTable($db, "genretomovie");
                 EmptyTable($db, "sets");
                 EmptyTable($db, "setsmeta");
                 DeleteGenres($db, "movies");
                 
                 // Remove posters and fanart files
                 if ($value) {
                     RemoveArtDirectories(cMOVIESART);
                 }
                 break;
        
        case 5 : $aJson['name']     = "tvshows";
                 $aJson['counter']  = CountRows($db, "tvshows");
                 $aJson['counter'] += CountRows($db, "seasons");
                 $aJson['counter'] += CountRows($db, "episodes");
                 EmptyTable($db, "tvshows");
                 EmptyTable($db, "tvshowsmeta");
                 EmptyTable($db, "genretotvshow");
                 EmptyTable($db, "seasons");
                 EmptyTable($db, "seasonsmeta");
                 EmptyTable($db, "episodes");
                 EmptyTable($db, "episodesmeta");
                 DeleteGenres($db, "tvshows");
                 
                 // Remove posters and fanart files
                 if ($value) {
                     RemoveArtDirectories(cTVSHOWSART);
                 }                 
                 break;
        
        case 9 : $aJson['name']     = "albums";
                 $aJson['counter']  = CountRows($db, "albums");
                 $aJson['counter'] += CountRows($db, "songs");
                 EmptyTable($db, "albums");
                 EmptyTable($db, "albumsmeta");
                 EmptyTable($db, "genretomusic");
                 DeleteGenres($db, "music");
                 EmptyTable($db, "songs");
                 EmptyTable($db, "songsmeta");
                 
                 // Remove posters and fanart files
                 if ($value) {
                     RemoveArtDirectories(cMUSICART);
                 }
                 break;
    }
    
    EmptyTable($db, "tmp_import");
    CloseDatabase($db);
    return $aJson;
}

/*
 * Function:	DeleteGenres
 *
 * Created on Jul 04, 2013
 * Updated on Jul 03, 2014
 *
 * Description: Delete media genres.
 *
 * In:  $db, $media
 * Out: Deleted Genres
 *
 */
function DeleteGenres($db, $media)
{
    $sql = "DELETE FROM genres ".
           "WHERE media = '$media'";
    
    QueryDatabase($db, $sql);
}

/*
 * Function:	RemoveArtDirectories
 *
 * Created on Jul 05, 2014
 * Updated on Jul 05, 2014
 *
 * Description: Delete media genres.
 *
 * In:  $artdir
 * Out: Removed art directories (0 to f)
 *
 */
function RemoveArtDirectories($artdir)
{
    for ($i = 0; $i <= 16; $i++) {
        RemoveDirectory($artdir."/".dechex($i));
    }
}

/*
 * Function:	CleanEventLog
 *
 * Created on Jun 15, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Clean the event log. 
 *
 * In:  -
 * Out: $aJson
 *
 */
function CleanEventLog()
{
    $aJson = null; 
    $db = OpenDatabase();
    
    $aJson['name']    = "log";    
    $aJson['counter'] = CountRows($db, "log");
    EmptyTable($db, "log");

    CloseDatabase($db);    
    return $aJson;
}