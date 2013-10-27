<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    import_convert.php
 *
 * Created on Jul 15, 2013
 * Updated on Oct 27, 2013
 *
 * Description: This page contains functions for converting media from XBMC (used by import.php).
 *
 */

/////////////////////////////////////////    Convert Functions    /////////////////////////////////////////

/*
 * Function:	ConvertMovie
 *
 * Created on Mar 11, 2013
 * Updated on Jul 21, 2013
 *
 * Description: Convert xbmc movie items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aMovie
 *
 */
function ConvertMovie($aXbmc)
{  
    $aMovie["xbmcid"] = $aXbmc["movieid"];
    $aMovie["title"]  = $aXbmc["label"];
    $aMovie["genre"]  = ConvertGenre($aXbmc["genre"], "movies");
    $aMovie["year"]   = $aXbmc["year"];
    
    $aMovie["rating"]   = $aXbmc["rating"];
    $aMovie["director"] = implode("|", $aXbmc["director"]);  
    $aMovie["trailer"]  = $aXbmc["trailer"];
    $aMovie["tagline"]  = $aXbmc["tagline"]; 
    
    $aMovie["plot"]          = $aXbmc["plot"];
    $aMovie["plotoutline"]   = $aXbmc["plotoutline"];    
    $aMovie["originaltitle"] = $aXbmc["originaltitle"];
    $aMovie["lastplayed"]    = $aXbmc["lastplayed"];
    
    $aMovie["playcount"] = $aXbmc["playcount"];
    $aMovie["writer"]    = ConvertWriter($aXbmc["writer"]);
    $aMovie["studio"]    = implode("|", $aXbmc["studio"]);
    $aMovie["mpaa"]      = $aXbmc["mpaa"];
    
    if (!empty($aXbmc["cast"])) {
        $aMovie["cast"] = ConvertCast($aXbmc["cast"]);
    }
    else {
        $aMovie["cast"] = null; 
    }
    
    $aMovie["country"] = implode("|", $aXbmc["country"]);
    $aMovie["imdbnr"]  = $aXbmc["imdbnumber"];
    $aMovie["runtime"] = $aXbmc["runtime"];   
    
    $aMovie["set"]   = $aXbmc["set"];
    //$aMovie["showlink"]      = $aXbmc["showlink"];
    
    if (!empty($aXbmc["streamdetails"]["audio"])) {
        $aMovie["audio"] = ConvertAudio($aXbmc["streamdetails"]["audio"]);   
    }
    else{
        $aMovie["audio"] = null;
    }
    
    if (!empty($aXbmc["streamdetails"]["video"])) {
        $aMovie["video"] = ConvertVideo($aXbmc["streamdetails"]["video"]);
    }
    else {
        $aMovie["video"] = null;
    }
    
    $aMovie["top250"]    = $aXbmc["top250"];    
    $aMovie["votes"]     = $aXbmc["votes"];
    $aMovie["file"]      = $aXbmc["file"];      
    $aMovie["sorttitle"] = CreateSortTitle($aXbmc["label"]);

    //$aMovie["resume"]    = $aXbmc["resume"];    
    $aMovie["setid"]     = $aXbmc["setid"];
    $aMovie["dateadded"] = $aXbmc["dateadded"];      
    //$aMovie["tag"]       = $aXbmc["tag"];

    $aMovie["fanart"] = EncodeLink($aXbmc["art"], "fanart");
    $aMovie["poster"] = EncodeLink($aXbmc["art"], "poster");
    $aMovie["thumb"]  = EncodeLink($aXbmc, "thumbnail");   
    
    return $aMovie;
}

/*
 * Function:	ConvertMovieSet
 *
 * Created on Oct 13, 2013
 * Updated on Oct 14, 2013
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
    $aMovie[1] = $aXbmc["label"];       // title
    $aMovie[2] = $aXbmc["playcount"];
    
    $aMovie[3] = EncodeLink($aXbmc["art"], "fanart");
    $aMovie[4] = EncodeLink($aXbmc["art"], "poster");
    $aMovie[5] = EncodeLink($aXbmc, "thumbnail");   
    
    return $aMovie;
}

/*
 * Function:	ConvertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Aug 24, 2013
 *
 * Description: Convert xbmc TV Show items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aTVShow
 *
 */
function ConvertTVShow($aXbmc)
{     
    $year = explode("-", $aXbmc["premiered"]);
    
    $aTVShow["xbmcid"] = $aXbmc["tvshowid"];
    $aTVShow["title"]  = $aXbmc["label"];
    $aTVShow["genre"]  = ConvertGenre($aXbmc["genre"], "tvshows");
    $aTVShow["year"]   = $year[0];
    
    $aTVShow["rating"] = $aXbmc["rating"];
    $aTVShow["plot"]   = $aXbmc["plot"];
    $aTVShow["studio"] = implode("|", $aXbmc["studio"]);
    $aTVShow["mpaa"]   = $aXbmc["mpaa"];    

    //$aTVShow["cast"]      = ConvertCast($aXbmc["cast"]);  
    if (!empty($aXbmc["cast"])) {
        $aTVShow["cast"] = ConvertCast($aXbmc["cast"]);
    }
    else {
        $aTVShow["cast"] = null; 
    }    
    
    $aTVShow["playcount"] = $aXbmc["playcount"];
    $aTVShow["episode"]   = $aXbmc["episode"];  
    $aTVShow["imdbnr" ]   = $aXbmc["imdbnumber"];
    
    $aTVShow["premiered"]  = $aXbmc["premiered"];
    $aTVShow["votes"]      = $aXbmc["votes"];
    $aTVShow["lastplayed"] = $aXbmc["lastplayed"];  
    $aTVShow["fanart"]     = EncodeLink($aXbmc["art"], "fanart");
        
    $aTVShow["poster"]        = EncodeLink($aXbmc["art"], "poster");
    $aTVShow["thumb"]         = EncodeLink($aXbmc, "thumbnail");
    $aTVShow["file"]          = $aXbmc["file"];
    $aTVShow["originaltitle"] = $aXbmc["originaltitle"];   
    
    $aTVShow["sorttitle"]       = CreateSortTitle($aXbmc["label"]);
    $aTVShow["episodeguide"]    = $aXbmc["episodeguide"];
    $aTVShow["season"]          = $aXbmc["season"];
    $aTVShow["watchedepisodes"] = $aXbmc["watchedepisodes"];        

    $aTVShow["dateadded"] = $aXbmc["dateadded"]; 
    //$aTVShow["tag"]      = $aXbmc["tag"];   
    
    return $aTVShow;
}

/*
 * Function:	ConvertTVShowSeason
 *
 * Created on Oct 20, 2013
 * Updated on Oct 20, 2013
 *
 * Description: Convert xbmc TV Show Season items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aSeason
 *
 */
function ConvertTVShowSeason($aXbmc)
{
    $aSeason[0] = $aXbmc["tvshowid"];
    $aSeason[1] = $aXbmc["label"]; // title
    $aSeason[2] = $aXbmc["showtitle"];    
    $aSeason[3] = EncodeLink($aXbmc, "thumbnail");
    $aSeason[4] = $aXbmc["playcount"];
    $aSeason[5] = $aXbmc["season"];    
    $aSeason[6] = $aXbmc["episode"];
    $aSeason[7] = $aXbmc["watchedepisodes"];  
    
    return $aSeason;
}

/*
 * Function:	ConvertTVShowEpisode
 *
 * Created on Oct 26, 2013
 * Updated on Oct 27, 2013
 *
 * Description: Convert xbmc TV Show Episode items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aSeason
 *
 */
function ConvertTVShowEpisode($aXbmc)
{
    $aEpisode[0]  = $aXbmc["episodeid"];
    $aEpisode[1]  = $aXbmc["tvshowid"]; 
    $aEpisode[2]  = $aXbmc["label"]; // title    
    $aEpisode[3]  = EncodeLink($aXbmc, "thumbnail");
    $aEpisode[4]  = $aXbmc["originaltitle"];
    $aEpisode[5]  = $aXbmc["rating"];
    $aEpisode[6]  = ConvertWriter($aXbmc["writer"]);
    $aEpisode[7]  = implode("|", $aXbmc["director"]);
    $aEpisode[8]  = !empty($aXbmc["cast"])?ConvertCast($aXbmc["cast"]):null;
    $aEpisode[9]  = $aXbmc["plot"];
    $aEpisode[10] = $aXbmc["playcount"];
    $aEpisode[11] = $aXbmc["episode"];
    $aEpisode[12] = !empty($aXbmc["firstaired"])?$aXbmc["firstaired"]:"0000-00-00";
    $aEpisode[13] = !empty($aXbmc["lastplayed"])?$aXbmc["lastplayed"]:"0000-00-00 00:00:00";
    $aEpisode[14] = !empty($aXbmc["dateadded"])?$aXbmc["dateadded"]:"0000-00-00 00:00:00";
    $aEpisode[15] = !empty($aXbmc["votes"])?$aXbmc["votes"]:0;  
    $aEpisode[16] = $aXbmc["file"];
    $aEpisode[17] = $aXbmc["showtitle"];
    $aEpisode[18] = $aXbmc["season"];
    $aEpisode[19] = !empty($aXbmc["streamdetails"]["audio"])?ConvertAudio($aXbmc["streamdetails"]["audio"]):null; 
    $aEpisode[20] = !empty($aXbmc["streamdetails"]["video"])?ConvertVideo($aXbmc["streamdetails"]["video"]):null;
    $aEpisode[21] = $aXbmc["runtime"];
    
    return $aEpisode;
}

/*
 * Function:	ConvertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on Aug 24, 2013
 *
 * Description: Convert XBMC album items. For instance to readably URL's.
 *
 * In:  $aXbmc
 * Out: $aAlbum
 *
 */
function ConvertAlbum($aXbmc)
{
    $aAlbum["xbmcid"]      = $aXbmc["albumid"];
    $aAlbum["title"]       = $aXbmc["label"];
    $aAlbum["description"] = $aXbmc["description"];    
    $aAlbum["artist"]      = implode("|", $aXbmc["artist"]);

    $aAlbum["genre"] = ConvertGenre($aXbmc["genre"], "music");
    $aAlbum["theme"] = implode("|", $aXbmc["theme"]);
    $aAlbum["mood"]  = implode("|", $aXbmc["mood"]);
    $aAlbum["style"] = implode("|", $aXbmc["style"]);
    
    $aAlbum["type"]       = $aXbmc["type"];    
    $aAlbum["albumlabel"] = $aXbmc["albumlabel"];
    $aAlbum["rating"]     = $aXbmc["rating"];
    $aAlbum["year"]       = $aXbmc["year"];
    
    $aAlbum["mbalbumid"]       = $aXbmc["musicbrainzalbumid"];    
    $aAlbum["mbalbumartistid"] = $aXbmc["musicbrainzalbumartistid"];
    $aAlbum["fanart"]          = $aXbmc["fanart"];
    $aAlbum["cover"]           = EncodeLink($aXbmc, "thumbnail");
    
    $aAlbum["playcount"]     = $aXbmc["playcount"];
    $aAlbum["displayartist"] = $aXbmc["displayartist"]; 
    $aAlbum["sorttitle"]     = CreateSortTitle($aXbmc["label"]);
    //$aAlbum["genreid"]       = $aXbmc["genreid"];
    
    //$aAlbum["artistid"]      = $aXbmc["artistid"];
    
    return $aAlbum;
}

/*
 * Function:	ConvertGenres
 *
 * Created on Jun 22, 2013
 * Updated on Jul 04, 2013
 *
 * Description: Convert genres from array to string and insert genres in database.
 *
 * In:  $aGenre, $media
 * Out: $genres
 *
 */
function ConvertGenre($aGenres, $media)
{
    InsertGenres($aGenres, $media);
    $genres = '"'.implode('"|"', $aGenres).'"';
    
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
