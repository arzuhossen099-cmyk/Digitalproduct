<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$message = '';
$error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Article deleted successfully!';
}

$articles = $pdo->query("SELECT a.*, c.name_en as category_name FROM articles a JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage News Articles</h1>
    <a href="article_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Article</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo h($message); ?></div>
<?php endif; ?>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title (EN)</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $art): ?>
                <tr>
                    <td><img src="../uploads/<?php echo h($art['featured_image']); ?>" width="50" height="30" style="object-fit:cover;"></td>
                    <td><?php echo h($art['title_en']); ?></td>
                    <td><?php echo h($art['category_name']); ?></td>
                    <td>
                        <?php if ($art['status'] == 'published'): ?>
                            <span class="badge bg-success">Published</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?php echo h(ucfirst($art['status'])); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $art['views']; ?></td>
                    <td><?php echo date('d M, Y', strtotime($art['created_at'])); ?></td>
                    <td>
                        <a href="article_edit.php?id=<?php echo $art['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <a href="?delete=<?php echo $art['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
