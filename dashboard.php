<?php
require_once 'auth/session_check.php'; // ensures only logged-in users can access
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>TeachMe | Dashboard</title>
</head>
<body>
  <h2>Welcome to TeachMe Dashboard</h2>
  <p>You are logged in as: <strong><?php echo $_SESSION['role']; ?></strong></p>
  <p>Your Student ID: <strong><?php echo $_SESSION['student_id']; ?></strong></p>

  <a href="auth/logout.php">Logout</a>
</body>
</html>
