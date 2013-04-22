<?php
/*
 * Title:   Toolbox
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    html.php
 *
 * Created on Mar 02, 2013
 * Updated on Apr 05, 2013
 *
 * Description: HTML toolbox functions.
 *
 */

/*
 * Function:	PageHeader
 *
 * Created on Mar 02, 2013
 * Updated on Mar 02, 2013
 *
 * Description: Returns a HTML5 page header.
 *
 * In:	$title, $css
 * Out:	header
 *
 */
function PageHeader($title, $css)
{
    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"en\">\n";
    echo " <head>\n";
    echo "  <meta charset=\"utf-8\">\n";
    echo "  <title>$title</title>\n";
    echo "  <link rel=\"stylesheet\" href=\"$css\">\n";
    echo " </head>\n";
   
    echo " <body>\n";
    
    // Start Main.
    echo "  <div id=\"main\">\n";
}

/*
 * Function:	PageFooter
 *
 * Created on Aug 12, 2013
 * Updated on Mar 31, 2013
 *
 * Description: Returns a page footer.
 *
 * In:	$url, $title, $footer
 * Out:	footer
 *
 */
function PageFooter($url, $title, $footer=true)
{
    echo "  </div>\n";
    // End Main.
    
    if ($footer) {
        echo "  <div id=\"footer\">\n";
        echo "   <div id=\"footer_txt\"><a href =\"$url\">$title</a></div>\n";
        echo "  </div>\n";
    }
    
    echo " </body>\n";
    echo "</html>";
}

/*
 * Function:	GetPageValue
 *
 * Created on Jun 26, 2011
 * Updated on Mar 22, 2013
 *
 * Description: Get input value from a page.
 *
 * In:	$name
 * Out:	$value
 *
 */
function GetPageValue($name)
{
    $value = null;
    
    if (isset($_GET[$name])) 
    {
        $value = $_GET[$name];
    }
    
    return $value;
}


/*
 * Function:	GetButtonValue
 *
 * Created on Jun 17, 2011
 * Updated on Jun 18, 2011
 *
 * Description: Get input value from a button.
 *
 * In:	$name
 * Out:	$value
 *
 */
function GetButtonValue($name)
{
    $value = null;
    
    if (isset($_POST[$name]) && !empty($_POST[$name]))
    {
        $value = $_POST[$name];
    }  
    
    return $value;
}


/*
 * Function:	ShortenString
 *
 * Created on Apr 06, 2013
 * Updated on Apr 06, 2013
 *
 * Description: Get input value from a page.
 *
 * In:	$string, length
 * Out:	$short
 *
 */
function ShortenString($string, $length)
{
    $short = $string;
    $string_length = strlen($string);
    $delta = $string_length - $length;
    
    if ($delta > 0)
    {
        $rest = substr($string, 0, -$delta-3);
        $short = rtrim($rest)."...";
        
    }
    
    return $short;
}

?>
