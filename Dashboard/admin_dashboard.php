<?php
session_start();

// redirect if not admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Teach Me</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Poppins", sans-serif;
      background: url('../img/bg.jpeg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      overflow-y: auto;
    }

    .navbar {
      background-color: rgba(0, 0, 0, 0.8);
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .navbar-brand, .nav-link, .navbar-text {
      color: #fff !important;
    }

    .navbar-text {
      font-weight: 500;
    }

    .admin-container {
      width: 90%;
      max-width: 900px;
      background: #ffffffcc;
      margin: 80px auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    h2 {
      text-align: center;
      color: #222;
      margin-bottom: 25px;
    }

    /* Horizontal action cards */
    .action-list {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .action-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #f7f9fc;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 15px 20px;
      transition: 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      color: #222;
    }

    .action-card:hover {
      background: #eaf2ff;
      transform: translateY(-2px);
    }

    .action-card i {
      font-size: 1.8rem;
      color: #1a73e8;
      margin-right: 10px;
    }

    footer {
      text-align: center;
      padding: 12px;
      background: rgba(0, 0, 0, 0.8);
      color: white;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><i class="bi bi-shield-lock"></i> ADMIN RIGHTS</a>
    <div class="d-flex ms-auto align-items-center">
      <a href="admin_dashboard.php" class="nav-link"><i class="bi bi-house-door-fill"></i></a>
      <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i></a>
      <span class="navbar-text ms-3"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($admin_name); ?></span>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="admin-container">
  <h2>Administrative Control Panel</h2>
  <div class="action-list">
    <a href="student_list.php" class="action-card">
      <div><i class="bi bi-people"></i> Show Student List</div>
      <i class="bi bi-arrow-right-circle"></i>
    </a>

    <a href="tutor_list.php" class="action-card">
      <div><i class="bi bi-person-badge"></i> Show Tutor List</div>
      <i class="bi bi-arrow-right-circle"></i>
    </a>

    <a href="tutor_grades.php" class="action-card">
      <div><i class="bi bi-bar-chart-line"></i> Tutor Grades</div>
      <i class="bi bi-arrow-right-circle"></i>
    </a>

    <a href="tutor_assign.php" class="action-card">
      <div><i class="bi bi-diagram-3"></i> Tutor Portal (Assign Tutors)</div>
      <i class="bi bi-arrow-right-circle"></i>
    </a>

    <a href="learner_feedback.php" class="action-card">
      <div><i class="bi bi-chat-square-text"></i> Learner Feedback</div>
      <i class="bi bi-arrow-right-circle"></i>
    </a>

    <a href="tutor_certificates.php" class="action-card">
      <div><i class="bi bi-award"></i> Certificate Assignment</div>
      <i class="bi bi-arrow-right-circle"></i>
    </a>
  </div>
</div>

<footer>
  &copy; <?php echo date('Y'); ?> Teach Me | Admin Dashboard
</footer>

</body>
</html>
