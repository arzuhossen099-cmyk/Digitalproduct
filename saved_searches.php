<?php
$title = "Saved Searches";
$active_page = "dashboard";
require_once __DIR__ . '/includes/header_user.php';

$user = get_logged_in_user();
$stmt = $pdo->prepare("SELECT * FROM saved_searches WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$searches = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Saved Searches</h2>
    <a href="search.php" class="btn btn-lp-primary"><i class="fas fa-search me-2"></i> New Search</a>
</div>

<div class="row g-4">
    <?php foreach ($searches as $s): ?>
    <div class="col-md-6">
        <div class="lp-card">
            <h5 class="fw-bold mb-3"><?php echo h($s['name']); ?></h5>
            <div class="text-muted small mb-4">
                <?php
                $criteria = json_decode($s['search_criteria'], true);
                foreach ($criteria as $key => $value) {
                    if ($value) echo '<span class="badge bg-secondary me-1">' . h($key) . ': ' . h($value) . '</span>';
                }
                ?>
            </div>
            <div class="d-flex gap-2">
                <a href="search.php?<?php echo http_build_query($criteria); ?>" class="btn btn-lp-outline btn-sm flex-grow-1">Run Search</a>
                <button class="btn btn-lp-outline btn-sm text-danger border-danger border-opacity-25"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($searches)): ?>
        <div class="col-12 text-center py-5 lp-card">
            <p class="text-muted">No saved searches found.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
