<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    jsonfargo.php
 *
 * Created on Apr 03, 2013
 * Updated on Jul 26, 2014
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
                     $aJson = GetMediaInfo($media, $id, $login);
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
                     $aJson = GetMedia($type, $page, $title, $level, $genre, $year, $sort, $login);                     
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
 * Updated on Jun 29, 2014
 *
 * Description: Get the media info from Fargo and return it as Json data. 
 *
 * In:  $media, $id, $login 
 * Out: $aJson
 *
 */
function GetMediaInfo($media, $id, $login)
{
    $aJson = null;
    
    switch($media)
    {
        case "movies"   : $aJson = GetMovieInfo($id, $login);
                          break;
                      
        case "titles"   : $aJson = GetPopupInfo("movies", "xbmc", $id, cMOVIESTHUMBS); // For Refresh and Delete Popups.
                          break;
                      
        case "sets"     : $aJson = GetPopupInfo("sets", "set", $id, cSETSTHUMBS); // For Refresh and Delete Popups.
                          break;   
                      
        case "movieset" : $aJson = GetPopupInfo("movies", "xbmc", $id, cMOVIESTHUMBS); // For Refresh and Delete Popups.
                          break;                      
        
        case "tvshows"  : $aJson = GetTVShowInfo($id, $login);
                          break;
                      
        case "tvtitles" : $aJson = GetPopupInfo("tvshows", "xbmc", $id, cTVSHOWSTHUMBS); // For Refresh and Delete Popups.
                          break;
                      
        case "series"   : $aItems = explode("_", $id);
                          $aJson = GetPopupInfo("tvshows", "xbmc", $aItems[0], cTVSHOWSTHUMBS); // For Refresh and Delete Popups.
                          break;                      
                     
        case "episodes" : $aJson = GetTVShowEpisodeInfo($id, $login);
                          break;                     
        
        case "albums"   : $aJson = GetAlbumInfo($id);
                          break;

        case "songs"    : $aJson = GetSongInfo($id, $login);
                          break;                      
    }
    
    return $aJson;
}

/*
 * Function:	GetMovieInfo
 *
 * Created on Jul 05, 2013
 * Updated on Jul 26, 2014
 *
 * Description: Get the movie info from Fargo and return it as Json data. 
 *
 * In:  $id, login 
 * Out: $aJson
 *
 */
function GetMovieInfo($id, $login)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;
    
    $sql = "SELECT xbmcid, refresh, title, director, writer, studio, genre, HEX(fanart), `year`, runtime, ROUND(rating,1), ". 
                  "FORMAT(votes,0) AS votes, tagline, plot, mpaa, country, trailer, audio, video, file, ".
                  "imdbnr, trailer ".
           "FROM movies ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($xbmcid, $refresh, $title, $director, $writer, $studio, $genre, $fanart, $year, $runtime, 
                               $rating, $votes, $tagline, $plot, $mpaa, $country, $trailer, $audio, $video, $file,
                               $imdbnr, $trailer);
            $stmt->fetch();
            
            $genre = str_replace('"', '', $genre);
            $votes = !empty($votes)?" ($votes votes)":"";
            
            $aMedia["xbmcid"]   = $xbmcid;
            $aMedia["refresh"]  = $refresh;
            $aMedia["title"]    = stripslashes($title);
            $aMedia["director"] = str_replace("|", " / ", $director);
            $aMedia["writer"]   = str_replace("|", " / ", $writer);
            $aMedia["studio"]   = $studio;
            $aMedia["genre"]    = str_replace("|", " / ", $genre);
            $aMedia["fanart"]   = !empty($fanart)?strtolower($fanart[0]."/".$fanart):0;
            $aMedia["year"]     = $year;
            $aMedia["runtime"]  = !empty($runtime)?round($runtime/60)." Minutes":0;
            $aMedia["rating"]   = strcmp($rating, "0.0")?$rating.$votes:0;          
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
            
            if ($login) {
                $aMedia["path"] = $file;
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
    
    // Fill parameters.
    $aParams['fanart'] = cMOVIESART;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;
    
    return $aJson;
}

/*
 * Function:	GetTVShowInfo
 *
 * Created on Jul 09, 2013
 * Updated on Jul 07, 2014
 *
 * Description: Get the TV show info from Fargo and return it as Json data. 
 *
 * In:  $id, login 
 * Out: $aJson
 *
 */
function GetTVShowInfo($id, $login)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;  
    
    $sql = "SELECT xbmcid, title, studio, genre, HEX(fanart), `year`, premiered, ROUND(rating,1), FORMAT(votes,0),".
           " plot, episode, watchedepisodes, episodeguide, imdbnr, file ".
           "FROM tvshows ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($xbmcid, $title, $studio, $genre, $fanart, $year, $premiered, $rating, $votes, 
                               $plot, $episode, $watchedepisodes, $episodeguide, $imdbnr, $file);
            $stmt->fetch();
            
            $genre = str_replace('"', '', $genre);
            $votes = !empty($votes)?" ($votes votes)":"";
            
            $aMedia["xbmcid"]          = $xbmcid;
            $aMedia["title"]           = stripslashes($title);
            $aMedia["studio"]          = $studio;
            $aMedia["genre"]           = str_replace("|", " / ", $genre);
            $aMedia["fanart"]          = !empty($fanart)?strtolower($fanart[0]."/".$fanart):0;      
            $aMedia["year"]            = $year;
            $aMedia["premiered"]       = date( 'd/m/Y', strtotime($premiered));
            $aMedia["rating"]          = strcmp($rating, "0.0")?$rating.$votes:0;            
            $aMedia["plot"]            = stripslashes($plot);
            $aMedia["episode"]         = $episode;
            $aMedia["watchedepisodes"] = $watchedepisodes;
            $aMedia["imdbnr"]          = ConverToMovieUrl($imdbnr, $episodeguide); 
            
            if ($login) {
                $aMedia["path"] = $file;
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
    
    // Fill parameters.
    $aParams['fanart'] = cTVSHOWSART;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;
    
    return $aJson;
}

/*
 * Function:	GetTVShowEpisodeInfo
 *
 * Created on Nov 17, 2013
 * Updated on Jul 07, 2014
 *
 * Description: Get the TV show episode info from Fargo and return it as Json data. 
 *
 * In:  $id, $login 
 * Out: $aJson
 *
 */
function GetTVShowEpisodeInfo($id, $login)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;  
    
    $sql = "SELECT episodeid, title, showtitle, season, episode, firstaired, director, writer, YEAR(firstaired) AS `year`, ".
                  "runtime, ROUND(rating,1), audio, video, `file`, HEX(poster) AS fanart, plot ".
           "FROM episodes ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($episodeid, $title, $showtitle, $season, $episode, $firstaired, $director, $writer, $year, 
                               $runtime, $rating, $audio, $video, $file, $fanart, $plot);
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
            $aMedia["rating"]     = strcmp($rating, "0.0")?$rating:0;
            $aMedia["audio"]      = ConvertToAudioFlag($audio);
            $aMedia["video"]      = ConvertToVideoFlag($video);
            $aMedia["aspect"]     = ConvertToAspectFlag($video, $file);  
            $aMedia["fanart"]     = !empty($fanart)?strtolower($fanart[0]."/".$fanart):0; 
            $aMedia["plot"]       = stripslashes($plot);
            
            if ($login) {
                $aMedia["path"] = $file;
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
    
    // Fill parameters.
    $aParams['thumbs'] = cTVSHOWSART;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;
    
    return $aJson;
}

/*
 * Function:	GetAlbumInfo
 *
 * Created on Jul 10, 2013
 * Updated on Jul 19, 2014
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
    
    $sql = "SELECT xbmcid, title, genre, theme, mood, style, HEX(poster) AS fanart, `year`, artist, displayartist,".
           " rating, description, albumlabel ".
           "FROM albums ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($xbmcid, $title, $genre, $theme, $mood, $style, $fanart, $year, $artist, $displayartist,
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
            $aMedia["fanart"]        = !empty($fanart)?strtolower($fanart[0]."/".$fanart):0; 
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
    $aParams['covers'] = cMUSICART;
    
    // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;    
    
    return $aJson;
}

/*
 * Function:	GetSongInfo
 *
 * Created on Jun 29, 2014
 * Updated on Jul 19, 2014
 *
 * Description: Get the song info from Fargo and return it as Json data. 
 *
 * In:  $id, $login
 * Out: $aJson
 *
 */
function GetSongInfo($id, $login)
{
    $aJson   = null;
    $aMedia  = null;
    $aParams = null;  
    
    $sql = "SELECT songid, title, artist, album, genre, HEX(poster) AS fanart, `year`, disc, track, ".
           " IF(duration > 59, TRIM(LEADING '00:' FROM SEC_TO_TIME(duration)), ".
            " SUBSTRING_INDEX(SEC_TO_TIME(duration),'00:',-2)) AS duration, rating, `comment`, `file` ".
           "FROM songs ".
           "WHERE id = $id";
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($xbmcid, $title, $artist, $album, $genre, $fanart, $year, $disc, $track, $duration, $rating, $comment, $file);
            $stmt->fetch();
            
            $genre = str_replace('"', '', $genre);
            
            if ($rating < 0) {
                $rating = 0;
            }
            
            if (!$comment) {
                $comment = "No comment for this song.";
            }
            
            $aMedia["xbmcid"]   = $xbmcid;
            $aMedia["title"]    = stripslashes($title);
            $aMedia["artist"]   = stripslashes($artist);
            $aMedia["album"]    = stripslashes($album);
            $aMedia["genre"]    = str_replace("|", " / ", $genre);   
            $aMedia["fanart"]   = !empty($fanart)?strtolower($fanart[0]."/".$fanart):0;             
            $aMedia["year"]     = $year;
            $aMedia["disc"]     = $disc;
            $aMedia["track"]    = $track;
            $aMedia["duration"] = $duration;
            $aMedia["rating"]   = $rating." (from 5 starts)";
            $aMedia["comment"]  = stripslashes($comment);
            
            if ($login) {
                $aMedia["path"] = $file;
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

    // Fill parameters.
    $aParams['covers'] = cMUSICART;
    
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
 * Function:	ConvertToListFlag
 *
 * Created on May 29, 2014
 * Updated on May 29, 2014
 *
 * Description: Convert to list flag (media flag icons). 
 *
 * In:  $video 
 * Out: $flag
 *
 */
function ConvertToListFlag($video)
{
    $flag = "&nbsp;";
    if ($video != -1)
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
        
        $flag = '<img src="images/flagging/lists/'.$res.'.png">';
  
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
 * Updated on Jul 14, 2014
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
        case "titles"   : $sql = "SELECT id, xbmcid, refresh, title, HEX(poster), NULL AS sub ".
                                 "FROM movies WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cMOVIESART);
                          break;
                      
        case "sets"     : $sql = "SELECT id, setid, refresh, title, HEX(poster), NULL AS sub ".
                                 "FROM sets WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cMOVIESART);
                          break;   
                      
        case "movieset" : $sql = "SELECT id, xbmcid, refresh, title, HEX(poster), NULL AS sub ".
                                 "FROM movies WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cMOVIESART);
                          break;                      
                      
        case "tvtitles" : $sql = "SELECT id, xbmcid, refresh, title, HEX(poster), NULL AS sub ".
                                 "FROM tvshows WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cTVSHOWSART);
                          break;
                      
        case "series"   : $sql = "SELECT s.id, seasonid, t.refresh, t.title, HEX(s.poster), s.title AS sub ".
                                 "FROM tvshows t, seasons s WHERE t.id = s.tvshowid AND t.id = $id ".
                                 "ORDER BY s.season ".
                                 "LIMIT 0, 1";
                          $aJson = GetPopupMediaInfo($sql, cTVSHOWSART);
                          break;
                      
        case "seasons"  : $sql = "SELECT id, seasonid, refresh, showtitle, HEX(poster), title AS sub ".
                                 "FROM seasons WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cTVSHOWSART);
                          break;
                      
        case "episodes" : $sql = "SELECT id, episodeid, refresh, showtitle AS title, HEX(poster),".
                                 " CONCAT(episode, '. ', title) AS sub ".                                 
                                 "FROM episodes WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cTVSHOWSART);
                          break;
                      
        case "albums"   : $sql = "SELECT id, xbmcid, refresh, title, HEX(poster), NULL AS sub ".
                                 "FROM albums WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cMUSICART);
                          break;    
                      
        case "songs"    : $sql = "SELECT s.id, s.songid, s.refresh, s.album, HEX(s.poster), CONCAT(s.track, '. ', s.title) AS sub ".
                                 "FROM albums a, songs s WHERE a.id = s.albumid AND a.id = $id ".
                                 "ORDER BY s.track, s.disc ".
                                 "LIMIT 0, 1";
                          $aJson = GetPopupMediaInfo($sql, cMUSICART);
                          break;
                      
        case "tracks"   : $sql = "SELECT id, songid, refresh, album AS title, HEX(poster), CONCAT(track, '. ', title) AS sub ".
                                 "FROM songs WHERE id = $id";
                          $aJson = GetPopupMediaInfo($sql, cMUSICART);
                          break;             
    }
    
    return $aJson;
}

/*
 * Function:	GetPopupMediaInfo
 *
 * Created on Nov 22, 2013
 * Updated on Jul 14, 2014
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
            $stmt->bind_result($id, $xbmcid, $refresh, $title, $poster, $sub);
            $stmt->fetch();
            
            $aMedia["fargoid"]  = $id;
            $aMedia["xbmcid"]   = $xbmcid;
            $aMedia["refresh"]  = $refresh;
            $aMedia["title"]    = stripslashes($title);
            $aMedia["poster"]   = !empty($poster)?strtolower($poster[0]."/".$poster):0;
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
 * Updated on Jul 13, 2014
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
    
    $db = OpenDatabase();

    $genre = mysqli_real_escape_string($db, $genre);
    
    switch ($type)
    {
        case "titles"  : $aParams['thumbs'] = cMOVIESART;
                         $aParams['column'] = cMediaColumn;
                         $header = "Movie Titles";
                         $sql    = CreateMoviesQuery($title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($db, $sql);
                         $max    = cMediaRow * cMediaColumn;                             
                         $aMedia = QueryMedia($db, $sql, $page, $max);
                         break;
                    
        case "sets"    : $aParams['thumbs'] = cMOVIESART;
                         $aParams['column'] = cMediaColumn;
                         $header = "Movie Sets";
                         $sql    = CreateSetsQuery($title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($db, $sql);
                         $max    = cMediaRow * cMediaColumn; 
                         $aMedia = QueryMedia($db, $sql, $page, $max);
                         break;
                     
        case "movieset": $aParams['thumbs'] = cMOVIESART; // The set movies.
                         $aParams['column'] = cMediaColumn;
                         $header = GetItemFromDatabase($db, "title", "SELECT title FROM `sets` WHERE id = $level");
                         $sql    = CreateMoviesSetQuery($title, $level, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($db, $sql);
                         $max    = cMediaRow * cMediaColumn; 
                         $aMedia = QueryMedia($db, $sql, $page, $max);
                         break;                     
                    
        case "tvtitles": $aParams['thumbs'] = cTVSHOWSART;
                         $aParams['column'] = cMediaColumn;
                         $header = "TV Show Titles";
                         $sql    = CreateTVShowsQuery($title, $genre, $year, $sort, $login);
                         $rows   = CountRowsWithQuery($db, $sql);
                         $max    = cMediaRow * cMediaColumn; 
                         $aMedia = QueryMedia($db, $sql, $page, $max);
                         break;

        case "series"   : $aParams['thumbs'] = cTVSHOWSART;
                          $aParams['column'] = cMediaColumn;
                          $header = "TV Show Series";
                          $sql    = CreateSeriesQuery($title, $genre, $year, $sort, $login);
                          $rows   = CountRowsWithQuery($db, $sql);
                          $max    = cMediaRow * cMediaColumn; 
                          $aMedia = QueryMedia($db, $sql, $page, $max);
                          break;
                      
        case "seasons"  : $aParams['thumbs'] = cTVSHOWSART;
                          $aParams['column'] = cMediaColumn;
                          $aItems = explode("_", $level);
                          $header = GetItemFromDatabase($db, "title", "SELECT title FROM tvshows WHERE id = $aItems[0]");
                          $sql    = CreateSeasonsQuery($aItems[0], $login);
                          $rows   = CountRowsWithQuery($db, $sql);
                          $max    = cMediaRow * cMediaColumn; 
                          $aMedia = QueryMedia($db, $sql, $page, $max);
                          break;
                      
        case "episodes" : $aParams['thumbs'] = cTVSHOWSART;
                          $aParams['column'] = cMediaEpisodeColumn;
                          $aItems = explode("_", $level);
                          if ($aItems[1] > -1) {
                            $header = GetItemFromDatabase($db, "showtitle", "SELECT CONCAT(showtitle, ' - ', title) AS title ".
                                                               "FROM seasons WHERE id = $aItems[0]");
                          }
                          else {
                            $header = GetItemFromDatabase($db, "showtitle", "SELECT showtitle FROM seasons WHERE tvshowid = $aItems[0]");  
                          }
                          $sql    = CreateEpisodesQuery($aItems[0], $aItems[1], $login);
                          $rows   = CountRowsWithQuery($db, $sql);
                          $max    = cMediaRow * cMediaEpisodeColumn; 
                          $aMedia = QueryMedia($db, $sql, $page, $max);
                          break;                      
                      
        case "albums"   : $aParams['thumbs'] = cMUSICART;
                          $aParams['column'] = cMediaColumn;
                          $header = "Music Albums";
                          $sql    = CreateAlbumsQuery($title, $genre, $year, $sort, $login);
                          $rows   = CountRowsWithQuery($db, $sql);
                          $max    = cMediaRow * cMediaColumn; 
                          $aMedia = QueryMedia($db, $sql, $page, $max);
                          break; 
                      
        case "songs"    : $aParams['thumbs'] = cMUSICART;
                          $aParams['column'] = cMediaColumn;
                          $header = "Music Songs";
                          $sql    = CreateSongsQuery($title, $genre, $year, $sort, $login);
                          $rows   = CountRowsWithQuery($db, $sql);
                          $max    = cMediaRow * cMediaColumn; 
                          $aMedia = QueryMedia($db, $sql, $page, $max);
                          break;
                      
        case "tracks"   : $aParams['thumbs'] = cMUSICART;
                          $aParams['column'] = cMediaColumn;
                          $header = GetItemFromDatabase($db, "album", "SELECT title FROM albums WHERE id = $level");
                          $sql    = CreateTracksQuery($level, $login);
                          $rows   = CountRowsWithQuery($db, $sql);
                          $max    = cMediaRow * cMediaColumn; 
                          $aMedia = QueryMedia($db, $sql, $page, $max);                          
                          break;
    }   
    
    CloseDatabase($db); 
       
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
 * Function:	CreateMoviesQuery
 *
 * Created on Apr 08, 2013
 * Updated on Jul 02, 2014
 *
 * Description: Create the sql query for the movie table. 
 *
 * In:  $table, $metaid, $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateMoviesQuery($title, $genre, $year, $sort, $login)
{   
    $switch = explode("_", $title);
    
    if (!$login)
    {
        $sql = "SELECT t.id, t.xbmcid, NULL, t.hide, t.refresh, t.title, HEX(t.poster), IF('$switch[0]' = 'year', ". 
               "       t.year, IF(t.rating = 0, 0, ROUND(t.rating,1))), IF (t.video = '', -1, t.video) ".
               "FROM movies t ";
        
        $sql .= CreateQuerySelection("t.", "WHERE ", $sort, $year, $genre, $login);
    }
    else
    {
        $sql = "SELECT t.id, t.xbmcid, IF (m.playcount IS NULL, -1, m.playcount), t.hide, t.refresh, t.title, ". 
               "       HEX(t.poster), IF('$switch[0]' = 'year', t.year, IF(t.rating = 0, 0, ROUND(t.rating,1))), ".
               "       IF (t.video = '', -1, t.video) ".
               "FROM movies t ".
               "LEFT JOIN moviesmeta m ON (t.xbmcid = m.movieid) ";
        
        $sql .= CreateQuerySelection("t.", "WHERE ", $sort, $year, $genre, $login);        
    }    
    
    $sql .= CreateQuerySortQrder("t.", $title);
       
    return $sql;
}

/*
 * Function:	CreateSetsQuery
 *
 * Created on Nov 08, 2013
 * Updated on Jul 02, 2014
 *
 * Description: Create the sql query for the media sets table. 
 *
 * In:  $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateSetsQuery($title, $genre, $year, $sort, $login)
{    
    if (!$login)
    {    
        $sql = "SELECT DISTINCT s.id AS id, s.setid, NULL, s.hide, s.refresh, s.title AS title, HEX(s.poster), ma.total, NULL ".
               "FROM (SELECT setid, MIN(`year`) AS minyear, COUNT(setid) AS total FROM movies GROUP BY setid) ma ".
               "JOIN sets s ON s.setid = ma.setid ".
               "INNER JOIN movies mb ON ma.setid = mb.setid ".
               "INNER JOIN movies mc ON ma.setid = mc.setid AND ma.minyear = mc.year ";
    }
    else
    {
        $sql = "SELECT DISTINCT s.id AS id, s.setid, IF (sm.playcount IS NULL, -1, sm.playcount), s.hide, s.refresh, ".
               "       s.title AS title, HEX(s.poster), ma.total, NULL ".
               "FROM (SELECT setid, MIN(`year`) AS minyear, COUNT(setid) AS total FROM movies GROUP BY setid) ma ".
               "JOIN sets s ON s.setid = ma.setid ".
               "INNER JOIN movies mb ON ma.setid = mb.setid ".
               "INNER JOIN movies mc ON ma.setid = mc.setid AND ma.minyear = mc.year ".
               "LEFT JOIN setsmeta sm ON s.setid = sm.setid ";
    }
    
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
 * Updated on Jul 02, 2014
 *
 * Description: Create the sql query for the media movies set table. 
 *
 * In:  $title, $id, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateMoviesSetQuery($title, $id, $genre, $year, $sort, $login)
{   
    $switch = explode("_", $title);
    
    if (!$login)
    {    
        $sql = "SELECT m.id, m.xbmcid, NULL, m.hide, m.refresh, m.title, HEX(m.poster), IF('$switch[0]' = 'year', m.year, ". 
               "       IF(m.rating = 0, 0, ROUND(m.rating,1))), IF (m.video = '', -1, m.video) ".
               "FROM sets s, movies m ".
               "WHERE s.setid = m.setid AND s.id = $id ";
    }
    else 
    {
        $sql = "SELECT m.id, m.xbmcid, IF (sm.playcount IS NULL, -1, sm.playcount), m.hide, m.refresh, m.title, HEX(m.poster), ".
               "       IF('$switch[0]' = 'year', m.year, IF( m.rating = 0, 0, ROUND(m.rating,1))), IF (m.video = '', -1, m.video) ". 
               "FROM sets s ".
               "LEFT JOIN setsmeta sm ON (s.setid = sm.setid) ".
               "INNER JOIN movies m ON (s.setid = m.setid) ". 
               "WHERE s.id = $id ";        
    }
    
    $sql .= CreateQuerySelection("m.", "AND ", $sort, $year, $genre, $login);
    $sql .= CreateQuerySortQrder("m.", $title);
    
    return $sql;
}

/*
 * Function:	CreateTVShowsQuery
 *
 * Created on May 29, 2014
 * Updated on Jul 02, 2014
 *
 * Description: Create the sql query for the media table. 
 *
 * In:  $table, $metaid, $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateTVShowsQuery($title, $genre, $year, $sort, $login)
{     
    if (!$login)
    {
        $sql = "SELECT t.id, t.xbmcid, NULL, t.hide, t.refresh, t.title, HEX(t.poster), t.episode, NULL ".
               "FROM tvshows t ";
        
        $sql .= CreateQuerySelection("t.", "WHERE ", $sort, $year, $genre, $login);
    }
    else
    {
        $sql = "SELECT t.id, t.xbmcid, IF (m.playcount IS NULL, -1, m.playcount), t.hide, t.refresh, t.title,".
               " HEX(t.poster), t.episode, NULL ".
               "FROM tvshows t ".
               "LEFT JOIN tvshowsmeta m ON (t.xbmcid = m.tvshowid) ";
        
        $sql .= CreateQuerySelection("t.", "WHERE ", $sort, $year, $genre, $login);        
    }    
    
    $sql .= CreateQuerySortQrder("t.", $title);
       
    return $sql;
}

/*
 * Function:	CreateSeriesQuery
 *
 * Created on Apr 08, 2013
 * Updated on Jul 13, 2014
 *
 * Description: Create the sql query for the media TV shows table. 
 *
 * In:  $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateSeriesQuery($title, $genre, $year, $sort, $login)
{   
    if (!$login)
    {
        $sql = "SELECT DISTINCT CONCAT(s.tvshowid, '_', s.seasons) AS id, s.seasonid AS xbmcid, NULL, t.hide, s.refresh,".
               " t.title, IF(s.poster IS NULL, HEX(t.poster), s.poster), s.total, NULL ".
               "FROM (SELECT a.id, a.seasonid, a.hide, a.refresh, HEX(a.poster) AS poster, a.tvshowid, b.seasons,".
                     " b.total, a.season ".
                     "FROM seasons a INNER JOIN ".
                     "(SELECT  tvshowid, MIN(season) AS min_val, COUNT(season) AS seasons, SUM(IF (season > 0,1,0)) AS total ".
                     "FROM seasons GROUP BY tvshowid) b ON a.tvshowid = b.tvshowid AND a.season = b.min_val) s ".
               "JOIN tvshows t ON s.tvshowid = t.id ";
    }
    else 
    {
        $sql = "SELECT DISTINCT CONCAT(s.tvshowid, '_', s.seasons) AS id, s.seasonid AS xbmcid,".
               " IF (tm.playcount IS NULL, -1, tm.playcount), t.hide, s.refresh, t.title, IF(s.poster IS NULL, HEX(t.poster), s.poster), s.total, NULL ".
               "FROM (SELECT a.id, a.seasonid, a.hide, a.refresh, HEX(a.poster) AS poster, a.tvshowid, b.seasons,".
                     " b.total, a.season ".
                     "FROM seasons a INNER JOIN ".
                     "(SELECT  tvshowid, MIN(season) AS min_val, COUNT(season) AS seasons, SUM(IF (season > 0,1,0)) AS total ".
                     "FROM seasons GROUP BY tvshowid) b ON a.tvshowid = b.tvshowid AND a.season = b.min_val) s ".
               "JOIN tvshows t ON s.tvshowid = t.id ".
               "LEFT JOIN tvshowsmeta tm ON t.xbmcid = tm.tvshowid ";        
    }
    
    $sql .= CreateQuerySelection("t.", "WHERE ", $sort, $year, $genre, $login);
    $sql .= CreateQuerySortQrder("t.", $title);
    
    return $sql;
}

/*
 * Function:	CreateSeasonsQuery
 *
 * Created on Nov 12, 2013
 * Updated on Jul 13, 2014
 *
 * Description: Create the sql query for the media seasons table. 
 *
 * In:  $login
 * Out: $sql
 *
 */
function CreateSeasonsQuery($id, $login)
{
    if (!$login)
    {    
        $sql = "SELECT CONCAT(id, '_', season) AS id, seasonid AS xbmcid, NULL, hide, refresh, title, HEX(poster), episode, NULL ".
               "FROM seasons WHERE tvshowid = $id ".
               "AND hide = 0 ".
               "ORDER BY season";    
    }
    else
    {
        $sql = "SELECT CONCAT(s.id, '_', s.season) AS id, s.seasonid AS xbmcid, IF (sm.playcount IS NULL, -1, sm.playcount),". 
               "       s.hide, s.refresh, s.title, HEX(s.poster), s.episode, NULL ".
               "FROM seasons s ".
               "LEFT JOIN seasonsmeta sm ON (s.seasonid = sm.seasonid) ".
               "WHERE tvshowid = $id ". 
               "ORDER BY s.season";
    }
    
    return $sql;
}

/*
 * Function:	 CreateEpisodesQuery
 *
 * Created on Nov 12, 2013
 * Updated on Jul 13, 2014
 *
 * Description: Create the sql query for the media seasons table. 
 *
 * In:  $id, $login
 * Out: $sql
 *
 */
function CreateEpisodesQuery($id, $season, $login)
{   
    if (!$login)
    {
        if ($season > -1) { // More the 1 season.
            $sql = "SELECT id, episodeid, NULL, hide, refresh, CONCAT(episode, '. ', title), HEX(poster),".
                   " IF(rating = 0, 0, ROUND(rating,1)), IF(video = '', -1, video) ".
                   "FROM episodes WHERE tvshowid = (SELECT tvshowid FROM seasons WHERE id = $id) ".
                   "AND season = $season AND hide = 0 ";
        }
        else { // One season.
            $sql = "SELECT id, episodeid, NULL, hide, refresh, CONCAT(episode, '. ', title), HEX(poster),".
                   " IF(rating = 0, 0, ROUND(rating,1)), IF(video = '', -1, video) ".
                   "FROM episodes WHERE tvshowid = $id ".
                   "AND hide = 0 ";       
        }
        
        $sql .= "ORDER BY episode";        
    }
    else
    {
        if ($season > -1) { // More the 1 season.
            $sql = "SELECT e.id, e.episodeid, IF (em.playcount IS NULL, -1, em.playcount), e.hide, e.refresh,".
                   "       CONCAT(e.episode, '. ', e.title), HEX(e.poster), IF(e.rating = 0, 0, ROUND(e.rating,1)),".
                         " IF(e.video = '', -1, e.video) ".
                   "FROM episodes e ".
                   "LEFT JOIN episodesmeta em ON (e.episodeid = em.episodeid) ".
                   "WHERE tvshowid = (SELECT tvshowid FROM seasons WHERE id = $id) AND e.season = $season ";        
        }
        else { // One season.
            $sql = "SELECT e.id, e.episodeid, IF (em.playcount IS NULL, -1, em.playcount), e.hide, e.refresh,". 
                   "       CONCAT(e.episode, '. ', e.title), HEX(e.poster), IF(e.rating = 0, 0, ROUND(e.rating,1)),".
                         " IF(e.video = '', -1, e.video) ".
                   "FROM episodes e ".
                   "LEFT JOIN episodesmeta em ON (e.episodeid = em.episodeid) ".
                   "WHERE e.tvshowid = $id "; 
        }
        
        $sql .= "ORDER BY e.episode";
    }        
    
    return $sql;
}

/*
 * Function:	CreateAlbumsQuery
 *
 * Created on May 29, 2014
 * Updated on Jul 03, 2014
 *
 * Description: Create the sql query for the albums table. 
 *
 * In:  $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateAlbumsQuery($title, $genre, $year, $sort, $login)
{     
    if (!$login)
    {
        $sql = "SELECT t.id, t.xbmcid, NULL, t.hide, t.refresh, t.title, HEX(t.poster), t.artist, NULL ".
               "FROM albums t ";
        
        $sql .= CreateQuerySelection("t.", "WHERE ", $sort, $year, $genre, $login);
    }
    else
    {
        $sql = "SELECT t.id, t.xbmcid, IF (m.playcount IS NULL, -1, m.playcount), t.hide, t.refresh, t.title,". 
               " HEX(t.poster), t.artist, NULL ".
               "FROM albums t ".
               "LEFT JOIN albumsmeta m ON (t.xbmcid = m.albumid) ";
        
        $sql .= CreateQuerySelection("t.", "WHERE ", $sort, $year, $genre, $login);        
    }    
    
    $sql .= CreateQuerySortQrder("t.", $title);
       
    return $sql;
}

/*
 * Function:	CreateSongsQuery
 *
 * Created on Jun 29, 2014
 * Updated on Jul 14, 2014
 *
 * Description: Create the sql query for the songs table. 
 *
 * In:  $title, $genre, $year, $sort, $login
 * Out: $sql
 *
 */
function CreateSongsQuery($title, $genre, $year, $sort, $login)
{     
    if (!$login)
    {
        $sql = "SELECT a.id, s.songid, NULL, a.hide, s.refresh, a.title, s.poster, s.tracks, NULL ".
               "FROM (SELECT id, songid, refresh, albumid, HEX(poster) AS poster, COUNT(track) AS tracks FROM songs ".
               "GROUP BY albumid) s ".
               "JOIN albums a ON s.albumid = a.id ";
        
        $sql .= CreateQuerySelection("a.", "WHERE ", $sort, $year, $genre, $login);
    }
    else
    {
        $sql = "SELECT a.id, s.songid, IF(am.playcount IS NULL, -1, am.playcount) AS playcount, a.hide, s.refresh,".
               " a.title, s.poster, s.tracks, NULL ".
               "FROM (SELECT id, songid, refresh, albumid, HEX(poster) AS poster, COUNT(track) AS tracks FROM songs ".
               "GROUP BY albumid) s ".
               "JOIN albums a ON s.albumid = a.id ".
               "LEFT JOIN albumsmeta am ON a.xbmcid = am.albumid ";
        
        $sql .= CreateQuerySelection("a.", "WHERE ", $sort, $year, $genre, $login);        
    }    
    
    $sql .= CreateQuerySortQrder("a.", $title);
       
    return $sql;
}

/*
 * Function:	CreateTracksQuery
 *
 * Created on Jun 29, 2014
 * Updated on Jul 19, 2014
 *
 * Description: Create the sql query for the album tracks table. 
 *
 * In:  $level, $login
 * Out: $sql
 *
 */
function CreateTracksQuery($level, $login)
{     
    if (!$login)
    {
        $sql = "SELECT id, songid, NULL, hide, refresh, CONCAT(track, '. ', title), HEX(poster),".
               " IF(duration > 59, TRIM(LEADING '00:' FROM SEC_TO_TIME(duration)),".
                " SUBSTRING_INDEX(SEC_TO_TIME(duration),'00:',-2)), NULL ".
               "FROM songs ".
               "WHERE albumid = $level AND hide = 0 ";
    }
    else
    {
        $sql = "SELECT s.id, s.songid, IF(sm.playcount IS NULL, -1, sm.playcount) AS playcount, s.hide, s.refresh,".
               " CONCAT(s.track, '. ', s.title), HEX(s.poster), IF(duration > 59, TRIM(LEADING '00:' FROM SEC_TO_TIME(duration)),".
                " SUBSTRING_INDEX(SEC_TO_TIME(duration),'00:',-2)), NULL ".
               "FROM songs s ".
               "LEFT JOIN songsmeta sm ON s.songid = sm.songid ".
               "WHERE s.albumid = $level ";    
    }    
    
    $sql .= "ORDER BY disc, track";
       
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
 * Updated on Jul 07, 2014
 *
 * Description: Get a page of media from Fargo and return it as Json data. 
 *
 * In:  $db, $sql, $page, $end
 * Out: $aJson
 *
 */
function QueryMedia($db, $sql, $page, $end)
{   
    $aMedia  = null; 
    $start = ($page - 1) * $end;
    
    // Add limit.
    $sql .=  " LIMIT $start , $end";

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
                
                $stmt->bind_result($id, $xbmcid, $playcount, $hide, $refresh, $title, $poster, $sub, $aux);
                while($stmt->fetch())
                {                
                    
                    $aMedia[$i]['id']        = $id;                    
                    $aMedia[$i]['xbmcid']    = $xbmcid;
                    $aMedia[$i]['playcount'] = $playcount;
                    $aMedia[$i]['hide']      = $hide;  
                    $aMedia[$i]['refresh']   = $refresh; 
                    $aMedia[$i]['title']     = stripslashes($title);
                    $aMedia[$i]['poster']    = !empty($poster)?strtolower($poster[0]."/".$poster):0;
                    $aMedia[$i]['sub']       = !empty($sub)?stripslashes($sub):"&nbsp;"; 
                    $aMedia[$i]['aux']       = !empty($aux)?ConvertToListFlag($aux):0;
                    $i++;
                }                  
            }
            else
            {
                    $aMedia[0]['id']        = 0;
                    $aMedia[0]['xbmcid']    = 0;
                    $aMedia[0]['playcount'] = -1;                      
                    $aMedia[0]['hide']      = -1;  
                    $aMedia[0]['refresh']   = -1;    
                    $aMedia[0]['title']     = 'empty';
                    $poster[0]['poster']    = 0;
                    $aMedia[0]['sub']       = -1;
                    $aMedia[0]['aux']       = -1;                                      
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
    
    return $aMedia;
}

//////////////////////////////////////////    Misc Functions    ///////////////////////////////////////////

/*
 * Function:	GetSystemOptionProperties
 *
 * Created on May 20, 2013
 * Updated on Jun 28, 2014
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
    $db = OpenDatabase();
    
    switch(strtolower($name))
    {
        case "statistics" : $html = GetSetting($db, $name);                             
                            $html = str_replace("[movies]", CountMedia($db, "movies", $login), $html);
                            $html = str_replace("[sets]", CountMedia($db, "sets", $login), $html);
                            $html = str_replace("[tvshows]", CountMedia($db, "tvshows", $login), $html);
                            $html = str_replace("[seasons]", CountMedia($db, "seasons", $login), $html);
                            $html = str_replace("[episodes]", CountMedia($db, "episodes", $login), $html);
                            $html = str_replace("[albums]", CountMedia($db, "albums", $login), $html);
                            $html = str_replace("[songs]", CountMedia($db, "songs", $login), $html);
                            break;
                                       
        case "settings"   : $html = GetSetting($db, $name);
                            $html = str_replace("[connection]", GetSetting($db, "XBMCconnection"), $html);
                            $html = str_replace("[port]", GetSetting($db, "XBMCport"), $html);
                            $html = str_replace("[xbmcuser]", GetSetting($db, "XBMCusername"), $html);
                            $html = str_replace("[fargouser]", GetUser($db, 1), $html);
                            $html = str_replace("[password]", "******", $html);
                            $html = str_replace("[timeout]", GetSetting($db, "Timeout"), $html);
                            break;
                        
        case "library"    : $html = GetSetting($db, $name);
                            break;
                        
        case "event log"  : $html  = "<div class=\"system_scroll\">";
                            $html .= "<table>";
                            $html .= GetSetting($db, $name);
                            $html .= GenerateEventRows($db);
                            $html .= "</table>";
                            $html .= "</div>";    
                            break;
                        
        case "credits"    : $html  = "<div class=\"system_scroll text\">";
                            $html .= GetSetting($db, $name);
                            $html .= "</div>";
                            break;                        
                        
        case "about"      : $html  = "<div class=\"system_scroll text\">";
                            $html .= GetSetting($db, $name);
                            $html .= "</div>";
                            
                            $html = str_replace("[version]", GetSetting($db, "Version"), $html);
                            break;                        
    }
    
    CloseDatabase($db);
    $aJson['html'] = $html;
    return $aJson;
}

/*
 * Function:	GenerateEventRows
 *
 * Created on Jun 10, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Generate event log table rows.
 *
 * In:  $db
 * Out: $events
 *
 */
function GenerateEventRows($db)
{
    $events = null;
    
    //$db = OpenDatabase();

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

    //CloseDatabase($db);  
    
    return $events;
}

/*
 * Function:	ProcessSetting
 *
 * Created on Jun 09, 2013
 * Updated on Jan 03, 2014
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
    $db = OpenDatabase();
    
    $value = GetSetting($db, $name);
    
    if ($name == "Hash") {
        $value = hash("sha512", $value);
    }
    
    $aJson["value"] = $value;
    
    CloseDatabase($db);
    return $aJson;
}

/*
 * Function:	GetSortList
 *
 * Created on Jun 27, 2013
 * Updated on Jan 03, 2014
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
    $db = OpenDatabase();    
    
    switch(strtolower($type))
    {
        case "genres" : $aJson["list"] = GetGenres($db, $filter, $media, $login);
                        break;
    
        case "years"  : $aJson["list"] = GetYears($db, $filter, $media, $login);
                        break;
    }
    
    CloseDatabase($db);    
    return $aJson;
}

/*
 * Function:	GetGenres
 *
 * Created on Jun 27, 2013
 * Updated on Feb 23, 2014
 *
 * Description: Get genres from database table genres. 
 *
 * In:  $db, $filter, $media, $login
 * Out: $aJson
 *
 */
function GetGenres($db, $filter, $media, $login)
{
    $stm = "";
    if (!$login) {
        $stm = " AND hide = 0 ";
    }   
    
    $md = "music";
    if ($media != "music") {
        $md = rtrim($media, "s");
    }
    else {
        $media = "albums";
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
    
    $aJson = GetItemsFromDatabase($db, $sql);
    
    return $aJson;
}

/*
 * Function:	GetYears
 *
 * Created on Jun 30, 2013
 * Updated on Feb 23, 2014
 *
 * Description: Get years from database media table. 
 *
 * In:  $db, $filter, $media, $login
 * Out: $aJson
 *
 */
function GetYears($db, $filter, $media, $login)
{
    $stm = "";
    if (!$login) {
        $stm = " AND hide = 0 ";
    }   
    
    $md = "music";
    if ($media != "music") {
        $md = rtrim($media, "s");
    }
    else {
        $media = "albums";
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
    
    $aJson = GetItemsFromDatabase($db, $sql);
    
    return $aJson;
}