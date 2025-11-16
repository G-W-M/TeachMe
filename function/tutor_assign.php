<?php
session_start();
require_once __DIR__ . "/../Database/conf.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch tutors & learners
$tutors = $conn->query("SELECT tutor_id, s.name FROM tutors t INNER JOIN students s ON t.student_id = s.student_id");
$learners = $conn->query("SELECT learner_id, s.name FROM learners l INNER JOIN students s ON l.student_id = s.student_id");

// Handle assignment
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tutor_id = $_POST['tutor_id'];
    $learner_id = $_POST['learner_id'];
    $unit = $_POST['unit'];

    $stmt = $conn->prepare("INSERT INTO sessions (tutor_id, learner_id, unit, session_date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $tutor_id, $learner_id, $unit);
    $stmt->execute();
    $msg = "Tutor successfully assigned!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assign Tutors</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {font-family:"Poppins",sans-serif;background:url('../img/bg.jpeg') no-repeat center/cover;}
.container {background:#ffffffcc;padding:30px;border-radius:10px;margin-top:60px;}
</style>
</head>
<body>
<div class="container">
<h2 class="text-center mb-4">Assign Tutor to Learner</h2>
<?php if (!empty($msg)): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<form method="POST" class="p-3">
  <div class="mb-3">
    <label class="form-label">Select Tutor</label>
    <select name="tutor_id" class="form-select" required>
      <option value="">-- Choose Tutor --</option>
      <?php while($t = $tutors->fetch_assoc()): ?>
      <option value="<?= $t['tutor_id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Select Learner</label>
    <select name="learner_id" class="form-select" required>
      <option value="">-- Choose Learner --</option>
      <?php while($l = $learners->fetch_assoc()): ?>
      <option value="<?= $l['learner_id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Unit</label>
    <input type="text" name="unit" class="form-control" placeholder="e.g. Mathematics" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">Assign</button>
</form>
</div>
</body>
</html>
