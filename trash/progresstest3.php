<?php

require_once 'tools/toolbox.php';

PageHeader("Progress Bar Test","css/progress.css");

echo "   <H1>Another XBMC Media Catalog</H1>\n";
echo "   <H2>Progress Bar Test Albums</H2>\n";

echo "    <div id=\"online\"></div>\n";
echo "    <div id=\"counter\"></div>\n";
echo "    <div id=\"delta\"></div>\n";

echo "    <div id=\"thumb\"></div>\n";
echo "    <div id=\"title\"></div>\n";
echo "    <div id=\"progress\"></div>\n";

// Cancel button.
echo "   <form name=\"progress\" action=\"importtest3.php\" method=\"post\"></br>\n";
echo "    <input type=\"submit\" value=\"Cancel\">";
echo "   </form></br>\n";
?>

<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/fargo-import.js"></script>
<script type="text/javascript">
    
$(document).ready(function() 
{   
    ImportMedia("music");
});

</script>


<?php
PageFooter("", "", false);
?>
