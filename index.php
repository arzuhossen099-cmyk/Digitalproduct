<?php
$title = "Dashboard";
$active_page = "dashboard";
require_once __DIR__ . '/includes/header_user.php';

$user = get_logged_in_user();

// User specific stats
$total_reveals = $pdo->prepare("SELECT COUNT(*) FROM lead_reveals WHERE user_id = ?");
$total_reveals->execute([$user['id']]);
$reveal_count = $total_reveals->fetchColumn();

$saved_lists_count = $pdo->prepare("SELECT COUNT(*) FROM saved_lists WHERE user_id = ?");
$saved_lists_count->execute([$user['id']]);
$lists_count = $saved_lists_count->fetchColumn();

// Credit usage (last 7 days)
$usage_stmt = $pdo->prepare("SELECT DATE(created_at) as date, SUM(credits_spent) as spent FROM lead_reveals WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at)");
$usage_stmt->execute([$user['id']]);
$usage_data = $usage_stmt->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="lp-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon p-2 rounded bg-accent bg-opacity-10 text-accent">
                    <i class="fas fa-bolt"></i>
                </div>
                <span class="badge lp-badge-info">Monthly</span>
            </div>
            <div class="text-muted small mb-1">Available Credits</div>
            <h3 class="fw-bold mb-0 counter"><?php echo number_format($user['credits']); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon p-2 rounded bg-success bg-opacity-10 text-success">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="text-muted small mb-1">Total Revealed</div>
            <h3 class="fw-bold mb-0 counter"><?php echo number_format($reveal_count); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon p-2 rounded bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-list-ul"></i>
                </div>
            </div>
            <div class="text-muted small mb-1">Saved Lists</div>
            <h3 class="fw-bold mb-0 counter"><?php echo number_format($lists_count); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="lp-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon p-2 rounded bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-gem"></i>
                </div>
            </div>
            <div class="text-muted small mb-1">Active Plan</div>
            <h5 class="fw-bold mb-0"><?php echo h($subscription['plan_name'] ?? 'Free Plan'); ?></h5>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="lp-card h-100">
            <h5 class="fw-bold mb-4">Credit Usage (7 Days)</h5>
            <canvas id="usageChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="lp-card h-100">
            <h5 class="fw-bold mb-4">Quick Search</h5>
            <form action="search.php" method="GET">
                <div class="mb-3">
                    <input type="text" name="industry" class="lp-input w-100" placeholder="Industry (e.g. SaaS)">
                </div>
                <div class="mb-3">
                    <input type="text" name="job_title" class="lp-input w-100" placeholder="Job Title (e.g. Sales Director)">
                </div>
                <button type="submit" class="btn-lp-primary w-100">Start Searching</button>
            </form>
            <hr class="my-4 border-secondary">
            <h6 class="fw-bold mb-3">Recent Activity</h6>
            <div class="small">
                <?php
                $recent = $pdo->prepare("SELECT l.full_name, r.created_at FROM lead_reveals r JOIN leads l ON r.lead_id = l.id WHERE r.user_id = ? ORDER BY r.created_at DESC LIMIT 3");
                $recent->execute([$user['id']]);
                while ($r = $recent->fetch()):
                ?>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-unlock-alt text-accent me-2 small"></i>
                    <div>
                        <div class="text-primary">Revealed <?php echo h($r['full_name']); ?></div>
                        <div class="text-muted" style="font-size: 0.7rem;"><?php echo date('M d, H:i', strtotime($r['created_at'])); ?></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('usageChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($usage_data, 'date')); ?>,
            datasets: [{
                label: 'Credits Used',
                data: <?php echo json_encode(array_column($usage_data, 'spent')); ?>,
                borderColor: '#00D4FF',
                backgroundColor: 'rgba(0, 212, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#22304A' }, ticks: { color: '#6B7280' } },
                x: { grid: { color: '#22304A' }, ticks: { color: '#6B7280' } }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
