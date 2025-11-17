<?php
require_once '../includes/session_check.php';
require_once '../../includes/header.php';
require_once '../../database/conf.php';
require_once('../../includes/logger.php');
// Check if user is learner using the new function
require_role('learner');

$user_id = get_current_user_id();

// Get learner stats
$stats = [];
try {
  // Total sessions
  $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_sessions 
        FROM sessions 
        WHERE learner_id = ?
    ");
  $stmt->execute([$user_id]);
  $stats['total_sessions'] = $stmt->fetch()['total_sessions'];

  // Completed sessions
  $stmt = $pdo->prepare("
        SELECT COUNT(*) as completed_sessions 
        FROM sessions 
        WHERE learner_id = ? AND status = 'completed'
    ");
  $stmt->execute([$user_id]);
  $stats['completed_sessions'] = $stmt->fetch()['completed_sessions'];

  // Pending requests
  $stmt = $pdo->prepare("
        SELECT COUNT(*) as pending_requests 
        FROM learning_requests 
        WHERE learner_id = ? AND status = 'open'
    ");
  $stmt->execute([$user_id]);
  $stats['pending_requests'] = $stmt->fetch()['pending_requests'];

  // Upcoming sessions
  $stmt = $pdo->prepare("
        SELECT s.*, u.user_name as tutor_name, un.unit_name 
        FROM sessions s 
        JOIN tutor t ON s.tutor_id = t.tutor_id 
        JOIN users u ON t.tutor_id = u.user_id 
        JOIN learning_requests lr ON s.request_id = lr.request_id 
        JOIN units un ON lr.unit_id = un.unit_id 
        WHERE s.learner_id = ? AND s.status = 'scheduled' 
        AND s.session_date >= CURDATE() 
        ORDER BY s.session_date, s.start_time 
        LIMIT 5
    ");
  $stmt->execute([$user_id]);
  $upcoming_sessions = $stmt->fetchAll();
} catch (PDOException $e) {
  error_log("Dashboard error: " . $e->getMessage());
  $stats = ['total_sessions' => 0, 'completed_sessions' => 0, 'pending_requests' => 0];
  $upcoming_sessions = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Learner Dashboard - TeachMe</title>
  <link rel="stylesheet" href="../../assets/css/main.css">
  <link rel="stylesheet" href="../../assets/css/learner.css">
</head>

<body class="learner-body">
  <?php include 'learner_nav.php'; ?>

  <div class="container">
    <div class="learner-header">
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
      <p>Your Learning Journey Dashboard</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
      <div class="learner-card">
        <h3>Total Sessions</h3>
        <p style="font-size: 2em; color: #003366; font-weight: bold;"><?php echo $stats['total_sessions']; ?></p>
      </div>
      <div class="learner-card">
        <h3>Completed Sessions</h3>
        <p style="font-size: 2em; color: #28a745; font-weight: bold;"><?php echo $stats['completed_sessions']; ?></p>
      </div>
      <div class="learner-card">
        <h3>Pending Requests</h3>
        <p style="font-size: 2em; color: #ffc107; font-weight: bold;"><?php echo $stats['pending_requests']; ?></p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="learner-card">
      <h3>Quick Actions</h3>
      <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;">
        <a href="find_tutor.php" class="button-primary">Find a Tutor</a>
        <a href="give_feedback.php" class="button-primary">Give Feedback</a>
        <a href="learner_utils.php?action=sessions" class="button-primary">View My Sessions</a>
      </div>
    </div>

    <!-- Upcoming Sessions -->
    <div class="learner-card" id="my-sessions">
      <h3>Upcoming Sessions</h3>
      <?php if (!empty($upcoming_sessions)): ?>
        <div class="sessions-list">
          <?php foreach ($upcoming_sessions as $session): ?>
            <div class="session-item" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;">
              <h4><?php echo htmlspecialchars($session['unit_name']); ?></h4>
              <p><strong>Tutor:</strong> <?php echo htmlspecialchars($session['tutor_name']); ?></p>
              <p><strong>Date:</strong> <?php echo date('M j, Y', strtotime($session['session_date'])); ?></p>
              <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($session['start_time'])); ?> - <?php echo date('g:i A', strtotime($session['end_time'])); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>No upcoming sessions. <a href="find_tutor.php" style="color: #0059b3;">Find a tutor now!</a></p>
      <?php endif; ?>
    </div>
  </div>

  <?php include '../../includes/footer.php'; ?>
</body>

</html>