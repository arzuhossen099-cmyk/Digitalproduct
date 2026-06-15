<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$id]);
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]);
}

$comments = $pdo->query("SELECT c.*, a.title_en as article_title FROM comments c JOIN articles a ON c.article_id = a.id ORDER BY c.created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Comments</h1>
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>User / Guest</th>
                    <th>Article</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $c): ?>
                <tr>
                    <td><?php echo h($c['guest_name'] ?: 'User ID: '.$c['user_id']); ?></td>
                    <td><?php echo h($c['article_title']); ?></td>
                    <td><?php echo h($c['comment']); ?></td>
                    <td>
                        <span class="badge <?php echo $c['status'] == 'approved' ? 'bg-success' : 'bg-warning'; ?>">
                            <?php echo h(ucfirst($c['status'])); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($c['status'] == 'pending'): ?>
                            <a href="?approve=<?php echo $c['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
