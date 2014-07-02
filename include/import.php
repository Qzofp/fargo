<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    import.php
 *
 * Created on Jul 15, 2013
 * Updated on Jul 01, 2014
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
 * Updated on Jun 28, 2014
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
        // libMediaCounter -> library id = 1.
        case 1  : UpdateStatus($db, "ImportEnd", $aData["result"]["limits"]["total"]);
                  break;
             
        // libMovies Import -> library id = 2.                   
        case 2  : ImportMovie($db, $aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                  break;
        
        // libMovies Refresh -> library id = 3.     
        case 3  : RefreshMovie($db, $aData["error"], $aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);
                  break;
              
        // libMovieSets Import -> library id = 5.                   
        case 5  : ImportMovieSet($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;
              
        // libMovieSets Reset -> library id = 6.
        case 6  : RefreshMovieSet($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;
             
        // libTVShowsCounter -> library id = 11.  
        case 11 : UpdateStatus($db, "XbmcTVShowsEnd", $aData["result"]["limits"]["total"]);
                  break;
        
        // libTVShows Import -> library id = 12.
        case 12 : ImportTVShow($db, $aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                  break;
             
        // libTVShows Refresh -> library id = 13.
        case 13 : RefreshTVShow($db, $aData["error"], $aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);
                  break;
              
        // libTVShowSeasons Import -> library id = 16.
        case 16 : ImportTVShowSeason($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;  
              
        // libTVShowSeasons Refresh -> library id = 17.    
        case 17 : RefreshTVShowSeason($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;        
              
        // libTVShowEpisodesCounter -> library id = 31.  
        case 31 : UpdateStatus($db, "XbmcEpisodesEnd", $aData["result"]["limits"]["total"]);
                  break;
                       
        // libTVShowEpisode Import -> library id = 32.
        case 32 : ImportTVShowEpisode($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;
              
        // libTVShowEpisode Refresh -> library id = 33.
        case 33 : RefreshTVShowEpisode($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;
        
        // libAlbums Import -> library id = 42.
        case 42 : ImportAlbum($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;
             
        // libAlbums Refresh -> library id = 43.
        case 43 : RefreshAlbum($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;
              
        // libSongs Import -> library id = 52.
        case 52 : ImportSong($db, $aData["error"], $aData["poster"], $aData["result"]);
                  break;
             
        // libSongs Refresh -> library id = 53.
        case 53 : RefreshSong($db, $aData["error"], $aData["poster"], $aData["result"], $aData["fargoid"]);
                  break;                
    }
}

/*
 * Function:	ImportMovie
 *
 * Created on Jul 15, 2013
 * Updated on Jul 01, 2014
 *
 * Description: Import the movie. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ImportMovie($db, $aError, $poster, $fanart, $aResult)
{   
    if (empty($aError))
    {        
        $aGenres = $aResult["moviedetails"]["genre"]; 
        $aMovie  = ConvertMovie($aResult["moviedetails"]);
        
        ResizeAndSaveImage($aMovie[28], $poster, "../".cMOVIESART, 125, 175);
        list($dkey, $id) = InsertMovie($db, $aMovie);
        if ($dkey == 1) // No dublicate key found.
        {             
            ResizeAndSaveImage($aMovie[29], $fanart, "../".cMOVIESART, 450, 280); 
            
            InsertGenres($db, $aGenres, "movies"); 
            InsertGenreToMedia($db, $aGenres, $id, "movies");
            
            IncrementStatus($db, "ImportCounter", 1);            
            UpdateStatus($db, "ImportStatus", cTRANSFER_READY);
        }
        else 
        {   
            if ($dkey == 2) {
                ResizeAndSaveImage($aMovie[29], $fanart, "../".cMOVIESART, 450, 280);  
            }
      
            UpdateStatus($db, "ImportStatus", cTRANSFER_DUPLICATE);
        }
        
        IncrementStatus($db, "ImportStart", 1);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) {
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    }
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }
}

/*
 * Function:	ImportMovieSet
 *
 * Created on Oct 13, 2013
 * Updated on Jun 28, 2014
 *
 * Description: Import the movie set. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportMovieSet($db, $aError, $poster, $aResult)
{  
    if (empty($aError))
    {
        $aMovie = ConvertMovieSet($aResult["setdetails"]);
 
        ResizeAndSaveImage($aMovie[0], $poster, "../".cSETSTHUMBS, 125, 175); //200, 280
        $dkey = InsertMovieSet($db, $aMovie);
        if ($dkey == 1) // No dublicate key found.
        {  
            IncrementStatus($db, "ImportCounter", 1);
            UpdateStatus($db, "ImportStatus", cTRANSFER_READY);            
        }        
        else {
            UpdateStatus($db, "ImportStatus", cTRANSFER_DUPLICATE);
        }
        
        IncrementStatus($db, "ImportStart", 1);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }    
}

/*
 * Function:	RefreshMovie
 *
 * Created on Sep 08, 2013
 * Updated on Feb 18, 2014
 *
 * Description: Refresh the movie. 
 *
 * In:  $aError, $poster, $fanart, $aResult, $fargoid
 * Out: -
 *
 */
function RefreshMovie($db, $aError, $poster, $fanart, $aResult, $fargoid)
{
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
        
        UpdateStatus($db, "ImportStatus", cTRANSFER_READY);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { // Not found.
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND);
    }
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error.
    }    
}

/*
 * Function:	RefreshMovieSet
 *
 * Created on Nov 23, 2013
 * Updated on Jun 28, 2014
 *
 * Description: Refresh the movie set. 
 *
 * In:  $db, $aError, $poster, $aResult, $fargoid
 * Out: -
 *
 */
function RefreshMovieSet($db, $aError, $poster, $aResult, $fargoid)
{
    if (empty($aError))
    {  
        $aMovie = ConvertMovieSet($aResult["setdetails"]);
    
        DeleteFile("../".cSETSTHUMBS."/".$aMovie[0].".jpg");
    
        UpdateMovieSet($db, $fargoid, $aMovie);   
        ResizeAndSaveImage($aMovie[0], $poster, "../".cSETSTHUMBS, 125, 175); //200, 280  

        UpdateStatus($db, "ImportStatus", cTRANSFER_READY);
    } 
    else if ($aError["code"] == cTRANSFER_INVALID) {  // Not found.
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND);
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error.
    }    
}

/*
 * Function:	ImportTVShow
 *
 * Created on Aug 24, 2013
 * Updated on Jun 28, 2014
 *
 * Description: Import the tv show. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ImportTVShow($db, $aError, $poster, $fanart, $aResult)
{   
    if (empty($aError))
    {
        $aGenres = $aResult["tvshowdetails"]["genre"];
        $aTVShow = ConvertTVShow($aResult["tvshowdetails"]);
     
        ResizeAndSaveImage($aTVShow[0], $poster, "../".cTVSHOWSTHUMBS, 125, 175);
        list($dkey, $id) = InsertTVShow($db, $aTVShow);
        if ($dkey == 1) // No dublicate key found.
        { 
            ResizeAndSaveImage($aTVShow[0], $fanart, "../".cTVSHOWSFANART, 450, 280); //562, 350 //675, 420 
            
            InsertGenres($db, $aGenres, "tvshows");
            InsertGenreToMedia($db, $aGenres, $id, "tvshows");
            
            IncrementStatus($db, "ImportCounter", 1);
            UpdateStatus($db, "ImportStatus", cTRANSFER_READY);            
        } 
        else 
        {
            if ($dkey == 2) {
                ResizeAndSaveImage($aTVShow[0], $fanart, "../".cTVSHOWSFANART, 450, 280); //562, 350 //675, 420
            }
            UpdateStatus($db, "ImportStatus", cTRANSFER_DUPLICATE);
        }        
        
        IncrementStatus($db, "ImportStart", 1);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }       
}

/*
 * Function:	ImportTVShowSeason
 *
 * Created on Oct 20, 2013
 * Updated on Jun 28, 2014
 *
 * Description: Import the tv show season. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportTVShowSeason($db, $aError, $poster, $aResult)
{      
    if (empty($aError))
    {    
        $aSeason = ConvertTVShowSeason($db, $aResult["seasondetails"]);

        ResizeAndSaveImage($aSeason[0], $poster, "../".cSEASONSTHUMBS, 125, 175);
        $dkey = InsertTVShowSeason($db, $aSeason);
        if ($dkey == 1) // No dublicate key found.
        { 
            IncrementStatus($db, "ImportCounter", 1);
            UpdateStatus($db, "ImportStatus", cTRANSFER_READY);            
        }
        else {    
            UpdateStatus($db, "ImportStatus", cTRANSFER_DUPLICATE);
        }        
        
        IncrementStatus($db, "ImportStart", 1);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }        
}

/*
 * Function:	ImportTVShowEpisode
 *
 * Created on Oct 26, 2013
 * Updated on Jun 28, 2014
 *
 * Description: Import the TV Show Episode. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportTVShowEpisode($db, $aError, $poster, $aResult)
{   
    if (empty($aError))
    {
        $aEpisode = ConvertTVShowEpisode($db, $aResult["episodedetails"]);
     
        SaveImage($aEpisode[0], $poster, "../".cEPISODESTHUMBS);
        $dkey = InsertTVShowEpisode($db, $aEpisode);
        if ($dkey == 1) // No dublicate key found.
        { 
            IncrementStatus($db, "ImportCounter", 1);
            UpdateStatus($db, "ImportStatus", cTRANSFER_READY);            
        }        
        else {
            UpdateStatus($db, "ImportStatus", cTRANSFER_DUPLICATE);
        }    

        IncrementStatus($db, "ImportStart", 1);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }  
}

/*
 * Function:	RefreshTVShow
 *
 * Created on Sep 09, 2013
 * Updated on Feb 18, 2014
 *
 * Description: Refresh the tv show. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult, $id
 * Out: -
 *
 */
function RefreshTVShow($db, $aError, $poster, $fanart, $aResult, $id)
{     
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
    
        UpdateStatus($db, "ImportStatus", cTRANSFER_READY);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }
}

/*
 * Function:	RefreshTVShowSeason
 *
 * Created on Dec 02, 2013
 * Updated on Jun 26, 2014
 *
 * Description: Refresh the tv show season. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult, $id
 * Out: -
 *
 */
function RefreshTVShowSeason($db, $aError, $poster, $aResult, $id)
{
    if (empty($aError))
    {
        $aSeason = ConvertTVShowSeason($db, $aResult["seasondetails"]);
        
        DeleteFile("../".cSEASONSTHUMBS."/".$aSeason[0].".jpg");

        ResizeAndSaveImage($aSeason[0], $poster, "../".cSEASONSTHUMBS, 125, 175);
        UpdateTVShowSeason($db, $id, $aSeason);
        
        UpdateStatus($db, "ImportStatus", cTRANSFER_READY);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }     
}

/*
 * Function:	RefreshTVShowEpisode
 *
 * Created on Nov 29, 2013
 * Updated on Jun 26, 2014
 *
 * Description: Refresh the tv show episode. 
 *
 * In:  $db, $aError, $poster, $fanart, $aResult, id
 * Out: -
 *
 */
function RefreshTVShowEpisode($db, $aError, $poster, $aResult, $id)
{   
    if (empty($aError))
    {
        $aEpisode = ConvertTVShowEpisode($db, $aResult["episodedetails"]);
    
        DeleteFile("../".cEPISODESTHUMBS."/".$aEpisode[0].".jpg");  
        
        SaveImage($aEpisode[0], $poster, "../".cEPISODESTHUMBS);        
        UpdateTVShowEpisode($db, $id, $aEpisode);
    
        UpdateStatus($db, "ImportStatus", cTRANSFER_READY);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }
}

/*
 * Function:	ImportAlbum
 *
 * Created on Aug 24, 2013
 * Updated on Jun 28, 2014
 *
 * Description: Import the music album. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportAlbum($db, $aError, $poster, $aResult)
{   
    if (empty($aError))
    {
        $aGenres = $aResult["albumdetails"]["genre"]; 
        $aAlbum = ConvertAlbum($aResult["albumdetails"]);
    
        ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSTHUMBS, 125, 125);
        list($dkey, $id) = InsertAlbum($db, $aAlbum);
        if ($dkey == 1) // No dublicate key found.
        { 
            ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSCOVERS, 300, 300);
        
            InsertGenres($db, $aGenres, "music");
            InsertGenreToMedia($db, $aGenres, $id, "music");

            IncrementStatus($db, "ImportCounter", 1); 
            UpdateStatus($db, "ImportStatus", cTRANSFER_READY);            
        }        
        else 
        {
            if ($dkey == 2) {
                ResizeAndSaveImage($aAlbum[0], $poster, "../".cALBUMSCOVERS, 300, 300);
            }
            UpdateStatus($db, "ImportStatus", cTRANSFER_DUPLICATE);
        }   
        
        IncrementStatus($db, "ImportStart", 1);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }   
}

/*
 * Function:	RefreshAlbum
 *
 * Created on Sep 09, 2013
 * Updated on Feb 18, 2014
 *
 * Description: Refresh the music album details. 
 *
 * In:  $db, $aError, $poster, $aResult, $id
 * Out: -
 *
 */
function RefreshAlbum($db, $aError, $poster, $aResult, $id)
{ 
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
    
        UpdateStatus($db, "ImportStatus", cTRANSFER_READY);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }    
}

/*
 * Function:	ImportSong
 *
 * Created on Jun 28, 2014
 * Updated on Jun 28, 2014
 *
 * Description: Import the music album. 
 *
 * In:  $db, $aError, $poster, $aResult
 * Out: -
 *
 */
function ImportSong($db, $aError, $poster, $aResult)
{   
    if (empty($aError))
    {
        $aGenres = $aResult["songdetails"]["genre"]; 
        $aSong = ConvertSong($db, $aResult["songdetails"]);
    
        ResizeAndSaveImage($aSong[0], $poster, "../".cSONGSTHUMBS, 125, 125);
        list($dkey, $id) = InsertSong($db, $aSong);
        if ($dkey == 1) // No dublicate key found.
        { 
            ResizeAndSaveImage($aSong[0], $poster, "../".cSONGSCOVERS, 300, 300);
        
            InsertGenres($db, $aGenres, "music");
            InsertGenreToMedia($db, $aGenres, $id, "music");

            IncrementStatus($db, "ImportCounter", 1); 
            UpdateStatus($db, "ImportStatus", cTRANSFER_READY);            
        }        
        else 
        {
            if ($dkey == 2) {
                ResizeAndSaveImage($aSong[0], $poster, "../".cSONGSCOVERS, 300, 300);
            }
            UpdateStatus($db, "ImportStatus", cTRANSFER_DUPLICATE);
        }   
        
        IncrementStatus($db, "ImportStart", 1);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
    }   
}

/*
 * Function:	RefreshSong
 *
 * Created on Jun 30, 2014
 * Updated on Jun 30, 2014
 *
 * Description: Refresh the music song details. 
 *
 * In:  $db, $aError, $poster, $aResult, $id
 * Out: -
 *
 */
function RefreshSong($db, $aError, $poster, $aResult, $id)
{ 
    if (empty($aError))
    {    
        $aGenres = $aResult["songdetails"]["genre"]; 
        $aSong = ConvertSong($db, $aResult["songdetails"]);
    
        DeleteFile("../".cSONGSTHUMBS."/".$aSong[0].".jpg");
        DeleteFile("../".cSONGSCOVERS."/".$aSong[0].".jpg");
              
        ResizeAndSaveImage($aSong[0], $poster, "../".cSONGSTHUMBS, 125, 125);
        UpdateSong($db, $id, $aSong);
        ResizeAndSaveImage($aSong[0], $poster, "../".cSONGSCOVERS, 300, 300);
        InsertGenres($db, $aGenres, "music");
    
        UpdateStatus($db, "ImportStatus", cTRANSFER_READY);
    }
    else if ($aError["code"] == cTRANSFER_INVALID) { 
        UpdateStatus($db, "ImportStatus", cTRANSFER_NOT_FOUND); // Not found, not used yet, only for refresh.
    } 
    else {
        UpdateStatus($db, "ImportStatus", cTRANSFER_ERROR); // Error, not used yet, only for refresh.
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
 * Updated on Jul 01, 2014
 *
 * Description: Resize and save image as jpg.
 *
 * In:  $id, $image, $path
 * Out: Resize jpg image.
 *
 */
function ResizeAndSaveImage($id, $image, $path, $w, $h)
{
    // Create hex directories (0, 1, 2, 3... a, b, c, d, e, f).
    $dir = $path."/".$id[0];
    if (!file_exists($dir)) {
        mkdir($dir, 0777);
    }
    
    // Resize and save image.
    $file = $dir."/".$id.".jpg";
    if ($image && !file_exists($file)) {
        ResizeJpegImage($image, $w, $h, $file);
    }    
}