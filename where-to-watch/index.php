<?php
require_once __DIR__ . '/../includes/init.php';
$page_title = $lang['streaming_guide'] . " - PLAYPULSE";
require_once __DIR__ . '/../includes/header.php';

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;
$query = "SELECT g.*, m.tournament_name, m.match_date, t1.name_en as t1_en, t2.name_en as t2_en, b.name as b_name, b.website as b_url, o.name as o_name, o.website as o_url
          FROM streaming_guides g
          JOIN matches m ON g.match_id = m.id
          JOIN teams t1 ON m.team1_id = t1.id
          JOIN teams t2 ON m.team2_id = t2.id
          LEFT JOIN broadcasters b ON g.broadcaster_id = b.id
          LEFT JOIN ott_platforms o ON g.ott_id = o.id";

if ($match_id) {
    $stmt = $pdo->prepare($query . " WHERE g.match_id = ?");
    $stmt->execute([$match_id]);
    $guides = $stmt->fetchAll();
} else {
    $guides = $pdo->query($query . " ORDER BY m.match_date DESC LIMIT 50")->fetchAll();
}
?>

<div class="container mt-5">
    <h3 class="section-title"><?php echo $lang['streaming_guide']; ?></h3>

    <div class="row g-4 mt-2">
        <?php foreach ($guides as $g): ?>
        <div class="col-lg-4">
            <div class="streaming-card">
                <h5 class="fw-bold mb-1 text-yellow"><?php echo h($g['b_name'] ?: $g['o_name']); ?></h5>
                <p class="small text-muted mb-4"><?php echo h($g['t1_en']); ?> vs <?php echo h($g['t2_en']); ?></p>

                <div class="mb-3 d-flex align-items-center">
                    <i class="fas fa-globe text-danger me-2"></i>
                    <span><?php echo h($g['country']); ?></span>
                </div>

                <div class="mb-4">
                    <div class="small text-muted mb-1 text-uppercase letter-spacing-1">Subscription</div>
                    <div class="fw-bold fs-4">$<?php echo $g['availability_type'] == 'free' ? '0.00' : '9.99'; ?> <span class="fs-6 fw-normal">/month</span></div>
                </div>

                <a href="<?php echo h($g['b_url'] ?: $g['o_url']); ?>" class="btn btn-yellow w-100">WATCH NOW</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
