<?php
$title = "Blog";
require_once __DIR__ . '/includes/header_user.php';

$stmt = $pdo->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="mb-5 text-center">
    <h1 class="fw-bold text-info">Lead Generation Insights</h1>
    <p class="text-muted">Stay updated with the latest trends in sales and marketing.</p>
</div>

<div class="row g-4">
    <?php foreach ($posts as $post): ?>
    <div class="col-md-4">
        <div class="stat-card h-100 d-flex flex-column">
            <?php if ($post['featured_image']): ?>
                <img src="<?php echo h($post['featured_image']); ?>" class="img-fluid rounded mb-3" alt="<?php echo h($post['title']); ?>">
            <?php endif; ?>
            <h5 class="fw-bold"><?php echo h($post['title']); ?></h5>
            <p class="text-muted small flex-grow-1"><?php echo h($post['excerpt']); ?></p>
            <div class="mt-auto pt-3 border-top border-secondary d-flex justify-content-between align-items-center">
                <span class="small text-muted"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                <a href="blog_post.php?slug=<?php echo h($post['slug']); ?>" class="text-info text-decoration-none small fw-bold">Read More <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($posts)): ?>
        <div class="col-12 text-center py-5">
            <p class="text-muted">No blog posts found.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
