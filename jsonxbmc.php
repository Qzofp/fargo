<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    jsonxbmc.php
 *
 * Created on Mar 22, 2013
 * Updated on Jul 02, 2013
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
    case "online"  : $aJson['online'] = OnlineCheckXBMC();
                     break;
                
    case "counter" : $media = GetPageValue('media');
                     $aJson = GetMediaCounterFromXBMC($media);
                     break;                    
                 
    case "import"  : $media = GetPageValue('media');
                     $start = GetPageValue('start');
                     $aJson = ImportMedia($start, $media);
                     break;
   
    case "test"    : $aJson = ImportMedia(0, "music");
                     //$aJson = GetTVShowsFromXBMC(6, 3);
                     //$aJson = GetAlbumsFromXBMC(0, 3);
                     break;                   
}

if (empty($aJson)) {
    $aJson['online'] = -1;  // XBMC offline or cannot connect.
}

//debug
//echo "<pre>";
//print_r($aJson);
//echo "</pre></br>"; 

// Return JSON code which is used as input for the JQuery functions.
echo json_encode($aJson, JSON_UNESCAPED_SLASHES);
?>
