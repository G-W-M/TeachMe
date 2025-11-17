<?php
require_once '../../includes/session_check.php';
require_role('admin');
include 'admin_nav.php';

require_once '../../database/conf.php';

// Get all sessions with related data
try {
    $stmt = $pdo->query("
        SELECT s.*, 
               tuser.user_name as tutor_name, 
               luser.user_name as learner_name,
               u.unit_name,
               lr.description as request_description
        FROM sessions s
        JOIN tutor t ON s.tutor_id = t.tutor_id
        JOIN users tuser ON t.tutor_id = tuser.user_id
        JOIN users luser ON s.learner_id = luser.user_id
        JOIN learning_requests lr ON s.request_id = lr.request_id
        JOIN units u ON lr.unit_id = u.unit_id
        ORDER BY s.session_date DESC, s.start_time DESC
    ");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sessions = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sessions - TeachMe Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_nav.php'; ?>

        <div class="admin-main">
            <div class="admin-header">
                <h1>Manage Sessions</h1>
                <div class="user-info">
                    <span>Session Monitoring</span>
                </div>
            </div>

            <div class="admin-content">
                <!-- Session Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Scheduled</h3>
                        <p class="number">
                            <?php echo count(array_filter($sessions, fn($s) => $s['status'] === 'scheduled')); ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Completed</h3>
                        <p class="number">
                            <?php echo count(array_filter($sessions, fn($s) => $s['status'] === 'completed')); ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Cancelled</h3>
                        <p class="number">
                            <?php echo count(array_filter($sessions, fn($s) => $s['status'] === 'cancelled')); ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Sessions</h3>
                        <p class="number"><?php echo count($sessions); ?></p>
                    </div>
                </div>

                <!-- Sessions Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-alt"></i> All Sessions</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($sessions): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Session ID</th>
                                        <th>Tutor</th>
                                        <th>Learner</th>
                                        <th>Subject</th>
                                        <th>Date & Time</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sessions as $session):
                                        $start = strtotime($session['session_date'] . ' ' . $session['start_time']);
                                        $end = strtotime($session['session_date'] . ' ' . $session['end_time']);
                                        $duration = round(($end - $start) / 3600, 1);
                                    ?>
                                        <tr>
                                            <td>#<?php echo $session['session_id']; ?></td>
                                            <td><?php echo htmlspecialchars($session['tutor_name']); ?></td>
                                            <td><?php echo htmlspecialchars($session['learner_name']); ?></td>
                                            <td><?php echo htmlspecialchars($session['unit_name']); ?></td>
                                            <td>
                                                <?php echo date('M j, Y', strtotime($session['session_date'])); ?><br>
                                                <small><?php echo date('g:i A', strtotime($session['start_time'])); ?> - <?php echo date('g:i A', strtotime($session['end_time'])); ?></small>
                                            </td>
                                            <td><?php echo $duration; ?> hours</td>
                                            <td>
                                                <span class="badge badge-<?php echo $session['status']; ?>">
                                                    <?php echo ucfirst($session['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 5px;">
                                                    <button class="btn btn-primary btn-sm" onclick="viewSession(<?php echo $session['session_id']; ?>)">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <?php if ($session['status'] === 'scheduled'): ?>
                                                        <button class="btn btn-warning btn-sm" onclick="cancelSession(<?php echo $session['session_id']; ?>)">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-alt fa-3x"></i>
                                <h3>No Sessions Found</h3>
                                <p>There are no tutoring sessions scheduled at this time.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewSession(sessionId) {
            alert('View session details: ' + sessionId);
            // Implement modal for session details
        }

        function cancelSession(sessionId) {
            if (confirm('Are you sure you want to cancel this session?')) {
                // Implement session cancellation
                alert('Session ' + sessionId + ' cancelled');
            }
        }
    </script>
</body>

</html>