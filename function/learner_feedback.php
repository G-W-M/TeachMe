<?php
session_start();
require_once __DIR__ . "/../Database/conf.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT s1.name AS tutor, s2.name AS learner, se.unit, se.feedback, se.session_date
          FROM sessions se
          INNER JOIN tutors t ON se.tutor_id = t.tutor_id
          INNER JOIN learners l ON se.learner_id = l.learner_id
          INNER JOIN students s1 ON t.student_id = s1.student_id
          INNER JOIN students s2 ON l.student_id = s2.student_id
          WHERE se.feedback IS NOT NULL";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Learner Feedback</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {font-family:"Poppins",sans-serif;background:url('../img/bg.jpeg') no-repeat center/cover;}
.container {background:#ffffffcc;padding:30px;border-radius:10px;margin-top:60px;}
</style>
</head>
<body>
<div class="container">
<h2 class="text-center mb-4">Learner Feedback</h2>
<table class="table table-bordered table-hover">
<thead class="table-dark"><tr><th>Tutor</th><th>Learner</th><th>Unit</th><th>Feedback</th><th>Date</th></tr></thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['tutor']) ?></td>
<td><?= htmlspecialchars($row['learner']) ?></td>
<td><?= htmlspecialchars($row['unit']) ?></td>
<td><?= htmlspecialchars($row['feedback']) ?></td>
<td><?= htmlspecialchars($row['session_date']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</body>
</html>