<?php
require_once '../../includes/session_check.php';
require_role('admin');
include '../admin/admin_nav.php';

// Database connection
require_once '../../database/conf.php';

// Get statistics
$stats = [];
try {
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['total_users'] = $stmt->fetchColumn();

    // Total tutors
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'tutor' AND is_active = 1");
    $stats['total_tutors'] = $stmt->fetchColumn();

    // Total learners
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'learner' AND is_active = 1");
    $stats['total_learners'] = $stmt->fetchColumn();

    // Pending applications
    $stmt = $pdo->query("SELECT COUNT(*) FROM tutor_applications WHERE status = 'pending'");
    $stats['pending_applications'] = $stmt->fetchColumn();

    // Active sessions
    $stmt = $pdo->query("SELECT COUNT(*) FROM sessions WHERE status = 'scheduled'");
    $stats['active_sessions'] = $stmt->fetchColumn();

    // Open requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM learning_requests WHERE status = 'open'");
    $stats['open_requests'] = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_nav.php'; ?>

        <div class="admin-main">
            <div class="admin-header">
                <h1>Admin Dashboard</h1>
                <div class="user-info">
                    <div class="notification-bell">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">3</span>
                    </div>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                </div>
            </div>

            <div class="admin-content">
                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Users</h3>
                        <p class="number"><?php echo $stats['total_users']; ?></p>
                        <span class="trend">+5% this month</span>
                    </div>
                    <div class="stat-card">
                        <h3>Active Tutors</h3>
                        <p class="number"><?php echo $stats['total_tutors']; ?></p>
                        <span class="trend">+2 new</span>
                    </div>
                    <div class="stat-card">
                        <h3>Active Learners</h3>
                        <p class="number"><?php echo $stats['total_learners']; ?></p>
                        <span class="trend">+8% growth</span>
                    </div>
                    <div class="stat-card">
                        <h3>Pending Applications</h3>
                        <p class="number"><?php echo $stats['pending_applications']; ?></p>
                        <span class="trend">Requires attention</span>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <a href="manage_tutors.php" class="btn btn-primary">
                                <i class="fas fa-users"></i> Manage Tutors
                            </a>
                            <a href="manage_sessions.php" class="btn btn-primary">
                                <i class="fas fa-calendar-alt"></i> View Sessions
                            </a>
                            <a href="reports.php" class="btn btn-primary">
                                <i class="fas fa-chart-bar"></i> Generate Reports
                            </a>
                            <a href="system_logs.php" class="btn btn-primary">
                                <i class="fas fa-history"></i> System Logs
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Recent Activity</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            $stmt = $pdo->query("
                                SELECT l.action, l.time, u.user_name, u.role 
                                FROM system_logs l 
                                LEFT JOIN users u ON l.user_id = u.user_id 
                                ORDER BY l.time DESC 
                                LIMIT 10
                            ");
                            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($activities) {
                                echo '<table class="data-table">';
                                echo '<thead><tr><th>User</th><th>Role</th><th>Action</th><th>Time</th></tr></thead>';
                                echo '<tbody>';
                                foreach ($activities as $activity) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($activity['user_name'] ?? 'System') . '</td>';
                                    echo '<td><span class="badge badge-' . ($activity['role'] ?? 'system') . '">' . ucfirst($activity['role'] ?? 'system') . '</span></td>';
                                    echo '<td>' . htmlspecialchars($activity['action']) . '</td>';
                                    echo '<td>' . date('M j, Y g:i A', strtotime($activity['time'])) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo '<div class="empty-state">No recent activity</div>';
                            }
                        } catch (PDOException $e) {
                            echo '<div class="empty-state">Error loading activity</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- System Status -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>System Status</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                            <div>
                                <h4>Active Sessions</h4>
                                <p class="number"><?php echo $stats['active_sessions']; ?></p>
                            </div>
                            <div>
                                <h4>Open Requests</h4>
                                <p class="number"><?php echo $stats['open_requests']; ?></p>
                            </div>
                            <div>
                                <h4>Database</h4>
                                <p style="color: #28a745;"><i class="fas fa-check-circle"></i> Connected</p>
                            </div>
                            <div>
                                <h4>System Load</h4>
                                <p style="color: #28a745;"><i class="fas fa-check-circle"></i> Normal</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>

</html>