<?php
/**
 * Session Check and Role-based Access Control
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Redirect to login if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: ../modules/auth/login.php');
        exit();
    }
}

/**
 * Check if user has specific role
 */
function has_role($required_role) {
    return is_logged_in() && $_SESSION['role'] === $required_role;
}

/**
 * Require specific role for access
 */
function require_role($required_role) {
    require_login();
    
    if (!has_role($required_role)) {
        http_response_code(403);
        echo "Access denied. Required role: " . ucfirst($required_role);
        exit();
    }
}

/**
 * Redirect based on user role
 */
function redirect_by_role() {
    if (!is_logged_in()) {
        return;
    }
    
    $role = $_SESSION['role'];
    switch ($role) {
        case 'admin':
            header('Location: ../modules/admin/admin_dash.php');
            break;
        case 'tutor':
            header('Location: ../modules/tutor/tutor_dash.php');
            break;
        case 'learner':
            header('Location: ../modules/learner/learner_dash.php');
            break;
        default:
            header('Location: ../modules/auth/login.php');
    }
    exit();
}

/**
 * Get current user ID
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function get_current_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Check if user is active
 */
function is_user_active() {
    return isset($_SESSION['is_active']) && $_SESSION['is_active'] === true;
}