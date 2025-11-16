<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: home.html?message=You have been logged out successfully.");

exit();
?>
