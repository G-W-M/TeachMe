<?php
session_start();
require_once __DIR__ . "/../Database/conf.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT s.student_id, s.name, s.email, t.availability, t.performance_score
          FROM students s
          INNER JOIN tutors t ON s.student_id = t.student_id
          WHERE s.role='tutor'";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tutor List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {font-family:"Poppins",sans-serif;background:url('../img/bg.jpeg') no-repeat center/cover;}
.container {background:#ffffffcc;padding:30px;border-radius:10px;margin-top:60px;}
</style>
</head>
<body>
<div class="container">
<h2 class="text-center mb-4">Tutors</h2>
<table class="table table-striped table-bordered">
<thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Email</th><th>Availability</th><th>Performance</th></tr></thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['student_id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars($row['availability']) ?></td>
<td><?= $row['performance_score'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</body>
</html>
