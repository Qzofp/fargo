<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    jsonfargo.php
 *
 * Created on Apr 03, 2013
 * Updated on Apr 13, 2013
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
    case "init"   : $sort = GetPageValue('sort');
                    $aJson = GetFargoValues($sort);
                    break;
        
    case "movies" : $page = GetPageValue('page');
                    $sort = GetPageValue('sort');
                    $sql  = CreateQuery($page, $sort);
                    $aJson = GetMovies($sql);
                    break;
    
    case "test"   : break;                   
}

// Return JSON code which is used as input for the JQuery functions.
if (!empty($aJson)) {
    echo json_encode($aJson);
}


//////////////////////////////////////////    Misc Functions    ///////////////////////////////////////////

/*
 * Function:	GetFargoValues
 *
 * Created on Apr 06, 2013
 * Updated on Apr 13, 2013
 *
 * Description: Get a the initialize values from Fargo and return it as Json data. 
 *
 * In:  sort
 * Out: $aJson
 *
 */
function GetFargoValues($sort)
{
    $aJson['row']    = cMediaRow;
    $aJson['column'] = cMediaColumn;
    
    $sql = "SELECT id, xbmcid, title ".
           "FROM movies ";
    
    if ($sort) {
        $sql .= "WHERE title LIKE '$sort%'";
    }    
    
    $total = CountRows($sql);
    
    $aJson['lastpage'] = ceil($total / (cMediaRow * cMediaColumn));
    
    return $aJson;
}

////////////////////////////////////////    Database Functions    /////////////////////////////////////////

/*
 * Function:	CreateQuery
 *
 * Created on Apr 08, 2013
 * Updated on Apr 08, 2013
 *
 * Description: Create the sql query for the media table. 
 *
 * In:  $page, $sort
 * Out: $sql
 *
 */
function CreateQuery($page, $sort)
{
     $sql = "SELECT id, xbmcid, title ".
            "FROM movies ";
    
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
 * Function:	GetMovies
 *
 * Created on Apr 03, 2013
 * Updated on Apr 10, 2013
 *
 * Description: Get a page of movies from Fargo and return it as Json data. 
 *
 * In:  $sql
 * Out: $aJson
 *
 */
function GetMovies($sql)
{
    $aJson = null;
    
    $db = OpenDatabase();

    $id     = 0;
    $xbmcid = 0;
    $title  = null;

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
                    
                    $aJson[$i]['id']     = $id;
                    $aJson[$i]['xbmcid'] = $xbmcid;  
                    $aJson[$i]['title']  = ShortenString($title, 22);
                    
                    $i++;
                }                  
            }
            else
            {
                    $aJson[0]['id']     = 0;
                    $aJson[0]['xbmcid'] = 0;  
                    $aJson[0]['title']  = 'empty';
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

?>
