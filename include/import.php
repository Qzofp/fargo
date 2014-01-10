<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    import.php
 *
 * Created on Jul 15, 2013
 * Updated on Jan 10, 2014
 *
 * Description: Fargo's import page. This page is called from XBMC which push the data to Fargo.
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

// Give the damn thing cross domain access. Something XBMC won't let you do, the bastards!!!
header("Access-Control-Allow-Origin: *");  // Add "*" to settings.

require_once '../settings.php';
require_once '../tools/toolbox.php';
require_once 'common.php';
require_once 'import_convert.php';
require_once 'import_db.php';

$db = OpenDatabase();

$login = CheckImportKey($db);
if($login)
{
    $aData = ReceiveDataFromXbmc();
    ProcessDataFromXbmc($db, $aData);
}
else 
{
    $aJson = LogEvent("Warning", "Unauthorized import call!");
    echo json_encode($aJson);
}

CloseDatabase($db);

//debug
//echo "<pre>";
//print_r($aData);
//echo "</pre></br>";
    
/////////////////////////////////////////    Import Functions    //////////////////////////////////////////    

/*
 * Function:	ReceiveDataFromXbmc
 *
 * Created on Jul 15, 2013
 * Updated on Jan 09, 2014
 *
 * Description: Receive data from XBMC. 
 *
 * In:  -
 * Out: $aData
 *
 */    
function ReceiveDataFromXbmc()
{
    $aData = null;    
    
    $aData["id"] = null;
    if (isset($_POST["id"]) && !empty($_POST["id"]))
    {
        $aData["id"] = $_POST["id"];
    } 
    
    $aData["error"] = null;
    if (isset($_POST["error"]) && !empty($_POST["error"]))
    {
        $aData["error"] = $_POST["error"];
    }  
    
    $aData["poster"] = null;
    if (isset($_POST["poster"]) && !empty($_POST["poster"]))
    {
        $aData["poster"] = $_POST["poster"];   
    }

    $aData["fanart"] = null;
    if (isset($_POST["fanart"]) && !empty($_POST["fanart"]))
    {
        $aData["fanart"] = $_POST["fanart"];
    }      
    
    $aData["start"] = null;
    if (isset($_POST["start"]) && !empty($_POST["start"]))
    {
        $aData["start"] = $_POST["start"];
    }    
    
    $aData["result"] = null;
    if (isset($_POST["result"]) && !empty($_POST["result"]))
    {
        $aData["result"] = $_POST["result"];
    }
    
    $aData["fargoid"] = null;
    if (isset($_POST["fargoid"]) && !empty($_POST["fargoid"]))
    {
        $aData["fargoid"] = $_POST["fargoid"];
    }    
    
    return $aData;
}
   
/*
 * Function:	ProcessDataFromXbmc
 *
 * Created on Jul 15, 2013
 * Updated on Jan 07, 2014
 *
 * Description: Process data from XBMC. 
 *
 * In:  $db, $aData
 * Out: -
 *
 */
function ProcessDataFromXbmc($db, $aData)
{
    switch($aData["id"])
    {
        // libMoviesCounter -> library id = 1.
        case 1  : UpdateMediaEndValue($db, $aData["error"], "Movies", $aData["start"]["movies"][0]["movieid"], $aData["result"]["movies"][0]["movieid"]);
                  //UpdateStatus($db, "XbmcMoviesEnd", $aData["result"]["movies"][0]["movieid"]);
                  break;
             
        // libMovies Import -> library id = 2.                   
        case 2  : ImportMovie($db, $aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                  break;
        
        // libMovies Refresh -> library id = 3.     
        case 3  : RefreshMovie($db, $aData["error"], $aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);
                  break;
        
        // libMovieSetsCounter -> library id = 4.    
        case 4  : UpdateMediaEndValue($db, $aData["error"], "Sets", $aData["start"]["sets"][0]["setid"], $aData["result"]["sets"][0]["setid"]);
                  //UpdateStatus($db, "XbmcSetsEnd", $aData["result"]["sets"][0]["setid"]); 
                  break;
              
        // libMovieSets Import -> library id = 5.                   
        case 5  : ImportMovieSet($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;
              
        // libMovieSets Reset -> library id = 6.
        case 6  : RefreshMovieSet($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;
             
        // libTVShowsCounter -> library id = 11.  
        case 11 : UpdateMediaEndValue($db, $aData["error"], "TVShows", $aData["start"]["tvshows"][0]["tvshowid"], $aData["result"]["tvshows"][0]["tvshowid"]);
                  //UpdateStatus($db, "XbmcTVShowsEnd", $aData["result"]["tvshows"][0]["tvshowid"]);   
                  break;
        
        // libTVShows Import -> library id = 12.
        case 12 : ImportTVShow($db, $aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                  break;
             
        // libTVShows Refresh -> library id = 13.
        case 13 : RefreshTVShow($db, $aData["error"], $aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);
                  break;
             
        // libTVShowSeasonsCounter -> library id = 14. Note TV Seasons uses the same counter as TV Shows.
        case 14 : UpdateMediaEndValue($db, $aData["error"], "TVShowsSeasons", $aData["start"]["tvshows"][0]["tvshowid"], $aData["result"]["tvshows"][0]["tvshowid"]);
                  //UpdateStatus($db, "XbmcTVShowsSeasonsEnd", $aData["result"]["tvshows"][0]["tvshowid"]);   
                  break;           
              
        // libSeasonsCounter -> library id = 15.
        case 15 : UpdateMediaEndValue($db, $aData["error"], "Seasons", 0, $aData["result"]["limits"]["total"]);
                  //UpdateStatus($db, "XbmcSeasonsEnd", $aData["result"]["limits"]["total"]);
                  break;   
              
        // libTVShowSeasons Import -> library id = 16.
        case 16 : ImportTVShowSeason($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;  
              
        // libTVShowSeasons Refresh -> library id = 17.    
        case 17 : RefreshTVShowSeason($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;        
              
        // libTVShowEpisodesCounter -> library id = 31.  
        case 31 : UpdateMediaEndValue($db, $aData["error"], "Episodes", $aData["start"]["episodes"][0]["episodeid"], $aData["result"]["episodes"][0]["episodeid"]);
                  //UpdateStatus($db, "XbmcEpisodesEnd", $aData["result"]["episodes"][0]["episodeid"]);   
                  break;
                       
        // libTVShowEpisode Import -> library id = 32.
        case 32 : ImportTVShowEpisode($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;
              
        // libTVShowEpisode Refresh -> library id = 33.
        case 33 : RefreshTVShowEpisode($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;         
                            
        // libAlbumsCounter -> library id = 41.  
        case 41 : UpdateMediaEndValue($db, $aData["error"], "Music", $aData["start"]["albums"][0]["albumid"], $aData["result"]["albums"][0]["albumid"]);
                  //UpdateStatus($db, "XbmcMusicEnd", $aData["result"]["albums"][0]["albumid"]); 
                  break;
        
        // libAlbums Import -> library id = 42.
        case 42 : ImportAlbum($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;
             
        // libAlbums Refresh -> library id = 43.
        case 43 : RefreshAlbum($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;                       
    }
}

/*
 * Function:	UpdateMediaEndValue
 *
 * Created on Jan 07, 2014
 * Updated on Jan 09, 2014
 *
 * Description: Update the media end value. 
 *
 * In:  $db, $aError, $end, $value
 * Out: -
 *
 */
function UpdateMediaEndValue($db, $aError, $type, $start, $end)
{
    if (empty($aError)) 
    {
        if ($start > GetStatus($db, "Xbmc".$type."Start")) {
            UpdateStatus($db, "Xbmc".$type."Start", $start);
        }
        
        UpdateStatus($db, "Xbmc".$type."End", $end);
    }
    else {
        UpdateStatus($db, $end, 0);
    }
}

/*
 * Function:	ImportMovie
 *
 * Created on Jul 15, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Import the movie. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ImportMovie($db, $aError, $poster, $fanart, $aResult)
{   
    //$db = OpenDatabase();
    
    if (empty($aError))
    {
        $aGenres = $aResult["moviedetails"]["genre"]; //$aMovie["genre"];
        $aMovie  = ConvertMovie($aResult["moviedetails"]);
        
        ResizeAndSaveImage($aMovie[0], $poster, "../".cMOVIESTHUMBS, 125, 175); //200, 280          
        $id = InsertMovie($db, $aMovie);    
        ResizeAndSaveImage($aMovie[0], $fanart, "../".cMOVIESFANART, 450, 280); //562, 350 //675, 420         
        
        InsertGenres($db, $aGenres, "movies");
        InsertGenreToMedia($db, $aGenres, $id, "movies");
    
        UpdateStatus($db, "XbmcSlack", 0);
        IncrementStatus($db, "XbmcMoviesStart", 1);
        IncrementStatus($db, "ImportCounter", 1);
    }
    else if ($aError["code"] == -32602) // Movie not found, continue with the next one.
    { 
        UpdateStatus($db, "XbmcSlack", 1);
        IncrementStatus($db, "XbmcMoviesStart", 1); 
    }
    
    //CloseDatabase($db); 
}

/*
 * Function:	ImportMovieSet
 *
 * Created on Oct 13, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Import the movie set. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportMovieSet($db, $aError, $poster, $aResult)
{  
    //$db = OpenDatabase();    
    
    if (empty($aError))
    {
        $aMovie = ConvertMovieSet($aResult["setdetails"]);
 
        ResizeAndSaveImage($aMovie[0], $poster, "../".cSETSTHUMBS, 125, 175); //200, 280    
        InsertMovieSet($db, $aMovie);         
        
        UpdateStatus($db, "XbmcSlack", 0);
        IncrementStatus($db, "XbmcSetsStart", 1);
        IncrementStatus($db, "ImportCounter", 1);
    }
    else if ($aError["code"] == -32602) // Movie not found, continue with the next one.
    { 
       UpdateStatus($db, "XbmcSlack", 1);
       IncrementStatus($db, "XbmcSetsStart", 1); 
    }
    
    //CloseDatabase($db);     
}


/*
 * Function:	RefreshMovie
 *
 * Created on Sep 08, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Refresh the movie. 
 *
 * In:  $aError, $poster, $fanart, $aResult, $fargoid
 * Out: -
 *
 */
function RefreshMovie($db, $aError, $poster, $fanart, $aResult, $fargoid)
{
    //$db = OpenDatabase();
    
    if (empty($aError))
    {
        $aGenres = $aResult["moviedetails"]["genre"]; 
        $aMovie  = ConvertMovie($aResult["moviedetails"]);
    
        DeleteFile("../".cMOVIESTHUMBS."/".$aMovie[0].".jpg");
        DeleteFile("../".cMOVIESFANART."/".$aMovie[0].".jpg");
        
        ResizeAndSaveImage($aMovie[0], $poster, "../".cMOVIESTHUMBS, 125, 175);  //200, 280
        UpdateMovie($db, $fargoid, $aMovie);   
        ResizeAndSaveImage($aMovie[0], $fanart, "../".cMOVIESFANART,  450, 280); //562, 350 //675, 420  
        InsertGenres($db, $aGenres, "movies");
        
        UpdateStatus($db, "RefreshReady", 1);
    }
    
    //CloseDatabase($db);    
}

/*
 * Function:	RefreshMovieSet
 *
 * Created on Nov 23, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Refresh the movie set. 
 *
 * In:  $db, $aError, $poster, $aResult, $fargoid
 * Out: -
 *
 */
function RefreshMovieSet($db, $aError, $poster, $aResult, $fargoid)
{
    //$db = OpenDatabase();    
    
    if (empty($aError))
    {  
        $aMovie = ConvertMovieSet($aResult["setdetails"]);
    
        DeleteFile("../".cSETSTHUMBS."/".$aMovie[0].".jpg");
        DeleteFile("../".cSETSFANART."/".$aMovie[0].".jpg");
    
        UpdateMovieSet($db, $fargoid, $aMovie);   
        ResizeAndSaveImage($aMovie[0], $poster, "../".cSETSTHUMBS, 125, 175); //200, 280  

        UpdateStatus($db, "RefreshReady", 1);
    }
    
    //CloseDatabase($db);    
}

/*
 * Function:	ImportTVShow
 *
 * Created on Aug 24, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Import the tv show. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ImportTVShow($db, $aError, $poster, $fanart, $aResult)
{   
    //$db = OpenDatabase();     
    
    if (empty($aError))
    {
        $aGenres = $aResult["tvshowdetails"]["genre"];
        $aTVShow = ConvertTVShow($aResult["tvshowdetails"]);
     
        ResizeAndSaveImage($aTVShow[0], $poster, "../".cTVSHOWSTHUMBS, 125, 175);
        
        $id = InsertTVShow($db, $aTVShow);
        
        ResizeAndSaveImage($aTVShow[0], $fanart, "../".cTVSHOWSFANART, 450, 280); //562, 350 //675, 420 
        
        InsertGenres($db, $aGenres, "tvshows");
        InsertGenreToMedia($db, $aGenres, $id, "tvshows");
    
        UpdateStatus($db, "XbmcSlack", 0);
        IncrementStatus($db, "XbmcTVShowsStart", 1);
        IncrementStatus($db, "ImportCounter", 1);
    }
    else if ($aError["code"] == -32602) // TVShow not found, continue with the next one.
    { 
       UpdateStatus($db, "XbmcSlack", 1);
       IncrementStatus($db, "XbmcTVShowsStart", 1); 
    }    
    
    //CloseDatabase($db);      
}

/*
 * Function:	ImportTVShowSeason
 *
 * Created on Oct 20, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Import the tv show season. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportTVShowSeason($db, $aError, $poster, $aResult)
{ 
    //$db = OpenDatabase();      
    
    if (empty($aError))
    {    
        $aSeason = ConvertTVShowSeason($aResult["seasons"][0]);

        ResizeAndSaveImage($aSeason[0]."_".$aSeason[4], $poster, "../".cSEASONSTHUMBS, 125, 175);

        InsertTVShowSeason($db, $aSeason);   
        
        IncrementStatus($db, "XbmcSeasonsStart", 1);
        IncrementStatus($db, "ImportCounter", 1);
        
        if ($aResult["limits"]["end"] >= $aResult["limits"]["total"]) {
            IncrementStatus($db, "XbmcTVShowsSeasonsStart", 1);           
        }
    }
    else {
        IncrementStatus($db, "XbmcTVShowsSeasonsStart", 1);
    }
    
    //CloseDatabase($db);       
}

/*
 * Function:	ImportTVShowEpisode
 *
 * Created on Oct 26, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Import the TV Show Episode. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportTVShowEpisode($db, $aError, $poster, $aResult)
{   
    //$db = OpenDatabase();     
    
    if (empty($aError))
    {
        $aEpisode = ConvertTVShowEpisode($aResult["episodedetails"]);
     
        SaveImage($aEpisode[0], $poster, "../".cEPISODESTHUMBS);
        InsertTVShowEpisode($db, $aEpisode);
    
        UpdateStatus($db, "XbmcSlack", 0);
        IncrementStatus($db, "XbmcEpisodesStart", 1);
        IncrementStatus($db, "ImportCounter", 1);
    }
    else if ($aError["code"] == -32602) // TVShow not found, continue with the next one.
    { 
       UpdateStatus($db, "XbmcSlack", 1);
       IncrementStatus($db, "XbmcEpisodesStart", 1); 
    }
    
    //CloseDatabase($db);       
}

/*
 * Function:	RefreshTVShow
 *
 * Created on Sep 09, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Refresh the tv show. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult, $id
 * Out: -
 *
 */
function RefreshTVShow($db, $aError, $poster, $fanart, $aResult, $id)
{   
    //$db = OpenDatabase();      
    
    if (empty($aError))
    {
        $aGenres = $aResult["tvshowdetails"]["genre"];
        $aTVShow = ConvertTVShow($aResult["tvshowdetails"]);
    
        DeleteFile("../".cTVSHOWSTHUMBS."/".$aTVShow[0].".jpg");
        DeleteFile("../".cTVSHOWSFANART."/".$aTVShow[0].".jpg");
        
        ResizeAndSaveImage($aTVShow[0], $poster, "../".cTVSHOWSTHUMBS, 125, 175);    
        UpdateTVShow($db, $id, $aTVShow);
        ResizeAndSaveImage($aTVShow[0], $fanart, "../".cTVSHOWSFANART, 450, 280); //562, 350 //675, 420 
        InsertGenres($db, $aGenres, "tvshows");
    
        UpdateStatus($db, "RefreshReady", 1);
    } 
    
    //CloseDatabase($db);     
}

/*
 * Function:	RefreshTVShowSeason
 *
 * Created on Dec 02, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Refresh the tv show season. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult, $id
 * Out: -
 *
 */
function RefreshTVShowSeason($db, $aError, $poster, $aResult, $id)
{   
    //$db = OpenDatabase();      
    
    if (empty($aError))
    {
        $aSeason = ConvertTVShowSeason($aResult["seasons"][0]);
    
        DeleteFile("../".cSEASONSTHUMBS."/".$aSeason[0]."_".$aSeason[4].".jpg");

        ResizeAndSaveImage($aSeason[0]."_".$aSeason[4], $poster, "../".cSEASONSTHUMBS, 125, 175);
        UpdateTVShowSeason($db, $id, $aSeason);
        
        UpdateStatus($db, "RefreshReady", 1);
    }  
    
    //CloseDatabase($db);      
}

/*
 * Function:	RefreshTVShowEpisode
 *
 * Created on Nov 29, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Refresh the tv show episode. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult, id
 * Out: -
 *
 */
function RefreshTVShowEpisode($db, $aError, $poster, $aResult, $id)
{   
    //$db = OpenDatabase();    
    
    if (empty($aError))
    {
        $aEpisode = ConvertTVShowEpisode($aResult["episodedetails"]);
    
        DeleteFile("../".cEPISODESTHUMBS."/".$aEpisode[0].".jpg");  
        
        SaveImage($aEpisode[0], $poster, "../".cEPISODESTHUMBS);        
        UpdateTVShowEpisode($db, $id, $aEpisode);
    
        UpdateStatus($db, "RefreshReady", 1);
    } 
    
   //CloseDatabase($db);       
}

/*
 * Function:	ImportAlbum
 *
 * Created on Aug 24, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Import the music album. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportAlbum($db, $aError, $poster, $aResult)
{   
    //$db = OpenDatabase();    
    
    if (empty($aError))
    {
        $aGenres = $aResult["albumdetails"]["genre"]; 
        $aAlbum = ConvertAlbum($aResult["albumdetails"]);
    
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSTHUMBS, 125, 125);
       
        $id = InsertAlbum($db, $aAlbum);
        
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSCOVERS, 300, 300);
        
        InsertGenres($db, $aGenres, "music");
        InsertGenreToMedia($db, $aGenres, $id, "music");
    
        UpdateStatus($db, "XbmcSlack", 0);
        IncrementStatus($db, "XbmcMusicStart", 1);
        IncrementStatus($db, "ImportCounter", 1); 
    }
    else if ($aError["code"] == -32602) // Album not found, continue with the next one.
    { 
       UpdateStatus($db, "XbmcSlack", 1);
       IncrementStatus($db, "XbmcMusicStart", 1); 
    }  
    
    //CloseDatabase($db);    
}

/*
 * Function:	RefreshAlbum
 *
 * Created on Sep 09, 2013
 * Updated on Jan 03, 2014
 *
 * Description: Refresh the music album details. 
 *
 * In:  $db, $aError, $poster, $aResult, $id
 * Out: -
 *
 */
function RefreshAlbum($db, $aError, $poster, $aResult, $id)
{ 
    //$db = OpenDatabase();    
    
    if (empty($aError))
    {    
        $aGenres = $aResult["albumdetails"]["genre"]; 
        $aAlbum = ConvertAlbum($aResult["albumdetails"]);
    
        DeleteFile("../".cALBUMSTHUMBS."/".$aAlbum[0].".jpg");
        DeleteFile("../".cALBUMSCOVERS."/".$aAlbum[0].".jpg");
              
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSTHUMBS, 125, 125);
        UpdateAlbum($db, $id, $aAlbum);
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSCOVERS, 300, 300);
        InsertGenres($db, $aGenres, "music");
    
        UpdateStatus($db, "RefreshReady", 1);
    }
    
    //CloseDatabase($db);    
}

/*
 * Function:	SaveImage
 *
 * Created on Aug 11, 2013
 * Updated on Aug 11, 2013
 *
 * Description: Save image.
 *
 * In:  $id, $image, $path
 * Out: Saved image.
 *
 */
function SaveImage($id, $image, $path)
{
    if ($image) 
    {
        $image = explode('base64,',$image); 
        file_put_contents($path.'/'.$id.'.jpg', base64_decode($image[1]));
    }    
}

/*
 * Function:	ResizeAndSaveImage
 *
 * Created on Aug 17, 2013
 * Updated on Sep 02, 2013
 *
 * Description: Resize and save image as jpg.
 *
 * In:  $id, $image, $path
 * Out: Resize jpg image.
 *
 */
function ResizeAndSaveImage($id, $image, $path, $w, $h)
{
    if ($image) {
        ResizeJpegImage($image, $w, $h, $path."/".$id.".jpg");
    }    
}