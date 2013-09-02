<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    import.php
 *
 * Created on Apr 14, 2013
 * Updated on Jul 08, 2013
 *
 * Description: Fargo's import functions page for the XBMC media import.
 *
 */


/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

require_once 'include/import_json.php';
require_once 'include/import_db.php';

/*
 * Function:	ImportMedia
 *
 * Created on Apr 19, 2013
 * Updated on Jul 07, 2013
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  $start, $media
 * Out: $aJson
 *
 */
function ImportMedia($start, $media)
{   
    $aJson = null;
    
    switch ($media)    
    {   
        case "movies"   : $aJson = ImportMovies($start);
                          break;
    
        case "tvshows"  : $aJson = ImportTVShows($start);
                          break;
                      
        case "music"    : $aJson = ImportAlbums($start);
                          break;                      
    }
    
    // Delete the temporary poster.
    DeleteFile(cTEMPPOSTERS."/*.j*");
    DeleteFile(cTEMPPOSTERS."/*.p*");
    DeleteFile(cTEMPPOSTERS."/*.m*");
    
    return $aJson;
}

/*
 * Function:	ImportMovies
 *
 * Created on Mar 11, 2013
 * Updated on May 19, 2013
 *
 * Description: Import the movies. 
 *
 * In:  $start
 * Out: $aJson
 *
 */
function ImportMovies($start)
{
    $offset  = 3;
    $aJson['online'] = true;
    $aMovies = GetMoviesFromXBMC($start, $offset);
    
    if (!empty($aMovies)) {
        ProcessMovies($aMovies);
    }
    else {
        $aJson['online'] = -1;
    }
    
    return $aJson;
}

/*
 * Function:	ImportTVShows
 *
 * Created on Apr 19, 2013
 * Updated on Jun 23, 2013
 *
 * Description: Import the tv shows. 
 *
 * In:  $start
 * Out: $aJson
 *
 */
function ImportTVShows($start)
{
    $offset  = 3;  
    $aJson['online'] = true;
    $aTVShows = GetTVShowsFromXBMC($start, $offset);
    
    if (!empty($aTVShows)) {
        ProcessTVShows($aTVShows);
    }
    else {
        $aJson['online'] = -1;
    }
    
    return $aJson;    
}

/*
 * Function:	ImportAlbums
 *
 * Created on Apr 20, 2013
 * Updated on Jun 23, 2013
 *
 * Description: Import the music albums. 
 *
 * In:  $start
 * Out: $aJson
 *
 */
function ImportAlbums($start)
{
    $offset  = 3;   
    $aJson['online'] = true;
    $aAlbums = GetAlbumsFromXBMC($start, $offset);
    
    if (!empty($aAlbums)) {
        ProcessAlbums($aAlbums);
    }
    else {
        $aJson['online'] = -1;
    }
    
    return $aJson;  
}

//////////////////////////////////    Process and Convert Functions    ////////////////////////////////////

/*
 * Function:	ProcessMovies
 *
 * Created on Mar 11, 2013
 * Updated on Jun 26, 2013
 *
 * Description: Process the movies. 
 *
 * In:  $aMovies
 * Out: -
 *
 */
function ProcessMovies($aMovies)
{  
    foreach ($aMovies as $aMovie)
    {            
        $aGenres = $aMovie["genre"];
        
        $aMovie = ConvertMovie($aMovie);    
        InsertMovie($aMovie);
        InsertGenreToMedia($aGenres, "movies");
    }
}

/*
 * Function:	ProcessTVShows
 *
 * Created on Apr 19, 2013
 * Updated on Jun 26, 2013
 *
 * Description: Process the TV Shows. 
 *
 * In:  $aTVShows
 * Out: -
 *
 */
function ProcessTVShows($aTVShows)
{  
    foreach ($aTVShows as $aTVShow)
    {            
        $aGenres = $aTVShow["genre"];
        
        $aTVShow = ConvertTVShow($aTVShow);        
        InsertTVShow($aTVShow);
        InsertGenreToMedia($aGenres, "tvshows");
    }
}

/*
 * Function:	ProcessAlbums
 *
 * Created on Apr 19, 2013
 * Updated on Jun 26, 2013
 *
 * Description: Process the music albums. 
 *
 * In:  $aAlbums
 * Out: -
 *
 */
function ProcessAlbums($aAlbums)
{  
    foreach ($aAlbums as $aAlbum)
    {            
        $aGenres = $aAlbum["genre"];
        
        $aAlbum = ConvertAlbum($aAlbum);        
        InsertAlbum($aAlbum);
        InsertGenreToMedia($aGenres, "music");
    }
}

/*
 * Function:	ConvertMovie
 *
 * Created on Mar 11, 2013
 * Updated on Jul 08, 2013
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
    
    $aMovie["cast"]    = ConvertCast($aXbmc["cast"]);
    $aMovie["country"] = implode("|", $aXbmc["country"]);
    $aMovie["imdbnr"]  = $aXbmc["imdbnumber"];
    $aMovie["runtime"] = $aXbmc["runtime"];   
    
    $aMovie["set"]   = $aXbmc["set"];
    //$aMovie["showlink"]      = $aXbmc["showlink"];      
    $aMovie["audio"] = ConvertAudio($aXbmc["streamdetails"]["audio"]);
    $aMovie["video"] = ConvertVideo($aXbmc["streamdetails"]["video"]);
    
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
    
    if (!empty($aMovie["poster"]))
    {
        $img = GetImageFromXbmc("pos", $aMovie["xbmcid"], $aMovie["poster"]);
        if ($img) {
            ResizeJpegImage($img, 100, 140, cMOVIESPOSTERS."/".$aMovie["xbmcid"].".jpg");
        }
        else {
            ResizeJpegImage("images/no_poster.jpg", 100, 140, cMOVIESPOSTERS."/".$aMovie["xbmcid"].".jpg");
        }
    }
    else {
        ResizeJpegImage("images/no_poster.jpg", 100, 140, cMOVIESPOSTERS."/".$aMovie["xbmcid"].".jpg");
    }
    
    if (!empty($aMovie["fanart"]))
    {
        $img = GetImageFromXbmc("fan", $aMovie["xbmcid"], $aMovie["fanart"]);
        if ($img) {
            ResizeJpegImage($img, 450, 280, cMOVIESFANART."/".$aMovie["xbmcid"].".jpg"); //500, 300
        }
    }   
    
    return $aMovie;
}

/*
 * Function:	ConvertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Jul 08, 2013
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

    $aTVShow["cast"]      = ConvertCast($aXbmc["cast"]);
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
    
    if (!empty($aTVShow["poster"]))
    {
        $img = GetImageFromXbmc("pos", $aTVShow["xbmcid"], $aTVShow["poster"]);
        if ($img) {
            ResizeJpegImage($img, 100, 140, cTVSHOWSPOSTERS."/".$aTVShow["xbmcid"].".jpg");
        }
        else {
            ResizeJpegImage("images/no_poster.jpg", 100, 140, cTVSHOWSPOSTERS."/".$aTVShow["xbmcid"].".jpg");
        }
    }
    else {
        ResizeJpegImage("images/no_poster.jpg", 100, 140, cTVSHOWSPOSTERS."/".$aTVShow["xbmcid"].".jpg");
    }
    
    if (!empty($aTVShow["fanart"]))
    {
        $img = GetImageFromXbmc("fan", $aTVShow["xbmcid"], $aTVShow["fanart"]);
        if ($img) {
            ResizeJpegImage($img, 450, 280, cTVSHOWSFANART."/".$aTVShow["xbmcid"].".jpg"); //500, 300
        }
    } 
    
    return $aTVShow;
}

/*
 * Function:	ConvertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on Jul 02, 2013
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
    
    if (!empty($aAlbum["cover"]))
    {
        $img = GetImageFromXbmc("cov", $aAlbum["xbmcid"], $aAlbum["cover"]);
        if ($img) {
            ResizeJpegImage($img, 100, 100, cALBUMSCOVERS."/".$aAlbum["xbmcid"].".jpg");
        }
        else {
            ResizeJpegImage("images/no_cover.jpg", 100, 100, cALBUMSCOVERS."/".$aAlbum["xbmcid"].".jpg");
        }
    }
    else {
        ResizeJpegImage("images/no_cover.jpg", 100, 100, cALBUMSCOVERS."/".$aAlbum["xbmcid"].".jpg");
    }
    
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
 * Updated on Jun 22, 2013
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
?>
