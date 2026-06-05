<?php
$title = "Dashboard Overview";
$active_page = "dashboard";
require_once __DIR__ . '/includes/header.php';

// Analytics
$total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$active_subscribers = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM subscriptions WHERE status = 'active' AND (expires_at IS NULL OR expires_at > NOW())")->fetchColumn();
$monthly_revenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn() ?: 0;

// Revenue data (last 6 months)
$revenue_stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%b %Y') as month, SUM(amount) as total FROM payments WHERE status = 'completed' GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY created_at DESC LIMIT 6");
$revenue_data = array_reverse($revenue_stmt->fetchAll());
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="lp-card">
            <div class="text-muted small mb-1">Total Database Leads</div>
            <h2 class="fw-bold mb-0 text-accent"><?php echo number_format($total_leads); ?></h2>
            <div class="mt-2 small text-success"><i class="fas fa-arrow-up"></i> 12% increase</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card">
            <div class="text-muted small mb-1">Active Subscribers</div>
            <h2 class="fw-bold mb-0"><?php echo number_format($active_subscribers); ?></h2>
            <div class="mt-2 small text-primary"><i class="fas fa-users"></i> Users: <?php echo number_format($total_users); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card">
            <div class="text-muted small mb-1">Monthly Revenue</div>
            <h2 class="fw-bold mb-0"><?php echo format_currency($monthly_revenue); ?></h2>
            <div class="mt-2 small text-accent"><i class="fas fa-chart-line"></i> MRR Growth</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card">
            <div class="text-muted small mb-1">System Health</div>
            <h2 class="fw-bold mb-0 text-success">99.9%</h2>
            <div class="mt-2 small text-muted"><i class="fas fa-server"></i> API Latency: 45ms</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="lp-card">
            <h5 class="fw-bold mb-4">Revenue Overview</h5>
            <canvas id="revenueChart" height="300"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="lp-card h-100">
            <h5 class="fw-bold mb-4">User Growth</h5>
            <canvas id="growthChart" height="300"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($revenue_data, 'month')); ?>,
            datasets: [{
                label: 'Monthly Revenue',
                data: <?php echo json_encode(array_column($revenue_data, 'total')); ?>,
                backgroundColor: '#00D4FF',
                borderRadius: 8
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

    // Growth Chart (Doughnut)
    new Chart(document.getElementById('growthChart'), {
        type: 'doughnut',
        data: {
            labels: ['Subscribers', 'Free Users'],
            datasets: [{
                data: [<?php echo $active_subscribers; ?>, <?php echo ($total_users - $active_subscribers); ?>],
                backgroundColor: ['#7C3AED', '#22304A'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom', labels: { color: '#A3AED0' } } },
            cutout: '70%'
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
