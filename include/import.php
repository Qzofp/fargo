<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    import.php
 *
 * Created on Jul 15, 2013
 * Updated on Nov 29, 2013
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

$login = CheckImportKey();
if($login)
{
    $aData = ReceiveDataFromXbmc();
    ProcessDataFromXbmc($aData);
}
else 
{
    $aJson = LogEvent("Warning", "Unauthorized import call!");
    echo json_encode($aJson);
}

//debug
//echo "<pre>";
//print_r($aData);
//echo "</pre></br>";
    
/////////////////////////////////////////    Import Functions    //////////////////////////////////////////    

/*
 * Function:	CheckImportKey()
 *
 * Created on Sep 28, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Check if import key is valid. This proves that the users has logged in. 
 *
 * In:  -
 * Out: $login
 *
 */
function CheckImportKey()
{
    $login = false;
    
    $key = null;    
    if (isset($_POST["key"]) && !empty($_POST["key"]))
    {
        $key = $_POST["key"];
    }      
    
    $import = GetStatus("ImportKey");
    if ($import == $key){    
        $login = true;
    }
    
    return $login;
}

/*
 * Function:	ReceiveDataFromXbmc
 *
 * Created on Jul 15, 2013
 * Updated on Oct 13, 2013
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
 * Updated on Nov 29, 2013
 *
 * Description: Process data from XBMC. 
 *
 * In:  $aData
 * Out: -
 *
 */
function ProcessDataFromXbmc($aData)
{

    switch($aData["id"])
    {
        // libMoviesCounter -> library id = 1.
        case 1  : UpdateStatus("XbmcMoviesEnd", $aData["result"]["movies"][0]["movieid"]);
                  break;
             
        // libMovies Import -> library id = 2.                   
        case 2  : ImportMovie($aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                  break;
        
        // libMovies Refresh -> library id = 3.     
        case 3  : RefreshMovie($aData["error"], $aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);
                  break;
        
        // libMovieSetsCounter -> library id = 4.    
        case 4  : UpdateStatus("XbmcSetsEnd", $aData["result"]["sets"][0]["setid"]); 
                  break;
              
        // libMovieSets Import -> library id = 5.                   
        case 5  : ImportMovieSet($aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                  break;
              
        // libMovieSets Reset -> library id = 6.
        case 6  : RefreshMovieSet($aData["error"], $aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);
                  break;
             
        // libTVShowsCounter -> library id = 11.  
        case 11 : UpdateStatus("XbmcTVShowsEnd", $aData["result"]["tvshows"][0]["tvshowid"]);   
                  break;
        
        // libTVShows Import -> library id = 12.
        case 12 : ImportTVShow($aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                  break;
             
        // libTVShows Refresh -> library id = 13.
        case 13 : RefreshTVShow($aData["error"], $aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);
                  break;
             
        // libTVShowSeasonsCounter -> library id = 14. Note TV Seasons uses the same counter as TV Shows.
        case 14 : UpdateStatus("XbmcTVShowsSeasonsEnd", $aData["result"]["tvshows"][0]["tvshowid"]);   
                  break;           
              
        // libSeasonsCounter -> library id = 15.
        case 15 : UpdateStatus("XbmcSeasonsEnd", $aData["result"]["limits"]["total"]);
                  break;   
              
        // libTVShowSeasons Import -> library id = 16.
        case 16 : ImportTVShowSeason($aData["error"], $aData["poster"], $aData["result"]);
                  break;  
              
        // libTVShowEpisodesCounter -> library id = 31.  
        case 31 : UpdateStatus("XbmcEpisodesEnd", $aData["result"]["episodes"][0]["episodeid"]);   
                  break;
                       
        // libTVShowEpisode Import -> library id = 32.
        case 32 : ImportTVShowEpisode($aData["error"], $aData["poster"], $aData["result"]);
                  break;
              
        // libTVShowEpisode Refresh -> library id = 33.
        case 33 : RefreshTVShowEpisode($aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;         
                            
        // libAlbumsCounter -> library id = 41.  
        case 41 : UpdateStatus("XbmcMusicEnd", $aData["result"]["albums"][0]["albumid"]); 
                  break;
        
        // libAlbums Import -> library id = 42.
        case 42 : ImportAlbum($aData["error"], $aData["poster"], $aData["result"]);
                  break;
             
        // libAlbums Refresh -> library id = 43.
        case 43 : RefreshAlbum($aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;                       
    }
}

/*
 * Function:	ImportMovie
 *
 * Created on Jul 15, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Import the movie. 
 *
 * In:  $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ImportMovie($aError, $poster, $fanart, $aResult)
{   
    if (empty($aError))
    {
        //$aMovie  = $aResult["moviedetails"];
        $aGenres = $aResult["moviedetails"]["genre"]; //$aMovie["genre"];
        $aMovie  = ConvertMovie($aResult["moviedetails"]);
 
        ResizeAndSaveImage($aMovie[0], $poster, "../".cMOVIESTHUMBS, 125, 175); //200, 280  
        InsertMovie($aMovie);
        InsertGenreToMedia($aGenres, "movies");
        ResizeAndSaveImage($aMovie[0], $fanart, "../".cMOVIESFANART, 562, 350); //675, 420 
    
        UpdateStatus("XbmcSlack", 0);
        IncrementStatus("XbmcMoviesStart", 1);
        IncrementStatus("ImportCounter", 1); 
    }
    else if ($aError["code"] == -32602) // Movie not found, continue with the next one.
    { 
        UpdateStatus("XbmcSlack", 1);
        IncrementStatus("XbmcMoviesStart", 1); 
    }
}

/*
 * Function:	ImportMovieSet
 *
 * Created on Oct 13, 2013
 * Updated on Oct 28, 2013
 *
 * Description: Import the movie set. 
 *
 * In:  $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ImportMovieSet($aError, $poster, $fanart, $aResult)
{   
    if (empty($aError))
    {
        $aMovie = ConvertMovieSet($aResult["setdetails"]);
 
        ResizeAndSaveImage($aMovie[0], $poster, "../".cSETSTHUMBS, 125, 175); //200, 280      
        InsertMovieSet($aMovie);

        ResizeAndSaveImage($aMovie[0], $fanart, "../".cSETSFANART, 562, 350); //675, 420 
        
        UpdateStatus("XbmcSlack", 0);
        IncrementStatus("XbmcSetsStart", 1); 
        IncrementStatus("ImportCounter", 1);
    }
    else if ($aError["code"] == -32602) // Movie not found, continue with the next one.
    { 
       UpdateStatus("XbmcSlack", 1);
       IncrementStatus("XbmcSetsStart", 1); 
    }
}


/*
 * Function:	RefreshMovie
 *
 * Created on Sep 08, 2013
 * Updated on Nov 23, 2013
 *
 * Description: Refresh the movie. 
 *
 * In:  $aError, $poster, $fanart, $aResult, $fargoid
 * Out: -
 *
 */
function RefreshMovie($aError, $poster, $fanart, $aResult, $fargoid)
{
    if (empty($aError))
    {
        $aMovie  = ConvertMovie($aResult["moviedetails"]);
    
        DeleteFile("../".cMOVIESTHUMBS."/".$aMovie[0].".jpg");
        DeleteFile("../".cMOVIESFANART."/".$aMovie[0].".jpg");
    
        UpdateMovie($fargoid, $aMovie);
       
        ResizeAndSaveImage($aMovie[0], $fanart, "../".cMOVIESFANART, 562, 350);  //675, 420    
        ResizeAndSaveImage($aMovie[0], $poster, "../".cMOVIESTHUMBS, 125, 175);  //200, 280
 
        UpdateStatus("RefreshReady", 1);
    }
}

/*
 * Function:	RefreshMovieSet
 *
 * Created on Nov 23, 2013
 * Updated on Nov 23, 2013
 *
 * Description: Refresh the movie set. 
 *
 * In:  $aError, $poster, $fanart, $aResult, $fargoid
 * Out: -
 *
 */
function RefreshMovieSet($aError, $poster, $fanart, $aResult, $fargoid)
{
    if (empty($aError))
    {  
        $aMovie = ConvertMovieSet($aResult["setdetails"]);
    
        DeleteFile("../".cSETSTHUMBS."/".$aMovie[0].".jpg");
        DeleteFile("../".cSETSFANART."/".$aMovie[0].".jpg");
    
        UpdateMovieSet($fargoid, $aMovie);
        
        ResizeAndSaveImage($aMovie[0], $poster, "../".cSETSTHUMBS, 125, 175); //200, 280  
        ResizeAndSaveImage($aMovie[0], $fanart, "../".cSETSFANART, 562, 350); //675, 420 
 
        UpdateStatus("RefreshReady", 1);
    }
}

/*
 * Function:	ImportTVShow
 *
 * Created on Aug 24, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Import the tv show. 
 *
 * In:  $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ImportTVShow($aError, $poster, $fanart, $aResult)
{   
    if (empty($aError))
    {
        //$aTVShow = $aResult["tvshowdetails"];
        $aGenres = $aResult["tvshowdetails"]["genre"];
        $aTVShow = ConvertTVShow($aResult["tvshowdetails"]);
     
        ResizeAndSaveImage($aTVShow[0], $poster, "../".cTVSHOWSTHUMBS, 125, 175);
        InsertTVShow($aTVShow);
        InsertGenreToMedia($aGenres, "tvshows");
        ResizeAndSaveImage($aTVShow[0], $fanart, "../".cTVSHOWSFANART, 562, 350);
    
        UpdateStatus("XbmcSlack", 0);
        IncrementStatus("XbmcTVShowsStart", 1);
        IncrementStatus("ImportCounter", 1);
    }
    else if ($aError["code"] == -32602) // TVShow not found, continue with the next one.
    { 
       UpdateStatus("XbmcSlack", 1);
       IncrementStatus("XbmcTVShowsStart", 1); 
    }    
}

/*
 * Function:	ImportTVShowSeason
 *
 * Created on Oct 20, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Import the tv show season. 
 *
 * In:  $poster, $aResult
 * Out: -
 *
 */
function ImportTVShowSeason($aError, $poster, $aResult)
{   
    if (empty($aError))
    {    
        $aSeason = ConvertTVShowSeason($aResult["seasons"][0]);

        ResizeAndSaveImage($aSeason[0]."_".$aSeason[4], $poster, "../".cSEASONSTHUMBS, 125, 175);

        InsertTVShowSeason($aSeason);    
        IncrementStatus("XbmcSeasonsStart", 1);
        IncrementStatus("ImportCounter", 1);
        if ($aResult["limits"]["end"] >= $aResult["limits"]["total"]) {
            IncrementStatus("XbmcTVShowsSeasonsStart", 1);           
        }
    }
    else {
        IncrementStatus("XbmcTVShowsSeasonsStart", 1);
    }
}

/*
 * Function:	ImportTVShowEpisode
 *
 * Created on Oct 26, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Import the TV Show Episode. 
 *
 * In:  $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportTVShowEpisode($aError, $poster, $aResult)
{   
    if (empty($aError))
    {
        $aEpisode = ConvertTVShowEpisode($aResult["episodedetails"]);
     
        SaveImage($aEpisode[0], $poster, "../".cEPISODESTHUMBS);
        InsertTVShowEpisode($aEpisode);
    
        UpdateStatus("XbmcSlack", 0);
        IncrementStatus("XbmcEpisodesStart", 1);
        IncrementStatus("ImportCounter", 1);
    }
    else if ($aError["code"] == -32602) // TVShow not found, continue with the next one.
    { 
       UpdateStatus("XbmcSlack", 1);
       IncrementStatus("XbmcEpisodesStart", 1); 
    }    
}

/*
 * Function:	RefreshTVShow
 *
 * Created on Sep 09, 2013
 * Updated on Nov 23, 2013
 *
 * Description: Refresh the tv show. 
 *
 * In:  $aError, $poster, $fanart, $aResult, $id
 * Out: -
 *
 */
function RefreshTVShow($aError, $poster, $fanart, $aResult, $id)
{   
    if (empty($aError))
    {
        $aTVShow = ConvertTVShow($aResult["tvshowdetails"]);
    
        DeleteFile("../".cTVSHOWSTHUMBS."/".$aTVShow[0].".jpg");
        DeleteFile("../".cTVSHOWSFANART."/".$aTVShow[0].".jpg");
    
        UpdateTVShow($id, $aTVShow);
    
        ResizeAndSaveImage($aTVShow[0], $fanart, "../".cTVSHOWSFANART, 562, 350);
        ResizeAndSaveImage($aTVShow[0], $poster, "../".cTVSHOWSTHUMBS, 125, 175);
    
        UpdateStatus("RefreshReady", 1);
    }        
}

/*
 * Function:	RefreshTVShowEpisode
 *
 * Created on Nov 29, 2013
 * Updated on Nov 29, 2013
 *
 * Description: Refresh the tv show episode. 
 *
 * In:  $aError, $poster, $fanart, $aResult, id
 * Out: -
 *
 */
function RefreshTVShowEpisode($aError, $poster, $aResult, $id)
{   
    if (empty($aError))
    {
        $aEpisode = ConvertTVShowEpisode($aResult["episodedetails"]);
    
        DeleteFile("../".cEPISODESTHUMBS."/".$aEpisode[0].".jpg");    
        UpdateTVShowEpisode($id, $aEpisode);
        SaveImage($aEpisode[0], $poster, "../".cEPISODESTHUMBS);
    
        UpdateStatus("RefreshReady", 1);
    }        
}

/*
 * Function:	ImportAlbum
 *
 * Created on Aug 24, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Import the music album. 
 *
 * In:  $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportAlbum($aError, $poster, $aResult)
{   
    if (empty($aError))
    {
        //$aAlbum = $aResult["albumdetails"];
        $aGenres = $aResult["albumdetails"]["genre"]; 
        $aAlbum = ConvertAlbum($aResult["albumdetails"]);
    
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSTHUMBS, 125, 125);
       
        InsertAlbum($aAlbum);
        InsertGenreToMedia($aGenres, "music");
    
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSCOVERS, 300, 300);
    
        UpdateStatus("XbmcSlack", 0);
        IncrementStatus("XbmcMusicStart", 1);
        IncrementStatus("ImportCounter", 1); 
    }
    else if ($aError["code"] == -32602) // Album not found, continue with the next one.
        { 
       UpdateStatus("XbmcSlack", 1);
       IncrementStatus("XbmcMusicStart", 1); 
    }     
}

/*
 * Function:	RefreshAlbum
 *
 * Created on Sep 09, 2013
 * Updated on Nov 23, 2013
 *
 * Description: Refresh the music album details. 
 *
 * In:  $aError, $poster, $aResult, $id
 * Out: -
 *
 */
function RefreshAlbum($aError, $poster, $aResult, $id)
{ 
    if (empty($aError))
    {    
        $aAlbum = ConvertAlbum($aResult["albumdetails"]);
    
        DeleteFile("../".cALBUMSTHUMBS."/".$aAlbum[0].".jpg");
        DeleteFile("../".cALBUMSCOVERS."/".$aAlbum[0].".jpg");
      
        UpdateAlbum($id, $aAlbum);
    
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSCOVERS, 300, 300);
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSTHUMBS, 125, 125);
    
        UpdateStatus("RefreshReady", 1);
    }
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