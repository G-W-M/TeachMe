<?php
require_once '../../includes/session_check.php';
require_once '../../includes/header.php';
require_once '../../database/conf.php';

if ($_SESSION['role'] !== 'learner') {
    header("Location: ../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sessions_for_feedback = [];
$submitted_feedback = [];

// Get completed sessions that don't have feedback from this learner
try {
    $stmt = $pdo->prepare("
        SELECT s.session_id, s.session_date, s.start_time, s.end_time,
               u.user_name as tutor_name, un.unit_name, un.unit_code
        FROM sessions s
        JOIN tutor t ON s.tutor_id = t.tutor_id
        JOIN users u ON t.tutor_id = u.user_id
        JOIN learning_requests lr ON s.request_id = lr.request_id
        JOIN units un ON lr.unit_id = un.unit_id
        WHERE s.learner_id = ? 
        AND s.status = 'completed'
        AND s.session_id NOT IN (
            SELECT session_id FROM feedback WHERE from_user = ?
        )
        ORDER BY s.session_date DESC, s.start_time DESC
    ");
    $stmt->execute([$user_id, $user_id]);
    $sessions_for_feedback = $stmt->fetchAll();

    // Get previously submitted feedback
    $stmt = $pdo->prepare("
        SELECT f.*, u.user_name as tutor_name, un.unit_name,
               s.session_date, s.start_time, s.end_time
        FROM feedback f
        JOIN sessions s ON f.session_id = s.session_id
        JOIN users u ON f.to_user = u.user_id
        JOIN learning_requests lr ON s.request_id = lr.request_id
        JOIN units un ON lr.unit_id = un.unit_id
        WHERE f.from_user = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $submitted_feedback = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Feedback data fetch error: " . $e->getMessage());
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $session_id = $_POST['session_id'];
    $tutor_id = $_POST['tutor_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'] ?? '';

    try {
        // Insert feedback
        $stmt = $pdo->prepare("
            INSERT INTO feedback (session_id, from_user, to_user, rating, comments) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$session_id, $user_id, $tutor_id, $rating, $comments]);

        // Update tutor rating
        $stmt = $pdo->prepare("
            UPDATE tutor 
            SET rating = (
                SELECT AVG(rating) 
                FROM feedback 
                WHERE to_user = tutor_id
            ) 
            WHERE tutor_id = ?
        ");
        $stmt->execute([$tutor_id]);

        $success = "Thank you for your feedback!";

        // Log the action
        $log_stmt = $pdo->prepare("
            INSERT INTO system_logs (user_id, action) 
            VALUES (?, 'Submitted feedback for session ID: ?')
        ");
        $log_stmt->execute([$user_id, $session_id]);

        // Refresh the page to show updated lists
        header("Location: give_feedback.php");
        exit();
    } catch (PDOException $e) {
        error_log("Feedback submission error: " . $e->getMessage());
        $error = "Error submitting feedback. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback - TeachMe</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/learner.css">
    <style>
        .rating-stars {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
        }

        .rating-stars input[type="radio"]:checked~label,
        .rating-stars label:hover,
        .rating-stars label:hover~label {
            color: #ffc107;
        }

        .rating-stars input[type="radio"]:checked+label {
            color: #ffc107;
        }

        .feedback-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
        }

        .stars-display {
            color: #ffc107;
        }
    </style>
</head>

<body class="learner-body">
    <?php include 'learner_nav.php'; ?>

    <div class="container">
        <div class="learner-header">
            <h1>Give Feedback</h1>
            <p>Help improve our tutoring community with your feedback</p>
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

        <!-- Sessions Awaiting Feedback -->
        <div class="learner-card">
            <h3>Sessions Awaiting Your Feedback</h3>
            <?php if (!empty($sessions_for_feedback)): ?>
                <div class="sessions-list">
                    <?php foreach ($sessions_for_feedback as $session): ?>
                        <div class="feedback-item">
                            <div style="display: flex; justify-content: between; align-items: start;">
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; color: #003366;">Session with <?php echo htmlspecialchars($session['tutor_name']); ?></h4>
                                    <p style="margin: 5px 0;"><strong>Subject:</strong> <?php echo htmlspecialchars($session['unit_code'] . ' - ' . $session['unit_name']); ?></p>
                                    <p style="margin: 5px 0;"><strong>Date:</strong> <?php echo date('M j, Y', strtotime($session['session_date'])); ?></p>
                                    <p style="margin: 5px 0;"><strong>Time:</strong> <?php echo date('g:i A', strtotime($session['start_time'])); ?> - <?php echo date('g:i A', strtotime($session['end_time'])); ?></p>
                                </div>
                                <button type="button" onclick="openFeedbackModal(<?php echo $session['session_id']; ?>, '<?php echo htmlspecialchars($session['tutor_name']); ?>')"
                                    class="button-primary">
                                    Give Feedback
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No sessions awaiting feedback. Great job keeping up with your feedback!</p>
            <?php endif; ?>
        </div>

        <!-- Previously Submitted Feedback -->
        <div class="learner-card">
            <h3>Your Previous Feedback</h3>
            <?php if (!empty($submitted_feedback)): ?>
                <div class="feedback-list">
                    <?php foreach ($submitted_feedback as $feedback): ?>
                        <div class="feedback-item">
                            <div style="display: flex; justify-content: between; align-items: start;">
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; color: #0059b3;">Feedback for <?php echo htmlspecialchars($feedback['tutor_name']); ?></h4>
                                    <p style="margin: 5px 0;"><strong>Subject:</strong> <?php echo htmlspecialchars($feedback['unit_name']); ?></p>
                                    <p style="margin: 5px 0;"><strong>Date:</strong> <?php echo date('M j, Y', strtotime($feedback['session_date'])); ?></p>
                                    <p style="margin: 5px 0;">
                                        <strong>Rating:</strong>
                                        <span class="stars-display">
                                            <?php echo str_repeat('⭐', $feedback['rating']); ?>
                                        </span>
                                        (<?php echo $feedback['rating']; ?>/5)
                                    </p>
                                    <?php if (!empty($feedback['comments'])): ?>
                                        <p style="margin: 5px 0;"><strong>Comments:</strong> <?php echo htmlspecialchars($feedback['comments']); ?></p>
                                    <?php endif; ?>
                                    <p style="margin: 5px 0; color: #666; font-size: 0.9em;">
                                        Submitted on <?php echo date('M j, Y g:i A', strtotime($feedback['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You haven't submitted any feedback yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="learner-card" style="width: 500px; max-width: 90%;">
            <h3>Submit Feedback</h3>
            <form method="POST" id="feedbackForm">
                <input type="hidden" name="session_id" id="modal_session_id">
                <input type="hidden" name="tutor_id" id="modal_tutor_id">

                <div id="modalSessionInfo" style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;"></div>

                <div>
                    <label><strong>Rating:</strong></label>
                    <div class="rating-stars">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                            <label for="star<?php echo $i; ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <label for="comments">Comments (optional):</label>
                    <textarea id="comments" name="comments" rows="4" placeholder="Share your experience with this session..."></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" name="submit_feedback" class="button-primary">Submit Feedback</button>
                    <button type="button" onclick="closeFeedbackModal()" style="background: #6c757d;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openFeedbackModal(sessionId, tutorName) {
            // In a real application, you'd fetch tutor_id via AJAX
            // For now, we'll set a placeholder - you might want to adjust this
            document.getElementById('modal_session_id').value = sessionId;
            document.getElementById('modal_tutor_id').value = 0; // This should be set properly
            document.getElementById('modalSessionInfo').innerHTML =
                `<strong>Session with:</strong> ${tutorName}<br>
                 <em>Please be honest and constructive in your feedback.</em>`;
            document.getElementById('feedbackModal').style.display = 'flex';
        }

        function closeFeedbackModal() {
            document.getElementById('feedbackModal').style.display = 'none';
            document.getElementById('feedbackForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('feedbackModal').addEventListener('click', function(e) {
            if (e.target === this) closeFeedbackModal();
        });

        // Star rating interaction
        const stars = document.querySelectorAll('.rating-stars input');
        stars.forEach(star => {
            star.addEventListener('change', function() {
                const labels = document.querySelectorAll('.rating-stars label');
                labels.forEach(label => label.style.color = '#ddd');

                let current = this;
                while (current = current.previousElementSibling) {
                    if (current.type === 'radio') {
                        current.nextElementSibling.style.color = '#ffc107';
                    }
                }
                this.nextElementSibling.style.color = '#ffc107';
            });
        });
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>