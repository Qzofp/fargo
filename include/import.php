<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    import.php
 *
 * Created on Jul 15, 2013
 * Updated on Sep 02, 2013
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
    
$aData = ReceiveDataFromXbmc();
ProcessDataFromXbmc($aData);
   
//debug
//echo "<pre>";
//print_r($aData);
//echo "</pre></br>";

    
/////////////////////////////////////////    Import Functions    //////////////////////////////////////////    

/*
 * Function:	ReceiveDataFromXbmc
 *
 * Created on Jul 15, 2013
 * Updated on Aug 11, 2013
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
    
    return $aData;
}
   
/*
 * Function:	ProcessDataFromXbmc
 *
 * Created on Jul 15, 2013
 * Updated on Aug 24, 2013
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
        case "counter" : ProcessCounter($aData["result"]);
                         break;
        
        case "movies"  : ProcessMovie($aData["poster"], $aData["fanart"], $aData["result"]);                             
                         break;
            
        case "tvshows" : ProcessTVShow($aData["poster"], $aData["fanart"], $aData["result"]); 
                         break;
            
        case "music"   : ProcessAlbum($aData["poster"], $aData["fanart"], $aData["result"]); 
                         break; 
    }
}

/*
 * Function:	ProcessCounter
 *
 * Created on Jul 22, 2013
 * Updated on Jul 22, 2013
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
    
    UpdateStatus("Xbmc".$media."Counter", $counter);
}

/*
 * Function:	ProcessMovie
 *
 * Created on Jul 15, 2013
 * Updated on Sep 02, 2013
 *
 * Description: Process the movie. 
 *
 * In:  $poster, $fanart, $aResult
 * Out: -
 *
 */
function ProcessMovie($poster, $fanart, $aResult)
{   
    $aMovie = $aResult["movies"][0];
    $aGenres = $aMovie["genre"];
    
    //SaveImage($aMovie["movieid"], $poster, "../".cMOVIESPOSTERS);    
    //SaveImage($aMovie["movieid"], $fanart, "../".cMOVIESFANART); 
    
    ResizeAndSaveImage($aMovie["movieid"], $fanart, "../".cMOVIESFANART, 675, 420);    
    ResizeAndSaveImage($aMovie["movieid"], $poster, "../".cMOVIESTHUMBS, 200, 280);
    
    $aMovie = ConvertMovie($aMovie);
    InsertMovie($aMovie);
    InsertGenreToMedia($aGenres, "movies");
}

/*
 * Function:	ProcessTVShow
 *
 * Created on Aug 24, 2013
 * Updated on Sep 02, 2013
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
    
    //SaveImage($aTVShow["tvshowid"], $poster, "../".cTVSHOWSPOSTERS);
    //SaveImage($aTVShow["tvshowid"], $fanart, "../".cTVSHOWSFANART);   
    
    ResizeAndSaveImage($aTVShow["tvshowid"], $fanart, "../".cTVSHOWSFANART, 675, 420);
    ResizeAndSaveImage($aTVShow["tvshowid"], $poster, "../".cTVSHOWSTHUMBS, 200, 280);
    
    $aTVShow = ConvertTVShow($aTVShow);
    InsertTVShow($aTVShow);
    InsertGenreToMedia($aGenres, "tvshows");
}

/*
 * Function:	ProcessAlbum
 *
 * Created on Aug 24, 2013
 * Updated on Sep 02, 2013
 *
 * Description: Process the music album. 
 *
 * In:  $poster, $fanart, $aResult
 * Out: -
 *
 */
function ProcessAlbum($poster, $fanart, $aResult)
{   
    $aAlbum = $aResult["albums"][0];
    $aGenres = $aAlbum["genre"];
    
    //SaveImage($aAlbum["albumid"], $poster, "../".cALBUMSCOVERS);
    //SaveImage($aAlbum["albumid"], $fanart, "../".cALBUMSFANART);   
    
    ResizeAndSaveImage($aAlbum["albumid"], $poster, "../".cALBUMSCOVERS, 300, 300);
    ResizeAndSaveImage($aAlbum["albumid"], $poster, "../".cALBUMSTHUMBS, 200, 200);
    
    $aAlbum = ConvertAlbum($aAlbum);
    InsertAlbum($aAlbum);
    InsertGenreToMedia($aGenres, "music");
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

?>
