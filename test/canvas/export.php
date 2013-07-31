<?php
 
/**
* More info about this script on:
* http://stackoverflow.com/questions/11511511/how-to-save-a-png-image-server-side-from-a-base64-data-string
*/


// Give the damn thing cross domain access. Something XBMC won't let you do, the bastards!!!
header("Access-Control-Allow-Origin: *");  // Add "*" to settings.


if (isset($_POST["id"]) && !empty($_POST["id"]))
{
    $id= $_POST["id"];
}

if (isset($_POST["image"]) && !empty($_POST["image"]))
{
    $data = $_POST["image"];   
}

$image = explode('base64,',$data); 
file_put_contents($id.'.jpg', base64_decode($image[1]));
 
?>

