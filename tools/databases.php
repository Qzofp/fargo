<?php
/*
 * Title:   Toolbox
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    databases.php
 *
 * Created on Mar 09, 2013
 * Updated on May 10, 2013
 *
 * Description: Database toolbox functions.
 *
 */

/*
 * Function:	OpenDatabase
 *
 * Created on Aug 22, 2008
 * Updated on Mar 11, 2013
 *
 * Description: Open the database.
 *
 * In:	$host, $user, $pass, $database
 * Out:	$db
 *
 * Note: cHOST, cUSER, cPASS, cDBASE must be defined of constants. For instance in a settings.php page like:
 *        
 *       define("cHOST",  "localhost");
 *       define("cDBASE", "database_name");
 *       define("cUSER",  "database_user");
 *       define("cPASS",  "password"); 
 * 
 */
function OpenDatabase()
{
    // Make a connection to the database.
    $db = mysqli_connect(cHOST, cUSER, cPASS, cDBASE);
    if (!$db) {
        die('Could not connect: '.mysqli_error($db));
    }

    // Select the database.
    $db_selected = mysqli_select_db($db, cDBASE);
    if (!$db_selected) {
        die ('Can\'t use '.cDBASE.' : '.mysqli_error($db));
    }

    return $db;
}


/*
 * Function:	CloseDatabase
 *
 * Created on Aug 22, 2008
 * Updated on Nov 29, 2009
 *
 * Description: Close the database.
 *
 * In:	$db
 * Out:	-
 *
 */
function CloseDatabase($db)
{
    mysqli_close($db);
}


/*
 * Function:	EmptyTable
 *
 * Created on Mar 09, 2012
 * Updated on Mar 09, 2013
 *
 * Description: Empty a database table.
 *
 * In:	$table
 * Out:	-
 *
 */
function EmptyTable($table)
{
    $db = OpenDatabase();   
    
    $sql = "TRUNCATE TABLE $table";
    
    //echo $sql."</br>";
    ExecuteQuery($sql);
    
    CloseDatabase($db);
}


/*
 * Function:	ExecuteQuery
 *
 * Created on Mar 07, 2010
 * Updated on May 11, 2013
 *
 * Description:  Execute a sql query.
 *
 * In:	$sql
 * Out:	-
 *
 */
function ExecuteQuery($sql)
{
    $db = OpenDatabase();

    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if(!$stmt->execute())
    	{
            die("Ececution of query \"$sql\" failed: </br><b>".mysqli_error($db)."</b>");
   	    // Foutpagina maken, doorgeven fout met session variabele.
    	}
    	$stmt->close();
    }
    else
    {
        die("Invalid query: $sql</br><b>".mysqli_error($db)."</b>");
   	// Foutpagina maken, doorgeven fout met session variabele.
    }

    CloseDatabase($db);
}


/*
 * Function:	ExecuteQueryWithEscapeStrings
 *
 * Created on May 10, 2013
 * Updated on May 10, 2013
 *
 * Description:  Execute a sql query with real escape strings.
 *
 * In:	$sql
 * Out:	-
 *
 * Note: All the items in the query must be defined as $aItems[0], $aItems[1], etc.
 *       For instance: $sql = INSERT INTO table(a, b) VALUES ($aItems[0], $aItems[1])
 * 
 */
function ExecuteQueryWithEscapeStrings($aItems, $sql)
{
    $db = OpenDatabase();

    for($i = 0; $i < count($aItems); $i++) 
    {
        $aItems[$i] = mysqli_real_escape_string($db, $aItems[$i]);
    }
        
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if(!$stmt->execute())
    	{
            die("Ececution of query \"$sql\" failed: </br><b>".mysqli_error($db)."</b>");
   	    // Foutpagina maken, doorgeven fout met session variabele.
    	}
    	$stmt->close();
    }
    else
    {
        die("Invalid query: $sql</br><b>".mysqli_error($db)."</b>");
   	// Foutpagina maken, doorgeven fout met session variabele.
    }

    CloseDatabase($db);
}


/*
 * Function:	GetItemsFromDatabase
 *
 * Created on Sep 12, 2010
 * Updated on Mar 11, 2013
 *
 * Description: Get a list of items from the database.
 *
 * In:	$sql
 * Out:	$aItems
 *
 */
function GetItemsFromDatabase($sql)
{
    $aItems = null;
    $name   = null;

    $db = OpenDatabase();
    $stmt = $db->prepare($sql);
    if($stmt)
    {
        if($stmt->execute())
        {
            $i = 0;
            $stmt->bind_result($name);
            while($stmt->fetch())
            {
                $aItems[$i] = $name;
                $i++;
            }
        }
        else
        {
            die("Ececution query failed: </br><b>".mysqli_error($db)."</b>");
            // Foutpagina maken, doorgeven fout met session variabele.
        }
        $stmt->close();
    }
    else
    {
        die("Invalid query: $sql</br><b>".mysqli_error($db)."</b>");
   	// Foutpagina maken, doorgeven fout met session variabele.
    }

    CloseDatabase($db);

    return $aItems;
}


/*
 * Function:	CountRows
 *
 * Created on Dec 20, 2009
 * Updated on May 13, 2013
 *
 * Description: Count the number of rows from a sql query.
 *
 * In:	$table
 * Out:	$rows
 *
 */
function CountRows($table)
{
    $db = OpenDatabase();
    $rows = 0;

    $sql = "SELECT count(*) FROM $table"; 
    
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
?>
