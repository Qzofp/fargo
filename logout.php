<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    logout.php
 *
 * Created on May 04, 2013
 * Updated on May 04, 2013
 *
 * Description: Fargo's login page. 
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

session_start();
session_destroy();
header('location:index.php');

?>
