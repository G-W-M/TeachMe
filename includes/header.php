<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeachMe - Peer Tutoring System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/main.css">
    <?php
    // Load role-specific CSS
    if (isset($_SESSION['role'])) {
        echo '<link rel="stylesheet" href="../assets/css/' . $_SESSION['role'] . '.css">';
    }
    ?>
    
    <!-- JavaScript Files -->
    <script src="../assets/js/main.js" defer></script>
    <script src="../assets/js/validation.js" defer></script>
    <script src="../assets/js/notifications.js" defer></script>
</head>
<body class="<?php echo isset($_SESSION['role']) ? $_SESSION['role'] . '-body' : 'auth-body'; ?>">
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="../assets/img/bg.png" alt="TeachMe Logo" style="height: 40px;">
                    <span>TeachMe</span>
                </div>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <span class="role-badge"><?php echo ucfirst($_SESSION['role']); ?></span>
                     <?php 
    if (file_exists('includes/notification_display.php')) {
        include 'includes/notification_display.php';
        display_notifications_ui();
    }
    ?>
                </div>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <div class="main-container">
        <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Sidebar will be included here for logged-in users -->
        <?php include 'sidebar.php'; ?>
        <?php endif; ?>

        <main class="main-content">