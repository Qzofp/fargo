<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    importtest2.php
 *
 * Created on Apr 14, 2013
 * Updated on Apr 17, 2013
 *
 * Description: Fargo's import test page.
 *
 */

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

//Empty table and reset counter for test purposes.
EmptyTable("tvshows");
UpdateSetting("TVshowsCounter", 0);
DeleteFile(cTVSHOWSTHUMBS."/*.jpg");

//UpdateSetting("MovieDelta", 1);

PageHeader("Another XBMC Media Catalog","css/frodo.css");

echo "   <H1>Another XBMC Media Catalog</H1>\n";
echo "   <H2>Progress Bar Test TV Shows</H2>\n";

echo "   <form name=\"progress\" action=\"progresstest2.php\" method=\"post\"></br>\n";
echo "    <input type=\"submit\" value=\"Import Movies\">";
echo "   </form></br>\n";

PageFooter("", "", false);
?>

