<?php
session_start();
require_once("Database/conf.php");


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
