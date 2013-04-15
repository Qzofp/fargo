<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    common.php
 *
 * Created on Mar 03, 2013
 * Updated on Apr 14, 2013
 *
 * Description: The main Fargo functions page.
 *
 */


////////////////////////////////////////    Database Functions    /////////////////////////////////////////

/*
 * Function:	InsertMovie
 *
 * Created on Mar 09, 2013
 * Updated on Mar 11, 2013
 *
 * Description: Insert movie in the database.
 *
 * In:  $aMovie
 * Out:	Movie in database table "movies".
 *
 */
function InsertMovie($aMovie)
{
    $xbmcid = $aMovie["xbmcid"];
    $title  = $aMovie["title"];
    $imdbnr = $aMovie["imdbnr"];
    $fanart = $aMovie["fanart"];
    $poster = $aMovie["poster"];
    $thumb  = $aMovie["thumb"];     
    
    $sql = "INSERT INTO movies(xbmcid, title, imdbnr, fanart, poster, thumb) ".
           "VALUES ($xbmcid, '$title', '$imdbnr', '$fanart', '$poster', '$thumb')";
      
    ExecuteQuery($sql);

}

/*
 * Function:	GetSetting
 *
 * Created on Mar 18, 2013
 * Updated on Mar 18, 2013
 *
 * Description: Get a value from the settings table.
 *
 * In:  $name
 * Out:	$value
 * 
 */
function GetSetting($name)
{
    $sql = "SELECT value ".
           "FROM settings ".
           "WHERE name = '$name'";
    
    list($value) = GetItemsFromDatabase($sql);
    
    return $value;
}

/*
 * Function:	UpdateMovieCounter
 *
 * Created on Mar 18, 2013
 * Updated on Mar 18, 2013
 *
 * Description: Update a row in the setting table.
 *
 * In:  $name, $value
 * Out:	Updated value.
 * 
 */
function UpdateSetting($name, $value)
{
    $sql = "UPDATE settings ".
           "SET value='$value' ".
           "WHERE name = '$name'";
    
    ExecuteQuery($sql);
}
?>
