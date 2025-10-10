<?php
session_start();
require_once("database/conf.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        echo "Please enter both email and password.";
        exit;
    }

    // Check user in the students table
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password_hash"])) {
            // Store session info
            $_SESSION["student_id"] = $user["student_id"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            
            if ($user["role"] === "admin") {
                header("Location: ../Dashboard/admin_dashboard.php");
            } elseif ($user["role"] === "tutor") {
                header("Location: ../Dashboard/tutor_dashboard.php");
            } else {
                header("Location: ../Dashboard/learner_dashboard.php");
            }
            exit;
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <title>TeachMe | Signup</title>
  <link rel="stylesheet" href="assets/css/forms.css">
</head>
<body>
  <div class="overlay"></div>
  <div class="signup-wrapper">
    <div class="signup-box">
      <h2>TeachMe - Login to Account</h2>
      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="POST" action="">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="example@strathmore.edu" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button class = "form-button" type="submit">Login</button>
        <button>Don't have an account? <a href="signup.php">Sign up here</a></p>
      </form>
    </div>
  </div>
</body>
</html>