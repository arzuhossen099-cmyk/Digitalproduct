<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$stats = [
    'articles' => $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'live_matches' => $pdo->query("SELECT COUNT(*) FROM matches WHERE status = 'live'")->fetchColumn(),
    'views' => $pdo->query("SELECT SUM(views) FROM articles")->fetchColumn() ?: 0,
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Dashboard</h2>
    <div class="text-muted small">Welcome back, Admin</div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="admin-card p-4">
            <div class="stat-label">Total News</div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="stat-value"><?php echo number_format($stats['articles']); ?></div>
                <div class="text-success small fw-bold">+12%</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card p-4">
            <div class="stat-label">Live Matches</div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="stat-value text-danger"><?php echo $stats['live_matches']; ?></div>
                <div class="badge bg-danger">LIVE</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card p-4">
            <div class="stat-label">Total Views</div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="stat-value"><?php echo number_format($stats['views']); ?></div>
                <div class="text-success small fw-bold">+8%</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Site Traffic</h5>
                <select class="form-select form-select-sm w-auto bg-dark text-white border-secondary">
                    <option>This Week</option>
                </select>
            </div>
            <canvas id="trafficChart" height="250"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card p-4 h-100 text-center">
            <h5 class="fw-bold mb-4">Top Sports</h5>
            <canvas id="sportsChart" height="250"></canvas>
            <div class="mt-4 small text-start">
                <div class="d-flex justify-content-between mb-1"><span>Football</span> <span>45%</span></div>
                <div class="d-flex justify-content-between mb-1"><span>Cricket</span> <span>25%</span></div>
                <div class="d-flex justify-content-between"><span>Basketball</span> <span>15%</span></div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('trafficChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Visits',
            data: [1200, 1900, 3000, 2500, 2800, 3500, 3200],
            borderColor: '#FFC107',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(255, 193, 7, 0.1)'
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { display: false }, x: { grid: { display: false } } }
    }
});

const ctx2 = document.getElementById('sportsChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Football', 'Cricket', 'Basketball', 'Others'],
        datasets: [{
            data: [45, 25, 15, 15],
            backgroundColor: ['#00D4FF', '#FFC107', '#E41E26', '#6C757D'],
            borderWidth: 0
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        cutout: '80%'
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
