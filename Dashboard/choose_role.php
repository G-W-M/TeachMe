<?php
include('../session_check.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Choose Role</title>
  <link rel="stylesheet" href="../assets/css/main.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f6fa;
      text-align: center;
      padding-top: 100px;
    }
    h2 {
      margin-bottom: 40px;
      color: #2c3e50;
    }
    .role-buttons {
      display: flex;
      justify-content: center;
      gap: 30px;
    }
    .btn {
      background-color: #3498db;
      color: white;
      padding: 15px 30px;
      border-radius: 10px;
      text-decoration: none;
      font-size: 18px;
      transition: 0.3s;
    }
    .btn:hover {
      background-color: #2980b9;
    }
    .admin-only {
      background-color: #2ecc71;
    }
    .admin-only:hover {
      background-color: #27ae60;
    }
  </style>
</head>
<body>
  <h2>Welcome, <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'User'; ?>! Choose Your Role</h2>

  <div class="role-buttons">
    <!-- Learner Page -->
    <a href="learner.php" class="btn">I want to Learn</a>

    <!-- Tutor Application (outside Dashboard folder) -->
  <a href="tutor_portal.php" class="btn">I want to be a Tutor</a>


    <!-- Admin Dashboard visible only for admin email -->
    <?php if (isset($_SESSION['email']) && $_SESSION['email'] === 'admin@strathmore.edu') { ?>
      <a href="admin_dashboard.php" class="btn admin-only">Admin Dashboard</a>
    <?php } ?>
  </div>
</body>
</html>
