<?php
include('Database/conf.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!str_ends_with($email, '@strathmore.edu')) {
        $error = "Only Strathmore emails are allowed.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO students (name, email, password_hash, role) VALUES (?, ?, ?, 'learner')");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Signup failed. Try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <title>TeachMe | Signup</title>
  <link rel="stylesheet" href="Assets/css/forms.css">
</head>
<body>
  <div class="overlay"></div>
  <div class="signup-wrapper">
    <div class="signup-box">
      <h2>TeachMe - Create Account</h2>
      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="POST" action="">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="example@strathmore.edu" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Sign Up</button>
        <p>Already have an account? <a href="login.php">Login</a></p>
      </form>
    </div>
  </div>
</body>
</html>
