<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../../tools/toolbox.php';

PageHeader("Progress Bar Test","progress.css");

echo "   <H1>Progress Bar Test</H1>\n";

echo "   <form name=\"progress\" action=\"progress.php\" method=\"post\"></br>\n";

echo "    <input type=\"submit\" value=\"Import Movies\">";

echo "   </form></br>\n";

PageFooter(false);
?>
