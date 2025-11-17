<?php
session_start();
require_once '../../database/conf.php'; // This now provides $conn
require_once '../../includes/logger.php'; // Include the logger function

$success_msg = '';
$error_msg = '';

if (isset($_POST['signup'])) {
  // Validate and sanitize inputs
  $user_name = trim($_POST['user_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $role = 'learner'; // default role

  // Basic validation
  if (empty($user_name) || empty($email) || empty($password)) {
    $error_msg = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_msg = "Invalid email format.";
  } elseif (strlen($password) < 6) {
    $error_msg = "Password must be at least 6 characters long.";
  } else {
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    if ($stmt) {
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        $error_msg = "Email already registered.";
      } else {
        // Create user
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("INSERT INTO users (user_name, email, password_hash, role) VALUES (?, ?, ?, ?)");

        if ($stmt2) {
          $stmt2->bind_param("ssss", $user_name, $email, $password_hash, $role);

          if ($stmt2->execute()) {
            $user_id = $stmt2->insert_id;

            // Generate student ID
            $student_id = "STU" . str_pad($user_id, 6, '0', STR_PAD_LEFT);

            // Update user with student ID
            $stmt3 = $conn->prepare("UPDATE users SET student_id = ? WHERE user_id = ?");
            $stmt3->bind_param("si", $student_id, $user_id);
            $stmt3->execute();
            $stmt3->close();

            // Log the activity
            logActivity($conn, $user_id, "User registered: $email");

            $success_msg = "Signup successful! Your Student ID is: $student_id";
          } else {
            $error_msg = "Signup failed. Please try again.";
            error_log("Signup failed: " . $stmt2->error);
          }
          $stmt2->close();
        } else {
          $error_msg = "Database error. Please try again.";
          error_log("Prepare failed: " . $conn->error);
        }
      }
      $stmt->close();
    } else {
      $error_msg = "Database error. Please try again.";
      error_log("Prepare failed: " . $conn->error);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - TeachMe</title>
  <link rel="stylesheet" href="../../assets/css/auth.css">
</head>

<body>
  <div class="signup-wrapper">
    <div class="signup-box">
      <h2>Sign Up</h2>

      <?php if ($success_msg): ?>
        <div style="color: green; margin-bottom: 15px; padding: 10px; background: #d4edda; border-radius: 5px;">
          <?php echo htmlspecialchars($success_msg); ?>
        </div>
        <a href="login.php" class="form-button">Login Now</a>
      <?php else: ?>
        <?php if ($error_msg): ?>
          <div class="error" style="color: red; margin-bottom: 15px; padding: 10px; background: #f8d7da; border-radius: 5px;">
            <?php echo htmlspecialchars($error_msg); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="">
          <input type="text" name="user_name" placeholder="Full Name" value="<?php echo htmlspecialchars($_POST['user_name'] ?? ''); ?>" required>
          <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
          <input type="password" name="password" placeholder="Password (min. 6 characters)" required>
          <button type="submit" name="signup" class="form-button">Sign Up</button>
        </form>

        <p class="signup-link">Already have an account? <a href="login.php">Login</a></p>
      <?php endif; ?>
    </div>
  </div>

  <script src="../../assets/js/validation.js"></script>
</body>

</html>