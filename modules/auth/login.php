<?php
ini_set('session.cookie_path', '/');
session_start();

require_once '../../database/conf.php';  // TeachMe database
require_once '../../includes/logger.php';

$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"]);
  $password = trim($_POST["password"]);

  if (empty($email) || empty($password)) {
    $error_msg = "Please enter both email and password.";
  } else {

    // Fetch user from TeachMe database
    $stmt = $conn->prepare("
            SELECT user_id, user_name, email, password_hash, role, is_active  
            FROM users 
            WHERE email = ?
        ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();

      // Check verification
      if (!$user['is_active']) {
        $error_msg = "Please verify your email before logging in.";
      } elseif (password_verify($password, $user['password_hash'])) {

        // Set session
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email']     = $user['email'];

        // Log success
        logActivity($user['user_id'], "LOGIN", "Authentication", "User logged in successfully.");

        // Redirect by role
        if ($user['role'] === "admin") {

          header("Location: ../admin/admin_dash.php");
        } elseif ($user['role'] === "tutor") {

          header("Location: ../tutor/tutor_dash.php");
        } else {

          header("Location: ../learner/learner_dash.php");
        }

        exit;
      } else {
        $error_msg = "Incorrect password.";
        logActivity(0, "FAILED_LOGIN", "Authentication", "Incorrect password for $email.");
      }
    } else {
      $error_msg = "No account found with that email.";
      logActivity(0, "FAILED_LOGIN", "Authentication", "Unknown email used: $email.");
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login - TeachMe</title>
  <link rel="stylesheet" href="../../assets/css/auth.css">
</head>

<body>
  <div class="login-wrapper">
    <div class="login-box">
      <h2>Login</h2>

      <?php if (!empty($error_msg)): ?>
        <p class="error"><?php echo htmlspecialchars($error_msg); ?></p>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login" class="form-button">Login</button>
      </form>

      <script src="../../assets/js/forms.js"></script>
      <script src="../../assets/js/notification.js"></script>

      <p class="login-link">Don't have an account?
        <a href="signup.php">Sign Up</a>
      </p>
    </div>
  </div>
</body>

</html>