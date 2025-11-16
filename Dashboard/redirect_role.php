<?php
session_start();

if (!isset($_POST["role"])) {
    header("Location: choose_role.php");
    exit();
}

$role = $_POST["role"];

switch ($role) {
    case "learner":
        header("Location: learner.php");
        break;
    case "tutor":
        header("Location: tutor.php");
        break;
    case "admin":
        header("Location: admin.php");
        break;
    default:
        header("Location: choose_role.php");
        break;
}
exit();
?>
