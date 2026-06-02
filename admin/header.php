<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Global stats for admin
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();
$total_deposits = $pdo->query("SELECT SUM(amount) FROM deposits WHERE status = 'approved'")->fetchColumn() ?? 0;
$pending_deposits_count = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'pending'")->fetchColumn();
$pending_withdrawals_count = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'")->fetchColumn();
$pending_tasks_count = $pdo->query("SELECT COUNT(*) FROM task_submissions WHERE status = 'pending'")->fetchColumn();
$pending_orders_count = $pdo->query("SELECT COUNT(*) FROM package_orders WHERE status = 'pending'")->fetchColumn();

// Site settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$site_name = $settings['site_name'] ?? 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_name; ?> - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #343a40; color: white; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; padding: 15px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { background: #495057; color: white; }
        .content { flex: 1; padding: 20px; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center py-4 border-bottom"><?php echo $site_name; ?></h4>
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>"><i class="fas fa-users me-2"></i> Users</a>
        <a href="deposits.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'deposits.php' ? 'active' : ''; ?>"><i class="fas fa-wallet me-2"></i> Deposits <?php if($pending_deposits_count > 0) echo "<span class='badge bg-danger'>$pending_deposits_count</span>"; ?></a>
        <a href="withdrawals.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'withdrawals.php' ? 'active' : ''; ?>"><i class="fas fa-money-bill-wave me-2"></i> Withdrawals <?php if($pending_withdrawals_count > 0) echo "<span class='badge bg-danger'>$pending_withdrawals_count</span>"; ?></a>
        <a href="manage_rewards.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_rewards.php' ? 'active' : ''; ?>"><i class="fas fa-gift me-2"></i> Manage Rewards</a>
        <a href="give_reward.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'give_reward.php' ? 'active' : ''; ?>"><i class="fas fa-hand-holding-usd me-2"></i> Give Reward</a>
        <a href="reward_logs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reward_logs.php' ? 'active' : ''; ?>"><i class="fas fa-list me-2"></i> Reward Logs</a>
        <a href="package_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'package_orders.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart me-2"></i> Package Orders <?php if($pending_orders_count > 0) echo "<span class='badge bg-danger'>$pending_orders_count</span>"; ?></a>
        <a href="manage_tasks.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_tasks.php' ? 'active' : ''; ?>"><i class="fas fa-tasks me-2"></i> Social Tasks</a>
        <a href="task_submissions.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'task_submissions.php' ? 'active' : ''; ?>"><i class="fas fa-check-double me-2"></i> Submissions <?php if($pending_tasks_count > 0) echo "<span class='badge bg-danger'>$pending_tasks_count</span>"; ?></a>
        <a href="manage_ads.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_ads.php' ? 'active' : ''; ?>"><i class="fas fa-ad me-2"></i> Manage Ads</a>
        <a href="plans.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'plans.php' ? 'active' : ''; ?>"><i class="fas fa-box me-2"></i> Plans</a>
        <a href="packages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'active' : ''; ?>"><i class="fas fa-mobile-alt me-2"></i> Telecom Packs</a>
        <a href="payment_settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'payment_settings.php' ? 'active' : ''; ?>"><i class="fas fa-credit-card me-2"></i> Payment Settings</a>
        <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog me-2"></i> Settings</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard</h2>
            <div>Welcome, Admin</div>
        </div>
