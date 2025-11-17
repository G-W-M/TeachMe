<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    return;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
?>

<aside class="sidebar">
    <nav class="sidebar-nav">
        <?php if ($role === 'admin'): ?>
            <!-- Admin Navigation -->
            <div class="nav-section">
                <h3>Admin Dashboard</h3>
                <ul>
                    <li><a href="../modules/admin/admin_dash.php">Dashboard</a></li>
                    <li><a href="../modules/admin/manage_tutors.php">Manage Tutors</a></li>
                    <li><a href="../modules/admin/manage_sessions.php">Manage Sessions</a></li>
                    <li><a href="../modules/admin/manage_feedback.php">Feedback Management</a></li>
                    <li><a href="../modules/admin/reports.php">Reports & Analytics</a></li>
                    <li><a href="../modules/admin/certificates.php">Certificate Management</a></li>
                    <li><a href="../modules/admin/system_logs.php">System Logs</a></li>
                </ul>
            </div>

        <?php elseif ($role === 'tutor'): ?>
            <!-- Tutor Navigation -->
            <div class="nav-section">
                <h3>Tutor Dashboard</h3>
                <ul>
                    <li><a href="../modules/tutor/tutor_dash.php">Dashboard</a></li>
                    <li><a href="../modules/tutor/tutor_apply.php">Apply for Subjects</a></li>
                    <li><a href="../modules/tutor/take_test.php">Take Competency Test</a></li>
                    <li><a href="../modules/tutor/manage_students.php">My Students</a></li>
                    <li><a href="../modules/tutor/tutor_certificate.php">My Certificates</a></li>
                </ul>
            </div>

        <?php elseif ($role === 'learner'): ?>
            <!-- Learner Navigation -->
            <div class="nav-section">
                <h3>Learner Dashboard</h3>
                <ul>
                    <li><a href="../modules/learner/learner_dash.php">Dashboard</a></li>
                    <li><a href="../modules/learner/find_tutor.php">Find a Tutor</a></li>
                    <li><a href="../modules/learner/give_feedback.php">Give Feedback</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Common Navigation Items -->
        <div class="nav-section">
            <h3>Account</h3>
            <ul>
                <li><a href="../modules/auth/logout.php" class="logout-link">Logout</a></li>
            </ul>
        </div>
    </nav>
</aside>

<style>
.sidebar {
    width: 250px;
    background: #003366;
    color: white;
    min-height: calc(100vh - 60px);
    padding: 20px 0;
}

.sidebar-nav h3 {
    padding: 10px 20px;
    margin: 0;
    font-size: 14px;
    text-transform: uppercase;
    color: #ccc;
    border-bottom: 1px solid #0059b3;
}

.sidebar-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-nav li {
    margin: 0;
}

.sidebar-nav a {
    display: block;
    padding: 12px 20px;
    color: white;
    text-decoration: none;
    transition: background-color 0.3s;
    border-left: 3px solid transparent;
}

.sidebar-nav a:hover {
    background-color: #0059b3;
    border-left-color: #ffcc00;
}

.sidebar-nav .logout-link {
    color: #ff6b6b;
    border-left-color: #ff6b6b;
}

.sidebar-nav .logout-link:hover {
    background-color: #ff6b6b;
    color: white;
}
</style>