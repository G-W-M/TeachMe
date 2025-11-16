<?php
include('../session_check.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tutor Portal | TeachMe</title>
  <link rel="stylesheet" href="../assets/css/main.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
      text-align: center;
      padding-top: 100px;
    }

    h2 {
      color: #2c3e50;
      margin-bottom: 20px;
    }

    p {
      color: #555;
      margin-bottom: 40px;
    }

    .options {
      display: flex;
      justify-content: center;
      gap: 40px;
      flex-wrap: wrap;
    }

    .btn {
      background-color: #3498db;
      color: white;
      padding: 15px 35px;
      border-radius: 10px;
      text-decoration: none;
      font-size: 18px;
      transition: 0.3s;
    }

    .btn:hover {
      background-color: #2980b9;
    }

    .back {
      background-color: #7f8c8d;
    }

    .back:hover {
      background-color: #636e72;
    }
  </style>
</head>
<body>
  <h2>Welcome, <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Tutor'; ?>!</h2>
  <p>Choose an option to get started as a Tutor.</p>

  <div class="options">
    <a href="tutor_dashboard.php" class="btn"> I'm a Tutor </a>

    <a href="../tutor_apply.php" class="btn"> Take Tutor Test</a>
   
    <a href="choose_role.php" class="btn back"> Back</a>
  </div>
</body>
</html>
