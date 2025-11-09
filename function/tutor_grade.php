<?php
session_start();
require_once __DIR__ . "/../Database/conf.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT s.name, t.test_score, t.performance_score
          FROM tutors t
          INNER JOIN students s ON s.student_id = t.student_id";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tutor Grades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {font-family:"Poppins",sans-serif;background:url('../img/bg.jpeg') no-repeat center/cover;}
.container {background:#ffffffcc;padding:30px;border-radius:10px;margin-top:60px;}
</style>
</head>
<body>
<div class="container">
<h2 class="text-center mb-4">Tutor Grades</h2>
<table class="table table-bordered table-hover">
<thead class="table-dark"><tr><th>Tutor Name</th><th>Test Score</th><th>Performance Score</th></tr></thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= $row['test_score'] ?></td>
<td><?= $row['performance_score'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</body>
</html>
