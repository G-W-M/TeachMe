<?php
// tutor_verify.php
// Admin interface to review tutors, revoke tutor status, and issue certificates.

include('Database/conf.php');
include('session_check.php');

// Only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = "";
