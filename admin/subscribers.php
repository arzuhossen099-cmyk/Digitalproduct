<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?")->execute([$id]);
}

if (isset($_GET['export'])) {
    $subscribers = $pdo->query("SELECT email, created_at FROM newsletter_subscribers WHERE status = 'active'")->fetchAll();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Subscribed At']);
    foreach ($subscribers as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

$subscribers = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Newsletter Subscribers</h1>
    <a href="?export=1" class="btn btn-success"><i class="fas fa-file-csv"></i> Export CSV</a>
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Subscribed At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $s): ?>
                <tr>
                    <td><?php echo h($s['email']); ?></td>
                    <td><span class="badge bg-primary"><?php echo h($s['status']); ?></span></td>
                    <td><?php echo date('d M, Y', strtotime($s['created_at'])); ?></td>
                    <td>
                        <a href="?delete=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
