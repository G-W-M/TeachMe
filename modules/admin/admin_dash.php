<?php
require_once('../../includes/session_check.php');
require_login();
require_role('admin');

require_once('../../database/conf.php');
require_once('../../includes/logger.php');

// Log page access
logActivity($_SESSION['user_id'], 'PAGE_ACCESS', 'ADMIN', 'Accessed admin dashboard');

// Fetch dashboard statistics
$counts = [];

// Total Users
$q = $conn->query("SELECT COUNT(*) AS c FROM users");
$counts['users'] = (int)$q->fetch_assoc()['c'];

// Total Tutors
$q = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'tutor'");
$counts['tutors'] = (int)$q->fetch_assoc()['c'];

// Total Learners
$q = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'learner'");
$counts['learners'] = (int)$q->fetch_assoc()['c'];

// Pending Tutor Applications
$q = $conn->query("SELECT COUNT(*) AS c FROM tutor_applications WHERE status = 'pending'");
$counts['pending_applications'] = (int)$q->fetch_assoc()['c'];

// Active Sessions
$q = $conn->query("SELECT COUNT(*) AS c FROM sessions WHERE status = 'scheduled'");
$counts['active_sessions'] = (int)$q->fetch_assoc()['c'];

// Open Learning Requests
$q = $conn->query("SELECT COUNT(*) AS c FROM learning_requests WHERE status = 'open'");
$counts['open_requests'] = (int)$q->fetch_assoc()['c'];

// Recent System Logs
$q = $conn->query("SELECT sl.*, u.user_name 
                   FROM system_logs sl 
                   LEFT JOIN users u ON sl.user_id = u.user_id 
                   ORDER BY sl.timestamp DESC 
                   LIMIT 10");
$recent_logs = $q->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body class="admin-body">
    <!-- Header -->
    <div class="admin-header">
        <div class="container">
            <div class="header-content">
                <h1>TeachMe Admin Dashboard</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
                    <a href="../auth/logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <?php include('admin_nav.php'); ?>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <div class="container">
                <div class="page-header">
                    <h2>Dashboard Overview</h2>
                    <p>System statistics and recent activity</p>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-info">
                            <h3><?php echo $counts['users']; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">🎓</div>
                        <div class="stat-info">
                            <h3><?php echo $counts['tutors']; ?></h3>
                            <p>Tutors</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3><?php echo $counts['learners']; ?></h3>
                            <p>Learners</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3><?php echo $counts['pending_applications']; ?></h3>
                            <p>Pending Applications</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3><?php echo $counts['active_sessions']; ?></h3>
                            <p>Active Sessions</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-info">
                            <h3><?php echo $counts['open_requests']; ?></h3>
                            <p>Open Requests</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="dashboard-section">
                    <h2>Recent System Activity</h2>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Category</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_logs)): ?>
                                        <?php foreach ($recent_logs as $log): ?>
                                            <tr>
                                                <td><?php echo date('M j, H:i', strtotime($log['timestamp'])); ?></td>
                                                <td><?php echo $log['user_name'] ?? 'System'; ?></td>
                                                <td><?php echo htmlspecialchars($log['action']); ?></td>
                                                <td><?php echo htmlspecialchars($log['category']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($log['details'], 0, 50)) . '...'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No recent activity</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('../../includes/footer.php'); ?>
</body>
</html>