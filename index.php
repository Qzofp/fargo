<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    index.php
 *
 * Created on Mar 02, 2013
 * Updated on May 20, 2013
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
    $aJavascript = array("//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js", 
                         "js/fargo-common.js", 
                         "js/fargo-login.js");
    
    $li_import = "";
    $li_login  = "<li id=\"login\">Login</li>\n";
    $system_states = null;
}
else
{
    $login = true;
    $user = "Welcome: <span>".$_SESSION['USER']."</span>";
    $aJavascript = array("//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js",
                         "//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js",
                         "js/fargo-common.js", 
                         "js/fargo-system.js", 
                         "js/fargo-import.js");
     
    $li_import = "<li id=\"import\">Import</li>\n";
    $li_login  = "<li id=\"logout\">Logout</li>\n";
    $system_states = "   <div id=\"state_xbmc\">offline</div>\n";
}
 
require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

$title = "Fargo: A Media Catalog For XBMC";
$aCss   = array("<link href='http://fonts.googleapis.com/css?family=Dancing+Script:700' rel='stylesheet' type='text/css'>",
                "<link href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/start/jquery-ui.css' rel='stylesheet' type='text/css'>",
                "<link rel=\"stylesheet\" href=\"css/confluence.css\">");

$media = GetPageValue('media');
if (!isset($media)) {
    $media = "movies";
}

PageHeader($title, $aCss, $aJavascript);

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
else 
{
    ShowHiddenImportBox();   
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

// Display system page.
echo "   <div id=\"display_system\">\n";
echo "    <div id=\"display_system_main\">\n";
echo "     <div id=\"display_system_left\">\n";
echo "      <div id=\"fargo\">Qzofp's Fargo</div>\n";
echo "     </div>\n";
echo "     <div id=\"display_system_right\">\n";
echo "     </div>\n";
echo "    </div>\n";
echo "   </div>\n";

// Control section.
echo "   <div id=\"control\">\n";

// Main control bar
echo "    <div id=\"control_bar\">\n";
echo "     <ul>\n";
echo "      <li id=\"movies\">MOVIES</li>\n";
echo "      <li id=\"tvshows\">TV SHOWS</li>\n";
echo "      <li id=\"music\">MUSIC</li>\n";
echo "      <li id=\"system\">SYSTEM</li>\n";
echo "     </ul>\n";
echo "    </div>\n";

// Sub control bar
echo "    <div id=\"control_sub\">\n";
echo "     <ul>\n";
echo "      $li_import";
echo "      <li id=\"genres\">Genres</li>\n";
echo "      $li_login";
echo "     </ul>\n";
echo "    </div>\n";

echo "   </div>\n";

// Page States
echo "   <div id=\"state_media\">movies</div>\n";
echo $system_states;

PageFooter("https://github.com/Qzofp/Fargo", "Qzofp's Fargo", true, "LoadFargoMedia('$media')");
?>
