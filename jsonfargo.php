<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    jsonfargo.php
 *
 * Created on Apr 03, 2013
 * Updated on Dec 31, 2013
 *
 * Description: The main Json Display page.
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
                 
    case "popup"   : // Info for Refresh and Delete Popups (Yes/No).
                     $media = GetPageValue('media');
                     $id    = GetPageValue('id');
                     $aJson = GetPopupInfo($media, $id);
                     break;
                     
    case "media"   : $type  = GetPageValue('type');
                     $page  = GetPageValue('page');
                     $title = GetPageValue('title'); // Sort by title, year, etc.
                     $level = GetPageValue('level'); // Set id or TV Show id.
                     $genre = GetPageValue('genre');
                     $year  = GetPageValue('year');
                     $sort  = GetPageValue('sort');
                     $aJson = GetMedia($type, $page, $title, $level, unescape($genre), $year, $sort, $login);                     
                     break;               
                     
    case "option" : $name  = GetPageValue('name');
                    $aJson = GetSystemOptionProperties($name, $login); 
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
  
    case "test"   : break;                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson);
}

//////////////////////////////////////////    Info Functions    ///////////////////////////////////////////

/*
 * Function:	GetMediaInfo
 *
 * Created on Jul 05, 2013
 * Updated on Nov 25, 2013
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
        case "movies"   : $aJson = GetMovieInfo($id);
                          break;
                      
        case "titles"   : $aJson = GetPopupInfo("movies", "xbmc", $id, cMOVIESTHUMBS); // For Refresh and Delete Popups.
                          break;
                      
        case "sets"     : $aJson = GetPopupInfo("sets", "set", $id, cSETSTHUMBS); // For Refresh and Delete Popups.
                          break;   
                      
        case "movieset" : $aJson = GetPopupInfo("movies", "xbmc", $id, cMOVIESTHUMBS); // For Refresh and Delete Popups.
                          break;                      
        
        case "tvshows"  : $aJson = GetTVShowInfo($id);
                          break;
                      
        case "tvtitles" : $aJson = GetPopupInfo("tvshows", "xbmc", $id, cTVSHOWSTHUMBS); // For Refresh and Delete Popups.
                          break;
                      
        case "series"   : $aItems = explode("_", $id);
                          $aJson = GetPopupInfo("tvshows", "xbmc", $aItems[0], cTVSHOWSTHUMBS); // For Refresh and Delete Popups.
                          break;                      
                     
        case "episodes" : $aJson = GetTVShowEpisodeInfo($id);
                          break;                     
        
        case "music"    : $aJson = GetAlbumInfo($id);
                          break;
    }
    
    return $aJson;
}

/*
 * Function:	GetMovieInfo
 *
 * Created on Jul 05, 2013
 * Updated on Dec 31, 2013
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
            $aMedia["title"]    = stripslashes($title);
            $aMedia["director"] = str_replace("|", " / ", $director);
            $aMedia["writer"]   = str_replace("|", " / ", $writer);
            $aMedia["studio"]   = $studio;
            $aMedia["genre"]    = str_replace("|", " / ", $genre);
            $aMedia["year"]     = $year;
            $aMedia["runtime"]  = round($runtime/60)." Minutes";
            //$aMedia["votes"]    = $votes;
            $aMedia["rating"]   = strcmp($rating, "0.00")?$rating." ($votes votes)":0;           
            $aMedia["tagline"]  = stripslashes($tagline);
            $aMedia["plot"]     = stripslashes($plot);
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
 * Updated on Dec 31, 2013
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
            $aMedia["title"]           = stripslashes($title);
            $aMedia["studio"]          = $studio;
            $aMedia["genre"]           = str_replace("|", " / ", $genre);
            $aMedia["year"]            = $year;
            $aMedia["premiered"]       = date( 'd/m/Y', strtotime($premiered));
            //$aMedia["votes"]           = $votes;
            //$aMedia["rating"]          = $rating." ($votes votes)";
            $aMedia["rating"]          = strcmp($rating, "0.00")?$rating." ($votes votes)":0;              
            $aMedia["plot"]            = stripslashes($plot);
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
 * Function:	GetTVShowEpisodeInfo
 *
 * Created on Nov 17, 2013
 * Updated on Dec 31, 2013
 *
 * Description: Get the TV show episode info from Fargo and return it as Json data. 
 *
 * In:  $id 
 * Out: $aJson
 *
 */
function GetTVShowEpisodeInfo($id)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;  
    
    $sql = "SELECT episodeid, title, showtitle, season, episode, firstaired, director, writer, YEAR(firstaired) AS `year`, ".
                  "runtime, rating, audio, video, `file`, plot ".
           "FROM episodes ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($episodeid, $title, $showtitle, $season, $episode, $firstaired, $director, $writer, $year, 
                               $runtime, $rating, $audio, $video, $file, $plot);
            $stmt->fetch();
            
            $aMedia["episodeid"]  = $episodeid;
            $aMedia["title"]      = stripslashes($title);
            $aMedia["showtitle"]  = stripslashes($showtitle);
            $aMedia["season"]     = $season;
            $aMedia["episode"]    = $episode;
            $aMedia["firstaired"] = date( 'd/m/Y', strtotime($firstaired));
            $aMedia["director"]   = str_replace("|", " / ", $director);
            $aMedia["writer"]     = str_replace("|", " / ", $writer);                        
            $aMedia["year"]       = $year;
            $aMedia["runtime"]    = round($runtime/60)." Minutes";
            $aMedia["rating"]     = strcmp($rating, "0.00")?$rating:0;
            $aMedia["audio"]      = ConvertToAudioFlag($audio);
            $aMedia["video"]      = ConvertToVideoFlag($video);
            $aMedia["aspect"]     = ConvertToAspectFlag($video, $file);            
            $aMedia["plot"]       = stripslashes($plot);
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
    $aParams['thumbs'] = cEPISODESTHUMBS;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;
    
    return $aJson;
}

/*
 * Function:	GetAlbumInfo
 *
 * Created on Jul 10, 2013
 * Updated on Dec 31, 2013
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
            $aMedia["title"]         = stripslashes($title);
            $aMedia["genre"]         = str_replace("|", " / ", $genre);
            $aMedia["theme"]         = str_replace("|", " / ", $theme);
            $aMedia["mood"]          = str_replace("|", " / ", $mood);
            $aMedia["style"]         = str_replace("|", " / ", $style);            
            $aMedia["year"]          = $year;
            $aMedia["artist"]        = $artist;
            $aMedia["displayartist"] = $displayartist;
            $aMedia["rating"]        = $rating." (from 5 starts)";
            $aMedia["description"]   = stripslashes($description);
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

//////////////////////////////////////////    Popup Functions    //////////////////////////////////////////

/*
 * Function:	GetPopupInfo
 *
 * Created on Nov 25, 2013
 * Updated on Dec 24, 2013
 *
 * Description: Get the popup info for the refresh or delete popups from Fargo and return it as Json data. 
 *
 * In:  $media, $id 
 * Out: $aJson
 *
 */
function GetPopupInfo($media, $id)
{
    $aJson = null;
    
    switch($media)
    {                      
        case "titles"   : $sql = "SELECT xbmcid, refresh, title, NULL AS sub ".
                                 //"IF(LENGTH(title) > 70, CONCAT(LEFT(title, 69),'...'), title) AS title ". 
                                 "FROM movies WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cMOVIESTHUMBS);
                          break;
                      
        case "sets"     : $sql = "SELECT setid, refresh, title, NULL AS sub ".
                                 //"IF(LENGTH(title) > 70, CONCAT(LEFT(title, 69),'...'), title) AS title ".
                                 "FROM sets WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cSETSTHUMBS);
                          break;   
                      
        case "movieset" : $sql = "SELECT xbmcid, refresh, title, NULL AS sub ".
                                 //"IF(LENGTH(title) > 70, CONCAT(LEFT(title, 69),'...'), title) AS title ".
                                 "FROM movies WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cMOVIESTHUMBS);
                          break;                      

                      
        case "tvtitles" : $sql = "SELECT xbmcid, refresh, title, NULL AS sub ".
                                 //"IF(LENGTH(title) > 70, CONCAT(LEFT(title, 69),'...'), title) AS title ". 
                                 "FROM tvshows WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cTVSHOWSTHUMBS);
                          break;
                      
        case "series"   : $aItems = explode("_", $id);
                          $sql = "SELECT CONCAT(t.xbmcid, '_', s.season) AS id, t.refresh, t.title, NULL AS sub ".
                                 //"IF(LENGTH(t.title) > 70, CONCAT(LEFT(t.title, 69),'...'), t.title) AS title ".
                                 "FROM tvshows t, seasons s WHERE t.xbmcid = s.tvshowid AND t.id = $aItems[0] ".
                                 "LIMIT 0, 1";
                          $aJson = GetPopupMediaInfo($sql, cSEASONSTHUMBS);
                          break;
                      
        case "seasons"  : $aItems = explode("_", $id);
                          $sql = "SELECT CONCAT(tvshowid, '_', season) AS id, refresh, showtitle, title AS sub ".
                                 //"IF(LENGTH(showtitle) > 70, CONCAT(LEFT(showtitle, 69),'...', '</br>', title), ".
                                 //"CONCAT(showtitle, '</br>', title)) AS title ".
                                 "FROM seasons WHERE id = $aItems[0] ";
                          $aJson = GetPopupMediaInfo($sql, cSEASONSTHUMBS);
                          break;
                      
        case "episodes" : $sql = "SELECT episodeid, refresh, showtitle AS title, CONCAT(episode, '. ', title) AS sub ".
                                 //"IF(LENGTH(title) > 67, CONCAT(episode, '. ', LEFT(title, 66), '...'), CONCAT(episode, '. ', title)) AS title ".
                                 "FROM episodes WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cEPISODESTHUMBS);
                          break;
                      
        case "albums"   : $sql = "SELECT xbmcid, refresh, title, NULL AS sub ".
                                 //"IF(LENGTH(title) > 70, CONCAT(LEFT(title, 69),'...'), title) AS title ". 
                                 "FROM music WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cALBUMSTHUMBS);
                          break;              
                                           
    }
    
    return $aJson;
}


/*
 * Function:	GetPopupMediaInfo
 *
 * Created on Nov 22, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Get the media info popups from Fargo and return it as Json data. 
 *
 * In:  $table, $xbmc, $id, $thumb 
 * Out: $aJson
 *
 */
function GetPopupMediaInfo($sql, $thumb)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($xbmcid, $refresh, $title, $sub);
            $stmt->fetch();
            
            $aMedia["xbmcid"]   = $xbmcid;
            $aMedia["refresh"]  = $refresh;
            $aMedia["title"]    = stripslashes($title);
            $aMedia["sub"]      = stripslashes($sub);
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
    $aParams['thumbs'] = $thumb;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;
    
    return $aJson;
}

//////////////////////////////////////////    Media Functions    //////////////////////////////////////////

/*
 * Function:	GetMedia
 *
 * Created on Nov 06, 2013
 * Updated on Dec 15, 2013
 *
 * Description: Get a page of media from Fargo and return it as Json data. 
 *
 * In:  $type, $page, $title, $genre, $year, $sort, $login
 * Out: $aJson
 *
 */
function GetMedia($type, $page, $title, $level, $genre, $year, $sort, $login)
{
    $aJson   = null;
    $aParams = null;
    $aMedia  = null;
    $header  = "";
    $rows    = 0;
    $max     = 0; 
    
    switch ($type)
    {
        case "titles"  : $aParams['thumbs'] = cMOVIESTHUMBS;
                         $aParams['column'] = cMediaColumn;
                         $header = "Movie Titles";
                         $sql    = CreateMediaQuery("movies", $title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($sql);
                         $max    = cMediaRow * cMediaColumn;                             
                         $aMedia = QueryMedia($sql, $page, $max, 20);
                         break;
                    
        case "sets"    : $aParams['thumbs'] = cSETSTHUMBS;
                         $aParams['column'] = cMediaColumn;
                         $header = "Movie Sets";
                         $sql    = CreateSetsQuery($title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($sql);
                         $max    = cMediaRow * cMediaColumn; 
                         $aMedia = QueryMedia($sql, $page, $max, 20);
                         break;
                     
        case "movieset": $aParams['thumbs'] = cMOVIESTHUMBS; // The set movies.
                         $aParams['column'] = cMediaColumn;
                         $header = GetItemFromDatabase("title", "SELECT title FROM `sets` WHERE id = $level");
                         $sql    = CreateMoviesSetQuery($title, $level, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($sql);
                         $max    = cMediaRow * cMediaColumn; 
                         $aMedia = QueryMedia($sql, $page, $max, 20);
                         break;                     
                    
        case "tvtitles": $aParams['thumbs'] = cTVSHOWSTHUMBS;
                         $aParams['column'] = cMediaColumn;
                         $header = "TV Show Titles";
                         $sql    = CreateMediaQuery("tvshows", $title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($sql);
                         $max    = cMediaRow * cMediaColumn; 
                         $aMedia = QueryMedia($sql, $page, $max, 20);
                         break;

        case "series"   : $aParams['thumbs'] = cSEASONSTHUMBS;
                          $aParams['column'] = cMediaColumn;
                          $header = "TV Show Series";
                          $sql    = CreateSeriesQuery($title, $genre, $year, $sort, $login);
                          $rows   = CountRowsWithQuery($sql);
                          $max    = cMediaRow * cMediaColumn; 
                          $aMedia = QueryMedia($sql, $page, $max, 20);
                          break;
                      
        case "seasons"  : $aParams['thumbs'] = cSEASONSTHUMBS;
                          $aParams['column'] = cMediaColumn;
                          $aItems = explode("_", $level);
                          $header = GetItemFromDatabase("showtitle", "SELECT showtitle FROM seasons WHERE tvshowid = ".
                                                        "(SELECT xbmcid FROM tvshows WHERE id = $aItems[0]) LIMIT 0, 1");
                          $sql    = CreateSeasonsQuery($aItems[0], $login);
                          $rows   = CountRowsWithQuery($sql);
                          $max    = cMediaRow * cMediaColumn; 
                          $aMedia = QueryMedia($sql, $page, $max, 20);
                          break;
                      
        case "episodes" : $aParams['thumbs'] = cEPISODESTHUMBS;
                          $aParams['column'] = cMediaEpisodeColumn;
                          $aItems = explode("_", $level);
                          if ($aItems[1] > -1) {
                            $header = GetItemFromDatabase("showtitle", "SELECT CONCAT(showtitle, ' - ', title) AS title ".
                                                          "FROM seasons WHERE id = $aItems[0]");
                          }
                          else {
                            $header = GetItemFromDatabase("showtitle", "SELECT title FROM tvshows WHERE id = $aItems[0]");  
                          }
                          $sql    = CreateEpisodesQuery($aItems[0], $aItems[1], $login);
                          $rows   = CountRowsWithQuery($sql);
                          $max    = cMediaRow * cMediaEpisodeColumn; 
                          $aMedia = QueryMedia($sql, $page, $max, 36);
                          break;                      
                      
        case "albums"   : $aParams['thumbs'] = cALBUMSTHUMBS;
                          $aParams['column'] = cMediaColumn;
                          $header = "Music Albums";
                          $sql    = CreateMediaQuery("music", $title, $genre, $year, $sort, $login);
                          $rows   = CountRowsWithQuery($sql);
                          $max    = cMediaRow * cMediaColumn; 
                          $aMedia = QueryMedia($sql, $page, $max, 20);
                          break;             
    }    
       
    // Fill parameters.
    $aParams['lastpage'] = ceil($rows/$max);
    $aParams['row']      = cMediaRow;
    $aParams['header']   = stripslashes($header); // Header title for sets and series.
    
    // Fill Json.
    $aJson['params'] = $aParams;
    $aJson['media']  = $aMedia;
    
    return $aJson;
}

/*
 * Function:	CreateMediaQuery
 *
 * Created on Apr 08, 2013
 * Updated on Nov 20, 2013
 *
 * Description: Create the sql query for the media table. 
 *
 * In:  $table, $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateMediaQuery($table, $title, $genre, $year, $sort, $login)
{   
    $sql = "SELECT id, xbmcid, hide, refresh, title ".
           "FROM $table ";
    
    $sql .= CreateQuerySelection("", "WHERE ", $sort, $year, $genre, $login);
    $sql .= CreateQuerySortQrder("", $title);
    
    return $sql;
}

/*
 * Function:	CreateSetsQuery
 *
 * Created on Nov 08, 2013
 * Updated on Nov 23, 2013
 *
 * Description: Create the sql query for the media sets table. 
 *
 * In:  $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateSetsQuery($title, $genre, $year, $sort, $login)
{    
    $sql = "SELECT DISTINCT s.id AS id, s.setid, s.hide, s.refresh, s.title AS title ".
           "FROM (SELECT setid, MIN(`year`) AS minyear FROM movies GROUP BY setid) ma ".
           "JOIN sets s ON s.setid = ma.setid ".
           "INNER JOIN movies mb ON ma.setid = mb.setid ".
           "INNER JOIN movies mc ON ma.setid = mc.setid AND ma.minyear = mc.year ";
    
    $stm = "WHERE";
    if (strlen($sort) == 1) {
        $sql .= "$stm s.sorttitle LIKE '$sort%' ";
        $stm = "AND";
    }
    
    if ($year) 
    {
        $sql .= "$stm mc.year = $year ";
        $stm = "AND";
    }
    
    if ($genre) 
    {
        $sql .= "$stm mc.genre LIKE '%\"$genre\"%' ";
        $stm = "AND";
    }
    
    // Hide media items if not login.
    if(!$login) {
        $sql .= "$stm s.hide = 0 ";
    }

    switch ($title) 
    {
        case "latest"    : $sql .= "ORDER BY s.id DESC";
                           break;
        
        case "oldest"    : $sql .= "ORDER BY s.id";
                           break;
        
        case "name_asc"  : $sql .= "ORDER BY s.sorttitle";
                           break;
        
        case "name_desc" : $sql .= "ORDER BY s.sorttitle DESC";
                           break;
                        
        case "year_asc"  : $sql .= "ORDER BY mc.year, s.sorttitle";
                           break;
        
        case "year_desc" : $sql .= "ORDER BY mc.year DESC, s.sorttitle DESC";
                           break;                         
    }
    
    return $sql;
}

/*
 * Function:	CreateMoviesSetQuery
 *
 * Created on Nov 08, 2013
 * Updated on Nov 21, 2013
 *
 * Description: Create the sql query for the media movies set table. 
 *
 * In:  $title, $id, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateMoviesSetQuery($title, $id, $genre, $year, $sort, $login)
{   
    $sql = "SELECT m.id, m.xbmcid, m.hide, m.refresh, m.title ".
           "FROM sets s, movies m ".
           "WHERE s.setid = m.setid AND s.id = $id ";
    
    $sql .= CreateQuerySelection("m.", "AND ", $sort, $year, $genre, $login);
    $sql .= CreateQuerySortQrder("m.", $title);
    
    return $sql;
}

/*
 * Function:	CreateSeriesQuery
 *
 * Created on Apr 08, 2013
 * Updated on Dec 07, 2013
 *
 * Description: Create the sql query for the media TV shows table. 
 *
 * In:  $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateSeriesQuery($title, $genre, $year, $sort, $login)
{   
    $sql = "SELECT DISTINCT  CONCAT(t.id, '_', s.seasons) AS id, CONCAT(t.xbmcid, '_', s.season) AS xbmcid, t.hide, t.refresh, t.title ".
           "FROM (SELECT tvshowid, COUNT(season) AS seasons, season FROM seasons GROUP BY tvshowid) s ".
           "JOIN tvshows t ON s.tvshowid = t.xbmcid ";
    
    $sql .= CreateQuerySelection("t.", "WHERE ", $sort, $year, $genre, $login);
    $sql .= CreateQuerySortQrder("t.", $title);
        
    return $sql;
}

/*
 * Function:	CreateSeasonsQuery
 *
 * Created on Nov 12, 2013
 * Updated on Nov 25, 2013
 *
 * Description: Create the sql query for the media seasons table. 
 *
 * In:  $login
 * Out: $sql
 *
 */
function CreateSeasonsQuery($id, $login)
{
    $sql = "SELECT CONCAT(id, '_', season) AS id, CONCAT(tvshowid, '_', season) AS xbmcid, hide, refresh, title ".
           "FROM seasons WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id) ";
    
    // Hide media items if not login.
    if(!$login) {
        $sql .= "AND hide = 0 ";
    }
    
    $sql .= "ORDER BY season";
    
    return $sql;
}

/*
 * Function:	 CreateEpisodesQuery
 *
 * Created on Nov 12, 2013
 * Updated on Nov 25, 2013
 *
 * Description: Create the sql query for the media seasons table. 
 *
 * In:  $id, $login
 * Out: $sql
 *
 */
function CreateEpisodesQuery($id, $season, $login)
{   
    if ($season > -1) {
       $sql = "SELECT id, episodeid, hide, refresh, CONCAT(episode, '. ', title) ".
              "FROM episodes WHERE tvshowid = (SELECT tvshowid FROM seasons WHERE id = $id) ".
              "AND season = $season ";
    }
    else {
       $sql = "SELECT id, episodeid, hide, refresh, CONCAT(episode, '. ', title) ".
              "FROM episodes WHERE tvshowid = (SELECT xbmcid FROM tvshows WHERE id = $id) ";       
    }    
    
    // Hide media items if not login.
    if(!$login) {
        $sql .= "AND hide = 0 ";
    }
    
    $sql .= "ORDER BY episode";
    
    return $sql;
}

/*
 * Function:	CreateQuerySelection
 *
 * Created on Nov 08, 2013
 * Updated on Nov 21, 2013
 *
 * Description: Create the sql query selection for the media table. 
 *
 * In:  $a, $stm, $sort, $year, $genre, $login
 * Out: $sql
 *
 */
function CreateQuerySelection($a, $stm, $sort, $year, $genre, $login)
{
    $sql = "";
    
    if (strlen($sort) == 1) {
        $sql .= $stm . $a . "sorttitle LIKE '$sort%' ";
        $stm = "AND ";
    }
    
    if ($year) 
    {
        $sql .= $stm . $a . "year = $year ";
        $stm = "AND ";
    }
    
    if ($genre) 
    {
        $sql .= $stm . $a . "genre LIKE '%\"$genre\"%' ";
        $stm = "AND ";
    }
    
    // Hide media items if not login.
    if(!$login) {
        $sql .= $stm . $a . "hide = 0 ";
    }
    
    return $sql;
}        

/*
 * Function:	CreateQuerySortQorder
 *
 * Created on Nov 08, 2013
 * Updated on Nov 21, 2013
 *
 * Description: Create the sql query sort order for the media table. 
 *
 * In:  $a, $title
 * Out: $sql
 *
 */
function CreateQuerySortQrder($a, $title)
{
    $sql = "";
    
    switch ($title) 
    {
        case "latest"    : $sql .= "ORDER BY ".$a."id DESC";
                           break;
        
        case "oldest"    : $sql .= "ORDER BY ".$a."id";
                           break;
        
        case "name_asc"  : $sql .= "ORDER BY ".$a."sorttitle";
                            break;
        
        case "name_desc" : $sql .= "ORDER BY ".$a."sorttitle DESC";
                            break;
                        
        case "year_asc"  : $sql .= "ORDER BY ".$a."year, ".$a."sorttitle";
                            break;
        
        case "year_desc" : $sql .= "ORDER BY ".$a."year DESC, ".$a."sorttitle DESC";
                            break;                        
    }
    
    return $sql;
}

/*
 * Function:	QueryMedia
 *
 * Created on Apr 03, 2013
 * Updated on Dec 20, 2013
 *
 * Description: Get a page of media from Fargo and return it as Json data. 
 *
 * In:  $sql, $page, $end, $length
 * Out: $aJson
 *
 */
function QueryMedia($sql, $page, $end, $length)
{   
    $aMedia  = null; 
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
                
                $stmt->bind_result($id, $xbmcid, $hide, $refresh, $title);
                while($stmt->fetch())
                {                
                    
                    $aMedia[$i]['id']      = $id;                    
                    $aMedia[$i]['xbmcid']  = $xbmcid;  
                    $aMedia[$i]['hide']    = $hide;  
                    $aMedia[$i]['refresh'] = $refresh; 
                    //$aMedia[$i]['title']   = stripslashes(ShortenString($title, $length));
                    $aMedia[$i]['title']   = stripslashes($title);
                    
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

//////////////////////////////////////////    Misc Functions    ///////////////////////////////////////////

/*
 * Function:	GetSystemOptionProperties
 *
 * Created on May 20, 2013
 * Updated on Dec 11, 2013
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
                            $html = str_replace("[timeout]", GetSetting("Timeout"), $html);
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
 * Updated on Dec 12, 2013
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
           "LIMIT 0, 250";
        
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
 * Updated on Nov 21, 2013
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
 * Updated on Nov 21, 2013
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
        $sql = "SELECT m.year FROM $media m, genreto$md gtm, genres g ".
               "WHERE m.id = gtm.".$md."id AND gtm.genreid = g.id ".
               "$stm".
               "GROUP BY m.`year` ".
               "ORDER BY m.`year` DESC";          
    }
    
    $aJson = GetItemsFromDatabase($sql);
    
    return $aJson;
}