<?php
/*
 * Title:   AXMC (Working title)
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    xbmc.php
 *
 * Created on Mar 22, 2013
 * Updated on Apr 14, 2013
 *
 * Description: The main XBMC functions page. 
 * 
 * Note: This page contains functions for importing media information, XBMC online and status checks. 
 *
 */


/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';
require_once 'include/import.php';

$aJson = null;
$action = GetPageValue('action');

switch ($action) 
{    
    case "init"    : $media = GetPageValue('media');
                     $aJson = GetXbmcValues($media);
                     break;
    
    case "online"  : $aJson['online'] = OnlineCheckXBMC();
                     break;
                
    case "import"  : ImportMovies();
                     break;
                
    case "tvshows" : $aJson = GetTotalNumberOfTVShowsFromXBMC();
                     break;
    
    case "status"  : $aJson = ImportMoviesStatus();
                     break;
    
    case "test"    : $url = "http://localhost:8080/jsonrpc";
                     $request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies","params": { "limits": { "start" : 0, "end": 1 }}, "id": "libMovies"}';
                     $aJson = GetHttpRequest($url, $request);
                     break;
                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson);
}
else {
//    echo -1; // Cannot connect!
}
?>
