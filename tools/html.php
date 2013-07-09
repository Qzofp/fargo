<?php
/*
 * Title:   Toolbox
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    html.php
 *
 * Created on Mar 02, 2013
 * Updated on Jul 08, 2013
 *
 * Description: HTML toolbox functions.
 *
 */

/*
 * Function:	PageHeader
 *
 * Created on Mar 02, 2013
 * Updated on May 11, 2013
 *
 * Description: Returns a HTML5 page header.
 *
 * In:	$title, $aCss, $aJscript
 * Out:	header
 *
 */
function PageHeader($title, $aCss, $aJscript)
{
    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"en\">\n";
    echo " <head>\n";
    echo "  <meta charset=\"utf-8\">\n";
    echo "  <title>$title</title>\n";

    if ($aCss) 
    {    
        foreach ($aCss as $css)
        {
            echo "  $css\n";
        }    
    }
    
    if ($aJscript) 
    {    
        foreach ($aJscript as $js)
        {
            echo "  <script type=\"text/javascript\" src=\"$js\"></script>\n";
        }    
    }
    
    echo " </head>\n";
   
    echo " <body>\n";
    
    // Start Main.
    echo "  <div id=\"main\">\n";
}

/*
 * Function:	PageFooter
 *
 * Created on Aug 12, 2013
 * Updated on Jun 12, 2013
 *
 * Description: Returns a page footer.
 *
 * In:	$url, $title, $footer
 * Out:	footer
 *
 */
function PageFooter($url, $title, $footer)
{
    echo "  </div>\n";
    // End Main.
    
    if ($footer) {
        echo "  <div id=\"footer\">\n";
        echo "   <div id=\"footer_txt\"><a href =\"$url\">$title</a></div>\n";
        echo "  </div>\n";
    }
}

/*
 * Function:	PageEnd
 *
 * Created on Jun 12, 2013
 * Updated on Jun 12, 2013
 *
 * Description: Returns a page end.
 *
 * In:	$js
 * Out:	page end.
 *
 */
function PageEnd($js)
{
    if ($js)
    {
        echo "  <script type=\"text/javascript\">\n";
        echo "  $(document).ready(function() {\n";   
        echo "      $js;\n";
        echo "  });\n";
        echo "  </script>\n";        
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


/*
 * Function:	unescape
 *
 * Created on Jul 08, 2013
 * Updated on Jul 08, 2013
 *
 * Description: Decode the Jquery escape(string) function.
 *
 * In:	$str
 * Out:	unescaped string
 *
 * Note: Code from: http://nl1.php.net/urldecode
 * 
 */
function unescape($str) 
{
    $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
    return html_entity_decode($str,null,'UTF-8');;
}
?>
