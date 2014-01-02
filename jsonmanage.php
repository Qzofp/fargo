<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    jsonmanage.php
 *
 * Created on Nov 20, 2013
 * Updated on Jan 02, 2014
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
                         $aJson["status"]     = ResetStatus($media, $counter);
                         $aJson["connection"] = GetSetting("XBMCconnection");
                         $aJson["port"]       = GetSetting("XBMCport");
                         $aJson["timeout"]    = GetSetting("Timeout");
                         $aJson["key"]        = GenerateKey();
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized reset action call!");
                      }
                      break;
                    
    case "counter"  : if($login)
                      {
                         $media = GetPageValue('media');
                         $aJson['xbmc']['start'] = GetStatus("Xbmc".$media."Start");
                         $aJson['xbmc']['end']   = GetStatus("Xbmc".$media."End");
                         $aJson['import']        = GetStatus("ImportCounter");
                      }
                      else {
                         $aJson = LogEvent("Warning", "Unauthorized counter action call!");
                      }                     
                      break;
                      
    case "init"     : if($login)
                      {
                          $aJson['ready'] = GetStatus("ImportReady");
                          if ($aJson['ready'] >= 0) {
                              UpdateStatus("ImportReady", -1);
                          }
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
                        
                         if ($mode == "import" ) 
                         {
                            $aJson = GetMediaStatus($media, -1);
                            $aJson['start'] = GetStatus("Xbmc".$media."Start");
                            $aJson['slack'] = GetStatus("XbmcSlack");
                         }
                         else 
                         {
                            $aJson = GetMediaStatus($media, $id);
                            $aJson['ready'] = GetStatus("RefreshReady");
                         }
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
 * Updated on Nov 28, 2013
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
                      
        case "series"   : $aItems = explode("_", $id);
                          $aJson  = DeleteMedia("tvshows", $aItems[0], $xbmcid); // TV Show + Seasons + Episodes.
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
 * Updated on Dec 24, 2013
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
    
    switch ($media)
    {
        case "movies"   : DeleteMediaQuery("movies", $id);
                          DeleteMediaGenreQuery("movie", $id);
                          DeleteFile(cMOVIESTHUMBS."/$xbmcid.jpg");
                          DeleteFile(cMOVIESFANART."/$xbmcid.jpg");
                          break;
                     
        case "sets"     : // Won't delete the movies in the set. Maybe in the future releases.
                          DeleteMediaQuery("sets", $id);
                          DeleteFile(cSETSTHUMBS."/$xbmcid.jpg");
                          //DeleteFile(cSETSFANART."/$xbmcid.jpg");
                          break;
                            
        case "tvshows"  : // Delete episodes.
                          $sql = "SELECT CONCAT(episodeid, '.jpg') AS thumb FROM episodes WHERE ".
                                 "tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          $aThumbs = GetItemsFromDatabase($sql);
                          DeleteMultipleFiles(cEPISODESTHUMBS, $aThumbs);
                          
                          $sql = "DELETE FROM episodes WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          ExecuteQuery($sql);
                          
                          // Delete seasons.
                          $sql = "SELECT CONCAT(tvshowid, '_', season) AS xbmcid FROM seasons ".
                                 "WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          $aThumbs = GetItemsFromDatabase($sql);
                          DeleteMultipleFiles(cSEASONSTHUMBS, $aThumbs);
                          
                          $sql = "DELETE FROM seasons WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id)";
                          ExecuteQuery($sql);
                          
                          // Delete TV show.
                          DeleteMediaQuery("tvshows", $id);
                          DeleteMediaGenreQuery("tvshow", $id);

                          DeleteFile(cTVSHOWSTHUMBS."/$xbmcid.jpg");
                          DeleteFile(cTVSHOWSFANART."/$xbmcid.jpg");
                          break;
                     
        case "seasons"  : $db = OpenDatabase();            
                          $aItems = explode("_", $id);
                          
                          // Delete episodes.
                          $sql = "SELECT CONCAT(episodeid, '.jpg') AS thumb FROM episodes WHERE tvshowid = ".
                                 "(SELECT tvshowid FROM seasons WHERE id = $aItems[0]) AND season = $aItems[1]";
                          $aThumbs = GetItemsFromDatabase($sql);
                          DeleteMultipleFiles(cEPISODESTHUMBS, $aThumbs);
                          
                          $sql = "DELETE FROM episodes WHERE tvshowid = (SELECT tvshowid FROM seasons ".
                                 "WHERE id = $aItems[0]) AND season = $aItems[1]";
                          ExecuteQuery($sql);
                         
                          // Delete seasons.
                          $sql = "SELECT CONCAT(tvshowid, '_', season) AS xbmcid FROM seasons WHERE id = $aItems[0]";
                          $xbmcid = GetItemFromDatabase($db, "xbmcid", $sql);
                          DeleteFile(cSEASONSTHUMBS."/$xbmcid.jpg");

                          DeleteMediaQuery("seasons", $aItems[0]);
                          CloseDatabase($db);                           
                          break;
                     
        case "episodes" : DeleteMediaQuery("episodes", $id);
                          DeleteFile(cEPISODESTHUMBS."/$xbmcid.jpg");
                          break;                     
                            
        case "music"    : DeleteMediaQuery("music", $id);
                          DeleteMediaGenreQuery("music", $id);
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
 * Updated on Nov 22, 2013
 *
 * Description: Delete media from the Fargo database.
 *
 * In:  $table, $id
 * Out:	Deleted media
 * 
 */
function DeleteMediaQuery($table, $id)
{
    $sql = "DELETE FROM $table ".
           "WHERE id = $id";
    
    ExecuteQuery($sql);
}

/*
 * Function:	DeleteMediaGenreQuery
 *
 * Created on Nov 22, 2013
 * Updated on Nov 22, 2013
 *
 * Description: Delete media genre from Fargo database.
 *
 * In:  $name, $id
 * Out:	Deleted media genre
 * 
 */
function DeleteMediaGenreQuery($name, $id)
{
    $sql = "DELETE FROM genreto$name ".
           "WHERE ".$name."id = $id";
    
    ExecuteQuery($sql);
}

/*
 * Function:	ResetStatus
 *
 * Created on Jul 22, 2013
 * Updated on Oct 21, 2013
 *
 * Description: Reset the status. 
 *
 * In:  $media, $counter
 * Out: $status
 *
 */
function ResetStatus($media, $counter)
{       
    if ($media == "seasons") 
    {
        $start = GetStatus("XbmcSeasonsStart");
        $end   = GetStatus("XbmcSeasonsEnd");
        
        if ($start >= $end) {
            UpdateStatus("XbmcSeasonsStart", 0);
        }
    }
    
    if ($counter == "true") {
        UpdateStatus("ImportCounter", 0);
    }
    else {
        UpdateStatus("RefreshReady", 0);
    }
    
    UpdateStatus("Xbmc".$media."End", -1);
        
    $status = "reset";    
    return $status;
}

/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	ProcessImportMode
 *
 * Created on Dec 13, 2013
 * Updated on Dec 13, 2013
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
    
    switch($mode)
    {
        case "check"  : $aJson["check"] = GetStatus("ImportReady");
                         break;
                    
        case "lock"   : UpdateStatus("ImportReady", 0); // 0 = false.
                        $aJson["check"] = 0;
                        break;
                   
        case "unlock" : UpdateStatus("ImportReady", 1); // 1 = true. 
                        $aJson["check"] = 1;
                        break;                   
    }
    
    return $aJson;
}

/*
 * Function:	GetMediaStatus
 *
 * Created on May 18, 2013
 * Updated on Dec 02, 2013
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  $media, $id
 * Out: $aJson
 *
 */
function GetMediaStatus($media, $id)
{
    $aJson = null;   
    switch ($media)    
    {   
        case "movies"         : $aJson = GetImportRefreshStatus($media, $id, "xbmcid", cMOVIESTHUMBS);
                                break;
                      
        case "sets"           : $aJson = GetImportRefreshStatus($media, $id, "setid", cSETSTHUMBS);
                                break;                      
    
        case "tvshows"        : $aJson = GetImportRefreshStatus($media, $id, "xbmcid", cTVSHOWSTHUMBS);
                                break;
                      
        case "tvshowsseasons" : $aJson['id'] = -1;
                                if (GetStatus("XbmcSeasonsStart") == GetStatus("XbmcSeasonsEnd")) {
                                    $aJson['id'] = 1;
                                }
                                break;
                      
        case "seasons"        : $aJson = GetSeasonsImportRefreshStatus($id, cSEASONSTHUMBS);
                                break;
                      
        case "episodes"       : $aJson = GetEpisodesImportRefreshStatus($id, cEPISODESTHUMBS);
                                break;                      
                      
        case "music"          : $aJson = GetImportRefreshStatus($media, $id, "xbmcid", cALBUMSTHUMBS);
                                break;                      
    }    
    return $aJson;
}

/*
 * Function:	GetImportRefreshStatus
 *
 * Created on May 18, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Reports the status of the import or refresh process.
 *
 * In:  $media, $id, $nameid, $thumbs
 * Out: $aJson
 *
 */
function GetImportRefreshStatus($media, $id, $nameid, $thumbs)
{
    $aJson['id']  = 0;
    $aJson['refresh'] = 0;
    $aJson['title']   = "empty";
    $aJson['thumbs']  = $thumbs;
  
    $db = OpenDatabase();

    if ($id < 0) { // Import.
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

    CloseDatabase($db);

    return $aJson;
}

/*
 * Function:	GetSeasonsImportRefreshStatus
 *
 * Created on Oct 21, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Reports the seasons status of the import process.
 *
 * In:  $id, $thumbs
 * Out: $aJson
 *
 */
function GetSeasonsImportRefreshStatus($seasonid, $thumbs)
{
    $aJson['id']      = 0;
    $aJson['refresh'] = 0;
    $aJson['title']   = "empty";
    $aJson['thumbs']  = $thumbs;
  
    $db = OpenDatabase();

    if ($seasonid < 0) { // Import. 
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
                    if ($seasonid < 0) {
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

    CloseDatabase($db);

    return $aJson;
}

/*
 * Function:	GetEpisodesImportRefreshStatus
 *
 * Created on Oct 27, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Reports the episode status of the import process.
 *
 * In:  $id, $thumbs
 * Out: $aJson
 *
 */
function GetEpisodesImportRefreshStatus($id, $thumbs)
{
    $aJson['id']  = 0;
    $aJson['refresh'] = 0;
    $aJson['title']   = "empty";
    $aJson['thumbs']  = $thumbs;
  
    $db = OpenDatabase();

    if ($id < 0) { // Import. 
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

    CloseDatabase($db);

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
        case "settings"  : SetSettingProperty($number, $value);            
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
 * Updated on Dec 11, 2013
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
    
    switch($number)
    {
        case 1 : // Set XBMC Connection
                 UpdateSetting("XBMCconnection", $value);
                 break;
             
        case 2 : // Set XBMC Port
                 UpdateSetting("XBMCport", $value);
                 break;
             
        case 3 : // Set XBMC Username
                 UpdateSetting("XBMCusername", $value);
                 break;
             
        case 4 : // Set XBMC Password
                 UpdateSetting("XBMCpassword", $value);
                 break; 
             
        case 6 : // Set Fargo Username
                 UpdateUser(1, $value);
                 break;
             
        case 7 : // Set Fargo Password
                 UpdatePassword(1, $value);
                 break; 
             
        case 9 : // Set Timer
                 UpdateSetting("Timeout", $value);
                 break;              
    }
    
    return $aJson;
}

/*
 * Function:	CleanLibrary
 *
 * Created on Jun 10, 2013
 * Updated on Oct 27, 2013
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
    
    switch($number)
    {
        case 1 : $aJson['name']    = "movies";
                 $aJson['counter']  = CountRows("movies");
                 $aJson['counter'] += CountRows("sets");
                 EmptyTable("movies");
                 EmptyTable("genretomovie");
                 EmptyTable("sets");
                 DeleteGenres("movies");
                 UpdateStatus("XbmcMoviesStart", 1);
                 UpdateStatus("XbmcSetsStart", 1);
                 DeleteFile(cMOVIESTHUMBS."/*.jpg");
                 DeleteFile(cMOVIESFANART."/*.jpg");
                 DeleteFile(cSETSTHUMBS."/*.jpg");
                 DeleteFile(cSETSFANART."/*.jpg");
                 break;
        
        case 4 : $aJson['name']    = "tvshows";
                 $aJson['counter']  = CountRows("tvshows");
                 $aJson['counter'] += CountRows("seasons");
                 $aJson['counter'] += CountRows("episodes");
                 EmptyTable("tvshows");
                 EmptyTable("genretotvshow");
                 EmptyTable("seasons");
                 EmptyTable("episodes");
                 DeleteGenres("tvshows");
                 UpdateStatus("XbmcTVShowsStart", 1);
                 UpdateStatus("XbmcTVShowsSeasonsStart", 1);
                 UpdateStatus("XbmcEpisodesStart", 1);
                 DeleteFile(cTVSHOWSTHUMBS."/*.jpg");
                 DeleteFile(cTVSHOWSFANART."/*.jpg");
                 DeleteFile(cSEASONSTHUMBS."/*.jpg");
                 DeleteFile(cEPISODESTHUMBS."/*.jpg");
                 break;
        
        case 7 : $aJson['name']    = "music";
                 $aJson['counter'] = CountRows("music");
                 EmptyTable("music");
                 EmptyTable("genretomusic");
                 DeleteGenres("music");
                 UpdateStatus("XbmcMusicStart", 1);
                 DeleteFile(cALBUMSTHUMBS."/*.jpg");
                 DeleteFile(cALBUMSCOVERS."/*.jpg");
                 break;
    }
    
    return $aJson;
}

/*
 * Function:	DeleteGenres
 *
 * Created on Jul 04, 2013
 * Updated on Jul 04, 2013
 *
 * Description: Delete media genres.
 *
 * In:  $media
 * Out: Deleted Genres
 *
 */
function DeleteGenres($media)
{
    $sql = "DELETE FROM genres ".
           "WHERE media = '$media'";
    
    ExecuteQuery($sql);
}

/*
 * Function:	CleanEventLog
 *
 * Created on Jun 15, 2013
 * Updated on Jun 15, 2013
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
    $aJson['name']    = "log";
    $aJson['counter'] = CountRows("log");
    EmptyTable("log");

    return $aJson;
}