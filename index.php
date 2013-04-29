<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    index.php
 *
 * Created on Mar 02, 2013
 * Updated on Apr 29, 2013
 *
 * Description: Fargo's main page (openingspage). 
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

$title = "Fargo: A Media Catalog For XBMC";
$css   = "css/confluence.css";
$aJavascript = array("js/jquery-1.9.1.min.js", "js/fargo-interface.js");

PageHeader($title, $css, $aJavascript);

// Header section. 
echo "   <div id=\"header\">\n";
echo "    <div id=\"header_txt\">XBMC Connected</div>\n";
echo "   </div>\n";

// Popup section (hidden).
echo "   <div id=\"mask\"></div>\n";

// Popup login section (hidden).
echo "   <div id=\"popup\">\n";
echo "    <form method=\"post\" class=\"login\" action=\"#\">\n";
echo "     <fieldset class=\"textbox\">\n";
echo "      <p>Login Box</p>\n";
echo "      <button class=\"close\" type=\"button\">x</button>\n";
echo "      <label class=\"username\">\n";
echo "       <span>Username</span>\n";
echo "       <input id=\"username\" name=\"username\" value=\"\" type=\"text\" autocomplete=\"on\" placeholder=\"Username\">\n";
echo "      </label>\n";
echo "      <label class=\"password\">\n";
echo "       <span>Password</span>\n";
echo "       <input id=\"password\" name=\"password\" value=\"\" type=\"password\" placeholder=\"Password\">\n";
echo "      </label>\n";
echo "      <button class=\"button\" type=\"submit\">Login</button>\n";
echo "      </br>\n";
echo "     </fieldset>\n";
echo "    </form>\n";
echo "   </div>\n";

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
echo "      <li id=\"system\">SYSTEM</li>\n";
echo "     </ul>\n";
echo "    </div>\n";
echo "   </div>\n";

PageFooter("https://github.com/Qzofp/Fargo", "Qzofp's Fargo", true, "LoadFargoMedia()");
?>
