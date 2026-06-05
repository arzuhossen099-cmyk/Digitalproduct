<?php
$title = "User Management";
$active_page = "users";
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">User Management</h2>
    <button class="btn btn-lp-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fas fa-user-plus me-2"></i> Add User</button>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Credits</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td class="fw-bold"><?php echo h($u['username']); ?></td>
                <td><?php echo h($u['email']); ?></td>
                <td><span class="badge bg-secondary"><?php echo h($u['role'] ?? 'user'); ?></span></td>
                <td class="text-accent"><?php echo number_format($u['credits']); ?></td>
                <td>
                    <span class="lp-badge lp-badge-<?php echo $u['status'] === 'active' ? 'success' : 'danger'; ?>">
                        <?php echo ucfirst($u['status']); ?>
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-lp-outline px-2 py-0"><i class="fas fa-edit small"></i></button>
                        <button class="btn btn-sm btn-lp-outline px-2 py-0 text-danger"><i class="fas fa-ban small"></i></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
