<?php
// tutor_verify.php
// Admin interface to review tutors, revoke tutor status, and issue certificates.

include('Database/conf.php');
include('session_check.php');
include('Dashboard/admin.php');

// Only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = "";

// Handle POST actions: revoke or issue certificate
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'revoke' && isset($_POST['tutor_id'])) {
        $tutor_id = intval($_POST['tutor_id']);

        // Get the student_id for this tutor
        $stmt = $conn->prepare("SELECT student_id FROM tutors WHERE tutor_id = ?");
        $stmt->bind_param('i', $tutor_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) 
            {
            $student_id = intval($row['student_id']);
              // Delete tutor record
            $del = $conn->prepare("DELETE FROM tutors WHERE tutor_id = ?");
            $del->bind_param('i', $tutor_id);
            $del->execute();

            // Revoke role to learner
            $upd = $conn->prepare("UPDATE students SET role = 'learner' WHERE student_id = ?");
            $upd->bind_param('i', $student_id);
            $upd->execute();

            $message = "Tutor (ID: $tutor_id) revoked successfully.";
        } else {
            $message = "Tutor not found.";
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'issue_cert' && isset($_POST['tutor_id'])) {
    $tutor_id = intval($_POST['tutor_id']);
    $criteria = trim($_POST['criteria']);

    // Store certificate info (make sure you have a 'certificates' table)
    $stmt = $conn->prepare("INSERT INTO certificates (tutor_id, criteria, issued_on) VALUES (?, ?, NOW())");
    $stmt->bind_param('is', $tutor_id, $criteria);
    if ($stmt->execute()) {
        $message = "Certificate issued successfully for Tutor ID: $tutor_id.";
    } else {
        $message = "Failed to issue certificate: " . $conn->error;
    }
}

}

// Fetch all tutors with student info
$sql = "SELECT t.tutor_id, t.student_id, t.test_score, t.availability, t.performance_score, s.name, s.email
        FROM tutors t
        JOIN students s ON t.student_id = s.student_id
        ORDER BY t.test_score DESC, t.performance_score DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Tutor Verification</title>
    <link rel="stylesheet" href="Assets/css/admin.css">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        .msg { padding: 10px; background: #e7f5e6; margin-bottom: 15px; border: 1px solid #c7e6c7; }
        .danger { background: #ffe6e6; border-color: #ffbcbc; }
    </style>
</head>
<body>
<?php include('Dashboard/admin.php'); ?>
<div class="container">
    <h2>Tutor Verification & Management</h2>
    <?php if (!empty($message)) echo '<div class="msg">'.htmlspecialchars($message).'</div>'; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Tutor ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Test Score</th>
                    <th>Performance Score</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['tutor_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['test_score']); ?></td>
                    <td><?php echo htmlspecialchars($row['performance_score']); ?></td>
                    <td><?php echo htmlspecialchars($row['availability']); ?></td>
                    <td>
                        <!-- Revoke form -->
                        <form method="POST" style="display:inline-block;" onsubmit="return confirm('Revoke this tutor?');">
                            <input type="hidden" name="action" value="revoke">
                            <input type="hidden" name="tutor_id" value="<?php echo intval($row['tutor_id']); ?>">
                            <button type="submit">Revoke</button>
                        </form>

                        <!-- Issue certificate form -->
                        <form method="POST" style="display:inline-block; margin-left:8px;">
                            <input type="hidden" name="action" value="issue_cert">
                            <input type="hidden" name="tutor_id" value="<?php echo intval($row['tutor_id']); ?>">
                            <input type="text" name="criteria" placeholder="Criteria (e.g. Excellent feedback)" required>
                            <button type="submit">Issue Certificate</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tutors found.</p>
    <?php endif; ?>
</div>
</body>
</html>   