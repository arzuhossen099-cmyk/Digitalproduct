<?php
$title = "User Management";
$active_page = "users";
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT * FROM users WHERE role != 'super_admin' ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">User Management</h2>
    <div class="btn-group">
        <button class="btn btn-lp-outline btn-sm"><i class="fas fa-user-plus me-2"></i> Add User</button>
    </div>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Credits</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td class="fw-bold"><?php echo h($u['username']); ?></td>
                <td><?php echo h($u['email']); ?></td>
                <td class="text-accent"><?php echo number_format($u['credits']); ?></td>
                <td>
                    <span class="lp-badge lp-badge-<?php echo $u['status'] === 'active' ? 'success' : 'danger'; ?>">
                        <?php echo ucfirst($u['status']); ?>
                    </span>
                </td>
                <td class="small text-muted"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-lp-outline"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-lp-outline text-danger"><i class="fas fa-ban"></i></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
