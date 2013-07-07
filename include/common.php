<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    common.php
 *
 * Created on Mar 03, 2013
 * Updated on Jul 06, 2013
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
 * Updated on Jul 06, 2013
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
    echo "      <div id=\"info_right\"><img src=\"images/no_fanart.jpg\"></div>\n";
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
 * Function:	ShowHiddenImportBox
 *
 * Created on May 06, 2013
 * Updated on Jul 04, 2013
 *
 * Description: Show hidden import box.
 *
 * In:  -
 * Out:	Hidden import box.
 *
 */
function ShowHiddenImportBox()
{
    echo "   <div class=\"popup\" id=\"import_box\">\n";
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
    echo "     <div id=\"media_title\">&nbsp;</div>\n";
    echo "     <div class=\"progress\"></div>\n";    
    echo "     <div class=\"button\">\n";
    echo "      <button type=\"button\" class=\"cancel\">Cancel</button>\n";
    echo "     </div>\n";
    echo "    </form>\n";    
    echo "   </div>\n";    
}

/*
 * Function:	ShowHiddenCleanLibraryBox
 *
 * Created on Jun 09, 2013
 * Updated on Jul 04, 2013
 *
 * Description: Show hidden clean library box.
 *
 * In:  -
 * Out:	Hidden clean library box.
 *
 */
function ShowHiddenCleanLibraryBox()
{
    echo "   <div class=\"popup\" id=\"clean_box\">\n";
    echo "    <form method=\"post\" action=\"#\">\n";
    echo "     <div class=\"close_left\">&nbsp;</div>\n";       
    echo "     <div class=\"close_right\">x</div>\n";    
    echo "     <div class=\"title\">Clean Library Box</div>\n";
    echo "     <div class=\"message\"><br/></div>\n";
    
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