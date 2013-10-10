<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    import.php
 *
 * Created on Jul 15, 2013
 * Updated on Oct 07, 2013
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
 * Updated on Oct 07, 2013
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
    
    /*
    $aData["action"] = null;
    if (isset($_POST["action"]) && !empty($_POST["action"]))
    {
        $aData["action"] = $_POST["action"];
    } 
    */   
    
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
 * Updated on Oct 07, 2013
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
        case 1 : UpdateStatus("XbmcMoviesEnd", $aData["result"]["movies"][0]["movieid"]);
                 break;
             
        // libMovies -> library id = 2.                   
        case 2 : ProcessMovie($aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                 break;                    
             
        // libTVShowsCounter -> library id = 3.  
        case 3 : UpdateStatus("XbmcTVShowsEnd", $aData["result"]["tvshows"][0]["tvshowid"]);   
                 break;
        
        // libTVShows -> library id = 4.
        case 4 : ProcessTVShow($aData["error"], $aData["poster"], $aData["fanart"], $aData["result"]);
                 break;
             
        // libAlbumsCounter -> library id = 5.  
        case 5 : UpdateStatus("XbmcMusicEnd", $aData["result"]["albums"][0]["albumid"]); 
                 break;
        
        // libAlbums -> library id = 6.
        case 6 : ProcessAlbum($aData["error"], $aData["poster"], $aData["result"]);
                 break;
            
        /*
        case "Counter"       : ProcessCounter($aData["result"]);
                               break;
        */
        /*     
        case "Movies"        : ProcessMovie($aData["poster"], $aData["fanart"], $aData["result"]);                             
                               break;
        */
             
        case "MovieDetails"  : ProcessMovieDetails($aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);                         
                               break;            
        /*    
        case "TVShows"       : ProcessTVShow($aData["poster"], $aData["fanart"], $aData["result"]); 
                               break;
        */                  
        case "TVShowDetails" : ProcessTVShowDetails($aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]); 
                               break;
        /*    
        case "Music"         : ProcessAlbum($aData["poster"], $aData["result"]); 
                               break;
        */                   
        case "MusicDetails"  : ProcessAlbumDetails($aData["poster"], $aData["result"], $aData["fargoid"]);
                               break;                            
    }
}

/*
 * Function:	ProcessMovie
 *
 * Created on Jul 15, 2013
 * Updated on Oct 07, 2013
 *
 * Description: Process the movie. 
 *
 * In:  $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ProcessMovie($aError, $poster, $fanart, $aResult)
{   
    if (empty($aError))
    {
        $aMovie  = $aResult["moviedetails"];
        $aGenres = $aMovie["genre"];
        $aMovie  = ConvertMovie($aMovie);
    
        //SaveImage($aMovie["movieid"], $poster, "../".cMOVIESPOSTERS);    
        //SaveImage($aMovie["movieid"], $fanart, "../".cMOVIESFANART);
 
        ResizeAndSaveImage($aMovie["xbmcid"], $poster, "../".cMOVIESTHUMBS, 125, 175); //200, 280  
    
        InsertMovie($aMovie);
        InsertGenreToMedia($aGenres, "movies");

        ResizeAndSaveImage($aMovie["xbmcid"], $fanart, "../".cMOVIESFANART, 562, 350); //675, 420 
    
        //UpdateStatus("XbmcMoviesStart", $aMovie["xbmcid"] + 1);
        IncrementStatus("XbmcMoviesStart", 1); 
    }
    else if ($aError["code"] == -32602) { // Movie not found, continue with the next one.
       IncrementStatus("XbmcMoviesStart", 1); 
    }
}

/*
 * Function:	ProcessMovieDetails
 *
 * Created on Sep 08, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Process the movie details. 
 *
 * In:  $poster, $fanart, $aResult, $id
 * Out: -
 *
 */
function ProcessMovieDetails($poster, $fanart, $aResult, $id)
{   
    $aMovie  = $aResult["moviedetails"];
    $aGenres = $aMovie["genre"];   
    $aMovie  = ConvertMovie($aMovie);
    
    DeleteFile("../".cMOVIESTHUMBS."/".$aMovie["xbmcid"].".jpg");
    DeleteFile("../".cMOVIESFANART."/".$aMovie["xbmcid"].".jpg");
    
    UpdateMovie($id, $aMovie);
    //InsertGenreToMedia($aGenres, "movies");
       
    ResizeAndSaveImage($aMovie["xbmcid"], $fanart, "../".cMOVIESFANART, 562, 350);  //675, 420    
    ResizeAndSaveImage($aMovie["xbmcid"], $poster, "../".cMOVIESTHUMBS, 125, 175);  //200, 280
 
    UpdateStatus("Ready", 1);
}

/*
 * Function:	ProcessTVShow
 *
 * Created on Aug 24, 2013
 * Updated on Oct 07, 2013
 *
 * Description: Process the tv show. 
 *
 * In:  $aError, $poster, $fanart, $aResult
 * Out: -
 *
 */
function ProcessTVShow($aError, $poster, $fanart, $aResult)
{   
    if (empty($aError))
    {
        $aTVShow = $aResult["tvshowdetails"];
        $aGenres = $aTVShow["genre"];
        $aTVShow = ConvertTVShow($aTVShow);
     
        ResizeAndSaveImage($aTVShow["xbmcid"], $poster, "../".cTVSHOWSTHUMBS, 125, 175);

        InsertTVShow($aTVShow);
        InsertGenreToMedia($aGenres, "tvshows");
    
        ResizeAndSaveImage($aTVShow["xbmcid"], $fanart, "../".cTVSHOWSFANART, 562, 350);
    
        IncrementStatus("XbmcTVShowsStart", 1);
    }
    else if ($aError["code"] == -32602) { // TVShow not found, continue with the next one.
       IncrementStatus("XbmcTVShowsStart", 1); 
    }    
}

/*
 * Function:	ProcessTVShowDetails
 *
 * Created on Sep 09, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Process the tv show. 
 *
 * In:  $poster, $fanart, $aResult
 * Out: -
 *
 */
function ProcessTVShowDetails($poster, $fanart, $aResult, $id)
{   
    $aTVShow = $aResult["tvshowdetails"];
    $aGenres = $aTVShow["genre"]; 
    $aTVShow = ConvertTVShow($aTVShow);
    
    DeleteFile("../".cTVSHOWSTHUMBS."/".$aTVShow["xbmcid"].".jpg");
    DeleteFile("../".cTVSHOWSFANART."/".$aTVShow["xbmcid"].".jpg");
    
    UpdateTVShow($id, $aTVShow);
    //InsertGenreToMedia($aGenres, "tvshows"); 
    
    ResizeAndSaveImage($aTVShow["xbmcid"], $fanart, "../".cTVSHOWSFANART, 562, 350);
    ResizeAndSaveImage($aTVShow["xbmcid"], $poster, "../".cTVSHOWSTHUMBS, 125, 175);
    
    UpdateStatus("Ready", 1);
}

/*
 * Function:	ProcessAlbum
 *
 * Created on Aug 24, 2013
 * Updated on Oct 07, 2013
 *
 * Description: Process the music album. 
 *
 * In:  $aError, $poster, $aResult
 * Out: -
 *
 */
function ProcessAlbum($aError, $poster, $aResult)
{   
    if (empty($aError))
    {
        $aAlbum = $aResult["albumdetails"];
        $aGenres = $aAlbum["genre"]; 
        $aAlbum = ConvertAlbum($aAlbum);
    
        ResizeAndSaveImage($aAlbum["xbmcid"], $poster, "../".cALBUMSTHUMBS, 125, 125);
       
        InsertAlbum($aAlbum);
        InsertGenreToMedia($aGenres, "music");
    
        ResizeAndSaveImage($aAlbum["xbmcid"], $poster, "../".cALBUMSCOVERS, 300, 300);
    
        IncrementStatus("XbmcMusicStart", 1);
    }
    else if ($aError["code"] == -32602) { // Album not found, continue with the next one.
       IncrementStatus("XbmcMusicStart", 1); 
    }     
}

/*
 * Function:	ProcessAlbumDetails
 *
 * Created on Sep 09, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Process the music album details. 
 *
 * In:  $poster, $aResult, $id
 * Out: -
 *
 */
function ProcessAlbumDetails($poster, $aResult, $id)
{   
    $aAlbum = $aResult["albumdetails"];
    $aGenres = $aAlbum["genre"]; 
    $aAlbum = ConvertAlbum($aAlbum);
    
    DeleteFile("../".cALBUMSTHUMBS."/".$aAlbum["xbmcid"].".jpg");
    DeleteFile("../".cALBUMSCOVERS."/".$aAlbum["xbmcid"].".jpg");
      
    UpdateAlbum($id, $aAlbum);
    //InsertGenreToMedia($aGenres, "music");
    
    ResizeAndSaveImage($aAlbum["xbmcid"], $poster, "../".cALBUMSCOVERS, 300, 300);
    ResizeAndSaveImage($aAlbum["xbmcid"], $poster, "../".cALBUMSTHUMBS, 125, 125);
    
    UpdateStatus("Ready", 1);
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
/*function SaveImage($id, $image, $path)
{
    if ($image) 
    {
        $image = explode('base64,',$image); 
        file_put_contents($path.'/'.$id.'.jpg', base64_decode($image[1]));
    }    
}*/

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

?>
