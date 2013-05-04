<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    index.php
 *
 * Created on Mar 02, 2013
 * Updated on May 04, 2013
 *
 * Description: Fargo's main page (openingspage). 
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////
session_start();


if(!isset($_SESSION['LOGIN']))
{
    $login = false;
    $user = "";
    $aJavascript = array("js/jquery-1.9.1.min.js", "js/fargo-common.js", "js/fargo-login.js");
    $system = '<li id="loginbox">LOGIN</li>';
}
else
{
    $login = true;
    $user = '<div id="logout">Logout: '.$_SESSION['USER'].'</div>';
    $aJavascript = array("js/jquery-1.9.1.min.js", "js/fargo-common.js", "js/fargo-system.js", "js/fargo-import.js");
    $system = '<li id="system">SYSTEM</li>';
}
 
require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

$title = "Fargo: A Media Catalog For XBMC";
$css   = "css/confluence.css";
//$aJavascript = array("js/jquery-1.9.1.min.js", "js/fargo-interface.js");

PageHeader($title, $css, $aJavascript);

// Header section. 
echo "   <div id=\"header\">\n";
echo "    <div id=\"header_txt\">$user</div>\n";
echo "   </div>\n";

// Popup section (hidden).
echo "   <div id=\"mask\"></div>\n";

// Popup section (default hidden).
if (!$login)
{
    ShowHiddenLoginBox();
}

// Display section.
echo "   <div id=\"display_left\">\n";
echo "    <div id=\"sort\"></div>\n";
echo "    <div id=\"prev\">&lt;</div>\n";
echo "   </div>\n";

echo "   <div id=\"display_right\">\n";
echo "    <div id=\"next\">&gt;</div>\n";
echo "   </div>\n";

// Display movie table. This is done by the jQuery functions createMovieTable().
echo "   <div id=\"display_content\">\n";
echo "   </div>\n";

// Control section.
echo "   <div id=\"control\">\n";
echo "    <div id=\"control_bar\">\n";
echo "     <ul>\n";
echo "      <li id=\"movies\">MOVIES</li>\n";
echo "      <li id=\"tvshows\">TV SHOWS</li>\n";
echo "      <li id=\"music\">MUSIC</li>\n";
echo "      $system\n";
echo "     </ul>\n";
echo "    </div>\n";
echo "   </div>\n";

PageFooter("https://github.com/Qzofp/Fargo", "Qzofp's Fargo", true, "LoadFargoMedia()");
?>
