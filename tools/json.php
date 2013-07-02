<?php
/*
 * Title:   Toolbox
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    json.php
 *
 * Created on Apr 14, 2013
 * Updated on Apr 15, 2013
 *
 * Description: JSON toolbox functions.
 *
 */

/*
 * Function:	GetHttpRequest
 *
 * Created on Apr 14, 2013
 * Updated on Apr 22, 2013
 *
 * Description: 
 *
 * In:	$url, $request
 * Out:	$json
 *
 */
function GetHttpRequest($url, $request)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-length: ".strlen($request)));
     
    $result = curl_exec($ch);
    curl_close($ch);
    
    $json = json_decode($result, true);
        
    return $json;
}
?>
