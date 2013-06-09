<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    jsonfargo.php
 *
 * Created on Apr 03, 2013
 * Updated on Jun 09, 2013
 *
 * Description: The main Json Fargo page.
 * 
 * Note: This page contains functions that returns Json data for Jquery code.
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/common.php';

$aJson = null;
$action = GetPageValue('action');

switch ($action) 
{
    case "init"    : $media = GetPageValue('media');
                     $sort  = GetPageValue('sort');
                     $aJson = GetFargoValues($media, $sort);
                     break;
                 
    case "counter" : $media = GetPageValue('media');
                     $aJson['counter'] = CountRows($media);
                     break;
                 
    case "status"  : $media = GetPageValue('media');
                     $id    = GetPageValue('id');
                     $aJson = GetStatus($media, $id);
                     break;                 
        
    case "movies"  : $page  = GetPageValue('page');
                     $sort  = GetPageValue('sort');
                     $sql   = CreateQuery($action, $page, $sort);
                     $aJson = GetMedia($action, $sql);
                     break;
                
    case "tvshows" : $page  = GetPageValue('page');
                     $sort  = GetPageValue('sort');
                     $sql   = CreateQuery($action, $page, $sort);
                     $aJson = GetMedia($action, $sql);
                     break;    
                 
    case "music"  : $page  = GetPageValue('page');
                    $sort  = GetPageValue('sort');
                    $sql   = CreateQuery($action, $page, $sort);
                    $aJson = GetMedia($action, $sql);
                    break;   
    
    case "option" : $name  = GetPageValue('name');
                    $aJson = GetSystemOptionProperties($name); 
                    break;
                
    case "property":$option = GetPageValue('option');
                    $number = GetPageValue('number');
                    $value  = GetPageValue('value');
                    $aJson  = SetSystemProperty($option, $number, $value);
                    break;                
                
    case "setting": $name  = GetPageValue('name');
                    $aJson = ProcessSetting($name);
                    break;
                
    case "log"    : $type  = GetPageValue('type');
                    $event = GetPageValue('event');
                    $aJson = LogEvent($type, $event);
                    break;
    
    case "test"   : break;                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson, JSON_UNESCAPED_SLASHES);
}

//////////////////////////////////////////    Misc Functions    ///////////////////////////////////////////

/*
 * Function:	GetMediaStatus
 *
 * Created on May 18, 2013
 * Updated on May 18, 2013
 *
 * Description: Reports the status of the import media process. 
 *
 * In:  $media, $id
 * Out: $aJson
 *
 */
function GetStatus($media, $id)
{
    $aJson = null;   
    switch ($media)    
    {   
        case "movies"   : $aJson = GetImportStatus($media, $id, cMOVIESPOSTERS);
                          break;
        
        case "music"    : $aJson = GetImportStatus($media, $id, cALBUMSCOVERS);
                          break;
    
        case "tvshows"  : $aJson = GetImportStatus($media, $id, cTVSHOWSPOSTERS);
                          break;
    }    
    return $aJson;
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
    
    $sql = "INSERT INTO log (date, type, event) ".
           "VALUES ('$aItems[0]', '$aItems[1]', '$aItems[2]')";
    
    ExecuteQueryWithEscapeStrings($aItems, $sql);
    
    return $aItems;
}


/*
 * Function:	GetFargoValues
 *
 * Created on Apr 06, 2013
 * Updated on May 18, 2013
 *
 * Description: Get a the initialize values from Fargo and return it as Json data. 
 *
 * In:  $media, $sort
 * Out: $aJson
 *
 */
function GetFargoValues($media, $sort)
{
    $aJson['row']    = cMediaRow;
    $aJson['column'] = cMediaColumn;
    
    $sql = "SELECT id, xbmcid, title ".
           "FROM $media ";
    
    if ($sort) {
        $sql .= "WHERE title LIKE '$sort%'";
    }    
    
    $total = CountRowsWithQuery($sql);
    
    $aJson['lastpage'] = ceil($total / (cMediaRow * cMediaColumn));
    
    return $aJson;
}

////////////////////////////////////////    Database Functions    /////////////////////////////////////////

/*
 * Function:	GetImportStatus
 *
 * Created on May 18, 2013
 * Updated on May 19, 2013
 *
 * Description: Reports the status of the import process.
 *
 * In:  $media, $thumbs
 * Out: $aJson
 *
 */
function GetImportStatus($media, $id, $thumbs)
{
    $aJson['xbmcid'] = 0; 
    $aJson['title']  = "empty";
    $aJson['thumbs'] = $thumbs;
  
    $db = OpenDatabase();

    $sql = "SELECT xbmcid, title ".
           "FROM $media ".
           "WHERE id = $id";
        
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            // Get number of rows.
            $stmt->store_result();
            $rows = $stmt->num_rows;

            if ($rows != 0)
            {              
                $stmt->bind_result($xbmcid, $title);
                while($stmt->fetch())
                {                
                    $aJson['xbmcid'] = $xbmcid;  
                    $aJson['title']  = ShortenString($title, 50);
                }                  
            }
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

    return $aJson;
}

/*
 * Function:	CreateQuery
 *
 * Created on Apr 08, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Create the sql query for the media table. 
 *
 * In:  $media, $page, $sort
 * Out: $sql
 *
 */
function CreateQuery($media, $page, $sort)
{
     $sql = "SELECT id, xbmcid, title ".
            "FROM $media ";
    
    // Number of movies for 1 page
    $total = cMediaRow * cMediaColumn;
    $offset = ($page - 1) * $total;
    
    if ($sort) {
        $sql .= "WHERE title LIKE '$sort%' ".
                "ORDER BY title ";
    }
    else {
        $sql .= "ORDER BY id DESC ";
    }
    
    $sql .= "LIMIT $offset , $total";
     
    return $sql;
}

/*
 * Function:	GetMedia
 *
 * Created on Apr 03, 2013
 * Updated on May 18, 2013
 *
 * Description: Get a page of media from Fargo and return it as Json data. 
 *
 * In:  $media, $sql, 
 * Out: $aJson
 *
 */
function GetMedia($media, $sql)
{
    $aJson   = null;
    $aParams = null;
    $aMedia  = null;
    
    switch($media)
    {
        case "movies"   : $aParams['thumbs'] = cMOVIESPOSTERS;
                          break;
                      
        case "tvshows"  : $aParams['thumbs'] = cTVSHOWSPOSTERS;
                          break; 
                      
        case "music"    : $aParams['thumbs'] = cALBUMSCOVERS;
                          break;
    }
    
    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            // Get number of rows.
            $stmt->store_result();
            $rows = $stmt->num_rows;

            if ($rows != 0)
            {              
                $i = 0;
                
                $stmt->bind_result($id, $xbmcid, $title);
                while($stmt->fetch())
                {                
                    
                    $aMedia[$i]['id']     = $id;
                    $aMedia[$i]['xbmcid'] = $xbmcid;  
                    $aMedia[$i]['title']  = ShortenString($title, 22);
                    
                    $i++;
                }                  
            }
            else
            {
                    $aMedia[0]['id']     = 0;
                    $aMedia[0]['xbmcid'] = 0;  
                    $aMedia[0]['title']  = 'empty';
            }
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
    
    $aJson['params'] = $aParams;
    $aJson['media']  = $aMedia;
    
    return $aJson;
}

/*
 * Function:	ProcessSetting
 *
 * Created on Jun 09, 2013
 * Updated on Jun 09, 2013
 *
 * Description: Get value from settings database and process value if necessary. 
 *
 * In:  $name 
 * Out: $aJson
 *
 */
function ProcessSetting($name)
{
    $aJson = null;
    $value = GetSetting($name);
    
    if ($value = "Hash") {
        $value = md5($value);
    }
    
    $aJson["value"] = $value;
    
    return $aJson;
}

/*
 * Function:	GetSystemOptionProperties
 *
 * Created on May 20, 2013
 * Updated on Jun 09, 2013
 *
 * Description: Get the system option properties page from the database table settings. 
 *
 * In:  $name
 * Out: $aJson
 *
 */
function GetSystemOptionProperties($name)
{
    $html = null;
    switch(strtolower($name))
    {
        case "statistics" : $html = GetSetting($name);                                
                            $html = str_replace("[movies]", CountRows("movies"), $html);
                            $html = str_replace("[tvshows]", CountRows("tvshows"), $html);
                            $html = str_replace("[music]", CountRows("music"), $html);
                            break;
                                       
        case "settings"   : $html = GetSetting($name);
                            $html = str_replace("[connection]", GetSetting("XBMCconnection"), $html);
                            $html = str_replace("[port]", GetSetting("XBMCport"), $html);
                            $html = str_replace("[xbmcuser]", GetSetting("XBMCusername"), $html);
                            $html = str_replace("[fargouser]", GetUser(1), $html);
                            $html = str_replace("[password]", "******", $html);
                            break;
                        
        case "library"    : $html = GetSetting($name);
                            break;
                        
        case "credits"    : $html = GetSetting($name);
                            break;                        
                        
        case "about"      : $html = GetSetting($name);
                            $html = str_replace("[version]", GetSetting("Version"), $html);
                            break;                        
    }
    
    $aJson['html'] = $html;
    return $aJson;
}

/*
 * Function:	SetSystemProperty
 *
 * Created on May 27, 2013
 * Updated on Jun 09, 2013
 *
 * Description: Set the system property. 
 *
 * In:  $option, $number, $value
 * Out: $aJson
 *
 */
function SetSystemProperty($option, $number, $value)
{
    $aJson = null;
    
    switch(strtolower($option))
    {
        case "settings" : SetSettingProperty($number, $value);            
                          break;
        
        default : break;
    }
    
    return $aJson;
}

/*
 * Function:	SetSettingProperty
 *
 * Created on May 27, 2013
 * Updated on May 27, 2013
 *
 * Description: Set the setting property. 
 *
 * In:  $number, $value
 * Out: -
 *
 */
function SetSettingProperty($number, $value)
{
    switch($number)
    {
        case 1 : // Set XBMC Connection
                 UpdateSetting("XBMCconnection", $value);
                 break;
             
        case 2 : // Set XBMC Port
                 UpdateSetting("XBMCport", $value);
                 break;
             
        case 3 : // Set XBMC Username
                 UpdateSetting("XBMCusername", $value);
                 break;
             
        case 4 : // Set XBMC Password
                 UpdateSetting("XBMCpassword", $value);
                 break; 
             
        case 6 : // Set Fargo Username
                 UpdateUser(1, $value);
                 break;
             
        case 7 : // Set Fargo Password
                 UpdatePassword(1, $value);
                 break;               
    }
}
?>
