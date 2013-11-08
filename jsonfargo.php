<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    jsonfargo.php
 *
 * Created on Apr 03, 2013
 * Updated on Nov 08, 2013
 *
 * Description: The main Json Fargo page.
 * 
 * Note: This page contains functions that returns Json data for Jquery code.
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
    case "info"    : $media = GetPageValue('media');
                     $id    = GetPageValue('id');
                     $aJson = GetMediaInfo($media, $id);
                     break;
                 
    case "hide"    : if($login)
                     {
                        $media = GetPageValue('media');
                        $id    = GetPageValue('id');
                        $value = GetPageValue('value');
                        $aJson = HideOrShowMedia($media, $id, $value);
                     }   
                     else {
                        $aJson = LogEvent("Warning", "Unauthorized hide action call!");
                     }                     
                     break;
                     
    case "delete"  : if($login)
                     {
                        $media  = GetPageValue('media'); 
                        $id     = GetPageValue('id');
                        $xbmcid = GetPageValue('xbmcid');
                        $aJson  = DeleteMedia($media, $id, $xbmcid);
                     }
                     else {
                        $aJson = LogEvent("Warning", "Unauthorized delete action call!");
                     }
                     break;                  
                 
    case "reset"   : if($login)
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
    
    case "counter" : if($login)
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
                 
    case "status"  : if($login)
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
    
    case "media"   : $type  = GetPageValue('type');
                     $page  = GetPageValue('page');
                     $title = GetPageValue('title'); // Sort by title.
                     $genre = GetPageValue('genre');
                     $year  = GetPageValue('year');
                     $sort  = GetPageValue('sort');
                     $aJson = GetMedia($type, $page, $title, unescape($genre), $year, $sort, $login);
                     
                     //$sql   = CreateQuery($type, $title, unescape($genre), $year, $sort, $login);
                     //$aJson = GetMedia($type, $page, $sql);
                      
                     break;               
                     
    /*    
    case "movies"  : $page  = GetPageValue('page');
                     $title = GetPageValue('title');
                     $genre = GetPageValue('genre');
                     $year  = GetPageValue('year');
                     $sort  = GetPageValue('sort');
                     $sql   = CreateQuery($action, $title, unescape($genre), $year, $sort, $login);
                     $aJson = GetMedia($action, $page, $sql);
                     break;
                
    case "tvshows" : $page  = GetPageValue('page');
                     $title = GetPageValue('title');
                     $genre = GetPageValue('genre');
                     $year  = GetPageValue('year');
                     $sort  = GetPageValue('sort');
                     $sql   = CreateQuery($action, $title, unescape($genre), $year, $sort, $login);
                     $aJson = GetMedia($action, $page, $sql);
                     break;    
                 
    case "music"  : $page  = GetPageValue('page');
                    $title = GetPageValue('title');
                    $genre = GetPageValue('genre');
                    $year  = GetPageValue('year');
                    $sort  = GetPageValue('sort');
                    $sql   = CreateQuery($action, $title, unescape($genre), $year, $sort, $login);
                    $aJson = GetMedia($action, $page, $sql);
                    break;   
    */
    case "option" : $name  = GetPageValue('name');
                    $aJson = GetSystemOptionProperties($name, $login); 
                    break;
                
    case "property":if($login)
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
                
    case "setting": // Only used for login box.
                    $name  = GetPageValue('name');
                    $aJson = ProcessSetting($name);                   
                    break;
                
    case "list"   : $type   = GetPageValue('type');
                    $filter = GetPageValue('filter');
                    $media  = GetPageValue('media');
                    $aJson  = GetSortList($type, $filter, $media, $login);
                    break;            
                
    case "log"    : if($login)
                    { 
                        $type  = GetPageValue('type');
                        $event = GetPageValue('event');
                        $aJson = LogEvent($type, $event);
                    }
                    else {
                        $aJson = LogEvent("Warning", "Unauthorized log action call!");
                    }                    
                    break;
    
    case "test"   : break;                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson);
}

//////////////////////////////////////////    Misc Functions    ///////////////////////////////////////////

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
 * Function:	GetSortList
 *
 * Created on Jun 27, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Get list of items for sorting purposes. 
 *
 * In:  $type, $filter, $media, $login
 * Out: $aJson
 *
 */
function GetSortList($type, $filter, $media, $login)
{
    $aJson = null;
    
    switch(strtolower($type))
    {
        case "genres" : $aJson["list"] = GetGenres($filter, $media, $login);
                        break;
    
        case "years"  : $aJson["list"] = GetYears($filter, $media, $login);
                        break;
    }
    
    return $aJson;
}

/*
 * Function:	GetGenres
 *
 * Created on Jun 27, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Get genres from database table genres. 
 *
 * In:  $filter, $media, $login
 * Out: $aJson
 *
 */
function GetGenres($filter, $media, $login)
{
    $stm = "";
    if (!$login) {
        $stm = " AND hide = 0 ";
    }   
    
    $md = "music";
    if ($media != "music") {
        $md = rtrim($media, "s");
    }
    
    if ($filter)
    {   
        $sql = "SELECT g.genre FROM genres g, genreto$md gtm, $media m ".
               "WHERE g.id = gtm.genreid AND m.id = gtm.".$md."id ".
               "AND m.`year` = $filter $stm".
               "GROUP BY g.genre ".
               "ORDER BY g.genre"; 
    }    
    else
    {    
        /*$sql = "SELECT genre FROM genres ".
               "WHERE media = '$media' ".
               "ORDER BY genre";
        
        */
        
        $sql = "SELECT g.genre FROM genres g, genreto$md gtm, $media m ".
               "WHERE g.id = gtm.genreid AND m.id = gtm.".$md."id ".
               "$stm".
               "GROUP BY g.genre ".
               "ORDER BY g.genre";         
    }
    
    $aJson = GetItemsFromDatabase($sql);
    
    return $aJson;
}

/*
 * Function:	GetYears
 *
 * Created on Jun 30, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Get years from database media table. 
 *
 * In:  $filter, $media, $login
 * Out: $aJson
 *
 */
function GetYears($filter, $media, $login)
{
    $stm = "";
    if (!$login) {
        $stm = " AND hide = 0 ";
    }   
    
    $md = "music";
    if ($media != "music") {
        $md = rtrim($media, "s");
    }
    
    if ($filter)
    {          
        $sql = "SELECT m.year FROM $media m, genreto$md gtm, genres g ".
               "WHERE m.id = gtm.".$md."id AND gtm.genreid = g.id ".
               "AND g.genre = '$filter' $stm".
               "GROUP BY m.`year` ".
               "ORDER BY m.`year` DESC"; 
    }
    else 
    {    
        /*$sql = "SELECT YEAR FROM $media ".
               "GROUP BY `year` ".
               "ORDER BY `year` DESC";
         */

        $sql = "SELECT m.year FROM $media m, genreto$md gtm, genres g ".
               "WHERE m.id = gtm.".$md."id AND gtm.genreid = g.id ".
               "$stm".
               "GROUP BY m.`year` ".
               "ORDER BY m.`year` DESC";          
    }
    
    $aJson = GetItemsFromDatabase($sql);
    
    return $aJson;
}

////////////////////////////////////////    Database Functions    /////////////////////////////////////////

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

/*
 * Function:	CreateMediaQuery
 *
 * Created on Apr 08, 2013
 * Updated on Nov 08, 2013
 *
 * Description: Create the sql query for the media table. 
 *
 * In:  $table, $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateMediaQuery($table, $title, $genre, $year, $sort, $login)
{   
    $sql = "SELECT id, xbmcid, hide, refresh, title, 0 AS items ".
           "FROM $table ";
    
    $sql .= CreateQuerySelection("WHERE", $sort, $year, $genre, $login);
    $sql .= CreateQuerySortQrder($title);
    
    //debug
    //echo $sql;
    
    return $sql;
}

/*
 * Function:	CreateSetsQuery
 *
 * Created on Nov 08, 2013
 * Updated on Nov 08, 2013
 *
 * Description: Create the sql query for the media sets table. 
 *
 * In:  $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateSetsQuery($title, $genre, $year, $sort, $login)
{
    $sql = "SELECT id, setid, hide, refresh, `set` AS title, COUNT(setid) AS items ".
           "FROM movies ";
    
    $sql .= CreateQuerySelection("WHERE setid > 0 AND", $sort, $year, $genre, $login);
    $sql .= "GROUP BY setid ";
    $sql .= CreateQuerySortQrder($title);
    
    //debug
    //echo $sql;
    
    return $sql;
}

/*
 * Function:	CreateEpisodesQuery
 *
 * Created on Apr 08, 2013
 * Updated on Nov 08, 2013
 *
 * Description: Create the sql query for the media episodes table. 
 *
 * In:  $table, $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateEpisodesQuery($table, $title, $genre, $year, $sort, $login)
{   
    $sql = "SELECT id, xbmcid, hide, refresh, title, episode AS items ".
           "FROM $table ";
    
    $sql .= CreateQuerySelection("WHERE", $sort, $year, $genre, $login);
    $sql .= CreateQuerySortQrder($title);
    
    //debug
    //echo $sql;
    
    return $sql;
}

/*
 * Function:	CreateQuerySelection
 *
 * Created on Nov 08, 2013
 * Updated on Nov 08, 2013
 *
 * Description: Create the sql query selection for the media table. 
 *
 * In:  $stm, $sort, $year, $genre, $login
 * Out: $sql
 *
 */
function CreateQuerySelection($stm, $sort, $year, $genre, $login)
{
    $sql = "";
    
    if (strlen($sort) == 1) {
        $sql .= "$stm sorttitle LIKE '$sort%' ";
        $stm = "AND";
    }
    
    if ($year) 
    {
        $sql .= "$stm `year` = $year ";
        $stm = "AND";
    }
    
    if ($genre) 
    {
        $sql .= "$stm genre LIKE '%\"$genre\"%' ";
        $stm = "AND";
    }
    
    // Hide media items if not login.
    if(!$login) {
        $sql .= "$stm hide = 0 ";
    }
    
    return $sql;
}        

/*
 * Function:	CreateQuerySortQorder
 *
 * Created on Nov 08, 2013
 * Updated on Nov 08, 2013
 *
 * Description: Create the sql query sort order for the media table. 
 *
 * In:  $title
 * Out: $sql
 *
 */
function CreateQuerySortQrder($title)
{
    $sql = "";
    
    switch ($title) 
    {
        case "Latest"     : $sql .= "ORDER BY id DESC";
                            break;
        
        case "Oldest"     : $sql .= "ORDER BY id";
                            break;
        
        case "Ascending"  : $sql .= "ORDER BY sorttitle";
                            break;
        
        case "Descending" : $sql .= "ORDER BY sorttitle DESC";
                            break;
    }
    
    return $sql;
}

/*
 * Function:	GetMediaInfo
 *
 * Created on Jul 05, 2013
 * Updated on Jul 10, 2013
 *
 * Description: Get the media info from Fargo and return it as Json data. 
 *
 * In:  $media, $id 
 * Out: $aJson
 *
 */
function GetMediaInfo($media, $id)
{
    $aJson = null;
    
    switch($media)
    {
        case "movies"  : $aJson = GetMovieInfo($id);
                         break;
        
        case "tvshows" : $aJson = GetTVShowInfo($id);
                         break;
        
        case "music"   : $aJson = GetAlbumInfo($id);
                         break;
    }
    
    return $aJson;
}

/*
 * Function:	GetMovieInfo
 *
 * Created on Jul 05, 2013
 * Updated on Sep 21, 2013
 *
 * Description: Get the movie info from Fargo and return it as Json data. 
 *
 * In:  $id 
 * Out: $aJson
 *
 */
function GetMovieInfo($id)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;
    
    $sql = "SELECT xbmcid, refresh, title, director, writer, studio, genre, `year`, runtime, rating,". 
                  "votes, tagline, plot, mpaa, country, trailer, audio, video, file,".
                  "imdbnr, trailer ".
           "FROM movies ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($xbmcid, $refresh, $title, $director, $writer, $studio, $genre, $year, $runtime, $rating,
                               $votes, $tagline, $plot, $mpaa, $country, $trailer, $audio, $video, $file,
                               $imdbnr, $trailer);
            $stmt->fetch();
            
            $genre = str_replace('"', '', $genre);
            
            $aMedia["xbmcid"]   = $xbmcid;
            $aMedia["refresh"]  = $refresh;
            $aMedia["title"]    = ShortenString($title, 50);
            $aMedia["director"] = str_replace("|", " / ", $director);
            $aMedia["writer"]   = str_replace("|", " / ", $writer);
            $aMedia["studio"]   = $studio;
            $aMedia["genre"]    = str_replace("|", " / ", $genre);
            $aMedia["year"]     = $year;
            $aMedia["runtime"]  = round($runtime/60)." Minutes";
            //$aMedia["votes"]    = $votes;
            $aMedia["rating"]   = $rating." ($votes votes)";
            $aMedia["tagline"]  = $tagline;
            $aMedia["plot"]     = $plot;
            $aMedia["mpaa"]     = ConvertToRatingsFlag($mpaa);
            $aMedia["country"]  = $country;
            $aMedia["trailer"]  = $trailer;
            $aMedia["audio"]    = ConvertToAudioFlag($audio);
            $aMedia["video"]    = ConvertToVideoFlag($video);
            $aMedia["aspect"]   = ConvertToAspectFlag($video, $file);
            $aMedia["imdbnr"]   = ConverToMovieUrl($imdbnr);
            $aMedia["trailer"]  = ConverToMovieUrl($trailer);
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
    
    // Fill parameters.
    $aParams['thumbs'] = cMOVIESTHUMBS;
    $aParams['fanart'] = cMOVIESFANART;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;
    
    return $aJson;
}

/*
 * Function:	GetTVShowInfo
 *
 * Created on Jul 09, 2013
 * Updated on Sep 09, 2013
 *
 * Description: Get the TV show info from Fargo and return it as Json data. 
 *
 * In:  $id 
 * Out: $aJson
 *
 */
function GetTVShowInfo($id)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;  
    
    $sql = "SELECT xbmcid, title, studio, genre, `year`, premiered, rating, votes, plot, episode,".
                  "watchedepisodes, episodeguide, imdbnr ".
           "FROM tvshows ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($xbmcid, $title, $studio, $genre, $year, $premiered, $rating, $votes, 
                               $plot, $episode, $watchedepisodes, $episodeguide, $imdbnr);
            $stmt->fetch();
            
            $genre = str_replace('"', '', $genre);
            
            $aMedia["xbmcid"]          = $xbmcid;
            $aMedia["title"]           = ShortenString($title, 50);
            $aMedia["studio"]          = $studio;
            $aMedia["genre"]           = str_replace("|", " / ", $genre);
            $aMedia["year"]            = $year;
            $aMedia["premiered"]       = date( 'd/m/Y', strtotime($premiered));
            //$aMedia["votes"]           = $votes;
            $aMedia["rating"]          = $rating." ($votes votes)";
            $aMedia["plot"]            = $plot;
            $aMedia["episode"]         = $episode;
            $aMedia["watchedepisodes"] = $watchedepisodes;
            //$aMedia["episodeguide"]    = $episodeguide;
            $aMedia["imdbnr"]          = ConverToMovieUrl($imdbnr, $episodeguide); 
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
    
    // Fill parameters.
    $aParams['thumbs'] = cTVSHOWSTHUMBS;
    $aParams['fanart'] = cTVSHOWSFANART;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;
    
    return $aJson;
}

/*
 * Function:	GetAlbumInfo
 *
 * Created on Jul 10, 2013
 * Updated on Sep 09, 2013
 *
 * Description: Get the album info from Fargo and return it as Json data. 
 *
 * In:  $id 
 * Out: $aJson
 *
 */
function GetAlbumInfo($id)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;  
    
    $sql = "SELECT xbmcid, title, genre, theme, mood, style, `year`, artist, displayartist, rating,".
           "description, albumlabel ".
           "FROM music ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($xbmcid, $title, $genre, $theme, $mood, $style, $year, $artist, $displayartist,
                               $rating, $description, $albumlabel);
            $stmt->fetch();
            
            $genre = str_replace('"', '', $genre);
            
            if ($rating < 0) {
                $rating = 0;
            }
            
            if (!$description) {
                $description = "No review for this album.";
            }
            
            $aMedia["xbmcid"]        = $xbmcid;
            $aMedia["title"]         = ShortenString($title, 50);
            $aMedia["genre"]         = str_replace("|", " / ", $genre);
            $aMedia["theme"]         = str_replace("|", " / ", $theme);
            $aMedia["mood"]          = str_replace("|", " / ", $mood);
            $aMedia["style"]         = str_replace("|", " / ", $style);            
            $aMedia["year"]          = $year;
            $aMedia["artist"]        = $artist;
            $aMedia["displayartist"] = $displayartist;
            $aMedia["rating"]        = $rating." (from 5 starts)";
            $aMedia["description"]   = $description;
            $aMedia["albumlabel"]    = $albumlabel;
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

    // Fill parameters.
    $aParams['thumbs'] = cALBUMSTHUMBS;
    $aParams['covers'] = cALBUMSCOVERS;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;    
    
    return $aJson;
}

/*
 * Function:	ConvertToRatingsFlag
 *
 * Created on Jul 08, 2013
 * Updated on Jul 08, 2013
 *
 * Description: Convert to ratings flag (media flag icons). 
 *
 * In:  $mpaa 
 * Out: $flag
 *
 */
function ConvertToRatingsFlag($mpaa)
{
    $flag = "&nbsp;";
    $rating = null;

    if ($mpaa)
    {    
        switch(strtolower($mpaa))
        {
            case "rated g"     : $rating = "mpaa_general";
                                 break;
                        
            case "rated nc-17" : $rating = "mpaa_nc17";
                                 break;
                          
            case "rated nr"    : $rating = "mpaa_notrated";
                                 break;     
                          
            case "rated pg-13" : $rating = "mpaa_pg13";
                                 break; 
                          
            case "rated pg"    : $rating = "mpaa_pg";
                                 break;
                          
            case "rated r"     : $rating = "mpaa_restricted";
                                 break;                            
                          
            default : break;                  
        }
        
        if ($rating) {
            $flag = '<img src="images/flagging/ratings/'.$rating.'.png">';   
        }
    }
    
    return $flag;
}

/*
 * Function:	ConvertToAudioFlag
 *
 * Created on Jul 08, 2013
 * Updated on Jul 08, 2013
 *
 * Description: Convert to audio flag (media flag icons). 
 *
 * In:  $audio
 * Out: $flag
 *
 */
function ConvertToAudioFlag($audio)
{
    $flag = "&nbsp;";
    if ($audio)
    {
        $aStreams = explode("|", $audio);    
        $aAudio   = explode(":", $aStreams[0]);
    
        if ($aAudio[0]) {
            $channels = '<img src="images/flagging/audio/'.$aAudio[0].'.png">';
        }
        
        if ($aAudio[1]) {
            $codec =  '<img src="images/flagging/audio/'.$aAudio[1].'.png">';
        }
        
        $flag = $codec.$channels;
    }  
    return $flag;
}

/*
 * Function:	ConvertToVideoFlag
 *
 * Created on Jul 08, 2013
 * Updated on Jul 08, 2013
 *
 * Description: Convert to video flag (media flag icons). 
 *
 * In:  $video 
 * Out: $flag
 *
 */
function ConvertToVideoFlag($video)
{
    $flag = "&nbsp;";
    if ($video)
    {
        $aStreams = explode("|", $video);    
        $aVideo   = explode(":", $aStreams[0]);
    
        // Determine resolution (height x width).
        if ($aVideo[2] <= 480 && $aVideo[3] <= 720) {
            $res = "480";
        }
        elseif ($aVideo[2] <= 544 && $aVideo[3] <= 960) {
            $res = "540";
        }        
        elseif ($aVideo[2] <= 576 && $aVideo[3] <= 768) {
            $res = "576";
        }
        elseif ($aVideo[2] <= 720 && $aVideo[3] <= 1280) {
            $res = "720";
        }        
        else {
            $res = "1080";
        }
        
        $resolution = '<img src="images/flagging/video/'.$res.'.png">';
        $codec = '<img src="images/flagging/video/'.$aVideo[1].'.png">';
        
        $flag = $resolution.$codec;
    }    
    return $flag;
}

/*
 * Function:	ConvertToAspectFlag
 *
 * Created on Jul 08, 2013
 * Updated on Jul 08, 2013
 *
 * Description: Convert to aspect flag (media flag icons). 
 *
 * In:  $video, file 
 * Out: $flag
 *
 */
function ConvertToAspectFlag($video, $file)
{
    $flag = "&nbsp;";
    $aspect = null;
    $source = null;
    
    if ($video)
    {
        $aStreams = explode("|", $video);    
        $aVideo   = explode(":", $aStreams[0]);
        
        // Determine aspect ratio.
        if ($aVideo[0] <= 1.4859) {
            $aspect = "1.33";
        }
        elseif ($aVideo[0] <= 1.7190) {
            $aspect = "1.66";
        }        
        elseif ($aVideo[0] <= 1.8147) {
            $aspect = "1.78";
        }
        elseif ($aVideo[0] <= 2.0174) {
            $aspect = "1.85";
        }    
        elseif ($aVideo[0] <= 2.2738) {
            $aspect = "2.20";
        }            
        else {
            $aspect = "2.35";
        }        
        $aspect = '<img src="images/flagging/aspectratio/'.$aspect.'.png">';        
    }        
    
    if ($file) 
    {
        if(preg_match("/dvd/i", $file)) {
            $source = "dvd";
        }
        elseif(preg_match("/((blu[\s\-_]?ray)|(bd[\s\-_]?rip)|(br[\s\-_]?rip)|(bd25)|db50)/i", $file)) {
            $source = "bluray";
        }
        elseif(preg_match("/(hd[\s\-_]?dvd)/i", $file)) {
            $source = "hddvd";
        }
        elseif(preg_match("/((hd[\s\-_]?tv)|(pd[\s\-_]?tv)|(dsr))/i", $file)) {
            $source = "tv";
        }
        elseif(preg_match("/vhs/i", $file)) {
            $source = "vhs";
        }
      
        if ($source) {
            $source = '<img src="images/flagging/video/'.$source.'.png">';
        }  
    }
    
    $flag = $aspect.$source;
    
    return $flag;
}

/*
 * Function:	ConverToMovieUrl
 *
 * Created on Jul 09, 2013
 * Updated on Jul 11, 2013
 *
 * Description: Convert to movie or TV shows database web site URL.
 *
 * In:  $id, $guide
 * Out: $url
 * 
 * Note: The first part of the URLs can be found in the settings.php page.
 *
 */
function ConverToMovieUrl($id, $guide="")
{
    $url = null;
    if (preg_match("/tt\\d{7}/", $id)) {
        $url = cIMDB.$id;
    }        
    elseif (preg_match("/(?<=\=)([^\=]+)$/", $id, $aMatches)) {
        $url = cYOUTUBE.$aMatches[0];
    }
    elseif (preg_match("/thetvdb.com/", $guide)) {
        $url = cTVDB.$id;
    }
    elseif (preg_match("/api.anidb.net/", $guide)) {
        $url = cANIDB.$id;
    }
    
    return $url;
}

/*
 * Function:	GetMedia
 *
 * Created on Nov 06, 2013
 * Updated on Nov 08, 2013
 *
 * Description: Get a page of media from Fargo and return it as Json data. 
 *
 * In:  $type, $page, $title, $genre, $year, $sort, $login
 * Out: $aJson
 *
 */
function GetMedia($type, $page, $title, $genre, $year, $sort, $login)
{
    $aJson   = null;
    $aParams = null;
    $aMedia  = null;
    $rows    = 0;
    
    switch ($type)
    {
        case "titles"  : $aParams['thumbs'] = cMOVIESTHUMBS;
                         $sql    = CreateMediaQuery("movies", $title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($sql);
                         $aMedia = QueryMedia($sql, $page);
                         break;
                    
        case "sets"    : $aParams['thumbs'] = cSETSTHUMBS;
                         $sql    = CreateSetsQuery($title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($sql);
                         $aMedia = QueryMedia($sql, $page);
                         break;
                    
        case "series"  : $aParams['thumbs'] = cTVSHOWSTHUMBS;
                         $sql    = CreateMediaQuery("tvshows", $title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($sql);
                         $aMedia = QueryMedia($sql, $page);
                         break;

        case "episodes" : $aParams['thumbs'] = cTVSHOWSTHUMBS;
                          $sql    = CreateEpisodesQuery("tvshows", $title, $genre, $year, $sort, $login);
                          $rows   = CountRowsWithQuery($sql);
                          $aMedia = QueryMedia($sql, $page);
                          break;  
                      
        case "albums"   : $aParams['thumbs'] = cALBUMSTHUMBS;
                          $sql    = CreateMediaQuery("music", $title, $genre, $year, $sort, $login);
                          $rows   = CountRowsWithQuery($sql);
                          $aMedia = QueryMedia($sql, $page);
                          break; 
                    
    }    
       
    // Fill parameters.
    $aParams['lastpage'] = ceil($rows / (cMediaRow * cMediaColumn));
    $aParams['row']      = cMediaRow;
    $aParams['column']   = cMediaColumn;  
    
    // Fill Json.
    $aJson['params'] = $aParams;
    $aJson['media']  = $aMedia;
    
    return $aJson;
}        


/*
 * Function:	QueryMedia
 *
 * Created on Apr 03, 2013
 * Updated on Nov 08, 2013
 *
 * Description: Get a page of media from Fargo and return it as Json data. 
 *
 * In:  $sql, $page
 * Out: $aJson
 *
 */
function QueryMedia($sql, $page)
{   
    $aMedia  = null; 

    // Number of movies for 1 page
    $end   = cMediaRow * cMediaColumn;
    $start = ($page - 1) * $end;
    
    // Add limit.
    $sql .=  " LIMIT $start , $end";
        
    $db = OpenDatabase();
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
                $i = 0;
                
                $stmt->bind_result($id, $xbmcid, $hide, $refresh, $title, $items);
                while($stmt->fetch())
                {                
                    
                    $aMedia[$i]['id']      = $id;                    
                    $aMedia[$i]['xbmcid']  = $xbmcid;  
                    $aMedia[$i]['hide']    = $hide;  
                    $aMedia[$i]['refresh'] = $refresh; 
                    $aMedia[$i]['title']   = ShortenString($title, 22);
                    $aMedia[$i]['items']   = $items;
                    
                    $i++;
                }                  
            }
            else
            {
                    $aMedia[0]['id']      = 0;
                    $aMedia[0]['xbmcid']  = 0; 
                    $aMedia[0]['hide']    = -1;  
                    $aMedia[0]['refresh'] = -1;    
                    $aMedia[0]['title']   = 'empty';
                    $aMedia[0]['items']   = 0;           
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
    
    return $aMedia;
}

/*
 * Function:	QuerySets
 *
 * Created on Nov 08, 2013
 * Updated on Nov 08, 2013
 *
 * Description: Get a page of movie sets from Fargo and return it as Json data. 
 *
 * In:  $sql, $page
 * Out: $aJson
 *
 */
/*function QuerySets($sql, $page)
{   
    $aMedia  = null; 

    // Number of movies for 1 page
    $end   = cMediaRow * cMediaColumn;
    $start = ($page - 1) * $end;
    
    // Add limit.
    $sql .=  " LIMIT $start , $end";
    
    $db = OpenDatabase();
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
                $i = 0;
                
                $stmt->bind_result($id, $setid, $hide, $refresh, $title, $items);
                while($stmt->fetch())
                {                
                    
                    $aMedia[$i]['id']      = $id;                    
                    $aMedia[$i]['xbmcid']  = $setid;  
                    $aMedia[$i]['hide']    = $hide;  
                    $aMedia[$i]['refresh'] = $refresh; 
                    $aMedia[$i]['title']   = ShortenString($title, 22);
                    $aMedia[$i]['items']   = $items;                  
                    
                    $i++;
                }                  
            }
            else
            {
                    $aMedia[0]['id']      = 0;
                    $aMedia[0]['xbmcid']  = 0;  
                    $aMedia[0]['hide']    = -1;  
                    $aMedia[0]['refresh'] = -1;                      
                    $aMedia[0]['title']   = 'empty';
                    $aMedia[0]['items']   = 0;  
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
    
    return $aMedia;
}*/

/*
 * Function:	ProcessSetting
 *
 * Created on Jun 09, 2013
 * Updated on Jul 15, 2013
 *
 * Description: Get value from settings database and process value if necessary. 
 *
 * In:  $name 
 * Out: $aJson
 *
 */
function ProcessSetting($name)
{
    $aJson = null;
    $value = GetSetting($name);
    
    if ($name == "Hash") {
        $value = md5($value);
    }
    
    $aJson["value"] = $value;
    
    return $aJson;
}

/*
 * Function:	GetSystemOptionProperties
 *
 * Created on May 20, 2013
 * Updated on Oct 06, 2013
 *
 * Description: Get the system option properties page from the database table settings. 
 *
 * In:  $name, $login
 * Out: $aJson
 *
 */
function GetSystemOptionProperties($name, $login)
{
    $html = null;
    switch(strtolower($name))
    {
        case "statistics" : $html = GetSetting($name);                                
                            $html = str_replace("[movies]", CountMedia("movies", $login), $html);
                            $html = str_replace("[tvshows]", CountMedia("tvshows", $login), $html);
                            $html = str_replace("[music]", CountMedia("music", $login), $html);
                            break;
                                       
        case "settings"   : $html = GetSetting($name);
                            $html = str_replace("[connection]", GetSetting("XBMCconnection"), $html);
                            $html = str_replace("[port]", GetSetting("XBMCport"), $html);
                            $html = str_replace("[xbmcuser]", GetSetting("XBMCusername"), $html);
                            $html = str_replace("[fargouser]", GetUser(1), $html);
                            $html = str_replace("[password]", "******", $html);
                            $html = str_replace("[timeout]", GetSetting("Timeout")/1000, $html);
                            break;
                        
        case "library"    : $html = GetSetting($name);
                            break;
                        
        case "event log"  : $html  = "<div class=\"system_scroll\">";
                            $html .= "<table>";
                            $html .= GetSetting($name);
                            $html .= GenerateEventRows();
                            $html .= "</table>";
                            $html .= "</div>";    
                            break;
                        
        case "credits"    : $html  = "<div class=\"system_scroll text\">";
                            $html .= GetSetting($name);
                            $html .= "</div>";
                            break;                        
                        
        case "about"      : $html  = "<div class=\"system_scroll text\">";
                            $html .= GetSetting($name);
                            $html .= "</div>";
                            
                            $html = str_replace("[version]", GetSetting("Version"), $html);
                            break;                        
    }
    
    $aJson['html'] = $html;
    return $aJson;
}

/*
 * Function:	GenerateEventRows
 *
 * Created on Jun 10, 2013
 * Updated on Jun 10, 2013
 *
 * Description: Generate event log table rows.
 *
 * In:  -
 * Out: $events
 *
 */
function GenerateEventRows()
{
    $events = null;
    
    $db = OpenDatabase();

    $sql = "SELECT date, type, event ".
           "FROM log ".
           "ORDER BY date DESC ".
           "LIMIT 0, 100";
        
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
                $stmt->bind_result($date, $type, $event);
                while($stmt->fetch())
                {                
                    $events .= "<tr class=\"property log\">";
                    $events .= "<td>$date</td>";
                    $events .= "<td>$type</td>"; 
                    $events .= "<td>$event</td>";                  
                    $events .= "</tr>";
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
    
    return $events;
}

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
 * Function:	CleanLibrary
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