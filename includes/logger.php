<?php
require_once(__DIR__ . '/../database/conf.php');

function logActivity($user_id, $action, $module, $description) {
    global $conn;

    if (!$conn) {
        error_log("Database connection not found in logger.php");
        return false;
    }

    // ✅ Handle invalid user_id (set to NULL for anonymous actions)
    if (empty($user_id) || $user_id == 0) {
        $user_id = null;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("
        INSERT INTO system_logs (user_id, action, category, details, timestamp)
        VALUES (?, ?, ?, ?, NOW())
    ");

    if (!$stmt) {
        error_log("Failed to prepare log statement: " . $conn->error);
        return false;
    }

    // ✅ Correct variable binding with proper NULL handling
    if ($user_id === null) {
        // Bind NULL for user_id
        $stmt->bind_param("isss", $user_id, $action, $module, $description);
        // Explicitly set user_id to NULL since bind_param can't directly bind NULL for integers
        $stmt->send_long_data(0, ''); // Alternative approach
    } else {
        $stmt->bind_param("isss", $user_id, $action, $module, $description);
    }

    $result = $stmt->execute();
    
    if (!$result) {
        error_log("Failed to execute log statement: " . $stmt->error);
    }
    
    $stmt->close();
    return $result;
}

// Alternative better approach with more robust NULL handling:
function logActivityImproved($user_id, $action, $module, $description) {
    global $conn;

    if (!$conn) {
        error_log("Database connection not found in logger.php");
        return false;
    }

    // Handle NULL user_id properly
    $user_id = (empty($user_id) || $user_id == 0) ? null : $user_id;

    // Use different query for NULL user_id
    if ($user_id === null) {
        $stmt = $conn->prepare("
            INSERT INTO system_logs (user_id, action, category, details, timestamp)
            VALUES (NULL, ?, ?, ?, NOW())
        ");
        if ($stmt) {
            $stmt->bind_param("sss", $action, $module, $description);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    } else {
        $stmt = $conn->prepare("
            INSERT INTO system_logs (user_id, action, category, details, timestamp)
            VALUES (?, ?, ?, ?, NOW())
        ");
        if ($stmt) {
            $stmt->bind_param("isss", $user_id, $action, $module, $description);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    }

    error_log("Failed to prepare log statement: " . $conn->error);
    return false;
}
?>