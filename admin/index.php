<?php
$title = "Master Dashboard";
$active_page = "dashboard";
require_once __DIR__ . '/includes/header.php';

// SaaS Analytics
$total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$active_subscribers = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM subscriptions WHERE status = 'active' AND (expires_at IS NULL OR expires_at > NOW())")->fetchColumn();
$monthly_revenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn() ?: 0;

// Earning Platform Analytics (Legacy Compatibility)
$pending_deposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'pending'")->fetchColumn() ?: 0;
$pending_withdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'")->fetchColumn() ?: 0;
$pending_tickets = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'open'")->fetchColumn() ?: 0;

// Revenue data for chart
$revenue_stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%b %d') as day, SUM(amount) as total FROM payments WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY created_at ASC");
$revenue_data = $revenue_stmt->fetchAll();
?>

<!-- KPI Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="lp-card">
            <div class="text-muted small mb-1">Total Database Leads</div>
            <h3 class="fw-bold mb-0 text-accent"><?php echo number_format($total_leads); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card">
            <div class="text-muted small mb-1">Active Subscribers</div>
            <h3 class="fw-bold mb-0"><?php echo number_format($active_subscribers); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card text-success">
            <div class="text-muted small mb-1">30-Day Revenue</div>
            <h3 class="fw-bold mb-0"><?php echo format_currency($monthly_revenue); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card">
            <div class="text-muted small mb-1">Total Users</div>
            <h3 class="fw-bold mb-0"><?php echo number_format($total_users); ?></h3>
        </div>
    </div>
</div>

<!-- Pending Actions Row -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="deposits.php" class="text-decoration-none">
            <div class="lp-card border-warning border-opacity-25 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-warning small fw-bold">Pending Deposits</div>
                        <h2 class="fw-bold mb-0"><?php echo $pending_deposits; ?></h2>
                    </div>
                    <i class="fas fa-wallet fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="withdrawals.php" class="text-decoration-none">
            <div class="lp-card border-danger border-opacity-25 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-danger small fw-bold">Pending Payouts</div>
                        <h2 class="fw-bold mb-0"><?php echo $pending_withdrawals; ?></h2>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="tickets.php" class="text-decoration-none">
            <div class="lp-card border-info border-opacity-25 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-info small fw-bold">Open Tickets</div>
                        <h2 class="fw-bold mb-0"><?php echo $pending_tickets; ?></h2>
                    </div>
                    <i class="fas fa-headset fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="lp-card">
            <h5 class="fw-bold mb-4">Revenue Trend (Last 7 Days)</h5>
            <canvas id="revTrendChart" height="250"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="lp-card h-100">
            <h5 class="fw-bold mb-4">Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="import_leads.php" class="btn btn-lp-primary"><i class="fas fa-upload me-2"></i> Import New Leads</a>
                <a href="plans.php" class="btn btn-lp-outline"><i class="fas fa-box me-2"></i> Manage Subscription Plans</a>
                <a href="users.php" class="btn btn-lp-outline"><i class="fas fa-users-cog me-2"></i> User Control Panel</a>
                <a href="settings.php" class="btn btn-lp-outline"><i class="fas fa-cogs me-2"></i> Global System Settings</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('revTrendChart'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($revenue_data, 'day')); ?>,
            datasets: [{
                label: 'Revenue',
                data: <?php echo json_encode(array_column($revenue_data, 'total')); ?>,
                borderColor: '#00D4FF',
                backgroundColor: 'rgba(0, 212, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#22304A' }, ticks: { color: '#A3AED0' } },
                x: { grid: { display: false }, ticks: { color: '#A3AED0' } }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
