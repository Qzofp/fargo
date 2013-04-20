<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'settings.php';
require_once 'tools/toolbox.php';
require_once 'include/axmc.php';

$aJson['id']     = 0;
$aJson['xbmcid'] = 0; 
$aJson['title']  = "empty";

$aJson['delta']  = GetSetting("MovieDelta");

$i = GetSetting("MovieCounter");
if ($i > 0)
{   
    $db = OpenDatabase();

    $id     = 0;
    $xbmcid = 0;
    $title  = null;

    $sql = "SELECT id, xbmcid, title ".
           "FROM movies ".
           "WHERE id = $i";

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
                    $stmt->bind_result($id, $xbmcid, $title);
                    while($stmt->fetch())
                    {                
                        $aJson['id']     = $id;
                        $aJson['xbmcid'] = $xbmcid;  
                        $aJson['title']  = $title;
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
}

echo json_encode($aJson);
?>
