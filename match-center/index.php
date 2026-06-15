<?php
require_once __DIR__ . '/../includes/init.php';
$page_title = $lang['match_center'] . " - PLAYPULSE";
require_once __DIR__ . '/../includes/header.php';

$matches = $pdo->query("SELECT m.*, t1.name_en as t1_en, t1.logo as t1_logo, t2.name_en as t2_en, t2.logo as t2_logo, c.name_en as cat_en
                        FROM matches m
                        JOIN teams t1 ON m.team1_id = t1.id
                        JOIN teams t2 ON m.team2_id = t2.id
                        JOIN categories c ON m.category_id = c.id
                        ORDER BY m.match_date DESC LIMIT 50")->fetchAll();
?>

<div class="container mt-5">
    <h3 class="section-title"><?php echo $lang['match_center']; ?></h3>

    <div class="row g-4 mt-2">
        <?php foreach ($matches as $m): ?>
        <div class="col-lg-4 col-md-6">
            <div class="live-score-card">
                <div class="d-flex justify-content-between mb-3">
                    <span class="small text-muted"><?php echo h($m['tournament_name']); ?></span>
                    <span class="badge <?php echo $m['status'] == 'live' ? 'bg-danger' : 'bg-secondary'; ?>"><?php echo strtoupper($m['status']); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="text-center" style="width: 35%;">
                        <img loading="lazy" src="/uploads/<?php echo $m['t1_logo']; ?>" width="50" class="mb-2">
                        <div class="fw-bold"><?php echo h($m['t1_en']); ?></div>
                    </div>
                    <div class="text-center" style="width: 30%;">
                        <?php if ($m['status'] != 'upcoming'): ?>
                            <div class="fs-2 fw-900"><?php echo $m['team1_score']; ?> - <?php echo $m['team2_score']; ?></div>
                        <?php else: ?>
                            <div class="text-muted small"><?php echo date('H:i', strtotime($m['match_date'])); ?></div>
                            <div class="fw-bold">VS</div>
                        <?php endif; ?>
                    </div>
                    <div class="text-center" style="width: 35%;">
                        <img loading="lazy" src="/uploads/<?php echo $m['t2_logo']; ?>" width="50" class="mb-2">
                        <div class="fw-bold"><?php echo h($m['t2_en']); ?></div>
                    </div>
                </div>
                <div class="text-center border-top border-secondary pt-3 mt-3">
                    <a href="/where-to-watch/index.php?match_id=<?php echo $m['id']; ?>" class="text-yellow text-decoration-none small fw-bold">WATCHING GUIDE <i class="fas fa-chevron-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
