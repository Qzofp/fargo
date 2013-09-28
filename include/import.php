<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    import.php
 *
 * Created on Jul 15, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Fargo's import page. This page is called from XBMC which push the data to Fargo.
 * 
 * TODO: Improve security. Don't allow anybody to access this page. Only logged on users and traffic from XBMC.
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
 * Updated on Sep 15, 2013
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
    
    $aData["action"] = null;
    if (isset($_POST["action"]) && !empty($_POST["action"]))
    {
        $aData["action"] = $_POST["action"];
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
 * Updated on Sep 15, 2013
 *
 * Description: Process data from XBMC. 
 *
 * In:  $aData
 * Out: -
 *
 */
function ProcessDataFromXbmc($aData)
{

    switch($aData["action"])
    {
        case "Counter"       : ProcessCounter($aData["result"]);
                               break;
        
        case "Movies"        : ProcessMovie($aData["poster"], $aData["fanart"], $aData["result"]);                             
                               break;
                     
        case "MovieDetails"  : ProcessMovieDetails($aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]);                         
                               break;            
            
        case "TVShows"       : ProcessTVShow($aData["poster"], $aData["fanart"], $aData["result"]); 
                               break;
                          
        case "TVShowDetails" : ProcessTVShowDetails($aData["poster"], $aData["fanart"], $aData["result"], $aData["fargoid"]); 
                               break;
            
        case "Music"         : ProcessAlbum($aData["poster"], $aData["result"]); 
                               break;
                           
        case "MusicDetails"  : ProcessAlbumDetails($aData["poster"], $aData["result"], $aData["fargoid"]);
                               break;                            
    }
}

/*
 * Function:	ProcessCounter
 *
 * Created on Jul 22, 2013
 * Updated on Sep 16, 2013
 *
 * Description: Process the media counter. 
 *
 * In:  $Results
 * Out: -
 *
 */
function ProcessCounter($aResults)
{
    $counter = 0;
    $media = null;
    
    if (!empty($aResults["limits"]["total"])) {
        $counter = $aResults["limits"]["total"];
    }
    
    if (!empty($aResults["movies"])) {
        $media = "Movies";
    }
    elseif(!empty($aResults["tvshows"])) {
        $media = "TVShows";
    }
    elseif(!empty($aResults["albums"])) {
        $media = "Music";
    }
    
    UpdateStatus("Xbmc".$media."End", $counter);
}

/*
 * Function:	ProcessMovie
 *
 * Created on Jul 15, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Process the movie. 
 *
 * In:  $poster, $fanart, $aResult
 * Out: -
 *
 */
function ProcessMovie($poster, $fanart, $aResult)
{   
    $aMovie  = $aResult["movies"][0];
    $aGenres = $aMovie["genre"];
    $aMovie  = ConvertMovie($aMovie);  
    
    //SaveImage($aMovie["movieid"], $poster, "../".cMOVIESPOSTERS);    
    //SaveImage($aMovie["movieid"], $fanart, "../".cMOVIESFANART);
 
    ResizeAndSaveImage($aMovie["xbmcid"], $poster, "../".cMOVIESTHUMBS, 125, 175); //200, 280  
    
    InsertMovie($aMovie);
    InsertGenreToMedia($aGenres, "movies");

    ResizeAndSaveImage($aMovie["xbmcid"], $fanart, "../".cMOVIESFANART, 562, 350); //675, 420 
    
    IncrementStatus("XbmcMoviesStart", 1);
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
 * Updated on Sep 23, 2013
 *
 * Description: Process the tv show. 
 *
 * In:  $poster, $fanart, $aResult
 * Out: -
 *
 */
function ProcessTVShow($poster, $fanart, $aResult)
{   
    $aTVShow = $aResult["tvshows"][0];
    $aGenres = $aTVShow["genre"];
    $aTVShow = ConvertTVShow($aTVShow);
     
    ResizeAndSaveImage($aTVShow["xbmcid"], $poster, "../".cTVSHOWSTHUMBS, 125, 175);

    InsertTVShow($aTVShow);
    InsertGenreToMedia($aGenres, "tvshows");
    
    ResizeAndSaveImage($aTVShow["xbmcid"], $fanart, "../".cTVSHOWSFANART, 562, 350);
    
    IncrementStatus("XbmcTVShowsStart", 1);
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
 * Updated on Sep 23, 2013
 *
 * Description: Process the music album. 
 *
 * In:  $poster, $aResult
 * Out: -
 *
 */
function ProcessAlbum($poster, $aResult)
{   
    $aAlbum = $aResult["albums"][0];
    $aGenres = $aAlbum["genre"]; 
    $aAlbum = ConvertAlbum($aAlbum);
    
    ResizeAndSaveImage($aAlbum["xbmcid"], $poster, "../".cALBUMSTHUMBS, 125, 125);
       
    InsertAlbum($aAlbum);
    InsertGenreToMedia($aGenres, "music");
    
    ResizeAndSaveImage($aAlbum["xbmcid"], $poster, "../".cALBUMSCOVERS, 300, 300);
    
    IncrementStatus("XbmcMusicStart", 1);
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
