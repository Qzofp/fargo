<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    login.php
 *
 * Created on May 04, 2013
 * Updated on May 04, 2013
 *
 * Description: Fargo's login page. 
 *
 */

/////////////////////////////////////////////    Main Code    /////////////////////////////////////////////

session_start();
require_once 'settings.php';
require_once 'tools/toolbox.php';

// POST data/
$username = GetButtonValue("username");
$password = GetButtonValue("password");

$db = OpenDatabase();

$username = mysqli_real_escape_string($db, $username);
$password = mysqli_real_escape_string($db, $password);

$sql = "SELECT * FROM users ".
       "WHERE user='$username' AND password='$password'";

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

CloseDatabase($db);

// Check if login if successful and return data to ajax.
if ($rows > 0) 
{
    $_SESSION['LOGIN'] = true;
    $_SESSION['USER']  = $username;
    echo true;
}
else {
    echo false;
}

?>
