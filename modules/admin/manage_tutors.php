<?php
require_once '../../includes/session_check.php';
require_role('admin');
include 'admin_nav.php';

require_once '../../database/conf.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_application'])) {
        $application_id = $_POST['application_id'];
        // Update application status and create tutor record
    } elseif (isset($_POST['reject_application'])) {
        $application_id = $_POST['application_id'];
        // Update application status to rejected
    } elseif (isset($_POST['update_tutor_status'])) {
        $tutor_id = $_POST['tutor_id'];
        $is_active = $_POST['is_active'];
        // Update tutor active status
    }
}

// Get tutor applications
try {
    $stmt = $pdo->query("
        SELECT ta.*, u.user_name, u.email, u.phone, un.unit_name 
        FROM tutor_applications ta 
        JOIN users u ON ta.user_id = u.user_id 
        JOIN units un ON ta.unit_id = un.unit_id 
        ORDER BY ta.application_date DESC
    ");
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $applications = [];
}

// Get active tutors
try {
    $stmt = $pdo->query("
        SELECT t.*, u.user_name, u.email, u.phone, u.is_active,
               COUNT(tc.unit_id) as subjects_count,
               AVG(f.rating) as avg_rating
        FROM tutor t 
        JOIN users u ON t.tutor_id = u.user_id 
        LEFT JOIN tutor_competencies tc ON t.tutor_id = tc.tutor_id 
        LEFT JOIN feedback f ON f.to_user = t.tutor_id 
        GROUP BY t.tutor_id
    ");
    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $tutors = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tutors - TeachMe Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_nav.php'; ?>

        <div class="admin-main">
            <div class="admin-header">
                <h1>Manage Tutors</h1>
                <div class="user-info">
                    <span>Admin Panel</span>
                </div>
            </div>

            <div class="admin-content">
                <!-- Tutor Applications -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-clipboard-list"></i> Tutor Applications</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($applications): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Subject</th>
                                        <th>Test Score</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($app['user_name']); ?></strong><br>
                                                <small><?php echo htmlspecialchars($app['email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($app['unit_name']); ?></td>
                                            <td>
                                                <?php if ($app['test_score']): ?>
                                                    <span class="badge badge-success"><?php echo $app['test_score']; ?>%</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $app['status']; ?>">
                                                    <?php echo ucfirst($app['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($app['application_date'])); ?></td>
                                            <td>
                                                <div style="display: flex; gap: 5px;">
                                                    <?php if ($app['status'] == 'pending'): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="application_id" value="<?php echo $app['application_id']; ?>">
                                                            <button type="submit" name="approve_application" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="application_id" value="<?php echo $app['application_id']; ?>">
                                                            <button type="submit" name="reject_application" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <button class="btn btn-primary btn-sm" onclick="viewApplication(<?php echo $app['application_id']; ?>)">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-clipboard-list fa-3x"></i>
                                <h3>No Applications Found</h3>
                                <p>There are no pending tutor applications at this time.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Active Tutors -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> Active Tutors</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($tutors): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Tutor</th>
                                        <th>Contact</th>
                                        <th>Subjects</th>
                                        <th>Rating</th>
                                        <th>Students</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tutors as $tutor): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($tutor['user_name']); ?></strong>
                                                <?php if ($tutor['bio']): ?>
                                                    <br><small><?php echo substr($tutor['bio'], 0, 50) . '...'; ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($tutor['email']); ?><br>
                                                <small><?php echo htmlspecialchars($tutor['phone']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge"><?php echo $tutor['subjects_count']; ?> subjects</span>
                                            </td>
                                            <td>
                                                <?php if ($tutor['avg_rating']): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-star"></i> <?php echo number_format($tutor['avg_rating'], 1); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">No ratings</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo $tutor['current_students'] . '/' . $tutor['max_students']; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $tutor['is_active'] ? 'active' : 'inactive'; ?>">
                                                    <?php echo $tutor['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 5px;">
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="tutor_id" value="<?php echo $tutor['tutor_id']; ?>">
                                                        <input type="hidden" name="is_active" value="<?php echo $tutor['is_active'] ? '0' : '1'; ?>">
                                                        <button type="submit" name="update_tutor_status" class="btn btn-<?php echo $tutor['is_active'] ? 'warning' : 'success'; ?> btn-sm">
                                                            <i class="fas fa-<?php echo $tutor['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                            <?php echo $tutor['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-primary btn-sm">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-users fa-3x"></i>
                                <h3>No Active Tutors</h3>
                                <p>There are no active tutors in the system.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewApplication(applicationId) {
            // Implement view application modal
            alert('View application: ' + applicationId);
            // You can implement a modal here to show application details
        }
    </script>
</body>

</html>