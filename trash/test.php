<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    test.php
 *
 * Created on Apr 22, 2013
 * Updated on Apr 27, 2013
 *
 * Description: Fargo's test page.
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

//require_once 'settings.php';
require_once 'tools/toolbox.php';
//require_once 'include/common.php';

$title = "Fargo: A Media Catalog For XBMC";
$css   = "";
$aJavascript = null;

PageHeader($title, $css, $aJavascript);

echo "   <H1>Fargo Tests</H1>\n";

echo "   <form name=\"test\" action=\"testing.php\" method=\"post\">\n";

echo "   <H2>Import Tests</H2>\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Import Movies\">\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Delete Movies\"></br>\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Import TV Shows\">\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Delete TV Shows\"></br>\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Import Music\">\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Delete Music\"></br>\n";

echo "   <H2>JSON XBMC Tests</H2>\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"XBMC Online Test\">\n</br>";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"XBMC Counters Test\">\n</br>";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Import Movies Test\">\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Status Movies Test\"></br>\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Import TV Shows Test\">\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Status TV Shows Test\"></br>\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Import Music Test\">\n";
echo "    <input type=\"submit\" name=\"btnTest\" value=\"Status Music Test\"></br>\n";

echo "   </form></br>\n";


PageFooter("", "", false, false);
?>
