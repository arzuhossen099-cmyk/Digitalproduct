<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$user = get_logged_in_user();

// Site settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$site_name = $settings['site_name'] ?? 'LeadPress Admin';
$pending_tickets = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'open'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin'; ?> - <?php echo h($site_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <style>
        .top-navbar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.75rem 0;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="lp-sidebar d-none d-lg-block" style="width: 260px;">
            <div class="p-4">
                <a href="dashboard.php" class="h3 fw-bold text-accent text-decoration-none">Admin CP</a>
            </div>
            <nav class="mt-2">
                <a href="dashboard.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="users.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> User Management
                </a>
                <a href="leads.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'leads' ? 'active' : ''; ?>">
                    <i class="fas fa-database"></i> Lead Database
                </a>
                <a href="plans.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'plans' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Plans
                </a>
                <a href="payments.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'payments' ? 'active' : ''; ?>">
                    <i class="fas fa-dollar-sign"></i> Revenue
                </a>
                <a href="blog.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'blog' ? 'active' : ''; ?>">
                    <i class="fas fa-newspaper"></i> Blog CMS
                </a>
                <a href="tickets.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'tickets' ? 'active' : ''; ?>">
                    <i class="fas fa-headset"></i> Support <?php if($pending_tickets > 0) echo "<span class='badge bg-danger ms-2'>$pending_tickets</span>"; ?>
                </a>
                <a href="settings.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <header class="top-navbar">
                <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><?php echo $title ?? 'Admin Dashboard'; ?></h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted small me-3">System Health: <span class="text-success fw-bold">Optimal</span></span>
                        <a href="../logout.php" class="btn btn-lp-outline btn-sm py-1 px-3">Logout</a>
                    </div>
                </div>
            </header>
            <main class="p-4">
