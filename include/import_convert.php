<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.5
 *
 * File:    import_convert.php
 *
 * Created on Jul 15, 2013
 * Updated on Jun 20, 2014
 *
 * Description: This page contains functions for converting media from XBMC (used by import.php).
 *
 */

/////////////////////////////////////////    Convert Functions    /////////////////////////////////////////

/*
 * Function:	ConvertMovie
 *
 * Created on Mar 11, 2013
 * Updated on Jun 20, 2014
 *
 * Description: Convert xbmc movie items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aMovie
 *
 */
function ConvertMovie($aXbmc)
{  
    $aMovie[0]  = $aXbmc["movieid"];
    $aMovie[1]  = $aXbmc["label"]; // title
    $aMovie[2]  = !empty($aXbmc["genre"])?ConvertGenre($aXbmc["genre"]):null;
    $aMovie[3]  = !empty($aXbmc["year"])?$aXbmc["year"]:0;
    
    $aMovie[4]  = !empty($aXbmc["rating"])?round($aXbmc["rating"], 2):0;
    $aMovie[5]  = !empty($aXbmc["director"])?implode("|", $aXbmc["director"]):null;  
    $aMovie[6]  = !empty($aXbmc["trailer"])?$aXbmc["trailer"]:null;
    $aMovie[7]  = !empty($aXbmc["tagline"])?$aXbmc["tagline"]:null; 
    
    $aMovie[8]  = !empty($aXbmc["plot"])?$aXbmc["plot"]:null;
    $aMovie[9]  = !empty($aXbmc["plotoutline"])?$aXbmc["plotoutline"]:null;    
    $aMovie[10] = !empty($aXbmc["originaltitle"])?$aXbmc["originaltitle"]:null;
    $aMovie[11] = !empty($aXbmc["lastplayed"])?$aXbmc["lastplayed"]:"0000-00-00 00:00:00";
    
    $aMovie[12] = !empty($aXbmc["playcount"])?$aXbmc["playcount"]:0;
    $aMovie[13] = !empty($aXbmc["writer"])?ConvertWriter($aXbmc["writer"]):null;
    $aMovie[14] = !empty($aXbmc["studio"])?implode("|", $aXbmc["studio"]):null;
    $aMovie[15] = !empty($aXbmc["mpaa"])?$aXbmc["mpaa"]:null;
    
    $aMovie[16] = !empty($aXbmc["cast"])?ConvertCast($aXbmc["cast"]):null;
    $aMovie[17] = !empty($aXbmc["country"])?implode("|", $aXbmc["country"]):null;
    $aMovie[18] = !empty($aXbmc["imdbnumber"])?$aXbmc["imdbnumber"]:null;
    $aMovie[19] = !empty($aXbmc["runtime"])?$aXbmc["runtime"]:0;   
    
    $aMovie[20] = !empty($aXbmc["set"])?$aXbmc["set"]:null;
    $aMovie[21] = !empty($aXbmc["streamdetails"]["audio"])?ConvertAudio($aXbmc["streamdetails"]["audio"]):null; 
    $aMovie[22] = !empty($aXbmc["streamdetails"]["video"])?ConvertVideo($aXbmc["streamdetails"]["video"]):null;
    $aMovie[23] = !empty($aXbmc["votes"])?(int)str_replace(',', '', $aXbmc["votes"]):0;
    
    $aMovie[24] = !empty($aXbmc["file"])?$aXbmc["file"]:null;      
    $aMovie[25] = !empty($aXbmc["label"])?CreateSortTitle($aXbmc["label"]):null;
    $aMovie[26] = !empty($aXbmc["setid"])?$aXbmc["setid"]:0;    
    $aMovie[27] = !empty($aXbmc["dateadded"])?$aXbmc["dateadded"]:"0000-00-00 00:00:00";
    
    // Hash title and file as unique db entry to prevent dublicates.
    $aMovie[28] = hash("sha256", $aXbmc["label"].$aMovie[24]);
    
    //$aMovie[23] = !empty($aXbmc["top250"])?$aXbmc["top250"]:null;  
    //$aMovie["showlink"] = $aXbmc["showlink"];
    //$aMovie["resume"] = $aXbmc["resume"];  
    //$aMovie["tag"]    = $aXbmc["tag"];
    //$aMovie["fanart"] = EncodeLink($aXbmc["art"], "fanart");
    //$aMovie["poster"] = EncodeLink($aXbmc["art"], "poster");
    //$aMovie["thumb"]  = EncodeLink($aXbmc, "thumbnail");   
    
    return $aMovie;
}

/*
 * Function:	ConvertMovieSet
 *
 * Created on Oct 13, 2013
 * Updated on Jun 20, 2014
 *
 * Description: Convert xbmc movie set items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aMovie
 *
 */
function ConvertMovieSet($aXbmc)
{  
    $aMovie[0] = $aXbmc["setid"];
    $aMovie[1] = $aXbmc["label"]; // title
    $aMovie[2] = !empty($aXbmc["label"])?CreateSortTitle($aXbmc["label"]):null;    
    $aMovie[3] = !empty($aXbmc["playcount"])?$aXbmc["playcount"]:0;
    
    // Hash title as unique db entry to prevent dublicates.
    $aMovie[4] = hash("sha256", $aXbmc["label"]); 
    
    /*$aMovie[3] = EncodeLink($aXbmc["art"], "fanart");
    $aMovie[4] = EncodeLink($aXbmc["art"], "poster");
    $aMovie[5] = EncodeLink($aXbmc, "thumbnail");*/   
    
    return $aMovie;
}

/*
 * Function:	ConvertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Jun 20, 2014
 *
 * Description: Convert xbmc TV Show items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aTVShow
 *
 */
function ConvertTVShow($aXbmc)
{     
    if (!empty($aXbmc["premiered"])) {
        $aYear = explode("-", $aXbmc["premiered"]);
    }
    else {
        $aYear[0] = 0;
    }
    
    $aTVShow[0]  = $aXbmc["tvshowid"];
    $aTVShow[1]  = $aXbmc["label"]; // title
    $aTVShow[2]  = !empty($aXbmc["genre"])?ConvertGenre($aXbmc["genre"], "tvshows"):null;
    $aTVShow[3]  = $aYear[0];
    
    $aTVShow[4]  = !empty($aXbmc["rating"])?round($aXbmc["rating"], 2):0;
    $aTVShow[5]  = !empty($aXbmc["plot"])?$aXbmc["plot"]:null;
    $aTVShow[6]  = !empty($aXbmc["studio"])?implode("|", $aXbmc["studio"]):null;
    $aTVShow[7]  = !empty($aXbmc["mpaa"])?$aXbmc["mpaa"]:null;    

    $aTVShow[8]  = !empty($aXbmc["cast"])?ConvertCast($aXbmc["cast"]):null;
    $aTVShow[9]  = !empty($aXbmc["playcount"])?$aXbmc["playcount"]:0;
    $aTVShow[10] = !empty($aXbmc["episode"])?$aXbmc["episode"]:0;  
    $aTVShow[11] = !empty($aXbmc["imdbnumber"])?$aXbmc["imdbnumber"]:null;
    
    $aTVShow[12] = !empty($aXbmc["premiered"])?$aXbmc["premiered"]:"0000-00-00";
    $aTVShow[13] = !empty($aXbmc["votes"])?$aXbmc["votes"]:0;
    $aTVShow[14] = !empty($aXbmc["lastplayed"])?$aXbmc["lastplayed"]:"0000-00-00 00:00:00";  
    $aTVShow[15] = !empty($aXbmc["file"])?$aXbmc["file"]:null;
    
    $aTVShow[16] = !empty($aXbmc["originaltitle"])?$aXbmc["originaltitle"]:null;       
    $aTVShow[17] = !empty($aXbmc["label"])?CreateSortTitle($aXbmc["label"]):null;
    $aTVShow[18] = !empty($aXbmc["season"])?$aXbmc["season"]:-1;
    $aTVShow[19] = !empty($aXbmc["episodeguide"])?$aXbmc["episodeguide"]:null;
    
    $aTVShow[20] = !empty($aXbmc["watchedepisodes"])?$aXbmc["watchedepisodes"]:0; 
    $aTVShow[21] = !empty($aXbmc["dateadded"])?$aXbmc["dateadded"]:"0000-00-00 00:00:00"; 
    
    // Hash title and file as unique db entry to prevent dublicates.
    $aTVShow[22] = hash("sha256", $aXbmc["label"].$aTVShow[15]);
    
    //$aTVShow["fanart"]  = EncodeLink($aXbmc["art"], "fanart");    
    //$aTVShow["poster"]  = EncodeLink($aXbmc["art"], "poster");
    //$aTVShow["thumb"]   = EncodeLink($aXbmc, "thumbnail");
    //$aTVShow["tag"]     = $aXbmc["tag"];   
    
    return $aTVShow;
}

/*
 * Function:	ConvertTVShowSeason
 *
 * Created on Oct 20, 2013
 * Updated on Jun 20, 2014
 *
 * Description: Convert xbmc TV Show Season items. For instance to readably URL's.
 *
 * In:  $db, $aXbmc
 * Out: $aSeason
 *
 */
function ConvertTVShowSeason($db, $aXbmc)
{
    $aSeason[0] = $aXbmc["seasonid"];
    $aSeason[1] = $aXbmc["label"]; // title
    
    //$aSeason[2] = $aXbmc["tvshowid"];
    $aSeason[2] = GetTVShowFargoId($db, $aXbmc["tvshowid"]);
    
    $aSeason[3] = !empty($aXbmc["showtitle"])?$aXbmc["showtitle"]:null;    
    $aSeason[4] = !empty($aXbmc["playcount"])?$aXbmc["playcount"]:0;
    $aSeason[5] = !empty($aXbmc["season"])?$aXbmc["season"]:0;
    $aSeason[6] = !empty($aXbmc["episode"])?$aXbmc["episode"]:0;
    $aSeason[7] = !empty($aXbmc["watchedepisodes"])?$aXbmc["watchedepisodes"]:0;
    
    // Hash title and showtitle as unique db entry to prevent dublicates.
    $aSeason[8] = hash("sha256", $aXbmc["label"].$aSeason[3]);
    
    return $aSeason;
}

/*
 * Function:	ConvertTVShowEpisode
 *
 * Created on Oct 26, 2013
 * Updated on Jun 21, 2014
 *
 * Description: Convert xbmc TV Show Episode items. For instance to readably URL's.
 *
 * In:  $db, $aXbmc
 * Out: $aSeason
 *
 */
function ConvertTVShowEpisode($db, $aXbmc)
{
    $aEpisode[0]  = $aXbmc["episodeid"];
    $aEpisode[1]  =  GetTVShowFargoId($db, $aXbmc["tvshowid"]);   
    $aEpisode[2]  = $aXbmc["label"]; // title
    $aEpisode[3]  = !empty($aXbmc["originaltitle"])?$aXbmc["originaltitle"]:null;
    
    $aEpisode[4]  = !empty($aXbmc["rating"])?round($aXbmc["rating"], 2):0;
    $aEpisode[5]  = !empty($aXbmc["writer"])?ConvertWriter($aXbmc["writer"]):null;
    $aEpisode[6]  = !empty($aXbmc["director"])?implode("|", $aXbmc["director"]):null;
    $aEpisode[7]  = !empty($aXbmc["cast"])?ConvertCast($aXbmc["cast"]):null;
    
    $aEpisode[8]  = !empty($aXbmc["plot"])?$aXbmc["plot"]:null;
    $aEpisode[9]  = !empty($aXbmc["playcount"])?$aXbmc["playcount"]:0;
    $aEpisode[10] = !empty($aXbmc["episode"])?$aXbmc["episode"]:0;
    $aEpisode[11] = !empty($aXbmc["firstaired"])?$aXbmc["firstaired"]:"0000-00-00";
    
    $aEpisode[12] = !empty($aXbmc["lastplayed"])?$aXbmc["lastplayed"]:"0000-00-00 00:00:00";
    $aEpisode[13] = !empty($aXbmc["dateadded"])?$aXbmc["dateadded"]:"0000-00-00 00:00:00";
    $aEpisode[14] = !empty($aXbmc["votes"])?$aXbmc["votes"]:0;  
    $aEpisode[15] = !empty($aXbmc["file"])?$aXbmc["file"]:null;
    
    $aEpisode[16] = !empty($aXbmc["showtitle"])?$aXbmc["showtitle"]:null;
    $aEpisode[17] = !empty($aXbmc["season"])?$aXbmc["season"]:0;
    $aEpisode[18] = !empty($aXbmc["streamdetails"]["audio"])?ConvertAudio($aXbmc["streamdetails"]["audio"]):null; 
    $aEpisode[19] = !empty($aXbmc["streamdetails"]["video"])?ConvertVideo($aXbmc["streamdetails"]["video"]):null;
    
    $aEpisode[20] = $aXbmc["runtime"];
    
    // Hash title and file as unique db entry to prevent dublicates.
    $aEpisode[21] = hash("sha256", $aEpisode[10].$aEpisode[15]);
    
    return $aEpisode;
}

/*
 * Function:	GetTVShowFargoId
 *
 * Created on Jun 08, 2014
 * Updated on Jun 08, 2014
 *
 * Description: Get the TV Show's Fargo Id.
 *
 * In:  $db, $tvshowid
 * Out: $id
 *
 */
function GetTVShowFargoId($db, $tvshowid)
{
    $sql = "SELECT id FROM tvshows WHERE xbmcid = $tvshowid";
    $id = GetItemFromDatabase($db, "id", $sql);
    
    return $id;
}        

/*
 * Function:	ConvertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on Jun 20, 2014
 *
 * Description: Convert XBMC album items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aAlbum
 *
 */
function ConvertAlbum($aXbmc)
{
    $aAlbum[0]  = $aXbmc["albumid"];
    $aAlbum[1]  = $aXbmc["label"];
    $aAlbum[2]  = !empty($aXbmc["description"])?$aXbmc["description"]:null;    
    $aAlbum[3]  = !empty($aXbmc["artist"])?implode("|", $aXbmc["artist"]):null;

    $aAlbum[4]  = !empty($aXbmc["genre"])?ConvertGenre($aXbmc["genre"], "music"):null;
    $aAlbum[5]  = !empty($aXbmc["theme"])?implode("|", $aXbmc["theme"]):null;
    $aAlbum[6]  = !empty($aXbmc["mood"])?implode("|", $aXbmc["mood"]):null;
    $aAlbum[7]  = !empty($aXbmc["style"])?implode("|", $aXbmc["style"]):null;
    
    $aAlbum[8]  = !empty($aXbmc["type"])?$aXbmc["type"]:null;    
    $aAlbum[9]  = !empty($aXbmc["albumlabel"])?$aXbmc["albumlabel"]:null;
    $aAlbum[10] = !empty($aXbmc["rating"])?$aXbmc["rating"]:0;
    $aAlbum[11] = !empty($aXbmc["year"])?$aXbmc["year"]:0;
    
    $aAlbum[12] = !empty($aXbmc["musicbrainzalbumid"])?$aXbmc["musicbrainzalbumid"]:null;    
    $aAlbum[13] = !empty($aXbmc["musicbrainzalbumartistid"])?$aXbmc["musicbrainzalbumartistid"]:null;
    $aAlbum[14] = !empty($aXbmc["playcount"])?$aXbmc["playcount"]:0;
    $aAlbum[15] = !empty($aXbmc["displayartist"])?$aXbmc["displayartist"]:null; 
    
    $aAlbum[16] = !empty($aXbmc["label"])?CreateSortTitle($aXbmc["label"]):null;
    
    // Hash title, artist and year as unique db entry to prevent dublicates.
    $aAlbum[17] = hash("sha256", $aXbmc["label"].$aAlbum[3].$aAlbum[11]);
    
    //$aAlbum["fanart"]          = $aXbmc["fanart"];
    //$aAlbum["cover"]           = EncodeLink($aXbmc, "thumbnail");
    //$aAlbum["genreid"]       = $aXbmc["genreid"];    
    //$aAlbum["artistid"]      = $aXbmc["artistid"];
    
    return $aAlbum;
}

/*
 * Function:	ConvertGenres
 *
 * Created on Jun 22, 2013
 * Updated on Jan 02, 2014
 *
 * Description: Convert genres from array to string and insert genres in database.
 *
 * In:  $aGenre
 * Out: $genres
 *
 */
function ConvertGenre($aGenres)
{
    $genres = null;

    if (!empty($aGenres)) 
    {
        // Remove dublicate entries.
        $aGenres = array_unique($aGenres);
        
        // Sort genres.
        sort($aGenres);
        
        $genres = '"'.implode('"|"', $aGenres).'"';
    }

    return $genres;
}

/*
 * Function:	ConvertWriter
 *
 * Created on Jul 06, 2013
 * Updated on Jul 06, 2013
 *
 * Description: Remove duplicates and convert writers from array to string.
 *
 * In:  $aWriters
 * Out: $writer
 *
 */
function ConvertWriter($aWriters)
{
    $i = 0;
    $writer = null;
    $aDummy = array_unique($aWriters);
    
    if (!empty($aDummy)) {
        $writer = implode("|", $aDummy);
    }
    
    return $writer;
}

/*
 * Function:	ConvertCast
 *
 * Created on Jun 22, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Convert cast from array to string.
 *
 * In:  $aCast
 * Out: $cast
 *
 */
function ConvertCast($aCast)
{
    $i = 0;
    $cast = null;
    $aDummy = null;
    
    foreach($aCast as $value)
    {    
        $aDummy[$i] = $value["name"].":".$value["role"];
        $i++;
    }
    
    if (!empty($aDummy)) {
        $cast = implode("|", $aDummy);
    }
    
    return $cast;
}

/*
 * Function:	ConvertAudio
 *
 * Created on Jun 22, 2013
 * Updated on Jul 15, 2013
 *
 * Description: Convert audio from array to string.
 *
 * In:  $aAudio
 * Out: $audio
 *
 */
function ConvertAudio($aAudio)
{
    $i = 0;
    $audio = null;
    $aDummy = null;
    
    foreach($aAudio as $value)
    {    
        $aDummy[$i] = $value["channels"].":".$value["codec"];
        $i++;
    }
    
    if (!empty($aDummy)) {
        $audio = implode("|", $aDummy);
    }
    
    return $audio;
}

/*
 * Function:	ConvertVideo
 *
 * Created on Jun 22, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Convert video from array to string.
 *
 * In:  $aVideo
 * Out: $video
 *
 */
function ConvertVideo($aVideo)
{
    $i = 0;
    $video = null;
    $aDummy = null;
    
    foreach($aVideo as $value)
    {    
        $aDummy[$i] = $value["aspect"].":".$value["codec"].":".$value["height"].":".$value["width"];
        $i++;
    }
    
    if (!empty($aDummy)) {
        $video = implode("|", $aDummy);
    }
    
    return $video;
}

/*
 * Function:	CreateSortTitle
 *
 * Created on Jun 22, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Create sort title (remove "The ").
 *
 * In:  $title
 * Out: $sorttitle
 *
 */
function CreateSortTitle($title)
{
    $aTitle = explode("The ", $title, 2);
    
    if (isset($aTitle[1]))
    {
        $sorttitle = $aTitle[1];
    }
    else 
    {    
        $sorttitle = $title;
    }
    
    return $sorttitle;
}
