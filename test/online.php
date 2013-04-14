<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'settings.php';
require_once 'rpc/HTTPClient.php';
require_once 'include/axmc.php';

// Check if XBMC is online
$aJson['online'] = OnlineCheckXBMC();

//$aJson['online'] = false;

//sleep(2);

echo json_encode($aJson);

//echo "<pre>"; print_r($aMovies); echo "</pre>"; 
?>
