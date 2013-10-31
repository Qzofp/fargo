<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    common.php
 *
 * Created on Mar 03, 2013
 * Updated on Oct 31, 2013
 *
 * Description: The main Fargo functions page.
 *
 */

////////////////////////////////////////    Interface Functions    ////////////////////////////////////////

/*
 * Function:	ShowHiddenButtonsBox
 *
 * Created on Jun 27, 2013
 * Updated on Jun 27, 2013
 *
 * Description: Show hidden genres box.
 *
 * In:  -
 * Out:	Hidden login box.
 *
 */
function ShowHiddenButtonsBox()
{
    echo "   <div class=\"popup\" id=\"buttons_box\">\n";
    
    echo "    <form method=\"post\" action=\"#\">\n";
    echo "     <div class=\"close_left\">&nbsp;</div>\n";       
    echo "     <div class=\"close_right\">x</div>\n";    
    echo "     <div class=\"title\">Buttons Box</div>\n";
    //echo "     <div class=\"message\"></div>\n";

    echo "     <div class=\"button\">\n";
    echo "     </div>\n";    
    echo "    </form>\n";
    echo "   </div>\n";
}

/*
 * Function:	ShowHiddenLoginBox
 *
 * Created on May 04, 2013
 * Updated on Jul 01, 2013
 *
 * Description: Show hidden login box.
 *
 * In:  -
 * Out:	Hidden login box.
 *
 */
function ShowHiddenLoginBox()
{
    echo "   <div class=\"popup\" id=\"login_box\">\n";
    
    echo "    <form method=\"post\" action=\"#\">\n";
    echo "     <div class=\"close_left\">&nbsp;</div>\n";       
    echo "     <div class=\"close_right\">x</div>\n";    
    echo "     <div class=\"title\">Login Box</div>\n";

//    echo "     <div class=\"message\">Warning!!!</div>\n";
    echo "     <fieldset class=\"textbox\">\n";
    echo "      <label class=\"username\">\n";
    echo "       <span>Username</span>\n";
    echo "       <input id=\"username\" name=\"username\" value=\"\" type=\"text\" autocomplete=\"on\" placeholder=\"Username\">\n";
    echo "      </label>\n";
    echo "      <label class=\"password\">\n";
    echo "       <span>Password</span>\n";
    echo "       <input id=\"password\" name=\"password\" value=\"\" type=\"text\" placeholder=\"Password\">\n";
    echo "      </label>\n";
    echo "     </fieldset>\n";
    
    echo "     <div class=\"button\">\n";
    echo "      <button type=\"button\" class=\"login\">Login</button>\n";
    echo "     </div>\n";    
    echo "    </form>\n";
    echo "   </div>\n";
}

/*
 * Function:	ShowHiddenInfoBox
 *
 * Created on Jul 05, 2013
 * Updated on Aug 25, 2013
 *
 * Description: Show hidden info media box.
 *
 * In:  -
 * Out:	Hidden login box.
 *
 */
function ShowHiddenInfoBox()
{
    echo "   <div class=\"popup\" id=\"info_box\">\n";
    
    echo "    <form method=\"post\" action=\"#\">\n";
    echo "     <div class=\"close_left\">&nbsp;</div>\n";       
    echo "     <div class=\"close_right\">x</div>\n";    
    echo "     <div class=\"title\">Info Box</div>\n";
    //echo "     <div class=\"message\"></div>\n";

    echo "     <div id=\"info_main\">\n";    
    echo "      <div id=\"info_wrapper_left\">\n";
    echo "          <div id=\"info_left\"></div>\n";
    echo "      </div>\n";  
    echo "      <div id=\"info_right\" class=\"fanart_space\">\n";
    echo "       <div id=\"info_fanart\"><img src=\"images/no_fanart.jpg\"></div>\n";    
    echo "       <div id=\"info_video\">&nbsp;</div>\n";
    echo "       <div id=\"info_audio\">&nbsp;</div>\n";
    echo "       <div id=\"info_aspect\">&nbsp;</div>\n";
    echo "       <div id=\"info_mpaa\">&nbsp;</div>\n";
    echo "      </div>\n";
    
    echo "      <div id=\"info_plot\">Plot</div>\n";
    echo "       <div id=\"info_plot_text\"></div>\n";
    echo "      </div>\n";
    
    echo "     <div class=\"button\">\n";
    echo "      <button type=\"button\" class=\"close\">Close</button>\n";
    echo "     </div>\n";    
    echo "    </form>\n";
    echo "   </div>\n";
}

/*
 * Function:	ShowHiddenActionBox
 *
 * Created on Sep 07, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Show hidden action box. This box with yes/no buttons is used for the Refresh and Delete modes.
 *
 * In:  -
 * Out:	Hidden action box.
 *
 */
function ShowHiddenActionBox()
{
    // Note: This should als be used for Import and CleanLibrary.
    echo "   <div class=\"popup\" id=\"action_box\">\n";
    echo "    <form method=\"post\" action=\"#\">\n";
    echo "     <div class=\"close_left\">&nbsp;</div>\n";       
    echo "     <div class=\"close_right\">x</div>\n";
    echo "     <div class=\"id\"></div>\n";
    echo "     <div class=\"xbmcid\"></div>\n";
    echo "     <div class=\"title\">Action Box</div>\n";
    echo "     <div class=\"message\"><br/></div>\n";
    
    // Refresh or Import div's.
    echo "     <div id=\"transfer\"><br/></div>\n";
    echo "     <div id=\"ready\"><br/></div>\n";
    
    echo "     <div id=\"action_wrapper\">\n";
    echo "      <div id=\"action_thumb\"><img src=\"\"/></div>\n";
    echo "     </div>\n";
    echo "     <div id=\"action_title\">&nbsp;</div>\n";
    echo "     <div class=\"progress\"></div>\n";   
    echo "     <div class=\"button\">\n";
    echo "      <button type=\"button\" class=\"yes\">Yes</button>\n";
    echo "      <button type=\"button\" class=\"no\">No</button>\n";
    echo "     </div>\n";
    echo "    </form>\n";    
    echo "   </div>\n";
}

////////////////////////////////////////    Database Functions    /////////////////////////////////////////

/*
 * Function:	GenerateKey
 *
 * Created on Sep 28, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Generate key and store it in the database status table.
 *
 * In:  -
 * Out:	$key
 * 
 */
function GenerateKey()
{
    $key = str_shuffle(md5(time()));    
    UpdateStatus("ImportKey", $key);
    
    return $key;
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
 * Function:	GetStatus
 *
 * Created on Jul 22, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Get a value from the status table.
 *
 * In:  $name
 * Out:	$value
 * 
 */
function GetStatus($name)
{
    $sql = "SELECT value ".
           "FROM status ".
           "WHERE name = '$name'";
    
    list($value) = GetItemsFromDatabase($sql);
    
    return $value;
}

/*
 * Function:	UpdateStatus
 *
 * Created on Jul 22, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Update a row in the status table.
 *
 * In:  $name, $value
 * Out:	Updated value.
 * 
 */
function UpdateStatus($name, $value)
{
    $sql = "UPDATE status ".
           "SET value='$value' ".
           "WHERE name = '$name'";
    
    ExecuteQuery($sql);
}

/*
 * Function:	IncrementStatus
 *
 * Created on Sep 16, 2013
 * Updated on Sep 16, 2013
 *
 * Description: Increment value in the status table.
 *
 * In:  $name, $incr
 * Out:	Incremented value.
 * 
 */
function IncrementStatus($name, $incr)
{
    $sql = "UPDATE status ".
           "SET `value`= `value` + $incr ".
           "WHERE `name` = '$name'";
    
    ExecuteQuery($sql);
}

/*
 * Function:	HideOrShowMedia
 *
 * Created on Sep 23, 2013
 * Updated on Oct 05, 2013
 *
 * Description: Update hide media hide column.
 *
 * In:  $media, $id, $value
 * Out:	Update hide column
 * 
 */
function HideOrShowMedia($media, $id, $value)
{
    $aJson = false;
    
    $sql = "UPDATE $media ".
           "SET hide = $value ".
           "WHERE id = $id";
            
    ExecuteQuery($sql);
    
    $aJson["ready"] = true;
    return $aJson;
}

/*
 * Function:	CountMedia
 *
 * Created on Oct 06, 2013
 * Updated on Oct 06, 2013
 *
 * Description: Count media with hide.
 *
 * In:	$table, $login
 * Out:	$rows
 *
 */
function CountMedia($table, $login)
{
    $db = OpenDatabase();
    $rows = 0;

    $sql = "SELECT count(*) FROM $table";
    if (!$login) {
        $sql .= " WHERE hide = 0";
    } 
    
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $stmt->bind_result($rows);
            $stmt->fetch();
        }
        else
        {
            die('Ececution query failed: '.mysqli_error($db));
        }
        $stmt->close();
    }
    else
    {
        die('Invalid query: '.mysqli_error($db));
    } 
    
    CloseDatabase($db);
    
    return $rows;
}

/*
 * Function:	DeleteMedia(
 *
 * Created on Oct 05, 2013
 * Updated on Oct 05, 2013
 *
 * Description: Delete media from Fargo database.
 *
 * In:  $media, $id, $xbmcid
 * Out:	Deleted media
 * 
 */
function DeleteMedia($media, $id, $xbmcid)
{
    $aJson = false;
    $name  = null;
    
    $sql = "DELETE FROM $media ".
           "WHERE id = $id";
    ExecuteQuery($sql);
    
    switch ($media)
    {
        case "movies"  : $name = "movie";
                         DeleteFile(cMOVIESTHUMBS."/$xbmcid.jpg");
                         DeleteFile(cMOVIESFANART."/$xbmcid.jpg");
                         break;
                            
        case "tvshows" : $name = "tvshow";
                         DeleteFile(cTVSHOWSTHUMBS."/$xbmcid.jpg");
                         DeleteFile(cTVSHOWSFANART."/$xbmcid.jpg");
                         break;
                            
        case "music"   : $name = "music";
                         DeleteFile(cALBUMSTHUMBS."/$xbmcid.jpg");
                         DeleteFile(cALBUMSCOVERS."/$xbmcid.jpg");
                         break;                             
    }
    
    $sql = "DELETE FROM genreto$name ".
           "WHERE ".$name."id = $id";
    ExecuteQuery($sql);
    
    $aJson["ready"] = true;
    return $aJson;    
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
 * Updated on Jun 17, 2013
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
    $aItems[0] = $user;
    
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aItems);
    
    $sql = "UPDATE users ".
           "SET user='$aItems[0]' ".
           "WHERE id = $id";
    
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);
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
    $aItems[0] = $pass;

    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aItems);
    
    $sql = "UPDATE users ".
           "SET password='$aItems[0]' ".
           "WHERE id = $id";
    
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);  
}

/*
 * Function:	LogEvent
 *
 * Created on May 10, 2013
 * Updated on Apr 10, 2013
 *
 * Description: Log event in the database log table. 
 *
 * In:  $type, $event
 * Out: $aItems
 *
 */
function LogEvent($type, $event)
{
    $aItems = null;
    
    if ($type != 'Error' && $type != 'Warning' && $type != 'Information') {
        $type = 'Unknown';
    }
    
    $aItems[0] = date("Y-m-d H:i:s");
    $aItems[1] = $type; 
    $aItems[2] = $event;
    
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aItems);
    
    $sql = "INSERT INTO log (date, type, event) ".
           "VALUES ('$aItems[0]', '$aItems[1]', '$aItems[2]')";
    
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);
    
    return $aItems;
}

//////////////////////////////////////////    Misc Functions    ///////////////////////////////////////////

/*
 * Function:    EncodeLink
 *
 * Created on Jul 02, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Encode image link from XBMC.
 *
 * In:  $aUrl, $type
 * Out:	$link
 *
 */
function EncodeLink($aUrl, $type)
{
    $link = null;
    
    if (!empty($aUrl["$type"]))
    {
        $dummy = rtrim($aUrl["$type"], "/");
        $link = urlencode($dummy);
    }    
    
    return $link;
}        

/*
 * Function:    CreateImageLink
 *
 * Created on Jun 24, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Cleans the image link from XBMC.
 *
 * In:  $aUrl, $type
 * Out:	$link
 *
 *
function CreateImageLink($aUrl, $type)
{
    $link = null;
    
    if (!empty($aUrl["$type"]))
    {
        $link = str_replace("image://", "", $aUrl["$type"]);
        //$link = rtrim($dummy, "/");
    }
    
    return $link;
}
*/

/*
 * Function:    GetImageFromXbmc
 *
 * Created on Jun 24, 2013
 * Updated on Jul 02, 2013
 *
 * Description: Get image (poster, fanart) from XBMC.
 *
 * In:  $type, $id, $link
 * Out:	$image
 *
 */
function GetImageFromXbmc($type, $id, $link)
{
   $conn = GetSetting("XBMCconnection");
   $port = GetSetting("XBMCport");
        
   $url = "http://$conn:$port/image/$link/";
   
   // Get extension.
   $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
   if ($ext == "com") {
       $ext = "jpg";
   }
   
   $image = cTEMPPOSTERS."/$type$id.$ext";
   $check = DownloadFile($url, $image);
    
   // download failed!
   if(!$check) {
       $image = null;
   }    
   
   return $image;
}
?>