<?php
// Content of database.php

	$mysqli = new mysqli('localhost', 'YourServerUserNameHere', 'YourPassword', 'MySQLDatabaseName');
    
    if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
    }
?>