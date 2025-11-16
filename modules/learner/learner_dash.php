<?php include('../session_check.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Learner Dashboard</title>
  <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
  <h2>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Learner'); ?>!</h2>
  <p>Ready to start your learning journey?</p>
  <a href="../logout.php">Logout</a>
</body>
</html>
