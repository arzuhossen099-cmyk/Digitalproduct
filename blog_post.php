<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT bp.*, u.username FROM blog_posts bp JOIN users u ON bp.author_id = u.id WHERE bp.slug = ? AND bp.status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found.");
}

$title = $post['title'];
require_once __DIR__ . '/includes/header_user.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-4">
            <a href="blog.php" class="btn btn-lp-outline btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Blog</a>
        </div>

        <article class="lp-card p-5">
            <?php if ($post['featured_image']): ?>
                <img src="<?php echo h($post['featured_image']); ?>" class="img-fluid rounded mb-4 w-100" alt="<?php echo h($post['title']); ?>">
            <?php endif; ?>

            <h1 class="fw-bold mb-3 text-accent"><?php echo h($post['title']); ?></h1>
            <div class="d-flex align-items-center text-muted small mb-5">
                <i class="fas fa-user-circle me-2"></i> <?php echo h($post['username']); ?>
                <span class="mx-2">•</span>
                <i class="fas fa-calendar-alt me-2"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
            </div>

            <div class="blog-content text-secondary" style="line-height: 1.8; font-size: 1.1rem;">
                <?php echo nl2br($post['content']); ?>
            </div>
        </article>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
