<?php
/**
 * AJAX handler for notifications
 */
session_start();
require_once 'logger.php';
require_once 'notifications.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$action = $_GET['action'] ?? '';
$notificationManager = initialize_notifications();

if (!$notificationManager) {
    echo json_encode(['error' => 'Notification system unavailable']);
    exit;
}

switch ($action) {
    case 'get_notifications':
        $unread_only = $_GET['unread_only'] ?? false;
        $notifications = $notificationManager->getUserNotifications($_SESSION['user_id'], $unread_only);
        echo json_encode($notifications);
        break;
        
    case 'mark_read':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $success = $notificationManager->markAsRead($id, $_SESSION['user_id']);
            echo json_encode(['success' => $success]);
        }
        break;
        
    case 'mark_all_read':
        $success = $notificationManager->markAllAsRead($_SESSION['user_id']);
        echo json_encode(['success' => $success]);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}