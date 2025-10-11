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

// Handle POST actions: revoke or issue certificate
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'revoke' && isset($_POST['tutor_id'])) {
        $tutor_id = intval($_POST['tutor_id']);

        // Get the student_id for this tutor
        $stmt = $conn->prepare("SELECT student_id FROM tutors WHERE tutor_id = ?");
        $stmt->bind_param('i', $tutor_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $student_id = intval($row['student_id']);