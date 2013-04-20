<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../../settings.php';
require_once '../../tools/toolbox.php';
require_once '../../include/axmc.php';

for ($i = 1; $i <= 14; $i++)
{
    // process data.
    sleep(1);
    
    UpdateMovieCounter($i);
    
    //session_start();
    //$_SESSION["progress"]=$i;
    //session_write_close();
}


?>
