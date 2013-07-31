<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    import.php
 *
 * Created on Jul 15, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Fargo's import page. This page is called from XBMC which push the data to Fargo.
 * 
 * TODO: Improve security. Don't allow anybody to access this page. Only logged on users and traffic from XBMC.
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

// Give the damn thing cross domain access. Something XBMC won't let you do, the bastards!!!
header("Access-Control-Allow-Origin: *");  // Add "*" to settings.

require_once '../../settings.php';
require_once '../../tools/toolbox.php';
require_once '../../include/common.php';
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
 * Updated on Jul 15, 2013
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
    
    if (isset($_POST["action"]) && !empty($_POST["action"]))
    {
        $aData["action"] = $_POST["action"];
    }    
    
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
 * Updated on Jul 22, 2013
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
        
        case "movies"  : ProcessMovies($aData["result"]["movies"]);                             
                         break;
            
        case "tvshows" : break;
            
        case "music"   : break; 
    }
}

/*
 * Function:	ProcessMovies
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
    elseif(!empty($aResults["music"])) {
        $media = "Music";
    }
    
    UpdateStatus("Xbmc".$media."Counter", $counter);
}

/*
 * Function:	ProcessMovies
 *
 * Created on Jul 15, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Process the movies. 
 *
 * In:  $aMovies
 * Out: -
 *
 */
function ProcessMovies($aMovies)
{   
   // echo $aLimits["total"];
    
   // if ($aLimits["total"] > CountRows("movies"))
   // {
        // Start import.
        foreach($aMovies as $aMovie) 
        {
            $aGenres = $aMovie["genre"];
        
            $aMovie = ConvertMovie($aMovie);
            InsertMovie($aMovie);
            InsertGenreToMedia($aGenres, "movies");
        } 
        
        
   // }
   // else {
        // Import is finished.
        //UpdateSetting("Status", "finished");
   // }
}

?>
