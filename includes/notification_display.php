<?php
/**
 * Notification Display UI
 * Separate file to avoid function definition order issues
 */

/**
 * Display notifications UI
 */
function display_notifications_ui() {
    if (!isset($_SESSION['user_id'])) {
        return;
    }
    
    $notificationManager = initialize_notifications();
    if (!$notificationManager) {
        return;
    }
    
    $unread_count = $notificationManager->getUnreadCount($_SESSION['user_id']);
    ?>
    
    <div class="notifications-wrapper">
        <div class="notifications-icon" onclick="toggleNotifications()">
            <span class="icon">🔔</span>
            <?php if ($unread_count > 0): ?>
                <span class="notification-badge"><?php echo $unread_count; ?></span>
            <?php endif; ?>
        </div>
        
        <div class="notifications-dropdown" id="notificationsDropdown">
            <div class="notifications-header">
                <h4>Notifications</h4>
                <button onclick="markAllAsRead()" class="btn-mark-read">Mark all as read</button>
            </div>
            <div class="notifications-list" id="notificationsList">
                <!-- Notifications will be loaded via AJAX -->
                <div class="notification-loading">Loading notifications...</div>
            </div>
        </div>
    </div>

    <script>
    function toggleNotifications() {
        const dropdown = document.getElementById('notificationsDropdown');
        const isVisible = dropdown.style.display === 'block';
        dropdown.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible) {
            loadNotifications();
        }
    }
    
    function loadNotifications() {
        fetch('../includes/notification_ajax.php?action=get_notifications')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('notificationsList');
                container.innerHTML = '';
                
                if (data.length === 0) {
                    container.innerHTML = '<div class="notification-item">No notifications</div>';
                    return;
                }
                
                data.forEach(notification => {
                    const item = document.createElement('div');
                    item.className = `notification-item ${notification.is_read ? 'read' : 'unread'}`;
                    item.innerHTML = `
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time">${formatTime(notification.created_at)}</div>
                    `;
                    item.onclick = () => markAsRead(notification.notification_id);
                    container.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('notificationsList').innerHTML = 
                    '<div class="notification-error">Error loading notifications</div>';
            });
    }
    
    function markAsRead(notificationId) {
        fetch('../includes/notification_ajax.php?action=mark_read&id=' + notificationId)
            .then(() => {
                loadNotifications();
                // Update badge count
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    const currentCount = parseInt(badge.textContent) - 1;
                    if (currentCount > 0) {
                        badge.textContent = currentCount;
                    } else {
                        badge.remove();
                    }
                }
            });
    }
    
    function markAllAsRead() {
        fetch('../includes/notification_ajax.php?action=mark_all_read')
            .then(() => {
                loadNotifications();
                // Remove badge
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    badge.remove();
                }
            });
    }
    
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('notificationsDropdown');
        const icon = document.querySelector('.notifications-icon');
        if (dropdown && icon && !icon.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
    
    // Auto-refresh notifications every 30 seconds
    setInterval(() => {
        const dropdown = document.getElementById('notificationsDropdown');
        if (dropdown && dropdown.style.display === 'block') {
            loadNotifications();
        }
    }, 30000);
    </script>
    
    <style>
    .notifications-wrapper {
        position: relative;
        display: inline-block;
    }
    
    .notifications-icon {
        position: relative;
        cursor: pointer;
        padding: 10px;
        font-size: 20px;
    }
    
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4444;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
    }
    
    .notifications-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        width: 350px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .notifications-header {
        padding: 10px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: between;
        align-items: center;
    }
    
    .notifications-header h4 {
        margin: 0;
        flex: 1;
    }
    
    .btn-mark-read {
        background: #007bff;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .notification-item {
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
    }
    
    .notification-item.unread {
        background: #f8f9fa;
        font-weight: bold;
    }
    
    .notification-item:hover {
        background: #e9ecef;
    }
    
    .notification-message {
        margin-bottom: 5px;
    }
    
    .notification-time {
        font-size: 11px;
        color: #666;
    }
    
    .notification-loading, .notification-error {
        padding: 20px;
        text-align: center;
        color: #666;
    }
    </style>
    <?php
}