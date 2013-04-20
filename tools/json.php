<?php
/*
 * Title:   Toolbox
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    json.php
 *
 * Created on Apr 14, 2013
 * Updated on Apr 17, 2013
 *
 * Description: JSON toolbox functions.
 *
 */

/*
 * Function:	GetHttpRequest
 *
 * Created on Apr 14, 2013
 * Updated on Apr 14, 2013
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
    
    $json = json_decode($result, true);
        
    return $json;
}


/*
 * Function:	OnlineCheckXBMC
 *
 * Created on Mar 11, 2013
 * Updated on Apr 15, 2013
 *
 * Description: Check with JSON if XBMC is online.
 *
 * In:  -
 * Out:	$online (true|false)
 *
 * Note: XBMC Connection is defined in constant cXBMC.
 * 
 */
function OnlineCheckXBMC()
{
    $online = true;
    
    // Check if JSON response.
    // JSON: {"jsonrpc": "2.0", "method": "JSONRPC.Ping", "id": 1}
    $request = '{"jsonrpc": "2.0", "method": "JSONRPC.Ping", "id": 1}';
    $aJson = GetHttpRequest(cURL, $request);    
    
    if ($aJson["result"] != "pong") {
        $online = false;
    }
    return $online;
}
?>
