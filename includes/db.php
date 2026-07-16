<?php
    // Require config file
    require_once 'config.php';

    // Create connection
    $conn = new mysqli(
        DB_HOST, 
        DB_USER, 
        DB_PASS,
        DB_NAME,
        DB_PORT
    );

    // Check connection
    if($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }

?>