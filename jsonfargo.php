<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    jsonfargo.php
 *
 * Created on Apr 03, 2013
 * Updated on Aug 17, 2013
 *
 * Description: The main Json Fargo page.
 * 
 * Note: This page contains functions that returns Json data for Jquery code.
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

$aJson = null;
$action = GetPageValue('action');

switch ($action) 
{                
    case "info"    : $media = GetPageValue('media');
                     $id    = GetPageValue('id');
                     $aJson = GetMediaInfo($media, $id);
                     break;
                 
    case "reset"   : $media = GetPageValue('media');  
                     $aJson = ResetStatus($media);
                     break;
    
    case "counter" : $media = GetPageValue('media');
                     $aJson['counter'] = CountRows($media);
                     $aJson['xbmc']['counter'] = GetStatus("Xbmc".$media."Counter");
                     break;
                 
    case "status"  : $media = GetPageValue('media');
                     $id    = GetPageValue('id');
                     $aJson = GetMediaStatus($media, $id);
                     break;                 
        
    case "movies"  : $page  = GetPageValue('page');
                     $title = GetPageValue('title');
                     $genre = GetPageValue('genre');
                     $year  = GetPageValue('year');
                     $sort  = GetPageValue('sort');
                     $sql   = CreateQuery($action, $title, unescape($genre), $year, $sort);
                     $aJson = GetMedia($action, $page, $sql);
                     break;
                
    case "tvshows" : $page  = GetPageValue('page');
                     $title = GetPageValue('title');
                     $genre = GetPageValue('genre');
                     $year  = GetPageValue('year');
                     $sort  = GetPageValue('sort');
                     $sql   = CreateQuery($action, $title, unescape($genre), $year, $sort);
                     $aJson = GetMedia($action, $page, $sql);
                     break;    
                 
    case "music"  : $page   = GetPageValue('page');
                    $title = GetPageValue('title');
                    $genre = GetPageValue('genre');
                    $year  = GetPageValue('year');
                    $sort  = GetPageValue('sort');
                    $sql   = CreateQuery($action, $title, unescape($genre), $year, $sort);
                    $aJson = GetMedia($action, $page, $sql);
                    break;   
    
    case "option" : $name  = GetPageValue('name');
                    $aJson = GetSystemOptionProperties($name); 
                    break;
                
    case "property":$option = GetPageValue('option');
                    $number = GetPageValue('number');
                    $value  = GetPageValue('value');
                    $aJson  = SetSystemProperty($option, $number, $value);
                    break;                
                
    case "setting": $name  = GetPageValue('name');
                    $aJson = ProcessSetting($name);
                    break;
                
    case "list"   : $type   = GetPageValue('type');
                    $filter = GetPageValue('filter');
                    $media  = GetPageValue('media');
                    $aJson  = GetSortList($type, $filter, $media);
                    break;            
                
    case "log"    : $type  = GetPageValue('type');
                    $event = GetPageValue('event');
                    $aJson = LogEvent($type, $event);
                    break;
    
    case "test"   : break;                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    //echo json_encode($aJson, JSON_UNESCAPED_SLASHES);
    echo json_encode($aJson);
}

//////////////////////////////////////////    Misc Functions    ///////////////////////////////////////////

/*
 * Function:	ResetStatus
 *
 * Created on Jul 22, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Reset the status. 
 *
 * In:  $media
 * Out: $aJson
 *
 */
function ResetStatus($media)
{
    UpdateStatus("Xbmc".$media."Counter", -1);
    
    $aJson["status"] = "reset";
    
    return $aJson;
}


/*
 * Function:	GetMediaStatus
 *
 * Created on May 18, 2013
 * Updated on Jul 22, 2013
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
        case "movies"   : $aJson = GetImportStatus($media, $id, cMOVIESPOSTERS);
                          break;
        
        case "music"    : $aJson = GetImportStatus($media, $id, cALBUMSCOVERS);
                          break;
    
        case "tvshows"  : $aJson = GetImportStatus($media, $id, cTVSHOWSPOSTERS);
                          break;
    }    
    return $aJson;
}

/*
 * Function:	GetSortList
 *
 * Created on Jun 27, 2013
 * Updated on Jun 30, 2013
 *
 * Description: Get list of items for sorting purposes. 
 *
 * In:  $type, $filter, $media
 * Out: $aJson
 *
 */
function GetSortList($type, $filter, $media)
{
    $aJson = null;
    
    switch(strtolower($type))
    {
        case "genres" : $aJson["list"] = GetGenres($filter, $media);
                        break;
    
        case "years"  : $aJson["list"] = GetYears($filter, $media);
                        break;
    }
    
    return $aJson;
}

/*
 * Function:	GetGenres
 *
 * Created on Jun 27, 2013
 * Updated on Jun 30, 2013
 *
 * Description: Get genres from database table genres. 
 *
 * In:  $media
 * Out: $aJson
 *
 */
function GetGenres($filter, $media)
{
    if ($filter)
    {
        $md = "music";
        if ($media != "music") {
            $md = rtrim($media, "s");
        }
        
        $sql = "SELECT g.genre FROM genres g, genreto$md gtm, $media m ".
               "WHERE g.id = gtm.genreid AND m.id = gtm.".$md."id ".
               "AND m.`year` = $filter ".
               "GROUP BY g.genre ".
               "ORDER BY g.genre"; 
    }    
    else
    {    
        $sql = "SELECT genre FROM genres ".
               "WHERE media = '$media' ".
               "ORDER BY genre";
    }
    
    $aJson = GetItemsFromDatabase($sql);
    
    return $aJson;
}

/*
 * Function:	GetYears
 *
 * Created on Jun 30, 2013
 * Updated on Jul 04, 2013
 *
 * Description: Get years from database media table. 
 *
 * In:  $filter, $media
 * Out: $aJson
 *
 */
function GetYears($filter, $media)
{
    if ($filter)
    {   
        $md = "music";
        if ($media != "music") {
            $md = rtrim($media, "s");
        }        
        
        $sql = "SELECT m.year FROM $media m, genreto$md gtm, genres g ".
               "WHERE m.id = gtm.".$md."id AND gtm.genreid = g.id ".
               "AND g.genre = '$filter' ".
               "GROUP BY m.`year` ".
               "ORDER BY m.`year` DESC"; 
    }
    else 
    {    
        $sql = "SELECT YEAR FROM $media ".
               "GROUP BY `year` ".
               "ORDER BY `year` DESC";
    }
    
    $aJson = GetItemsFromDatabase($sql);
    
    return $aJson;
}

/*
 * Function:	LogEvent
 *
 * Created on May 10, 2013
 * Updated on Apr 10, 2013
 *
 * Description: Log event in the database log table. 
 *
 * In:  $type, $event
 * Out: $aItems
 *
 */
function LogEvent($type, $event)
{
    $aItems = null;
    
    if ($type != 'Error' && $type != 'Warning' && $type != 'Information') {
        $type = 'Unknown';
    }
    
    $aItems[0] = date("Y-m-d H:i:s");
    $aItems[1] = $type; 
    $aItems[2] = $event;
    
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aItems);
    
    $sql = "INSERT INTO log (date, type, event) ".
           "VALUES ('$aItems[0]', '$aItems[1]', '$aItems[2]')";
    
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);
    
    return $aItems;
}

////////////////////////////////////////    Database Functions    /////////////////////////////////////////

/*
 * Function:	GetImportStatus
 *
 * Created on May 18, 2013
 * Updated on May 19, 2013
 *
 * Description: Reports the status of the import process.
 *
 * In:  $media, $thumbs
 * Out: $aJson
 *
 */
function GetImportStatus($media, $id, $thumbs)
{
    $aJson['xbmcid'] = 0; 
    $aJson['title']  = "empty";
    $aJson['thumbs'] = $thumbs;
  
    $db = OpenDatabase();

    $sql = "SELECT xbmcid, title ".
           "FROM $media ".
           "WHERE id = $id";
        
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
                $stmt->bind_result($xbmcid, $title);
                while($stmt->fetch())
                {                
                    $aJson['xbmcid'] = $xbmcid;  
                    $aJson['title']  = ShortenString($title, 50);
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
 * Function:	CreateQuery
 *
 * Created on Apr 08, 2013
 * Updated on Jul 04, 2013
 *
 * Description: Create the sql query for the media table. 
 *
 * In:  $media, $title, $genre, $year, $sort
 * Out: $sql
 *
 */
function CreateQuery($media, $title, $genre, $year, $sort)
{
    $sql = "SELECT id, xbmcid, title ".
           "FROM $media ";
    
    $stm = "WHERE";
    
    if (strlen($sort) == 1) {
        $sql .= "$stm sorttitle LIKE '$sort%' ";
        $stm = "AND";
    }
    
    if ($year) 
    {
        $sql .= "$stm  `year` = $year ";
        $stm = "AND";
    }
    
    if ($genre) 
    {
        $sql .= "$stm genre LIKE '%\"$genre\"%' ";
        //$stm = "AND";
    }
    
    // Add sort order.
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
 * Updated on Jul 09, 2013
 *
 * Description: Get the movie info from Fargo and return it as Json data. 
 *
 * In:  $id 
 * Out: $aJson
 *
 */
function GetMovieInfo($id)
{
    $aJson = null;
    
    $sql = "SELECT xbmcid, title, director, writer, studio, genre, `year`, runtime, rating,". 
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
            $stmt->bind_result($xbmcid, $title, $director, $writer, $studio, $genre, $year, $runtime, $rating,
                               $votes, $tagline, $plot, $mpaa, $country, $trailer, $audio, $video, $file,
                               $imdbnr, $trailer);
            $stmt->fetch();
            
            $genre = str_replace('"', '', $genre);
            
            $aJson["xbmcid"]   = $xbmcid;
            $aJson["title"]    = $title;
            $aJson["director"] = str_replace("|", " / ", $director);
            $aJson["writer"]   = str_replace("|", " / ", $writer);
            $aJson["studio"]   = $studio;
            $aJson["genre"]    = str_replace("|", " / ", $genre);
            $aJson["year"]     = $year;
            $aJson["runtime"]  = round($runtime/60)." Minutes";
            //$aJson["votes"]    = $votes;
            $aJson["rating"]   = $rating." ($votes votes)";
            $aJson["tagline"]  = $tagline;
            $aJson["plot"]     = $plot;
            $aJson["mpaa"]     = ConvertToRatingsFlag($mpaa);
            $aJson["country"]  = $country;
            $aJson["trailer"]  = $trailer;
            $aJson["audio"]    = ConvertToAudioFlag($audio);
            $aJson["video"]    = ConvertToVideoFlag($video);
            $aJson["aspect"]   = ConvertToAspectFlag($video, $file);
            $aJson["imdbnr"]   = ConverToMovieUrl($imdbnr);
            $aJson["trailer"]  = ConverToMovieUrl($trailer);   
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
 * Function:	GetTVShowInfo
 *
 * Created on Jul 09, 2013
 * Updated on Jul 10, 2013
 *
 * Description: Get the TV show info from Fargo and return it as Json data. 
 *
 * In:  $id 
 * Out: $aJson
 *
 */
function GetTVShowInfo($id)
{
    $aJson = null;
    
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
            
            $aJson["xbmcid"]          = $xbmcid;
            $aJson["title"]           = $title;
            $aJson["studio"]          = $studio;
            $aJson["genre"]           = str_replace("|", " / ", $genre);
            $aJson["year"]            = $year;
            $aJson["premiered"]       = date( 'd/m/Y', strtotime($premiered));
            //$aJson["votes"]           = $votes;
            $aJson["rating"]          = $rating." ($votes votes)";
            $aJson["plot"]            = $plot;
            $aJson["episode"]         = $episode;
            $aJson["watchedepisodes"] = $watchedepisodes;
            //$aJson["episodeguide"]    = $episodeguide;
            $aJson["imdbnr"]          = ConverToMovieUrl($imdbnr, $episodeguide); 
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
 * Function:	GetAlbumInfo
 *
 * Created on Jul 10, 2013
 * Updated on Jul 10, 2013
 *
 * Description: Get the album info from Fargo and return it as Json data. 
 *
 * In:  $id 
 * Out: $aJson
 *
 */
function GetAlbumInfo($id)
{
    $aJson = null;
    
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
            
            $aJson["xbmcid"]        = $xbmcid;
            $aJson["title"]         = $title;
            $aJson["genre"]         = str_replace("|", " / ", $genre);
            $aJson["theme"]         = str_replace("|", " / ", $theme);
            $aJson["mood"]          = str_replace("|", " / ", $mood);
            $aJson["style"]         = str_replace("|", " / ", $style);            
            $aJson["year"]          = $year;
            $aJson["artist"]        = $artist;
            $aJson["displayartist"] = $displayartist;
            $aJson["rating"]        = $rating." (from 5 starts)";
            $aJson["description"]   = $description;
            $aJson["albumlabel"]    = $albumlabel;
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
 * Created on Apr 03, 2013
 * Updated on Aug 17, 2013
 *
 * Description: Get a page of media from Fargo and return it as Json data. 
 *
 * In:  $media, $sql
 * Out: $aJson
 *
 */
function GetMedia($media, $page, $sql)
{
    $aJson   = null;
    $aParams = null;
    $aMedia  = null;
    
    // Get total number of media items.
    $total = CountRowsWithQuery($sql);    

    // Number of movies for 1 page
    $end   = cMediaRow * cMediaColumn;
    $start = ($page - 1) * $end;
    
    // Add limit.
    $sql .=  " LIMIT $start , $end";
    
    // debug
    //echo $sql;
    
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
                
                $stmt->bind_result($id, $xbmcid, $title);
                while($stmt->fetch())
                {                
                    
                    $aMedia[$i]['id']     = $id;
                    $aMedia[$i]['xbmcid'] = $xbmcid;  
                    $aMedia[$i]['title']  = ShortenString($title, 22);
                    
                    $i++;
                }                  
            }
            else
            {
                    $aMedia[0]['id']     = 0;
                    $aMedia[0]['xbmcid'] = 0;  
                    $aMedia[0]['title']  = 'empty';
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
    
    // Get Json parameters.
    switch($media)
    {
        case "movies"   : $aParams['thumbs'] = cMOVIESTHUMBS;
                          break;
                      
        case "tvshows"  : $aParams['thumbs'] = cTVSHOWSPOSTERS;
                          break; 
                      
        case "music"    : $aParams['thumbs'] = cALBUMSCOVERS;
                          break;
    }    
    $aParams['lastpage'] = ceil($total / (cMediaRow * cMediaColumn));
    $aParams['row']      = cMediaRow;
    $aParams['column']   = cMediaColumn;
    
     // Fill Json.
    $aJson['params']   = $aParams;
    $aJson['media']    = $aMedia;
    
    return $aJson;
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
 * Function:	GetSystemOptionProperties
 *
 * Created on May 20, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Get the system option properties page from the database table settings. 
 *
 * In:  $name
 * Out: $aJson
 *
 */
function GetSystemOptionProperties($name)
{
    $html = null;
    switch(strtolower($name))
    {
        case "statistics" : $html = GetSetting($name);                                
                            $html = str_replace("[movies]", CountRows("movies"), $html);
                            $html = str_replace("[tvshows]", CountRows("tvshows"), $html);
                            $html = str_replace("[music]", CountRows("music"), $html);
                            break;
                                       
        case "settings"   : $html = GetSetting($name);
                            $html = str_replace("[connection]", GetSetting("XBMCconnection"), $html);
                            $html = str_replace("[port]", GetSetting("XBMCport"), $html);
                            $html = str_replace("[xbmcuser]", GetSetting("XBMCusername"), $html);
                            $html = str_replace("[fargouser]", GetUser(1), $html);
                            $html = str_replace("[password]", "******", $html);
                            $html = str_replace("[timer]", GetSetting("Timer")/1000, $html);
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
 * Updated on Jun 22, 2013
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
                 UpdateSetting("Timer", $value * 1000);
                 break;              
    }
    
    return $aJson;
}

/*
 * Function:	CleanLibrary
 *
 * Created on Jun 10, 2013
 * Updated on Jul 04, 2013
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
        case 1 : $aJson['name']   = "movies";
                 $aJson['counter'] = CountRows("movies");
                 EmptyTable("movies");
                 EmptyTable("genretomovie");
                 DeleteGenres("movies");
                 DeleteFile(cMOVIESPOSTERS."/*.jpg");
                 DeleteFile(cMOVIESFANART."/*.jpg");
                 break;
        
        case 4 : $aJson['name']   = "tvshows";
                 $aJson['counter'] = CountRows("tvshows");
                 EmptyTable("tvshows");
                 EmptyTable("genretotvshow");
                 DeleteGenres("tvshows");
                 DeleteFile(cTVSHOWSPOSTERS."/*.jpg");
                 DeleteFile(cTVSHOWSFANART."/*.jpg");
                 break;
        
        case 7 : $aJson['name']   = "music";
                 $aJson['counter'] = CountRows("music");
                 EmptyTable("music");
                 EmptyTable("genretomusic");
                 DeleteGenres("music");
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
?>
