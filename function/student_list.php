<?php
session_start();
require_once __DIR__ . "/../Database/conf.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT student_id, name, email FROM students WHERE role='learner'";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {font-family: "Poppins", sans-serif; background: url('../img/bg.jpeg') no-repeat center/cover; color:#222;}
.container {background:#ffffffcc; padding:30px; border-radius:10px; margin-top:60px;}
</style>
</head>
<body>
<div class="container">
<h2 class="text-center mb-4">Registered Learners</h2>
<table class="table table-bordered table-hover">
<thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Email</th></tr></thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['student_id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</body>
</html>
