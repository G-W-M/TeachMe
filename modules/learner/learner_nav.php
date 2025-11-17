<?php
// learner_nav.php - Navigation for learner section
// Session is already started, don't start again
?>

<nav class="sidebar">
    <div style="text-align: center; margin-bottom: 30px; padding: 10px;">
        <img src="../../assets/img/learner_icon.png" alt="Learner Icon" style="width: 60px; height: 60px; border-radius: 50%; margin-bottom: 10px;">
        <h3 style="color: white; margin: 5px 0;">Learner Portal</h3>
        <p style="color: #ccc; font-size: 0.9em;">
            Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Learner'); ?>
        </p>
    </div>

    <ul style="list-style: none; padding: 0;">
        <li><a href="learner_dash.php">📊 Dashboard</a></li>
        <li><a href="find_tutor.php">👨‍🏫 Find a Tutor</a></li>
        <li><a href="give_feedback.php">⭐ Give Feedback</a></li>
        <li><a href="learner_utils.php?action=sessions">📅 My Sessions</a></li>
        <li><a href="learner_utils.php?action=profile">👤 My Profile</a></li>
        <li><a href="../../modules/auth/logout.php" style="color: #ff6b6b;">🚪 Logout</a></li>
    </ul>

    <div style="margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 8px;">
        <p style="color: #ccc; font-size: 0.8em; margin: 0;">
            <strong>Quick Stats</strong><br>
            <?php
            // Display quick stats
            require_once '../../database/conf.php';
            try {
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as pending 
                    FROM learning_requests 
                    WHERE learner_id = ? AND status = 'open'
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $pending = $stmt->fetch()['pending'];
                echo "Pending Requests: " . $pending;
            } catch (PDOException $e) {
                echo "Stats unavailable";
            }
            ?>
        </p>
    </div>
</nav>

<div style="margin-left: 220px; min-height: 100vh;">
    <!-- Content will be placed here -->