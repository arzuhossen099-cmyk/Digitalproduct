<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// Pages that don't require plan activation
$public_pages = ['index.php', 'login.php', 'register.php', 'plans.php', 'deposit.php', 'transactions.php', 'menu.php', 'profile.php', 'wallet.php', 'support.php', 'logout.php', 'notifications.php'];

$current_page = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!in_array($current_page, $public_pages)) {
    // Check for active plan
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_plans WHERE user_id = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->execute([$_SESSION['user_id']]);
    $has_active_plan = $stmt->fetchColumn() > 0;

    if (!$has_active_plan) {
        $_SESSION['error'] = "You must activate a plan to access this feature.";
        header("Location: plans.php");
        exit();
    }
}
?>
