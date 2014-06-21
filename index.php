<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.5
 *
 * File:    index.php
 *
 * Created on Mar 02, 2013
 * Updated on Jun 21, 2014
 *
 * Description: Fargo's main page (openingspage). 
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////
session_start();
if(!isset($_SESSION['LOGIN']))
{
    $login = false;
    $mode = "&nbsp;";
    $user = "&nbsp;";
    $aJavascript = array("//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js", 
                         "//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js",
                         "//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha256.js",
                         "js/jquery.slimscroll.min.js", 
                         "js/fargo.public.constants.js",
                         "js/fargo.common.js", 
                         "js/fargo.public.media.js",         
                         "js/fargo.system.js", 
                         "js/fargo.public.main.js");
    
    $li_modes = "";
    $li_login  = "<li id=\"login\">Login</li>\n";
    $system_states = null;
}
else
{
    $login = true;
    $mode = "";
    $user = "Welcome: <span>".$_SESSION['USER']."</span>";
    $aJavascript = array("//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js",
                         "//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js",
                         "//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha256.js",
                         "js/jquery.slimscroll.min.js",
                         "js/fargo.public.constants.js",
                         "js/fargo.private.constants.js",
                         "js/fargo.common.js",       
                         "js/fargo.public.media.js",         
                         "js/fargo.system.js", 
                         "js/fargo.private.main.js", 
                         "js/fargo.private.media.js",         
                         "js/fargo.private.import.js",
                         "js/fargo.private.refresh.js");
     
    $li_modes = "<li id=\"modes\">Manage</li>\n";
    $li_login  = "<li id=\"logout\">Logout</li>\n";
    $system_states = "   <div id=\"state_xbmc\">offline</div>\n".
                     "   <div id=\"state_property\"></div>\n".
                     "   <div id=\"state_row\"></div>\n";
}
 
require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

$title = "Fargo: A Media Catalog For XBMC";
$aCss   = array("<link href='http://fonts.googleapis.com/css?family=Dancing+Script:700' rel='stylesheet' type='text/css'>",
                "<link href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/start/jquery-ui.css' rel='stylesheet' type='text/css'>",
                "<link rel=\"stylesheet\" href=\"css/confluence.css\">");

$media = GetPageValue('media');
if (!isset($media)) {
    $media = "movies";
}

PageHeader($title, $aCss, $aJavascript);

// Header section. 
echo "   <div id=\"header_left\">\n";
echo "    <div id=\"header_mode\">$mode</div>\n";
echo "   </div>\n";

echo "   <div id=\"header_right\">\n";
echo "    <div id=\"header_login\">$user</div>\n";
echo "   </div>\n";

echo "   <div id=\"header_center\">\n";
echo "    <div id=\"header_info\"></div>\n";
echo "    <div id=\"header_sort\"></div>\n";
echo "   </div>\n";

// Display section.
echo "   <div id=\"display_left\">\n";
echo "    <div id=\"sort\"></div>\n";
echo "    <div id=\"prev\">&lt;</div>\n";
echo "   </div>\n";

echo "   <div id=\"display_right\">\n";
echo "    <div id=\"next\">&gt;</div>\n";
echo "   </div>\n";

// Display movie table. This is done by the jQuery function ShowMediaTable.
echo "   <div id=\"display_content\">\n";
echo "    <div id=\"display_thumb\"></div>\n";
echo "    <div id=\"display_list\"></div>\n";
echo "   </div>\n";

// Display system page.
echo "   <div id=\"display_system\">\n";
echo "    <div id=\"display_system_main\">\n";
echo "     <div id=\"display_system_left\">\n";
echo "     </div>\n";
echo "     <div id=\"display_system_right\">\n";
echo "     </div>\n";
echo "    </div>\n";
echo "   </div>\n";

// Control section.
echo "   <div id=\"control\">\n";

// Control pagination.
echo "    <div id=\"control_pag\">\n";
echo "     <div id=\"bullets\">\n";
echo "     </div>\n";
echo "    </div>\n";

// Main control bar.
echo "    <div id=\"control_bar\">\n";
echo "     <ul>\n";
echo "      <li id=\"movies\">MOVIES</li>\n";
echo "      <li id=\"tvshows\">TV SHOWS</li>\n";
echo "      <li id=\"music\">MUSIC</li>\n";
echo "      <li id=\"system\">SYSTEM</li>\n";
echo "     </ul>\n";
echo "    </div>\n";

// Sub control bar.
echo "    <div id=\"control_sub\">\n";
echo "     <ul>\n";
echo "      $li_modes";
echo "      <li id=\"type\"></li>\n"; // Media type.
echo "      <li id=\"screen\"></li>\n";  // List or Thumbnail.
echo "      <li id=\"title\">Sort</li>\n";
echo "      <li id=\"genres\">Genres</li>\n";
echo "      <li id=\"years\">Years</li>\n";
echo "      $li_login";
echo "     </ul>\n";
echo "    </div>\n";

echo "   </div>\n";

PageFooter("https://github.com/Qzofp/Fargo", "Qzofp's Fargo", true);

// Popup section (default hidden).
if (!$login) {
    ShowHiddenLoginBox();
}
else {
    ShowHiddenActionBox();
}

ShowHiddenButtonsBox();
ShowHiddenInfoBox();

// Popup section (hidden).
echo "   <div id=\"mask\"></div>\n";

// Page States.
echo "   <div id=\"state_media\">movies</div>\n";  // movies, tvshows, music, system.
echo "   <div id=\"state_type\">titles</div>\n"; // media types: movies titles, sets, series, episodes, albums.
echo "   <div id=\"state_level\"></div>\n"; // Movie sets, seasons, episodes level (sets id, tvshows id).
echo "   <div id=\"state_page\">movies</div>\n"; // movies, tvshows, music, system or popup.
echo "   <div id=\"state_choice\"></div>\n"; // manage, title, genre or year.
echo "   <div id=\"state_mode\"></div>\n";
echo "   <div id=\"state_title\"></div>\n"; // sort by title.
echo "   <div id=\"state_genre\"></div>\n";
echo "   <div id=\"state_year\"></div>\n";
echo $system_states;

PageEnd("LoadFargoMedia('$media')");