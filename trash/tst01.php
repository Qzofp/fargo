<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$params = 'xbmc:xbmc@localhost:8080';
require_once '../rpc/HTTPClient.php';
try {
    $rpc = new XBMC_RPC_HTTPClient($params);
} 
catch (XBMC_RPC_ConnectionException $e) {
    die($e->getMessage());
}

// Mute XBMC  
// JSON: {"id":1,"jsonrpc":"2.0","method":"Application.SetMute","params":{"mute":"toggle"}}
try {
    $response = $rpc->Application->SetMute(array("mute" => "toggle"));       
} 
catch (XBMC_RPC_Exception $e) {
    die($e->getMessage());
}

echo "<pre>"; print_r($response); echo "</pre>"; 

?>
