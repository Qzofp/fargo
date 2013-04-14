<?php
/*
 * Title:   AXMC (Working title)
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    xbmc.php
 *
 * Created on Mar 22, 2013
 * Updated on Mar 22, 2013
 *
 * Description: The main XBMC functions page. 
 * 
 * Note: This page contains functions for importing media information, XBMC online and status checks. 
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'rpc/HTTPClient.php';
require_once 'include/axmc.php';

$aJson = null;
$action = GetPageValue('action');

switch ($action) 
{
    case "online" : $aJson['online'] = OnlineCheckXBMC();
                    break;
                
    case "import" : ImportMovies();
                    break;
    
    case "status" : $aJson = ImportMoviesStatus();
                    break;
    
    case "test"   : break;
                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson);
}


/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	ImportMovies
 *
 * Created on Mar 11, 2013
 * Updated on Mar 20, 2013
 *
 * Description: Import the movies. 
 *
 * In:  -
 * Out: -
 *
 */
function ImportMovies()
{
    // First online check in progress.php -> onlineCheck() JQuery function.
    $total   = GetTotalNumberOfMoviesFromXBMC();
    $counter = (int)GetSetting("MovieCounter");
    $offset  = 5;

    // Check if there are new movies available.
    $delta = $total - $counter;
    UpdateSetting("MovieDelta", $delta);

    if ($delta > 0) 
    {        
        list($aLimits, $aMovies) = GetMoviesFromXBMC($counter, $offset); 
    
        ProcessMovies($aMovies, $counter);
    }
}

/*
 * Function:	ImportMoviesStatus
 *
 * Created on Mar 22, 2013
 * Updated on Mar 22, 2013
 *
 * Description: Reports the status of the import movies process. 
 *
 * In:  -
 * Out: $aJson
 *
 */
function ImportMoviesStatus()
{
    $aJson['id']     = 0;
    $aJson['xbmcid'] = 0; 
    $aJson['title']  = "empty";

    $aJson['delta']  = GetSetting("MovieDelta");
    $aJson['online'] = OnlineCheckXBMC();

    $i = GetSetting("MovieCounter");
    if ($i > 0)
    {   
        $db = OpenDatabase();

        $id     = 0;
        $xbmcid = 0;
        $title  = null;

        $sql = "SELECT id, xbmcid, title ".
               "FROM movies ".
            "WHERE id = $i";

        $stmt = $db->prepare($sql);
        if($stmt)
        {
            if($stmt->execute())
            {
                // Get number of rows.
                $stmt->store_result();
                $rows = $stmt->num_rows;

                if ($rows != 0)
                {              
                    $stmt->bind_result($id, $xbmcid, $title);
                    while($stmt->fetch())
                    {                
                        $aJson['id']     = $id;
                        $aJson['xbmcid'] = $xbmcid;  
                        $aJson['title']  = $title;
                    }                  
                }
            }
            else
            {
                die('Ececution query failed: '.mysqli_error($db));
            }
            $stmt->close();
        }
        else
        {
            die('Invalid query: '.mysqli_error($db));
        } 

        CloseDatabase($db);
    }
    
    return $aJson;
}


?>
