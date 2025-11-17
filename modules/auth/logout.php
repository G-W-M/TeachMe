<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Logout - TeachMe</title>
<link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
<div class="signup-wrapper">
  <div class="signup-box">
    <h2>Goodbye!</h2>
    <p>We hope you enjoyed learning with us at TeachMe.</p>
    <a href="login.php" class="form-button">Login Again</a>
  </div>
</div>
</body>
</html>
