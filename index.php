<?php
require_once __DIR__ . '/includes/header.php';

// Fetch Trending Ticker
$trending_news = $pdo->query("SELECT title_en, slug FROM articles WHERE is_trending = 1 AND status = 'published' LIMIT 5")->fetchAll();

// Fetch Live Scores
$live_matches = $pdo->query("SELECT m.*, t1.name_en as t1_en, t2.name_en as t2_en, t1.logo as t1_logo, t2.logo as t2_logo, t1.short_name as t1_short, t2.short_name as t2_short
                            FROM matches m
                            JOIN teams t1 ON m.team1_id = t1.id
                            JOIN teams t2 ON m.team2_id = t2.id
                            WHERE m.status = 'live' LIMIT 4")->fetchAll();

// Fetch Latest News
$latest_news = $pdo->query("SELECT * FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 4")->fetchAll();

// Fetch Categories for "Explore All Sports"
$sports_cats = $pdo->query("SELECT * FROM categories WHERE status = 'active' LIMIT 6")->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container position-relative z-1">
        <h1 class="hero-title text-white mb-4">EVERY GAME.<br>EVERY MOMENT.<br>ALL SPORTS.</h1>
        <p class="fs-5 text-light mb-5 max-width-700 mx-auto">Breaking news, live scores, stats and streaming guides. Your ultimate destination for everything in the world of sports.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="/match-center/index.php" class="btn btn-yellow"><i class="fas fa-bolt me-2"></i> LIVE SCORES</a>
            <a href="/where-to-watch/index.php" class="btn btn-outline-white"><i class="fas fa-play me-2"></i> WATCH NOW</a>
        </div>
    </div>
</section>

<!-- Trending Ticker -->
<div class="trending-ticker">
    <div class="container">
        <div class="d-flex align-items-center">
            <span class="text-yellow fw-bold me-4 text-nowrap" style="color: var(--accent-yellow)">TRENDING NOW</span>
            <marquee behavior="scroll" direction="left" class="small">
                <?php foreach ($trending_news as $tn): ?>
                    <a href="/news/index.php?slug=<?php echo $tn['slug']; ?>" class="text-white text-decoration-none me-5">• <?php echo h($tn['title_en']); ?></a>
                <?php endforeach; ?>
            </marquee>
        </div>
    </div>
</div>

<!-- Live Scores -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title mb-0">LIVE SCORES <span class="text-danger">• LIVE</span></h3>
            <a href="/match-center/index.php" class="text-muted text-decoration-none small">VIEW ALL SCORES <i class="fas fa-chevron-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($live_matches as $lm): ?>
            <div class="col-lg-3 col-md-6">
                <div class="live-score-card">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="small text-muted"><?php echo h($lm['tournament_name']); ?></span>
                        <span class="score-badge">LIVE</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-center">
                            <img loading="lazy" src="/uploads/<?php echo $lm['t1_logo']; ?>" width="40" class="mb-2">
                            <div class="fw-bold small"><?php echo h($lm['t1_short']); ?></div>
                        </div>
                        <div class="fs-3 fw-900"><?php echo $lm['team1_score']; ?> - <?php echo $lm['team2_score']; ?></div>
                        <div class="text-center">
                            <img loading="lazy" src="/uploads/<?php echo $lm['t2_logo']; ?>" width="40" class="mb-2">
                            <div class="fw-bold small"><?php echo h($lm['t2_short']); ?></div>
                        </div>
                    </div>
                    <div class="text-center text-danger small fw-bold">82'</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Breaking News -->
<section class="py-5 bg-secondary">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title mb-0">BREAKING NEWS</h3>
            <a href="/news/index.php" class="text-muted text-decoration-none small">VIEW ALL NEWS <i class="fas fa-chevron-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($latest_news as $art): ?>
            <div class="col-lg-3 col-md-6">
                <div class="news-card">
                    <div class="position-relative">
                        <img loading="lazy" src="/uploads/<?php echo $art['featured_image']; ?>" alt="">
                        <span class="badge bg-danger position-absolute top-0 start-0 m-3">BREAKING</span>
                    </div>
                    <div class="text-muted small mt-2"><?php echo h($art['status']); ?> • 5m ago</div>
                    <h5 class="card-title">
                        <a href="/news/index.php?slug=<?php echo $art['slug']; ?>" class="text-white text-decoration-none">
                            <?php echo h($art['title_en']); ?>
                        </a>
                    </h5>
                    <p class="text-muted small">Late goals seal a thrilling comeback in London.</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Explore Sports -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title mb-0">EXPLORE ALL SPORTS</h3>
            <a href="#" class="text-muted text-decoration-none small">VIEW ALL SPORTS <i class="fas fa-chevron-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($sports_cats as $sc): ?>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="sport-icon-box">
                    <i class="<?php echo h($sc['icon'] ?: 'fas fa-futbol'); ?> fa-3x mb-3 text-white"></i>
                    <div class="fw-bold small text-uppercase"><?php echo h($sc['name_en']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="sport-icon-box">
                    <i class="fas fa-th-large fa-3x mb-3 text-white"></i>
                    <div class="fw-bold small text-uppercase">MORE SPORTS</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Streaming Guides Section -->
<section class="py-5 bg-secondary">
    <div class="container text-center mb-5">
        <h2 class="hero-title fs-2">NOW IN <span class="text-yellow" style="color: var(--accent-yellow)">ENGLISH</span> & <span class="text-success" style="color: #00FF88">BANGLA</span></h2>
        <p class="text-muted">Read news in your language. Stay updated, stay connected.</p>
        <div class="d-flex justify-content-center gap-2">
            <a href="?lang=en" class="btn btn-yellow px-4">ENGLISH</a>
            <a href="?lang=bn" class="btn btn-success px-4" style="background-color: #00D166; border: none;">বাংলা</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
