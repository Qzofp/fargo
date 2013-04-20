<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'rpc/HTTPClient.php';
require_once 'include/axmc.php';

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

?>
