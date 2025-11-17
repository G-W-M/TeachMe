<?php

/**
 * System Logger for TeachMe
 */

/**
 * Log system actions for auditing and debugging
 */
class SystemLogger
{
    private $db;

    public function __construct($database_connection)
    {
        $this->db = $database_connection;
    }

    /**
     * Log an action to the system_logs table
     */
    public function log($user_id, $action, $details = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO system_logs (user_id, action, details, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");

            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            $stmt->execute([
                $user_id,
                $action,
                $details,
                $ip_address,
                $user_agent
            ]);

            return true;
        } catch (PDOException $e) {
            // Fallback to file logging if database fails
            $this->logToFile($user_id, $action, $details, $e->getMessage());
            return false;
        }
    }

    /**
     * Fallback file logging
     */
    private function logToFile($user_id, $action, $details, $error = null)
    {
        $log_entry = sprintf(
            "[%s] User: %d, Action: %s, Details: %s, Error: %s\n",
            date('Y-m-d H:i:s'),
            $user_id,
            $action,
            $details ?? 'None',
            $error ?? 'None'
        );

        file_put_contents('../logs/system.log', $log_entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log user login
     */
    public function logLogin($user_id)
    {
        return $this->log($user_id, 'USER_LOGIN', 'User logged in successfully');
    }

    /**
     * Log user logout
     */
    public function logLogout($user_id)
    {
        return $this->log($user_id, 'USER_LOGOUT', 'User logged out');
    }

    /**
     * Log tutor application
     */
    public function logTutorApplication($user_id, $unit_id)
    {
        return $this->log($user_id, 'TUTOR_APPLICATION', "Applied for unit: $unit_id");
    }

    /**
     * Log session creation
     */
    public function logSessionCreation($user_id, $session_id)
    {
        return $this->log($user_id, 'SESSION_CREATED', "Session ID: $session_id");
    }

    /**
     * Log feedback submission
     */
    public function logFeedback($user_id, $session_id, $rating)
    {
        return $this->log($user_id, 'FEEDBACK_SUBMITTED', "Session: $session_id, Rating: $rating");
    }

    /**
     * Get logs for a specific user
     */
    public function getUserLogs($user_id, $limit = 50)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM system_logs 
                WHERE user_id = ? 
                ORDER BY time DESC 
                LIMIT ?
            ");
            $stmt->execute([$user_id, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get all system logs (admin only)
     */
    public function getAllLogs($limit = 100)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT sl.*, u.user_name, u.email 
                FROM system_logs sl
                LEFT JOIN users u ON sl.user_id = u.user_id
                ORDER BY sl.time DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

// Initialize logger if database connection exists
function initialize_logger()
{
    global $pdo;
    if (isset($pdo)) {
        return new SystemLogger($pdo);
    }
    return null;
}
/**
 * Log an activity performed by a user
 *
 * @param mysqli $conn The MySQLi connection object
 * @param int $user_id ID of the user performing the activity
 * @param string $activity Description of the activity
 * @return bool True on success, false on failure
 */

/**
 * Activity Logger
 */
function logActivity($conn, $user_id, $activity)
{
    if (!$conn || !$user_id || !$activity) {
        error_log("Invalid input for logActivity: user_id=$user_id, activity=$activity");
        return false;
    }

    try {
        // Ensure activity_logs table exists (you should create this in your SQL schema)
        $createTableSQL = "
            CREATE TABLE IF NOT EXISTS activity_logs (
                log_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                action VARCHAR(255) NOT NULL,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX(user_id),
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        if (!$conn->query($createTableSQL)) {
            error_log("Failed to create activity_logs table: " . $conn->error);
            // Continue anyway - table might already exist
        }

        // Insert activity log
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
        if (!$stmt) {
            error_log("Failed to prepare statement: " . $conn->error);
            return false;
        }

        $stmt->bind_param("is", $user_id, $activity);
        $success = $stmt->execute();

        if (!$success) {
            error_log("Failed to log activity: " . $stmt->error);
        }

        $stmt->close();
        return $success;
    } catch (Exception $e) {
        error_log("Logger error: " . $e->getMessage());
        return false;
    }
}
