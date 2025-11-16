<?php
include('Database/conf.php');
include('session_check.php');

// Restrict access — only learners can apply
if ($_SESSION['role'] !== 'learner') {
    header('Location: dashboard/learner.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'];
    $score = floatval($_POST['score']);
    $availability = "Not set"; // Placeholder for future update
    $statusMsg = "";

    // Check if learner already applied
    $check = mysqli_query($conn, "SELECT * FROM tutors WHERE student_id = '$student_id'");
    if (mysqli_num_rows($check) > 0) {
        $statusMsg = "You have already applied to become a tutor.";
    } else {
        // Insert into tutors table if score >= 70
        if ($score >= 70) {
            $insert = "INSERT INTO tutors (student_id, test_score, availability) 
                       VALUES ('$student_id', '$score', '$availability')";
            if (mysqli_query($conn, $insert)) {
                // Optionally update their role in students table to pending tutor
                $update = "UPDATE students SET role = 'tutor' WHERE student_id = '$student_id'";
                mysqli_query($conn, $update);

                $statusMsg = "Congratulations! You passed with $score%. You are now registered as a tutor.";
            } else {
                $statusMsg = "Error saving your result. Try again later.";
            }
        } else {
            $statusMsg = "You scored $score%. Minimum passing score is 70%. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tutor Application | TeachMe</title>
  <link rel="stylesheet" href="assets/css/tutor.css">
</head>
<body>
  <div class="tutor-container">
    <h2>Apply to Become a Tutor</h2>
    <p>Take this short test to demonstrate your knowledge. You need at least 70% to qualify.</p>

    <?php if (!empty($statusMsg)) echo "<div class='status-msg'>$statusMsg</div>"; ?>

    <form id="tutorTestForm" method="POST">
      <div id="quiz-container"></div>
      <input type="hidden" name="score" id="scoreInput" value="0">
      <button type="submit" id="submitTestBtn">Submit Test</button>
    </form>
  </div>
   <a href="Dashboard/learner.php">Back to Dashboard</a>
  <script src="assets/js/test.js"></script>
</body>
</html>
