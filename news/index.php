<?php
require_once __DIR__ . '/../includes/init.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT a.*, c.name_en as cat_en, c.slug as cat_slug
                        FROM articles a
                        JOIN categories c ON a.category_id = c.id
                        WHERE a.slug = ? AND a.status = 'published'");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) { die("News not found."); }
$pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$article['id']]);

$page_title = h($article['title_en']) . " - PLAYPULSE";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="fw-900 mb-3 text-uppercase"><?php echo h($article['title_en']); ?></h1>
            <div class="d-flex align-items-center mb-4 text-muted small">
                <span class="me-3"><i class="fas fa-clock me-1"></i> <?php echo date('M d, Y', strtotime($article['published_at'])); ?></span>
                <span class="me-3"><i class="fas fa-eye me-1"></i> <?php echo $article['views']; ?> views</span>
                <span class="badge bg-danger text-uppercase"><?php echo h($article['cat_en']); ?></span>
            </div>

            <img loading="lazy" src="/uploads/<?php echo $article['featured_image']; ?>" class="img-fluid rounded-4 mb-4 w-100 shadow">

            <div class="fs-5 text-light" style="line-height: 1.8;">
                <?php echo $article['content_en']; ?>
            </div>

            <div class="mt-5 pt-4 border-top border-secondary">
                <h5 class="fw-bold mb-3">SHARE THIS STORY</h5>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-primary btn-sm"><i class="fab fa-facebook-f me-1"></i> FACEBOOK</a>
                    <a href="#" class="btn btn-info btn-sm text-white"><i class="fab fa-twitter me-1"></i> TWITTER</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card p-4">
                <h5 class="fw-bold mb-4">TRENDING STORIES</h5>
                <?php
                $trending = $pdo->query("SELECT * FROM articles WHERE status = 'published' ORDER BY views DESC LIMIT 5")->fetchAll();
                foreach ($trending as $tr):
                ?>
                <div class="mb-3">
                    <a href="index.php?slug=<?php echo $tr['slug']; ?>" class="text-white text-decoration-none fw-bold small"><?php echo h($tr['title_en']); ?></a>
                    <div class="small text-muted"><?php echo $tr['views']; ?> views</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
