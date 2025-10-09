<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database configuration
$servername = "localhost";
$username = "root";        // your MySQL username
$password = "postgres";            // your MySQL password
$dbname = "teachme";    // database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connected successfully!";
?>
