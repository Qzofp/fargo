<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.5
 *
 * File:    jsonmanage.php
 *
 * Created on Nov 20, 2013
 * Updated on Jun 17, 2014
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
                         $xbmcid = GetPageValue('xbmcid');
                         $aJson  = RemoveMediaFromFargo($media, $id, $xbmcid);
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
                         //$mode  = GetPageValue('mode');
                         $id     = GetPageValue('id');
                         $xbmcid = GetPageValue('xbmcid');
                        
                         //$aJson = GetMediaStatus($mode, $media, $id);
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
                      
    /*                  
    case "convert" : // Convert TV show id's to season id's for refresh serie.
                     if($login)
                     { 
                        $id    = GetPageValue('id');
                        $aJson = ConvertTVShowToSeasonID($id);
                     }
                     else {
                        $aJson = LogEvent("Warning", "Unauthorized convert action call!");
                     }                    
                     break;                      
    */ 
                      
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
 * Updated on Feb 19, 2014
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
                          $sql = "SELECT id FROM tvshows WHERE xbmcid = (SELECT tvshowid FROM seasons WHERE id = $aItems[0])";
                          $id = GetItemFromDatabase($db, "xbmcid", $sql);
                          $aJson = HideOrShowMedia($db, "tvshows", $id, $value);
                          break;                      

        case "seasons"  : $aItems = explode("_", $id);
                          $aJson = HideOrShowMedia($db, "seasons", $aItems[0], $value);
                          break;
                      
        case "episodes" : $aJson = HideOrShowMedia($db, "episodes", $id, $value);
                          break;
                      
        case "albums"   : $aJson = HideOrShowMedia($db, "albums", $id, $value);
                          break;                      
    }
    
    CloseDatabase($db);     
    return $aJson;
}

/*
 * Function:	HideOrShowMedia
 *
 * Created on Sep 23, 2013
 * Updated on Feb 09, 2014
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
    //ExecuteQuery($sql);
    
    $aJson["ready"] = true;
    return $aJson;
}

/*
 * Function:	RemoveMediaFromFargo
 *
 * Created on Nov 22, 2013
 * Updated on Feb 19, 2014
 *
 * Description: Delete media from Fargo database.
 *
 * In:  $media, $id, $xbmcid
 * Out:	Deleted media
 * 
 */
function RemoveMediaFromFargo($media, $id, $xbmcid)
{   
    $aJson = null;
    $db = OpenDatabase();    
    
    switch ($media)
    {
        case "titles"   : $aJson = DeleteMedia($db, "movies", $id, $xbmcid);
                          break;
        
        case "sets"     : $aJson = DeleteMedia($db, "sets", $id, $xbmcid);
                          break;
                    
        case "movieset" : $aJson = DeleteMedia($db, "movies", $id, $xbmcid);
                          break;
                      
        case "tvtitles" : $aJson = DeleteMedia($db, "tvshows", $id, $xbmcid); // TV Show + Seasons + Episodes.
                          break;
                      
        case "series"   : $sql = "SELECT id FROM tvshows WHERE xbmcid = (SELECT tvshowid FROM seasons WHERE id = $id)";
                          $id = GetItemFromDatabase($db, "id", $sql);
                          $sql = "SELECT xbmcid FROM tvshows WHERE id = $id";
                          $xbmcid = GetItemFromDatabase($db, "xbmcid", $sql);
                          $aJson = DeleteMedia($db, "tvshows", $id, $xbmcid); // TV Show + Seasons + Episodes.
                          break;  
                      
        case "seasons"  : $aJson = DeleteMedia($db, "seasons", $id, $xbmcid); // Season + Episodes.
                          break;                      
                      
        case "episodes" : $aJson = DeleteMedia($db, "episodes", $id, $xbmcid);
                          break;
                      
        case "albums"   : $aJson = DeleteMedia($db, "albums", $id, $xbmcid);
                          break;                      
                    
    }
    
    CloseDatabase($db);      
    return $aJson;   
}

/*
 * Function:	DeleteMediaQuery
 *
 * Created on Oct 05, 2013
 * Updated on Jun 08, 2014
 *
 * Description: Delete media from Fargo database.
 *
 * In:  $db, $media, $id, $xbmcid
 * Out:	Deleted media
 * 
 */
function DeleteMedia($db, $media, $id, $xbmcid)
{
    $aJson = null;
    
    switch ($media)
    {
        case "movies"   : DeleteMediaQuery($db, "movies", $id);
                          DeleteMediaGenreQuery($db, "movie", $id);
                          DeleteFile(cMOVIESTHUMBS."/$xbmcid.jpg");
                          DeleteFile(cMOVIESFANART."/$xbmcid.jpg");
                          break;
                     
        case "sets"     : // Won't delete the movies in the set. Maybe in the future releases.
                          DeleteMediaQuery($db, "sets", $id);
                          DeleteFile(cSETSTHUMBS."/$xbmcid.jpg");
                          //DeleteFile(cSETSFANART."/$xbmcid.jpg");
                          break;
                            
        case "tvshows"  : // Delete episodes.
                          $sql = "SELECT CONCAT(episodeid, '.jpg') AS thumb FROM episodes WHERE ".
                                 "tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          $aThumbs = GetItemsFromDatabase($db, $sql);
                          DeleteMultipleFiles(cEPISODESTHUMBS, $aThumbs);
                          
                          $sql = "DELETE FROM episodes WHERE tvshowid = $id";
                          QueryDatabase($db, $sql);
                          
                          // Delete seasons.
                          $sql = "SELECT CONCAT(tvshowid, '_', season, '.jpg') AS xbmcid FROM seasons ".
                                 "WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          $aThumbs = GetItemsFromDatabase($db, $sql);
                          DeleteMultipleFiles(cSEASONSTHUMBS, $aThumbs);
                          
                          $sql = "DELETE FROM seasons WHERE tvshowid = $id";
                          QueryDatabase($db, $sql);
                          
                          // Delete TV show.
                          DeleteMediaQuery($db, "tvshows", $id);
                          DeleteMediaGenreQuery($db, "tvshow", $id);

                          DeleteFile(cTVSHOWSTHUMBS."/$xbmcid.jpg");
                          DeleteFile(cTVSHOWSFANART."/$xbmcid.jpg");
                          break;
                     
        case "seasons"  : //$aItems = explode("_", $id);
                          
                          // Delete episodes.
                          $sql = "SELECT CONCAT(episodeid, '.jpg') AS thumb FROM episodes ".
                                 "WHERE tvshowid = (SELECT tvshowid FROM seasons WHERE id = $id) ".
                                 "AND season = (SELECT season FROM seasons WHERE id = $id)";
                          $aThumbs = GetItemsFromDatabase($db, $sql);
                          DeleteMultipleFiles(cEPISODESTHUMBS, $aThumbs);
                          
                          $sql = "DELETE FROM episodes ".
                                 "WHERE tvshowid = (SELECT tvshowid FROM seasons WHERE id = $id) ".
                                 "AND season = (SELECT season FROM seasons WHERE id = $id)";                                  
                          QueryDatabase($db, $sql);
                          //ExecuteQuery($sql);
                         
                          // Delete seasons.
                          $sql = "SELECT seasonid AS xbmcid FROM seasons WHERE id = $id";
                          $xbmcid = GetItemFromDatabase($db, "xbmcid", $sql);
                          DeleteFile(cSEASONSTHUMBS."/$xbmcid.jpg");

                          DeleteMediaQuery($db, "seasons", $id);  
                          break;
                     
        case "episodes" : DeleteMediaQuery($db, "episodes", $id);
                          DeleteFile(cEPISODESTHUMBS."/$xbmcid.jpg");
                          break;                     
                            
        case "albums"   : DeleteMediaQuery($db, "albums", $id);
                          DeleteMediaGenreQuery($db, "music", $id);
                          DeleteFile(cALBUMSTHUMBS."/$xbmcid.jpg");
                          DeleteFile(cALBUMSCOVERS."/$xbmcid.jpg");
                          break;                             
    } 
                          
    $aJson["ready"] = true;
    return $aJson;    
}

/*
 * Function:	DeleteMediaQuery
 *
 * Created on Nov 22, 2013
 * Updated on Jan 03, 2014
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
    //ExecuteQuery($sql);
}

/*
 * Function:	DeleteMediaGenreQuery
 *
 * Created on Nov 22, 2013
 * Updated on Jan 03, 2013
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
    //ExecuteQuery($sql);
}

/////////////////////////////////////////    Status Functions    //////////////////////////////////////////

/*
 * Function:	ResetStatus
 *
 * Created on Jul 22, 2013
 * Updated on Jun 15, 2014
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
    
    //UpdateStatus($db, "MetaEnd", -1);
    
    UpdateStatus($db, "ImportStatus", -1); // Needed for refresh media.   
    UpdateStatus($db, "ImportCounter", 1); // 0
    //UpdateStatus($db, "Xbmc".$media."End", -1); // Obsolete
    
    // Obsolete begin
    //$sql    = CreateMetaStartQuery($db, $media);
    //$lastid = GetItemFromDatabase($db, "id", $sql);
    //UpdateStatus($db, "ImportStart", !empty($lastid)?$lastid:0);
    // Obsolete end
    
    UpdateStatus($db, "ImportStart", 1);
    
    // Fill json.
    $aJson["connection"] = GetSetting($db, "XBMCconnection");
    $aJson["port"]       = GetSetting($db, "XBMCport");
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
 * Updated on Jun 16, 2014
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
    
    //$sql = CreateMetaStartQuery($db, $media);
  
    //$aJson['xbmc']['start'] = GetItemFromDatabase($db, "id", $sql);
    //$aJson['xbmc']['end']   = GetStatus($db, "Xbmc".$media."End");
     
    $aJson['import']        = GetStatus($db, "ImportCounter");
    
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
function CreateMetaStartQuery($db, $media) // Obsolete
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
}

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
 * Updated on Feb 19, 2014
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
        case "movies"         : $aJson = GetImportStatus($db, "movies", "movieid", "xbmcid", $id, $xbmcid, cMOVIESTHUMBS);
                                break;
                      
        case "sets"           : $aJson = GetImportStatus($db, "sets", "setid", "setid", $id, $xbmcid, cSETSTHUMBS);
                                break;                      
    
        case "tvshows"        : $aJson = GetImportStatus($db, "tvshows", "tvshowid", "xbmcid", $id, $xbmcid, cTVSHOWSTHUMBS);
                                break;
                                            
        case "seasons"        : $aJson = GetSeriesImportStatus($db, "seasons", "seasonid", $id, $xbmcid, cSEASONSTHUMBS);
                                break;
                      
        case "episodes"       : $aJson = GetSeriesImportStatus($db, "episodes", "episodeid", $id, $xbmcid, cEPISODESTHUMBS);
                                break;                      
                      
        case "albums"         : $aJson = GetImportStatus($db, "albums", "albumid", "xbmcid", $id, $xbmcid, cALBUMSTHUMBS);
                                //$aJson = GetMusicImportStatus($db, $id, $xbmcid);
                                break;                      
    }      
 
    CloseDatabase($db);
    return $aJson;    
}    

/*
 * Function:	GetImportStatus
 *
 * Created on May 18, 2013
 * Updated on Jun 15, 2014
 *
 * Description: Reports the status of the import process.
 *
 * In:  $db, $table, $typeid, $nameid, $id, $xbmcid, $thumbs
 * Out: $aJson
 *
 */
function GetImportStatus($db, $table, $typeid, $nameid, $id, $xbmcid, $thumbs)
{
    $aJson['thumbs'] = $thumbs;

    //$id -= 1;
    
    //$sql = "SELECT $typeid FROM ".$table."meta ".
    //       "ORDER BY $typeid LIMIT $id, 1"; 
    
    $sql = "SELECT mediaid FROM tmp_import ".
           "WHERE id = $id"; 
       //  "ORDER BY mediaid LIMIT $id, 1"; 
    
    $aJson['xbmcid'] = GetItemFromDatabase($db, $typeid, $sql);

    $sql = "SELECT title FROM $table ".
           "WHERE $nameid = $xbmcid";
    
    $aJson['title'] = GetItemFromDatabase($db, "title", $sql);
    $aJson['sub']   = "&nbsp;";
    
    $aJson['status']  = GetStatus($db, "ImportStatus");
    if ($aJson['status'] == cTRANSFER_READY) {
        UpdateStatus($db, "ImportStatus", cTRANSFER_WAIT);
    }
    
    $aJson['counter'] = GetStatus($db, "ImportStart");
    
    return $aJson;
}

/*
 * Function:	GetSeriesImportStatus
 *
 * Created on Jan 20, 2014
 * Updated on Jun 16, 2014
 *
 * Description: Reports the status of the series (seasons, episodes) import process.
 *
 * In:  $db, $table, $typeid, $id, $xbmcid, $thumbs
 * Out: $aJson
 *
 */
function GetSeriesImportStatus($db, $table, $typeid, $id, $xbmcid, $thumbs)
{
    $aJson['thumbs'] = $thumbs;

    /*
    $id -= 1;    
    $sql = "SELECT $typeid FROM ".$table."meta ".
           "ORDER BY $typeid LIMIT $id, 1";
    */
    
    $sql = "SELECT mediaid FROM tmp_import ".
           "WHERE id = $id"; 
    
    $aJson['xbmcid'] = GetItemFromDatabase($db, $typeid, $sql);
    
    $sql = "SELECT showtitle FROM $table ".
           "WHERE $typeid = $xbmcid";       
    $aJson['title'] = GetItemFromDatabase($db, "showtitle", $sql);
    
    $title = "title";
    if ($table == "episodes") {
        $title = "CONCAT(episode, '. ', title) AS title";
    }
    
    $sql = "SELECT $title FROM $table ".
           "WHERE $typeid = $xbmcid";    
    $aJson['sub'] = GetItemFromDatabase($db, "title", $sql);
    
    $aJson['status']  = GetStatus($db, "ImportStatus");
    if ($aJson['status'] == cTRANSFER_READY) {
        UpdateStatus($db, "ImportStatus", cTRANSFER_WAIT);
    }    
    
    $aJson['counter'] =  GetStatus($db, "ImportStart");
    
    return $aJson;
}

/*
 * Function:	ConvertTVShowToSeasonID
 *
 * Created on Dec 10, 2013
 * Updated on Jan 02, 2014
 *
 * Description: Convert TV show id's to season id for serie (season 1) refresh. 
 *
 * In:  $id
 * Out: $aJson
 *
 */
/*function ConvertTVShowToSeasonID($id) // Obsolete?
{
    $db = OpenDatabase();    
    
    $aItems = explode("_", $id);    
    $sql = "SELECT id FROM seasons WHERE tvshowid = $aItems[0] AND season = $aItems[1]"; 
    $aJson['id'] = GetItemFromDatabase($db, "id", $sql);
    
    CloseDatabase($db); 
    
    return $aJson;
}*/

//////////////////////////////////////////    Meta Functions    ///////////////////////////////////////////

/*
 * Function:	InitMeta
 *
 * Created on Jan 27, 2014
 * Updated on Jun 09, 2014
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
    
    if ($type != "music") {
        EmptyTable($db, $type."meta");
    }
    else {
        EmptyTable($db, "albumsmeta"); 
    }    

    $aJson['status'] = "ready";
    
    CloseDatabase($db); 
    return $aJson;
}

/*
 * Function:	CheckMeta
 *
 * Created on Feb 12, 2014
 * Updated on Jun 16, 2014
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

    /*
    if ($type != "music") {
        $total = CountRows($db, $type."meta");
    }
    else {
        $total = CountRows($db, "albumsmeta");
    }    
    */
    
    $aJson['total']  = CountRows($db, $type."meta");
    
    //$aJson['import'] = CountRows($db, "tmp_import");
    //$end   = GetStatus($db, "Xbmc".$type."End");
    
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
 * Updated on Jun 15, 2013
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
                    
        case "library"   : $aJson = CleanLibrary($number);
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
 * Updated on Jan 03, 2014
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
                 break;
             
        case 2 : // Set XBMC Port
                 UpdateSetting($db, "XBMCport", $value);
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
 * Function:	CleanLibrary
 *
 * Created on Jun 10, 2013
 * Updated on Jun 17, 2014
 *
 * Description: Clean the media library. 
 *
 * In:  $number
 * Out: $aJson
 *
 */
function CleanLibrary($number)
{
    $aJson = null;
    $db = OpenDatabase();
    
    switch($number)
    {
        case 1 : $aJson['name']    = "movies";
                 $aJson['counter']  = CountRows($db, "movies");
                 $aJson['counter'] += CountRows($db, "sets");
                 EmptyTable($db, "movies");
                 EmptyTable($db, "moviesmeta");
                 EmptyTable($db, "genretomovie");
                 EmptyTable($db, "sets");
                 EmptyTable($db, "setsmeta");
                 DeleteGenres($db, "movies");
                /* UpdateStatus($db, "XbmcMoviesStart", 0); // 1
                 UpdateStatus($db, "XbmcSetsStart", 0); // 1 */
                 DeleteFile(cMOVIESTHUMBS."/*.jpg");
                 DeleteFile(cMOVIESFANART."/*.jpg");
                 DeleteFile(cSETSTHUMBS."/*.jpg");
                 DeleteFile(cSETSFANART."/*.jpg");
                 break;
        
        case 4 : $aJson['name']    = "tvshows";
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
               /*  UpdateStatus($db, "XbmcTVShowsStart", 0); // 1
                 UpdateStatus($db, "XbmcSeasonsStart", 0); // 1
                 UpdateStatus($db, "XbmcEpisodesStart", 0); // 1 */
                 DeleteFile(cTVSHOWSTHUMBS."/*.jpg");
                 DeleteFile(cTVSHOWSFANART."/*.jpg");
                 DeleteFile(cSEASONSTHUMBS."/*.jpg");
                 DeleteFile(cEPISODESTHUMBS."/*.jpg");
                 break;
        
        case 7 : $aJson['name']    = "albums";
                 $aJson['counter'] = CountRows($db, "albums");
                 EmptyTable($db, "albums");
                 EmptyTable($db, "albumsmeta");
                 EmptyTable($db, "genretomusic");
                 DeleteGenres($db, "music");
               /*  UpdateStatus($db, "XbmcAlbumsStart", 0); // 1*/
                 DeleteFile(cALBUMSTHUMBS."/*.jpg");
                 DeleteFile(cALBUMSCOVERS."/*.jpg");
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
 * Updated on Jan 03, 2014
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
    //ExecuteQuery($sql);
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