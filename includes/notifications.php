<?php
/**
 * Notification System for TeachMe
 */

/**
 * Notification Manager
 */
class NotificationManager {
    private $db;
    
    public function __construct($database_connection) {
        $this->db = $database_connection;
    }
    
    /**
     * Create a new notification
     */
    public function create($user_id, $message, $type = 'info', $related_id = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO notifications (user_id, message, type, related_id) 
                VALUES (?, ?, ?, ?)
            ");
            
            return $stmt->execute([$user_id, $message, $type, $related_id]);
        } catch (PDOException $e) {
            error_log("Notification creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user notifications
     */
    public function getUserNotifications($user_id, $unread_only = false, $limit = 20) {
        try {
            $sql = "
                SELECT * FROM notifications 
                WHERE user_id = ?
            ";
            
            if ($unread_only) {
                $sql .= " AND is_read = FALSE";
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id, $user_id = null) {
        try {
            $sql = "UPDATE notifications SET is_read = TRUE WHERE notification_id = ?";
            $params = [$notification_id];
            
            if ($user_id) {
                $sql .= " AND user_id = ?";
                $params[] = $user_id;
            }
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($user_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE notifications 
                SET is_read = TRUE 
                WHERE user_id = ? AND is_read = FALSE
            ");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM notifications 
                WHERE user_id = ? AND is_read = FALSE
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Create notification for tutor application approval
     */
    public function notifyTutorApplicationApproved($user_id, $unit_name) {
        $message = "Your tutor application for $unit_name has been approved!";
        return $this->create($user_id, $message, 'success');
    }
    
    /**
     * Create notification for new session request
     */
    public function notifyNewSessionRequest($tutor_id, $learner_name, $unit_name) {
        $message = "New session request from $learner_name for $unit_name";
        return $this->create($tutor_id, $message, 'info');
    }
    
    /**
     * Create notification for session confirmation
     */
    public function notifySessionConfirmed($user_id, $session_date, $tutor_name = null) {
        $message = "Your tutoring session on $session_date has been confirmed";
        if ($tutor_name) {
            $message .= " with $tutor_name";
        }
        return $this->create($user_id, $message, 'success');
    }
    
    /**
     * Create notification for feedback received
     */
    public function notifyFeedbackReceived($user_id, $rating) {
        $message = "You received a $rating-star rating for your recent session";
        return $this->create($user_id, $message, 'info');
    }
}

// Initialize notification manager
function initialize_notifications() {
    global $pdo;
    if (isset($pdo)) {
        return new NotificationManager($pdo);
    }
    return null;
}