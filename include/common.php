<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    common.php
 *
 * Created on Mar 03, 2013
 * Updated on May 27, 2013
 *
 * Description: The main Fargo functions page.
 *
 */

////////////////////////////////////////    Interface Functions    ////////////////////////////////////////

/*
 * Function:	ShowHiddenLoginBox
 *
 * Created on May 04, 2013
 * Updated on May 06, 2013
 *
 * Description: Show hidden login box.
 *
 * In:  -
 * Out:	Hidden login box.
 *
 */
function ShowHiddenLoginBox()
{
    echo "   <div id=\"popup\" class=\"login_size\">\n";
    
    echo "    <form method=\"post\" action=\"#\">\n";
    echo "     <div class=\"close_left\">&nbsp;</div>\n";       
    echo "     <div class=\"close_right\">x</div>\n";    
    echo "     <div class=\"title\">Login</div>\n";

//    echo "     <div class=\"message\">Warning!!!</div>\n";
    echo "     <fieldset class=\"textbox\">\n";
    echo "      <label class=\"username\">\n";
    echo "       <span>Username</span>\n";
    echo "       <input id=\"username\" name=\"username\" value=\"\" type=\"text\" autocomplete=\"on\" placeholder=\"Username\">\n";
    echo "      </label>\n";
    echo "      <label class=\"password\">\n";
    echo "       <span>Password</span>\n";
    echo "       <input id=\"password\" name=\"password\" value=\"\" type=\"password\" placeholder=\"Password\">\n";
    echo "      </label>\n";
    echo "     </fieldset>\n";
    
    echo "     <div class=\"button\">\n";
    echo "      <button type=\"button\" class=\"login\">Login</button>\n";
    echo "     </div>\n";    
    echo "    </form>\n";
    echo "   </div>\n";
}

/*
 * Function:	ShowHiddenImportBox
 *
 * Created on May 06, 2013
 * Updated on May 25, 2013
 *
 * Description: Show hidden import box.
 *
 * In:  -
 * Out:	Hidden import box.
 *
 */
function ShowHiddenImportBox()
{
    echo "   <div id=\"popup\" class=\"import\">\n";
    echo "    <form method=\"post\" action=\"#\">\n";
    echo "     <div class=\"close_left\">&nbsp;</div>\n";       
    echo "     <div class=\"close_right\">x</div>\n";    
    echo "     <div class=\"title\">Import Box</div>\n";
    echo "     <div class=\"message\"><br/></div>\n";
    
    // debug.
    //echo "     <div id=\"counter\"><br/></div>\n";
    //echo "     <div id=\"start\"><br/></div>\n";
    
    echo "     <div id=\"import_wrapper\">\n";
    echo "      <div id=\"thumb\"><img src=\"\"/></div>\n";
    echo "     </div>\n";
    echo "     <div id=\"title\">&nbsp;</div>\n";
    echo "     <div id=\"progress\"></div>\n";    
    echo "     <div class=\"button\">\n";
    echo "      <button type=\"button\" class=\"cancel\">Cancel</button>\n";
    echo "     </div>\n";
    echo "    </form>\n";    
    echo "   </div>\n";    
}

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
 * Updated on Apr 22, 2013
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
    
    $sql = "INSERT INTO music(xbmcid, title, artist, cover) ".
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
 * Function:	UpdateSetting
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

/*
 * Function:	GetUser
 *
 * Created on Mar 27, 2013
 * Updated on Mar 27, 2013
 *
 * Description: Get a user from the user table.
 *
 * In:  $id
 * Out:	$value
 * 
 * Note: Id 1 = Fargo User and Id 2 = XBMC user.
 * 
 */
function GetUser($id)
{
    $sql = "SELECT user ".
           "FROM users ".
           "WHERE id = $id";
    
    list($value) = GetItemsFromDatabase($sql);
    
    return $value;
}

/*
 * Function:	UpdateUser
 *
 * Created on Mar 27, 2013
 * Updated on Mar 27, 2013
 *
 * Description: Update user in the user table.
 *
 * In:  $id, $user
 * Out:	-
 * 
 * Note: Id 1 = Fargo User.
 * 
 */
function UpdateUser($id, $user)
{    
    $aItems = null;
    $aItems[0] = $user;
    
    $sql = "UPDATE users ".
           "SET user='$aItems[0]' ".
           "WHERE id = $id";
    
    ExecuteQueryWithEscapeStrings($aItems, $sql);
}

/*
 * Function:	UpdatePassword
 *
 * Created on Mar 27, 2013
 * Updated on Mar 27, 2013
 *
 * Description: Update password in the user table.
 *
 * In:  $id, $user
 * Out:	-
 * 
 * Note: Id 1 = Fargo User.
 * 
 */
function UpdatePassword($id, $pass)
{    
    $aItems = null;
    $aItems[0] = $pass;
    
    $sql = "UPDATE users ".
           "SET password='$aItems[0]' ".
           "WHERE id = $id";
    
    ExecuteQueryWithEscapeStrings($aItems, $sql);
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
