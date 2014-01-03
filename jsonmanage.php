<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    jsonmanage.php
 *
 * Created on Nov 20, 2013
 * Updated on Jan 03, 2014
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
                         $counter = GetPageValue('counter');
                         $aJson   = ResetStatus($media, $counter);
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
                         $media = GetPageValue('media');
                         $mode  = GetPageValue('mode');
                         $id    = GetPageValue('id');
                        
                         $aJson = GetMediaStatus($mode, $media, $id);
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized status action call!");
                      }                        
                      break;
                      
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
 * Updated on Nov 25, 2013
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
    
    switch ($media)
    {
        case "titles"   : $aJson = HideOrShowMedia("movies", $id, $value);
                          break;
        
        case "sets"     : $aJson = HideOrShowMedia("sets", $id, $value);
                          break;
                      
        case "movieset" : $aJson = HideOrShowMedia("movies", $id, $value);
                          break;                      
                    
        case "tvtitles" : $aJson = HideOrShowMedia("tvshows", $id, $value);
                          break;
                      
        case "series"   : $aItems = explode("_", $id);
                          $aJson = HideOrShowMedia("tvshows", $aItems[0], $value);
                          break;                      

        case "seasons"  : $aItems = explode("_", $id);
                          $aJson = HideOrShowMedia("seasons", $aItems[0], $value);
                          break;
                      
        case "episodes" : $aJson = HideOrShowMedia("episodes", $id, $value);
                          break;
                      
        case "albums"   : $aJson = HideOrShowMedia("music", $id, $value);
                          break;                      
    }
    
    return $aJson;
}

/*
 * Function:	HideOrShowMedia
 *
 * Created on Sep 23, 2013
 * Updated on Nov 22, 2013
 *
 * Description: Update hide media hide column.
 *
 * In:  $media, $id, $value
 * Out:	Update hide column
 * 
 */
function HideOrShowMedia($table, $id, $value)
{
    $aJson = null;
    
    $sql = "UPDATE $table ".
           "SET hide = $value ".
           "WHERE id = $id";
            
    ExecuteQuery($sql);
    
    $aJson["ready"] = true;
    return $aJson;
}

/*
 * Function:	RemoveMediaFromFargo
 *
 * Created on Nov 22, 2013
 * Updated on Jan 03, 2013
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
    
    switch ($media)
    {
        case "titles"   : $aJson = DeleteMedia("movies", $id, $xbmcid);
                          break;
        
        case "sets"     : $aJson = DeleteMedia("sets", $id, $xbmcid);
                          break;
                    
        case "movieset" : $aJson = DeleteMedia("movies", $id, $xbmcid);
                          break;
                      
        case "tvtitles" : $aJson = DeleteMedia("tvshows", $id, $xbmcid); // TV Show + Seasons + Episodes.
                          break;
                      
        case "series"   : $aId   = explode("_", $id);
                          $aXbmc = explode("_", $xbmcid);
                          $aJson = DeleteMedia("tvshows", $aId[0], $aXbmc[0]); // TV Show + Seasons + Episodes.
                          break;  
                      
        case "seasons"  : $aJson = DeleteMedia("seasons", $id, $xbmcid); // Season + Episodes.
                          break;                      
                      
        case "episodes" : $aJson = DeleteMedia("episodes", $id, $xbmcid);
                          break;
                      
        case "albums"   : $aJson = DeleteMedia("music", $id, $xbmcid);
                          break;                      
                    
    }
    
    return $aJson;   
}

/*
 * Function:	DeleteMediaQuery
 *
 * Created on Oct 05, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Delete media from Fargo database.
 *
 * In:  $media, $id, $xbmcid
 * Out:	Deleted media
 * 
 */
function DeleteMedia($media, $id, $xbmcid)
{
    $aJson = null; 
    $db = OpenDatabase();
    
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
                          
                          $sql = "DELETE FROM episodes WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          QueryDatabase($db, $sql);
                          //ExecuteQuery($sql);
                          
                          // Delete seasons.
                          $sql = "SELECT CONCAT(tvshowid, '_', season, '.jpg') AS xbmcid FROM seasons ".
                                 "WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          $aThumbs = GetItemsFromDatabase($db, $sql);
                          DeleteMultipleFiles(cSEASONSTHUMBS, $aThumbs);
                          
                          $sql = "DELETE FROM seasons WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          QueryDatabase($db, $sql);
                          //ExecuteQuery($sql);
                          
                          // Delete TV show.
                          DeleteMediaQuery($db, "tvshows", $id);
                          DeleteMediaGenreQuery($db, "tvshow", $id);

                          DeleteFile(cTVSHOWSTHUMBS."/$xbmcid.jpg");
                          DeleteFile(cTVSHOWSFANART."/$xbmcid.jpg");
                          break;
                     
        case "seasons"  : $aItems = explode("_", $id);
                          
                          // Delete episodes.
                          $sql = "SELECT CONCAT(episodeid, '.jpg') AS thumb FROM episodes WHERE tvshowid = ".
                                 "(SELECT tvshowid FROM seasons WHERE id = $aItems[0]) AND season = $aItems[1]";
                          $aThumbs = GetItemsFromDatabase($db, $sql);
                          DeleteMultipleFiles(cEPISODESTHUMBS, $aThumbs);
                          
                          $sql = "DELETE FROM episodes WHERE tvshowid = (SELECT tvshowid FROM seasons ".
                                 "WHERE id = $aItems[0]) AND season = $aItems[1]";
                          QueryDatabase($db, $sql);
                          //ExecuteQuery($sql);
                         
                          // Delete seasons.
                          $sql = "SELECT CONCAT(tvshowid, '_', season) AS xbmcid FROM seasons WHERE id = $aItems[0]";
                          $xbmcid = GetItemFromDatabase($db, "xbmcid", $sql);
                          DeleteFile(cSEASONSTHUMBS."/$xbmcid.jpg");

                          DeleteMediaQuery($db, "seasons", $aItems[0]);                        
                          break;
                     
        case "episodes" : DeleteMediaQuery($db, "episodes", $id);
                          DeleteFile(cEPISODESTHUMBS."/$xbmcid.jpg");
                          break;                     
                            
        case "music"    : DeleteMediaQuery($db, "music", $id);
                          DeleteMediaGenreQuery($db, "music", $id);
                          DeleteFile(cALBUMSTHUMBS."/$xbmcid.jpg");
                          DeleteFile(cALBUMSCOVERS."/$xbmcid.jpg");
                          break;                             
    }
   
    CloseDatabase($db);   
                          
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
 * Updated on Jan 03, 2014
 *
 * Description: Reset the status. 
 *
 * In:  $media, $counter
 * Out: $aJson
 *
 */
function ResetStatus($media, $counter)
{       
    $aJson = null;
    
    $db = OpenDatabase();

    if ($media == "seasons") 
    {
        $start = GetStatus($db, "XbmcSeasonsStart");
        $end   = GetStatus($db, "XbmcSeasonsEnd");
        
        if ($start >= $end) {
            UpdateStatus($db, "XbmcSeasonsStart", 0);
        }
    }
    
    if ($counter == "true") {
        UpdateStatus($db, "ImportCounter", 0);
    }
    else {
        UpdateStatus($db, "RefreshReady", 0);
    }
    
    UpdateStatus($db, "Xbmc".$media."End", -1);
    
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
 * Updated on Jan 03, 2014
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
    
    $aJson['xbmc']['start'] = GetStatus($db, "Xbmc".$media."Start");
    $aJson['xbmc']['end']   = GetStatus($db, "Xbmc".$media."End");
    $aJson['import']        = GetStatus($db, "ImportCounter");
    
    CloseDatabase($db); 
    return $aJson;    
}

/*
 * Function:	InitStatus()
 *
 * Created on Jan 03, 2014
 * Updated on Jan 03, 2014
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
    
    $aJson['ready'] = GetStatus($db, "ImportReady");
    if ($aJson['ready'] >= 0) {
        UpdateStatus($db, "ImportReady", -1);
    }
    
    CloseDatabase($db);
    return $aJson;
}
                          
/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	ProcessImportMode
 *
 * Created on Dec 13, 2013
 * Updated on Jan 03, 2013
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
        case "check"  : $aJson["check"] = GetStatus($db, "ImportReady");
                         break;
                    
        case "lock"   : UpdateStatus($db, "ImportReady", 0); // 0 = false.
                        $aJson["check"] = 0;
                        break;
                   
        case "unlock" : UpdateStatus($db, "ImportReady", 1); // 1 = true. 
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
 * Updated on Jan 03, 2014
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  $mode, $media, $id
 * Out: $aJson
 *
 */
function GetMediaStatus($mode, $media, $id)
{
    $aJson = null;   
    $db = OpenDatabase();
    
    switch ($media)    
    {   
        case "movies"         : $aJson = GetImportRefreshStatus($db, $mode, $media, $id, "xbmcid", cMOVIESTHUMBS);
                                break;
                      
        case "sets"           : $aJson = GetImportRefreshStatus($db, $mode, $media, $id, "setid", cSETSTHUMBS);
                                break;                      
    
        case "tvshows"        : $aJson = GetImportRefreshStatus($db, $mode, $media, $id, "xbmcid", cTVSHOWSTHUMBS);
                                break;
                      
        case "tvshowsseasons" : $aJson['id'] = -1;
                                if (GetStatus($db, "XbmcSeasonsStart") == GetStatus($db, "XbmcSeasonsEnd")) {
                                    $aJson['id'] = 1;
                                }
                                break;
                      
        case "seasons"        : $aJson = GetSeasonsImportRefreshStatus($db, $mode, $id, cSEASONSTHUMBS);
                                break;
                      
        case "episodes"       : $aJson = GetEpisodesImportRefreshStatus($db, $mode, $id, cEPISODESTHUMBS);
                                break;                      
                      
        case "music"          : $aJson = GetImportRefreshStatus($db, $mode, $media, $id, "xbmcid", cALBUMSTHUMBS);
                                break;                      
    }    
    
    if ($mode == "import")  
    {
        $aJson['start'] = GetStatus($db, "Xbmc".$media."Start");
        $aJson['slack'] = GetStatus($db, "XbmcSlack");
    }
    else {
        $aJson['ready'] = GetStatus($db, "RefreshReady");
    }    
    
    CloseDatabase($db);
    return $aJson;
}

/*
 * Function:	GetImportRefreshStatus
 *
 * Created on May 18, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Reports the status of the import or refresh process.
 *
 * In:  $db, $mode, $media, $id, $nameid, $thumbs
 * Out: $aJson
 *
 */
function GetImportRefreshStatus($db, $mode, $media, $id, $nameid, $thumbs)
{
    $aJson['id']  = 0;
    $aJson['refresh'] = 0;
    $aJson['title']   = "empty";
    $aJson['thumbs']  = $thumbs;
  
    //$db = OpenDatabase();

    if ($mode == "import") { // Import.
        $sql = "SELECT $nameid, refresh, title ".
               "FROM $media ".
               "ORDER BY id DESC LIMIT 1";
    }
    else { // Refresh.
        $sql = "SELECT $nameid, refresh, title ".
               "FROM $media ".
               "WHERE $nameid = $id";   
    }
     
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
                $stmt->bind_result($xbmcid, $refresh, $title);
                while($stmt->fetch())
                {                
                    $aJson['id']      = $xbmcid;
                    $aJson['refresh'] = $refresh;
                    $aJson['title']   = stripslashes($title);
                    $aJson['sub']     = "&nbsp;";
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

    //CloseDatabase($db);

    return $aJson;
}

/*
 * Function:	GetSeasonsImportRefreshStatus
 *
 * Created on Oct 21, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Reports the seasons status of the import process.
 *
 * In:  $db, $mode, $seasonid, $thumbs
 * Out: $aJson
 *
 */
function GetSeasonsImportRefreshStatus($db, $mode, $seasonid, $thumbs)
{
    $aJson['id']      = 0;
    $aJson['refresh'] = 0;
    $aJson['title']   = "empty";
    $aJson['thumbs']  = $thumbs;
  
    //$db = OpenDatabase();

    if ($mode == "import") { // Import. 
        $sql = "SELECT id, refresh, tvshowid, showtitle, title, season ".
               "FROM seasons ".
               "ORDER BY id DESC LIMIT 1";
    }
    else { // Refresh.
        $sql = "SELECT id, refresh, tvshowid, showtitle, title, season ".
               "FROM seasons ".
               "WHERE id = $seasonid";    
    }
        
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
                $stmt->bind_result($id, $refresh, $tvshowid, $showtitle, $title, $season);
                while($stmt->fetch())
                {                
                    if ($mode == "import") {
                        $aJson['id'] = $id;
                    }
                    else {
                       $aJson['id'] = $tvshowid."_".$season; 
                    }
                    $aJson['refresh']  = $refresh;
                    $aJson['tvshowid'] = $tvshowid;
                    $aJson['title']    = stripslashes($showtitle);
                    $aJson['sub']      = stripslashes($title);
                    $aJson['season']   = $season;
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

    //CloseDatabase($db);

    return $aJson;
}

/*
 * Function:	GetEpisodesImportRefreshStatus
 *
 * Created on Oct 27, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Reports the episode status of the import process.
 *
 * In:  $db, $mode, $id, $thumbs
 * Out: $aJson
 *
 */
function GetEpisodesImportRefreshStatus($db, $mode, $id, $thumbs)
{
    $aJson['id']  = 0;
    $aJson['refresh'] = 0;
    $aJson['title']   = "empty";
    $aJson['thumbs']  = $thumbs;
  
    //$db = OpenDatabase();

    if ($mode == "import") { // Import. 
        $sql = "SELECT episodeid, refresh, showtitle, episode, title ".
               "FROM episodes ".
               "ORDER BY id DESC LIMIT 1";
    }
    else { // Refresh.
        $sql = "SELECT episodeid, refresh, showtitle, episode, title ".
               "FROM episodes ".
               "WHERE episodeid = $id";      
    }       
        
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
                $stmt->bind_result($episodeid, $refresh, $showtitle, $episode, $title);
                while($stmt->fetch())
                {                
                    $aJson['id']      = $episodeid;
                    $aJson['refresh'] = $refresh;
                    $aJson['title']   = stripslashes($showtitle);
                    $aJson['sub']     = $episode.". ".stripslashes($title);
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

    //CloseDatabase($db);

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
function ConvertTVShowToSeasonID($id)
{
    $db = OpenDatabase();    
    
    $aItems = explode("_", $id);    
    $sql = "SELECT id FROM seasons WHERE tvshowid = $aItems[0] AND season = $aItems[1]"; 
    $aJson['id'] = GetItemFromDatabase($db, "id", $sql);
    
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
 * Updated on Jan 03, 2014
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
                 EmptyTable($db, "genretomovie");
                 EmptyTable($db, "sets");
                 DeleteGenres($db, "movies");
                 UpdateStatus($db, "XbmcMoviesStart", 1);
                 UpdateStatus($db, "XbmcSetsStart", 1);
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
                 EmptyTable($db, "genretotvshow");
                 EmptyTable($db, "seasons");
                 EmptyTable($db, "episodes");
                 DeleteGenres($db, "tvshows");
                 UpdateStatus($db, "XbmcTVShowsStart", 1);
                 UpdateStatus($db, "XbmcTVShowsSeasonsStart", 1);
                 UpdateStatus($db, "XbmcEpisodesStart", 1);
                 DeleteFile(cTVSHOWSTHUMBS."/*.jpg");
                 DeleteFile(cTVSHOWSFANART."/*.jpg");
                 DeleteFile(cSEASONSTHUMBS."/*.jpg");
                 DeleteFile(cEPISODESTHUMBS."/*.jpg");
                 break;
        
        case 7 : $aJson['name']    = "music";
                 $aJson['counter'] = CountRows($db, "music");
                 EmptyTable($db, "music");
                 EmptyTable($db, "genretomusic");
                 DeleteGenres($db, "music");
                 UpdateStatus($db, "XbmcMusicStart", 1);
                 DeleteFile(cALBUMSTHUMBS."/*.jpg");
                 DeleteFile(cALBUMSCOVERS."/*.jpg");
                 break;
    }
    
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