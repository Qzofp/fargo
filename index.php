<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    index.php
 *
 * Created on Mar 02, 2013
 * Updated on Apr 13, 2013
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
?>

<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/fargo-interface.js"></script>
<script type="text/javascript">
$(document).ready(function() {   
    LoadFargoMedia();
});
</script>


<?php
PageFooter("https://github.com/Qzofp/Fargo", "Qzofp's Fargo");
?>
