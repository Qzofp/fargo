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

// Get Movies  
// JSON: {"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", "params": {"properties" : ["art", "rating", "thumbnail", "playcount", "file"] }, "id": "libMovies"}
try {
    $response = $rpc->VideoLibrary->GetMovies(array("properties" => array("art", "rating", "thumbnail", "playcount", "file", "dateadded", "plot", "imdbnumber", "runtime")));
} 
catch (XBMC_RPC_Exception $e) {
    die($e->getMessage());
}

echo "<pre>"; print_r($response); echo "</pre>"; 

?>
