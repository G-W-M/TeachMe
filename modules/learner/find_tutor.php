<?php
require_once '../../includes/session_check.php';
require_once '../../includes/header.php';
require_once '../../database/conf.php';

if ($_SESSION['role'] !== 'learner') {
    header("Location: ../../index.php");
    exit();
}

$search_results = [];
$units = [];

// Get all units for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM units ORDER BY unit_name");
    $units = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Units fetch error: " . $e->getMessage());
}

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $unit_id = $_POST['unit_id'] ?? '';
    $day = $_POST['day'] ?? '';

    try {
        $sql = "
            SELECT DISTINCT u.user_id, u.user_name, u.email, t.rating, 
                   tc.unit_id, un.unit_name, un.unit_code,
                   ta.day, ta.start_time, ta.end_time
            FROM tutor t 
            JOIN users u ON t.tutor_id = u.user_id 
            JOIN tutor_competencies tc ON t.tutor_id = tc.tutor_id 
            JOIN units un ON tc.unit_id = un.unit_id 
            LEFT JOIN tutor_availability ta ON t.tutor_id = ta.tutor_id 
            WHERE u.is_active = TRUE 
            AND t.current_students < t.max_students
        ";

        $params = [];

        if (!empty($unit_id)) {
            $sql .= " AND tc.unit_id = ?";
            $params[] = $unit_id;
        }

        if (!empty($day)) {
            $sql .= " AND ta.day = ?";
            $params[] = $day;
        }

        $sql .= " ORDER BY t.rating DESC, u.user_name";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $search_results = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Tutor search error: " . $e->getMessage());
        $error = "Error searching for tutors. Please try again.";
    }
}

// Handle request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_tutor'])) {
    $learner_id = $_SESSION['user_id'];
    $tutor_id = $_POST['tutor_id'];
    $unit_id = $_POST['unit_id'];
    $description = $_POST['description'] ?? '';

    try {
        $stmt = $pdo->prepare("
            INSERT INTO learning_requests (learner_id, unit_id, description, status) 
            VALUES (?, ?, ?, 'open')
        ");
        $stmt->execute([$learner_id, $unit_id, $description]);

        $success = "Learning request submitted successfully!";

        // Log the action
        $log_stmt = $pdo->prepare("
            INSERT INTO system_logs (user_id, action) 
            VALUES (?, 'Submitted learning request for unit ID: ?')
        ");
        $log_stmt->execute([$learner_id, $unit_id]);
    } catch (PDOException $e) {
        error_log("Request submission error: " . $e->getMessage());
        $error = "Error submitting request. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Tutor - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/learner.css">
</head>

<body class="learner-body">
    <?php include 'learner_nav.php'; ?>

    <div class="container">
        <div class="learner-header">
            <h1>Find a Tutor</h1>
            <p>Search for available tutors by subject and availability</p>
        </div>

        <!-- Search Form -->
        <div class="learner-card">
            <h3>Search Tutors</h3>
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
                    <div>
                        <label for="unit_id">Subject:</label>
                        <select id="unit_id" name="unit_id">
                            <option value="">All Subjects</option>
                            <?php foreach ($units as $unit): ?>
                                <option value="<?php echo $unit['unit_id']; ?>"
                                    <?php echo isset($_POST['unit_id']) && $_POST['unit_id'] == $unit['unit_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($unit['unit_code'] . ' - ' . $unit['unit_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="day">Available Day:</label>
                        <select id="day" name="day">
                            <option value="">Any Day</option>
                            <option value="mon" <?php echo isset($_POST['day']) && $_POST['day'] == 'mon' ? 'selected' : ''; ?>>Monday</option>
                            <option value="tue" <?php echo isset($_POST['day']) && $_POST['day'] == 'tue' ? 'selected' : ''; ?>>Tuesday</option>
                            <option value="wed" <?php echo isset($_POST['day']) && $_POST['day'] == 'wed' ? 'selected' : ''; ?>>Wednesday</option>
                            <option value="thu" <?php echo isset($_POST['day']) && $_POST['day'] == 'thu' ? 'selected' : ''; ?>>Thursday</option>
                            <option value="fri" <?php echo isset($_POST['day']) && $_POST['day'] == 'fri' ? 'selected' : ''; ?>>Friday</option>
                            <option value="sat" <?php echo isset($_POST['day']) && $_POST['day'] == 'sat' ? 'selected' : ''; ?>>Saturday</option>
                            <option value="sun" <?php echo isset($_POST['day']) && $_POST['day'] == 'sun' ? 'selected' : ''; ?>>Sunday</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit" name="search" class="button-primary">Search Tutors</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Messages -->
        <?php if (isset($success)): ?>
            <div class="learner-card" style="border-left-color: #28a745; background-color: #d4edda;">
                <p style="color: #155724; margin: 0;"><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="learner-card" style="border-left-color: #dc3545; background-color: #f8d7da;">
                <p style="color: #721c24; margin: 0;"><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <!-- Search Results -->
        <?php if (!empty($search_results)): ?>
            <div class="learner-card">
                <h3>Available Tutors</h3>
                <div class="tutors-grid" style="display: grid; gap: 20px;">
                    <?php foreach ($search_results as $tutor): ?>
                        <div class="tutor-item" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
                            <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
                                <div>
                                    <h4 style="margin: 0; color: #003366;"><?php echo htmlspecialchars($tutor['user_name']); ?></h4>
                                    <p style="margin: 5px 0; color: #666;"><?php echo htmlspecialchars($tutor['unit_code'] . ' - ' . $tutor['unit_name']); ?></p>
                                    <p style="margin: 5px 0;">
                                        <strong>Rating:</strong>
                                        <?php echo $tutor['rating'] ? number_format($tutor['rating'], 1) . ' ⭐' : 'No ratings yet'; ?>
                                    </p>
                                    <?php if ($tutor['day']): ?>
                                        <p style="margin: 5px 0;">
                                            <strong>Available:</strong>
                                            <?php echo ucfirst($tutor['day']) . ' ' . date('g:i A', strtotime($tutor['start_time'])) . ' - ' . date('g:i A', strtotime($tutor['end_time'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <button type="button" onclick="openRequestModal(<?php echo $tutor['user_id']; ?>, <?php echo $tutor['unit_id']; ?>, '<?php echo htmlspecialchars($tutor['user_name']); ?>', '<?php echo htmlspecialchars($tutor['unit_code']); ?>')"
                                    class="button-primary">
                                    Request Session
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="learner-card">
                <p>No tutors found matching your criteria. Please try different search terms.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Request Modal -->
    <div id="requestModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="learner-card" style="width: 500px; max-width: 90%;">
            <h3>Request Learning Session</h3>
            <form method="POST" id="requestForm">
                <input type="hidden" name="tutor_id" id="modal_tutor_id">
                <input type="hidden" name="unit_id" id="modal_unit_id">

                <div id="modalTutorInfo" style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;"></div>

                <div>
                    <label for="description">What do you need help with?</label>
                    <textarea id="description" name="description" rows="4" placeholder="Describe the topics you want to cover..." required></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" name="request_tutor" class="button-primary">Submit Request</button>
                    <button type="button" onclick="closeRequestModal()" style="background: #6c757d;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRequestModal(tutorId, unitId, tutorName, unitCode) {
            document.getElementById('modal_tutor_id').value = tutorId;
            document.getElementById('modal_unit_id').value = unitId;
            document.getElementById('modalTutorInfo').innerHTML =
                `<strong>Tutor:</strong> ${tutorName}<br><strong>Subject:</strong> ${unitCode}`;
            document.getElementById('requestModal').style.display = 'flex';
        }

        function closeRequestModal() {
            document.getElementById('requestModal').style.display = 'none';
            document.getElementById('requestForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('requestModal').addEventListener('click', function(e) {
            if (e.target === this) closeRequestModal();
        });
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>