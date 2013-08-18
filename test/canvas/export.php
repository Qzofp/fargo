<?php
 
/**
* More info about this script on:
* http://stackoverflow.com/questions/11511511/how-to-save-a-png-image-server-side-from-a-base64-data-string
*/


// Give the damn thing cross domain access. Something XBMC won't let you do, the bastards!!!
header("Access-Control-Allow-Origin: *");  // Add "*" to settings.

$poster = null;
$fanart = null;

if (isset($_POST["result"]) && !empty($_POST["result"]))
{
    $aJson = $_POST["result"];
}

if (isset($_POST["poster"]) && !empty($_POST["poster"]))
{
    $poster = $_POST["poster"];   
}

if (isset($_POST["fanart"]) && !empty($_POST["fanart"]))
{
    $fanart = $_POST["fanart"];
}

$aMovie = $aJson["movies"][0];
$id = $aMovie["movieid"];

if ($poster) 
{
    $image = explode('base64,',$poster); 
    file_put_contents($id.'_poster.jpg', base64_decode($image[1]));
}

if ($fanart) 
{    
    $image = explode('base64,',$fanart); 
    file_put_contents($id.'_fanart.jpg', base64_decode($image[1]));
}
?>

