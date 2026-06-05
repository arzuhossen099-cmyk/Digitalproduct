<?php
$title = "Blog Management";
$active_page = "blog";
require_once __DIR__ . '/includes/header.php';

if (isset($_POST['add_post'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $title = trim($_POST['post_title']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $excerpt = trim($_POST['excerpt']);
    $content = $_POST['content'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO blog_posts (author_id, title, slug, content, excerpt, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $slug, $content, $excerpt, $status]);
    $success = "Post added successfully!";
    audit_log("Blog Post Created", "Title: $title");
}

$stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">Blog Management</h2>
    <button class="btn btn-lp-primary btn-sm" data-bs-toggle="modal" data-bs-target="#postModal"><i class="fas fa-plus me-2"></i> New Post</button>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td><?php echo h($post['title']); ?></td>
                <td><span class="lp-badge lp-badge-<?php echo $post['status'] === 'published' ? 'success' : 'info'; ?>"><?php echo ucfirst($post['status']); ?></span></td>
                <td class="small text-muted"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                <td>
                    <button class="btn btn-sm btn-lp-outline px-2 py-0"><i class="fas fa-edit small"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-card border-secondary">
            <form method="POST">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Create Blog Post</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="post_title" class="form-control bg-dark border-secondary text-primary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" class="form-control bg-dark border-secondary text-primary" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control bg-dark border-secondary text-primary" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select bg-dark border-secondary text-primary">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" name="add_post" class="btn btn-lp-primary">Save Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
