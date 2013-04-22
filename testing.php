<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    test.php
 *
 * Created on Apr 22, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Fargo's testing page.
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

define("cTESTXBMCURL", "http://localhost/fargo/jsonxbmc.php");

PageHeader("Testing Page", "");
echo "   <H1>Fargo Tests</H1>\n";

$button = GetButtonValue("btnTest");
switch ($button)
{
    case "Import Movies" : ShowImportFields("Importing Movies...");
                           StartImport("movies");
                           break;
    
    case "Delete Movies" : EmptyTable("movies");
                           UpdateSetting("MoviesCounter", 0);
                           DeleteFile(cMOVIESPOSTERS."/*.jpg");
                           echo "   <H2>Movies Deleted!</H2>\n";
                           break;
                         
    case "Import TV Shows" : ShowImportFields("Importing TV Shows...");
                             StartImport("tvshows");
                             break;
    
    case "Delete TV Shows" : EmptyTable("tvshows");
                             UpdateSetting("TVShowsCounter", 0);
                             DeleteFile(cTVSHOWSPOSTERS."/*.jpg");
                             echo "   <H2>TV Shows Deleted!</H2>\n";
                             break;    
    
    case "Import Music" : ShowImportFields("Importing Music...");
                          StartImport("music");
                          break;
    
    case "Delete Music" : EmptyTable("music");
                          UpdateSetting("AlbumsCounter", 0);
                          DeleteFile(cALBUMSCOVERS."/*.jpg");
                          echo "   <H2>Music Deleted!</H2>\n";
                          break;
                      
    case "XBMC Online Test" : echo "   <H2>XBMC Online Test</H2>\n";
                              $request = "action=online";
                              $aJson = GetTestRequest(cTESTXBMCURL, $request);
                              ShowJson($aJson);
                              break;
                          
    case "XBMC Counters Test" : echo "   <H2>XBMC Counters Test</H2>\n";
                                echo "   <H3>XBMC Number of Movies</H3>\n";
                                $request = "action=counter&media=movies";
                                $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                ShowJson($aJson);
                                
                                echo "   <H3>XBMC Number of TV Shows</H3>\n";
                                $request = "action=counter&media=tvshows";
                                $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                ShowJson($aJson);    
                            
                                echo "   <H3>XBMC Number of Music Albums</H3>\n";
                                $request = "action=counter&media=music";
                                $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                ShowJson($aJson);                                
                                break;
                            
    case "Import Movies Test" : echo "   <H2>Import Movies Test</H2>\n";
                                $request = "action=import&media=movies";
                                $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                ShowJson($aJson);  
                                break;
                            
    case "Status Movies Test" : echo "   <H2>Status Movies Test</H2>\n";
                                $request = "action=status&media=movies";
                                $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                ShowJson($aJson);  
                                break;                            
                                                    
    case "Import TV Shows Test" : echo "   <H2>Import TV Shows Test</H2>\n";
                                  $request = "action=import&media=tvshows";
                                  $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                  ShowJson($aJson);  
                                  break;
                            
    case "Status TV Shows Test" : echo "   <H2>Status TV Shows Test</H2>\n";
                                  $request = "action=status&media=tvshows";
                                  $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                  ShowJson($aJson);  
                                  break;                               
                            
    case "Import Music Test" :  echo "   <H2>Import Music Test</H2>\n";
                                $request = "action=import&media=music";
                                $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                ShowJson($aJson);  
                                break;
                            
    case "Status Music Test" :  echo "   <H2>Status Music Test</H2>\n";
                                $request = "action=status&media=music";
                                $aJson = GetTestRequest(cTESTXBMCURL, $request);
                                ShowJson($aJson);  
                                break;                            
        
    default : break;
}

// Cancel button.
echo "   <form name=\"testing\" action=\"test.php\" method=\"post\"></br>\n";
echo "    <input type=\"submit\" value=\"Cancel\">\n";
echo "   </form></br>\n";

PageFooter("", "", false);


//////////////////////////////////////////    Test Functions    ///////////////////////////////////////////

/*
 * Function:	ShowImportFields
 *
 * Created on Apr 22, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Show the import fields.
 *
 * In:	$header
 * Out:	Import fields.
 *
 */
function ShowImportFields($header)
{
    echo "   <H2>$header</H2>\n";
    
    echo "   <div id=\"online\"></div>\n";
    echo "   <div id=\"counter\"></div>\n";
    echo "   <div id=\"delta\"></div>\n";
    echo "   <div id=\"thumb\"></div>\n";
    echo "   <div id=\"title\"></div>\n";
    echo "   <div id=\"progress\"></div>\n";    
}

/*
 * Function:	StartImport
 *
 * Created on Apr 22, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Show the import fields.
 *
 * In:	$media
 * Out:	start import.
 *
 */
function StartImport($media)
{
    echo "   <script type=\"text/javascript\" src=\"js/jquery-1.9.1.min.js\"></script>\n";
    echo "   <script type=\"text/javascript\" src=\"js/fargo-import.js\"></script>\n";
    echo "   <script type=\"text/javascript\">\n";
    
    echo "   $(document).ready(function()\n"; 
    echo "   {\n";   
    echo "     ImportMedia(\"$media\");\n";
    echo "   });\n";

    echo "   </script>\n";    
}

/*
 * Function:	ShowJson
 *
 * Created on Apr 22, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Show JSON code.
 *
 * In:	$json
 * Out:	JSON
 *
 */
function ShowJson($json)
{
    $json = json_decode($json);
    
    echo "<pre>";
    print_r($json);
    echo "</pre></br>";
}

/*
 * Function:	GetTestRequest
 *
 * Created on Apr 22, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Get http request.
 *
 * In:	$url, $request
 * Out:	$resp.
 *
 */
function GetTestRequest($url, $request)
{
    // Get cURL resource
    $curl = curl_init();
    
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => "$url/?$request",
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));
    
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);
    
    return $resp;
}
?>
