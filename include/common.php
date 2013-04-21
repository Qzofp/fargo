<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    common.php
 *
 * Created on Mar 03, 2013
 * Updated on Apr 21, 2013
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
 * Function:	InsertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Insert TV Show in the database.
 *
 * In:  $aTVShow
 * Out:	TV Show in database table "tvshows".
 *
 */
function InsertTVShow($aTVShow)
{
    $xbmcid = $aTVShow["xbmcid"];
    $title  = $aTVShow["title"];
    $imdbnr = $aTVShow["imdbnr"];
    $fanart = $aTVShow["fanart"];
    $poster = $aTVShow["poster"];
    $thumb  = $aTVShow["thumb"];     
    
    $sql = "INSERT INTO tvshows(xbmcid, title, imdbnr, fanart, poster, thumb) ".
           "VALUES ($xbmcid, '$title', '$imdbnr', '$fanart', '$poster', '$thumb')";
      
    ExecuteQuery($sql);
}


/*
 * Function:	InsertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on Apr 21, 2013
 *
 * Description: Insert music album in the database.
 *
 * In:  $aAlbum
 * Out:	Music album in database table "albums".
 *
 */
function InsertAlbum($aAlbum)
{
    $xbmcid = $aAlbum["xbmcid"];
    $title  = $aAlbum["title"];
    $artist = $aAlbum["artist"];
    $cover  = $aAlbum["cover"];
    
    $sql = "INSERT INTO albums(xbmcid, title, artist, cover) ".
           "VALUES ($xbmcid, '$title', '$artist', '$cover')";
      
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


//////////////////////////////////////////    Misc Functions    ///////////////////////////////////////////

/*
 * Function:    CleanImageLink
 *
 * Created on Mar 03, 2013
 * Updated on Apr 21, 2013
 *
 * Description: Cleans the image link from XBMC.
 *
 * In:  $dirty (link)
 * Out:	$clean (link)
 *
 */
function CleanImageLink($dirty)
{
    $dummy = str_replace("image://http%3a", "http:", $dirty);
    $dummy = rtrim(str_replace("%2f", "/", $dummy), "/");
    
    $dummy = str_replace("%3f", "?", $dummy);
    $clean = explode("?", $dummy);
     
    return $clean[0];
}
?>
