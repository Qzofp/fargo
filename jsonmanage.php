<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    jsonmanage.php
 *
 * Created on Nov 20, 2013
 * Updated on Nov 22, 2013
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
 * Updated on Nov 22, 2013
 *
 * Description: Hide or show media.
 *
 * In:  $media, $id, $value
 * Out:	$aJson
 * 
 */
function HideOrShowMediaInFargo($media, $id, $value)
{
    switch ($media)
    {
        case "titles" : $aJson = HideOrShowMedia("movies", $id, $value);
                        break;
        
        case "sets"   : $aJson = HideOrShowMedia("sets", $id, $value);
                        break;
                    
                    
    }
    
    return $aJson;
}

/*
 * Function:	RemoveMediaFromFargo
 *
 * Created on Nov 22, 2013
 * Updated on Nov 22, 2013
 *
 * Description: Delete media from Fargo database.
 *
 * In:  $media, $id, $xbmcid
 * Out:	Deleted media
 * 
 */
function RemoveMediaFromFargo($media, $id, $xbmcid)
{   
    switch ($media)
    {
        case "titles" : $aJson = DeleteMedia("movies", $id, $xbmcid);
                        break;
        
        case "sets"   : $aJson = DeleteMedia("sets", $id, $xbmcid);
                        break;
                    
                    
    }
    
    return $aJson;   
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
    
    UpdateStatus("Xbmc".$media."End", -1);
    UpdateStatus("RefreshReady", true);    
    
    $status = "reset";    
    return $status;
}

/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	GetMediaStatus
 *
 * Created on May 18, 2013
 * Updated on Oct 31, 2013
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
                      
        case "seasons"        : $aJson = GetSeasonsImportStatus(cSEASONSTHUMBS);
                                break;
                      
        case "episodes"       : $aJson = GetEpisodesImportStatus(cEPISODESTHUMBS);
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
 * Updated on Oct 31, 2013
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
                    $aJson['title']   = ShortenString($title, 70);
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
 * Function:	GetSeasonsImportStatus
 *
 * Created on Oct 21, 2013
 * Updated on Oct 28, 2013
 *
 * Description: Reports the seasons status of the import process.
 *
 * In:  $thumbs
 * Out: $aJson
 *
 */
function GetSeasonsImportStatus($thumbs)
{
    $aJson['id']  = 0;
    $aJson['refresh'] = 0;
    $aJson['title']   = "empty";
    $aJson['thumbs']  = $thumbs;
  
    $db = OpenDatabase();

    $sql = "SELECT id, refresh, tvshowid, showtitle, title, season ".
           "FROM seasons ".
           "ORDER BY id DESC LIMIT 1";
        
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
                    $aJson['id']       = $id;
                    $aJson['refresh']  = $refresh;
                    $aJson['tvshowid'] = $tvshowid;
                    $aJson['title']    = ShortenString($showtitle, 70)."</br>".ShortenString($title, 70);
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
 * Function:	GetEpisodesImportStatus
 *
 * Created on Oct 27, 2013
 * Updated on Oct 27, 2013
 *
 * Description: Reports the episode status of the import process.
 *
 * In:  $thumbs
 * Out: $aJson
 *
 */
function GetEpisodesImportStatus($thumbs)
{
    $aJson['id']  = 0;
    $aJson['refresh'] = 0;
    $aJson['title']   = "empty";
    $aJson['thumbs']  = $thumbs;
  
    $db = OpenDatabase();

    $sql = "SELECT episodeid, refresh, showtitle, episode, title ".
           "FROM episodes ".
           "ORDER BY id DESC LIMIT 1";
        
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
                    $aJson['title']   = ShortenString($showtitle, 70)."</br>$episode. ".ShortenString($title, 70);
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
 * Updated on Aug 25, 2013
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
                 UpdateSetting("Timeout", $value * 1000);
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