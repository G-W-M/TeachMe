<?php
require_once '../../includes/session_check.php';
require_once '../../includes/header.php';
require_once '../../database/conf.php';

if ($_SESSION['role'] !== 'learner') {
    header("Location: ../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'profile';

// Handle different utility actions
switch ($action) {
    case 'profile':
        displayProfile($pdo, $user_id);
        break;
    case 'sessions':
        displaySessions($pdo, $user_id);
        break;
    case 'update_profile':
        updateProfile($pdo, $user_id);
        break;
    default:
        displayProfile($pdo, $user_id);
}

function displayProfile($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            echo "<div class='error'>User not found.</div>";
            return;
        }
    } catch (PDOException $e) {
        error_log("Profile fetch error: " . $e->getMessage());
        echo "<div class='error'>Error loading profile.</div>";
        return;
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Profile - TeachMe</title>
        <link rel="stylesheet" href="../../assets/css/main.css">
        <link rel="stylesheet" href="../../assets/css/learner.css">
    </head>

    <body class="learner-body">
        <?php include 'learner_nav.php'; ?>

        <div class="container">
            <div class="learner-header">
                <h1>My Profile</h1>
                <p>Manage your account information</p>
            </div>

            <div class="learner-card">
                <h3>Personal Information</h3>
                <form method="POST" action="?action=update_profile">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="user_name">Full Name:</label>
                            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name'] ?? ''); ?>" required>
                        </div>
                        <div>
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                        <div>
                            <label for="student_id">Student ID:</label>
                            <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($user['student_id'] ?? ''); ?>" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div>
                            <label for="phone">Phone:</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="button-primary">Update Profile</button>
                        <a href="?action=change_password" class="button-primary" style="background: #6c757d; margin-left: 10px;">Change Password</a>
                    </div>
                </form>
            </div>

            <div class="learner-card">
                <h3>Account Statistics</h3>
                <?php
                try {
                    // Get learner stats
                    $stmt = $pdo->prepare("
                        SELECT 
                            COUNT(DISTINCT s.session_id) as total_sessions,
                            COUNT(DISTINCT lr.request_id) as total_requests,
                            COUNT(DISTINCT t.tutor_id) as tutors_worked_with
                        FROM users u
                        LEFT JOIN learning_requests lr ON u.user_id = lr.learner_id
                        LEFT JOIN sessions s ON lr.request_id = s.request_id
                        LEFT JOIN tutor t ON s.tutor_id = t.tutor_id
                        WHERE u.user_id = ?
                    ");
                    $stmt->execute([$user_id]);
                    $stats = $stmt->fetch();
                } catch (PDOException $e) {
                    error_log("Stats fetch error: " . $e->getMessage());
                    $stats = ['total_sessions' => 0, 'total_requests' => 0, 'tutors_worked_with' => 0];
                }
                ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                    <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h4 style="color: #003366; margin: 0;">Total Sessions</h4>
                        <p style="font-size: 1.5em; font-weight: bold; margin: 5px 0;"><?php echo $stats['total_sessions']; ?></p>
                    </div>
                    <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h4 style="color: #0059b3; margin: 0;">Learning Requests</h4>
                        <p style="font-size: 1.5em; font-weight: bold; margin: 5px 0;"><?php echo $stats['total_requests']; ?></p>
                    </div>
                    <div style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h4 style="color: #28a745; margin: 0;">Tutors Worked With</h4>
                        <p style="font-size: 1.5em; font-weight: bold; margin: 5px 0;"><?php echo $stats['tutors_worked_with']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../../includes/footer.php'; ?>
    </body>

    </html>
<?php
}

function displaySessions($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, u.user_name as tutor_name, un.unit_name, un.unit_code,
                   lr.description, lr.status as request_status
            FROM sessions s
            JOIN tutor t ON s.tutor_id = t.tutor_id
            JOIN users u ON t.tutor_id = u.user_id
            JOIN learning_requests lr ON s.request_id = lr.request_id
            JOIN units un ON lr.unit_id = un.unit_id
            WHERE s.learner_id = ?
            ORDER BY s.session_date DESC, s.start_time DESC
        ");
        $stmt->execute([$user_id]);
        $sessions = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Sessions fetch error: " . $e->getMessage());
        $sessions = [];
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Sessions - TeachMe</title>
        <link rel="stylesheet" href="../../assets/css/main.css">
        <link rel="stylesheet" href="../../assets/css/learner.css">
    </head>

    <body class="learner-body">
        <?php include 'learner_nav.php'; ?>

        <div class="container">
            <div class="learner-header">
                <h1>My Learning Sessions</h1>
                <p>View your tutoring session history</p>
            </div>

            <div class="learner-card">
                <h3>Session History</h3>
                <?php if (!empty($sessions)): ?>
                    <div class="sessions-list">
                        <?php foreach ($sessions as $session): ?>
                            <div class="session-item" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;">
                                <div style="display: flex; justify-content: between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0; color: #003366;">
                                            <?php echo htmlspecialchars($session['unit_code'] . ' - ' . $session['unit_name']); ?>
                                        </h4>
                                        <p style="margin: 5px 0;"><strong>Tutor:</strong> <?php echo htmlspecialchars($session['tutor_name']); ?></p>
                                        <p style="margin: 5px 0;">
                                            <strong>Date:</strong> <?php echo date('M j, Y', strtotime($session['session_date'])); ?>
                                            | <strong>Time:</strong> <?php echo date('g:i A', strtotime($session['start_time'])); ?> - <?php echo date('g:i A', strtotime($session['end_time'])); ?>
                                        </p>
                                        <p style="margin: 5px 0;">
                                            <strong>Status:</strong>
                                            <span style="padding: 3px 8px; border-radius: 4px; font-size: 0.9em;
                                                background-color: <?php
                                                                    echo $session['status'] === 'completed' ? '#d4edda' : ($session['status'] === 'scheduled' ? '#fff3cd' : '#f8d7da');
                                                                    ?>;
                                                color: <?php
                                                        echo $session['status'] === 'completed' ? '#155724' : ($session['status'] === 'scheduled' ? '#856404' : '#721c24');
                                                        ?>;">
                                                <?php echo ucfirst($session['status']); ?>
                                            </span>
                                        </p>
                                        <?php if (!empty($session['description'])): ?>
                                            <p style="margin: 5px 0;"><strong>Topic:</strong> <?php echo htmlspecialchars($session['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No sessions found. <a href="find_tutor.php" style="color: #0059b3;">Request your first session!</a></p>
                <?php endif; ?>
            </div>
        </div>

        <?php include '../../includes/footer.php'; ?>
    </body>

    </html>
<?php
}

function updateProfile($pdo, $user_id)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ?action=profile");
        exit();
    }

    $user_name = $_POST['user_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET user_name = ?, email = ?, phone = ? 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_name, $email, $phone, $user_id]);

        // Update session data
        $_SESSION['user_name'] = $user_name;
        $_SESSION['email'] = $email;

        // Log the action
        $log_stmt = $pdo->prepare("
            INSERT INTO system_logs (user_id, action) 
            VALUES (?, 'Updated profile information')
        ");
        $log_stmt->execute([$user_id]);

        header("Location: ?action=profile&success=1");
        exit();
    } catch (PDOException $e) {
        error_log("Profile update error: " . $e->getMessage());
        header("Location: ?action=profile&error=1");
        exit();
    }
}
?>