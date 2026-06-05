<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$user = get_logged_in_user();

// Site settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$site_name = $settings['site_name'] ?? 'LeadPress';

// Subscription check
$stmt = $pdo->prepare("SELECT p.name as plan_name, s.expires_at FROM subscriptions s JOIN plans p ON s.plan_id = p.id WHERE s.user_id = ? AND s.status = 'active' ORDER BY s.starts_at DESC LIMIT 1");
$stmt->execute([$user['id']]);
$subscription = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Dashboard'; ?> - <?php echo h($site_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        .top-navbar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.75rem 0;
        }
        .global-search {
            max-width: 400px;
            position: relative;
        }
        .global-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        .global-search input {
            padding-left: 35px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="lp-sidebar d-none d-lg-block" style="width: 260px;">
            <div class="p-4">
                <a href="index.php" class="h3 fw-bold text-accent text-decoration-none">LeadPress</a>
            </div>
            <nav class="mt-2">
                <a href="index.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="search.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'search' ? 'active' : ''; ?>">
                    <i class="fas fa-search"></i> Search Leads
                </a>
                <a href="saved_lists.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'lists' ? 'active' : ''; ?>">
                    <i class="fas fa-list-ul"></i> My Lists
                </a>
                <a href="affiliate.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'affiliate' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Affiliate
                </a>
                <a href="billing.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'billing' ? 'active' : ''; ?>">
                    <i class="fas fa-credit-card"></i> Billing
                </a>
                <a href="api_management.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'api' ? 'active' : ''; ?>">
                    <i class="fas fa-code"></i> API
                </a>
                <a href="tickets.php" class="lp-nav-item <?php echo ($active_page ?? '') == 'support' ? 'active' : ''; ?>">
                    <i class="fas fa-headset"></i> Support
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-grow-1">
            <header class="top-navbar">
                <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
                    <div class="global-search d-none d-md-block">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" placeholder="Search leads, lists, or invoices... (Ctrl + K)">
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="me-3 text-end d-none d-sm-block">
                            <div class="small fw-bold">Credits: <span class="text-accent"><?php echo number_format($user['credits']); ?></span></div>
                            <div class="text-muted small" style="font-size: 0.7rem;"><?php echo h($subscription['plan_name'] ?? 'No Plan'); ?></div>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-primary" data-bs-toggle="dropdown">
                                <div class="bg-accent rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: var(--accent-primary);">
                                    <span class="text-dark fw-bold"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark border-secondary">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="billing.php"><i class="fas fa-cog me-2"></i> Settings</a></li>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4">
