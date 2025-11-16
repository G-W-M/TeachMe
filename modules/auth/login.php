<?php
session_start();
require_once("database/conf.php");

$error = ""; // store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
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
                $_SESSION["name"] = $user["full_name"];

                // Redirect to Choose Role page
                header("Location: Dashboard/choose_role.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <title>TeachMe | Login</title>
  <link rel="stylesheet" href="assets/css/forms.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f6fa;
    }
    .signup-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .signup-box {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 400px;
      text-align: center;
    }
    input {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    .form-button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 12px 20px;
      width: 100%;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s;
    }
    .form-button:hover {
      background-color: #2980b9;
    }
    .error {
      color: red;
      margin-bottom: 10px;
    }
    .signup-link {
      margin-top: 15px;
      font-size: 14px;
    }
    .signup-link a {
      color: #3498db;
      text-decoration: none;
    }
    .signup-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="signup-wrapper">
    <div class="signup-box">
      <h2>TeachMe - Login</h2>
      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="POST" action="">
        <input type="email" name="email" placeholder="example@strathmore.edu" required>
        <input type="password" name="password" placeholder="Password" required>
        <button class="form-button" type="submit">Login</button>
      </form>
      <p class="signup-link">Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
  </div>
</body>
</html>
