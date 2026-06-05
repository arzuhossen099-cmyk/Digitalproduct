<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_logged_in_user() {
    global $pdo;
    if (!is_logged_in()) return null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function require_login() {
    if (!is_logged_in()) {
        $path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../login.php' : 'login.php';
        header("Location: $path");
        exit();
    }
}

function require_admin() {
    $user = get_logged_in_user();
    $is_admin = $user && (($user['role'] ?? '') === 'admin' || ($user['role'] ?? '') === 'super_admin' || ($user['is_admin'] ?? 0) == 1);
    if (!$is_admin) {
        $path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php';
        header("Location: $path");
        exit();
    }
}

function require_super_admin() {
    $user = get_logged_in_user();
    if (!$user || ($user['role'] ?? '') !== 'super_admin') {
        $path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'dashboard.php' : 'admin/dashboard.php';
        header("Location: $path");
        exit();
    }
}

// Check for session timeout (optional but recommended)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
    session_unset();
    session_destroy();
    redirect('login.php?timeout=1');
}
$_SESSION['last_activity'] = time();
?>
