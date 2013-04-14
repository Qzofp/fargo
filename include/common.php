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


/////////////////////////////////////////    Misc Functions    ////////////////////////////////////////////

/*
 * Function:	ProcessMovies
 *
 * Created on Mar 11, 2013
 * Updated on Apr 05, 2013
 *
 * Description: Process the movies. 
 *
 * In:  $aMovies, $counter
 * Out: -
 *
 */
function ProcessMovies($aMovies, $counter)
{  
    foreach ($aMovies as $aMovie)
    {            
        $aMovie = ConvertMovie($aMovie);
        
        // Import movie and create thumbnail locally. This cost some time.
        ResizeJpegImage($aMovie["thumb"], 100, 140, cMOVIESTHUMBS."/".$aMovie["xbmcid"].".jpg");
        
        InsertMovie($aMovie);
        
        $counter++;
        UpdateSetting("MovieCounter", $counter);
        
        // Debug
        //sleep(1);
    }
}


/*
 * Function:	ConvertMovie
 *
 * Created on Mar 11, 2013
 * Updated on Mar 11, 2013
 *
 * Description: Convert xbmc movie items. For instance to readably URL's.
 *
 * In:  $aXbmc_movie
 * Out: $aMovie
 *
 */
function ConvertMovie($aXbmc_movie)
{
    $aMovie["xbmcid"]  = $aXbmc_movie["movieid"];
    $aMovie["title"]   = $aXbmc_movie["label"];
    $aMovie["imdbnr" ] = $aXbmc_movie["imdbnumber"];
    $aMovie["fanart"]  = CleanImageLink($aXbmc_movie["art"]["fanart"]);
    $aMovie["poster"]  = CleanImageLink($aXbmc_movie["art"]["poster"]);
    $aMovie["thumb"]   = CleanImageLink($aXbmc_movie["thumbnail"]);        
    
    return $aMovie;
}


/*
 * Function:    CleanImageLink
 *
 * Created on Mar 03, 2013
 * Updated on Mar 04, 2013
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
    $clean = str_replace("%2f", "/", $dummy);
    
    return $clean;
}


?>
