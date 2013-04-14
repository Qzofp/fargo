<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    index.php
 *
 * Created on Mar 02, 2013
 * Updated on Apr 01, 2013
 *
 * Description: Fargo's main page (openingspage). 
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'rpc/HTTPClient.php';
require_once 'include/common.php';

PageHeader("Fargo: A Media Catalog For XBMC","css/confluence.css");

// Header section. 
echo "   <div id=\"header\">\n";
echo "    <div id=\"header_txt\">XBMC Connected</div>\n";
echo "   </div>\n";

// Display section.
echo "   <div id=\"display_left\">left\n";
echo "   </div>\n";

echo "   <div id=\"display_right\">right\n";
echo "   </div>\n";

echo "   <div id=\"display_content\">\n";

// Movies table.
echo "    <table>\n";
echo "     <tr>\n";
echo "      <td><img src=\"images/13.jpg\"/></br>Fargo</td>\n";
echo "      <td><img src=\"images/1.jpg\"/></br>Kill Bill</td>\n";
echo "      <td><img src=\"images/2.jpg\"/></br>O Brother, Where Art...</td>\n";
echo "      <td><img src=\"images/11.jpg\"/></br>Akira</td>\n";
echo "      <td><img src=\"images/12.jpg\"/></br>Dune</td>\n";
echo "      <td><img src=\"images/18.jpg\"/></br>Ghost in the Shell</td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td><img src=\"images/8.jpg\"/></br>Old Boy</td>\n";
echo "      <td><img src=\"images/9.jpg\"/></br>The Dark Knight Rises</td>\n";
echo "      <td><img src=\"images/10.jpg\"/></br>Dr. Strangelove</td>\n";
echo "      <td><img src=\"images/14.jpg\"/></br>Gladiator</td>\n";
echo "      <td><img src=\"images/15.jpg\"/></br>Ronin</td>\n";
echo "      <td><img src=\"images/19.jpg\"/></br>Terminator</td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td><img src=\"images/3.jpg\"/></br>Pulp Fiction</td>\n";
echo "      <td><img src=\"images/5.jpg\"/></br>Star Wars</td>\n";
echo "      <td><img src=\"images/7.jpg\"/></br>Red Dragon</td>\n";
echo "      <td><img src=\"images/16.jpg\"/></br>Ringu</td>\n";
echo "      <td><img src=\"images/17.jpg\"/></br>Finding Nemo</td>\n";
echo "      <td><img src=\"images/20.jpg\"/></br>Dogma</td>\n";
echo "     </tr>\n";
echo "    </table>\n";

echo "   </div>\n";

// Control section.
echo "   <div id=\"control\">\n";
echo "    <div id=\"control_bar\">\n";
echo "     MOVIES TV SHOWS MUSIC SYSTEM\n";
echo "    </div>\n";
echo "   </div>\n";

PageFooter("https://github.com/Qzofp/Fargo", "Qzofp's Fargo");
?>
