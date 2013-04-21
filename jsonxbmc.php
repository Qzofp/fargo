<?php
/*
 * Title:   AXMC (Working title)
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    xbmc.php
 *
 * Created on Mar 22, 2013
 * Updated on Apr 20, 2013
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
                 
    case "counter" : $media = GetPageValue('media');
                     $aJson = GetMediaCounter($media);
                     break;             
    
    case "online"  : $aJson['online'] = OnlineCheckXBMC();
                     break;
                
    case "import"  : $media = GetPageValue('media');
                     $aJson = ImportMedia($media);
                     break;
    
    case "status"  : $media = GetPageValue('media');
                     $aJson = GetMediaStatus($media);
                     break;
    
    case "test"    : $aJson = GetAlbumsFromXBMC(11, 12);
                     break;
                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson, JSON_UNESCAPED_SLASHES);
}
else {
//    echo -1; // Cannot connect!
}
?>
