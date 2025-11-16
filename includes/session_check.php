<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id']) || !isset($_SESSION['role'])) {
    // Not logged in — redirect to login page
    header("Location: ../home.html");
    exit();
}


$inactive = 1800; // 1800 seconds = 30 mins
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    session_unset();     
    session_destroy();   
    header("Location: ../home.html?message=Session expired, please log in again.");
    exit();
}

$_SESSION['last_activity'] = time(); // Update activity time
?>
