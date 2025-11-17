<?php
// Admin navigation component
?>
<!-- Sidebar Navigation -->
<div class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-graduation-cap"></i> TeachMe Admin</h3>
    </div>

    <nav class="sidebar-nav">
        <a href="admin_dash.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dash.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <a href="manage_tutors.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_tutors.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Manage Tutors
        </a>

        <a href="manage_sessions.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_sessions.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i> Manage Sessions
        </a>

        <a href="manage_feedback.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_feedback.php' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i> Feedback
        </a>

        <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i> Reports
        </a>

        <a href="certificates.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'certificates.php' ? 'active' : ''; ?>">
            <i class="fas fa-certificate"></i> Certificates
        </a>

        <a href="system_logs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'system_logs.php' ? 'active' : ''; ?>">
            <i class="fas fa-history"></i> System Logs
        </a>

        <a href="../auth/logout.php" class="logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>