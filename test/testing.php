<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Fargo Test Results</title>
 </head>
 <body>
  <H1>Fargo Tests Results</H1>
 <?php
// Database connection settings.
define("cHOST",  "localhost");
define("cDBASE", "fargo");
define("cUSER",  "fargo_dbo");
define("cPASS",  "Mime1276"); 

$name = "btnTest";
if (isset($_POST[$name]) && !empty($_POST[$name])) {
        $button = $_POST[$name];
}  

switch($button)
{
    case "Query 1" : echo "<h2>Test 1</h2>";
                     $start = microtime(true);
                     query1("tvshows");
                     $end = microtime(true);
                     echo "<b>".($end - $start)." seconds</b>";
                     break;
                 
    case "Query 2" : echo "<h2>Test 2</h2>";
                     $start = microtime(true);
                     query1("tvshows");
                     query1("music"); 
                     $end = microtime(true);
                     echo "<b>".($end - $start)." seconds</b>";
                     break;
                 
    case "Query 3" : echo "<h2>Test 3</h2>";
                     $start = microtime(true);
                     query1("tvshows");
                     query1("music");
                     query1("movies");
                     $end = microtime(true);
                     echo "<b>".($end - $start)." seconds</b>";
                     break;              
}

// Test 1
function query1($media)
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
    
    // Query
    $sql = "SELECT id, title FROM $media";
    
    // Execute Query
    $result = $db->query($sql);
    if ($result)
    {
        // Determine number of rows result set.
    	$rows = $result->num_rows;

    	// Close result set.
    	$result->close();
    }
    else {
        die('Ececution query failed: '.$db->error);
    }
    
    echo "Query : $sql</br>";
    echo "Result: $rows rows<br>";
    
    // Close connection
    mysqli_close($db);
}

 ?>
     
  <form name="testing" action="test.php" method="post"></br>
   <input type="submit" value="Back">
  </form>    
 </body>
</html>
