<?php
$title = "Plan Management";
$active_page = "plans";
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT * FROM plans ORDER BY price ASC");
$plans = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">Subscription Plans</h2>
    <button class="btn btn-lp-primary btn-sm" data-bs-toggle="modal" data-bs-target="#planModal"><i class="fas fa-plus me-2"></i> New Plan</button>
</div>

<div class="row g-4">
    <?php foreach ($plans as $plan): ?>
    <div class="col-md-4">
        <div class="lp-card h-100">
            <div class="d-flex justify-content-between mb-3">
                <h5 class="fw-bold mb-0"><?php echo h($plan['name']); ?></h5>
                <span class="text-accent fw-bold"><?php echo format_currency($plan['price']); ?></span>
            </div>
            <p class="text-muted small mb-4"><?php echo h($plan['description']); ?></p>
            <ul class="list-unstyled small mb-4">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> <?php echo number_format($plan['credits_per_month']); ?> Credits</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> <?php echo $plan['searches_per_day']; ?> Searches/day</li>
                <li><i class="fas fa-check text-success me-2"></i> <?php echo $plan['duration_days']; ?> Days Validity</li>
            </ul>
            <div class="mt-auto d-flex gap-2">
                <button class="btn btn-lp-outline btn-sm flex-grow-1">Edit</button>
                <button class="btn btn-lp-outline btn-sm text-danger border-danger border-opacity-25"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
