<?php
require_once 'header.php';

if (isset($_POST['update_balance'])) {
    $uid = intval($_POST['user_id']);
    $new_balance = floatval($_POST['balance']);
    $new_level = intval($_POST['user_level']);
    $stmt = $pdo->prepare("UPDATE users SET balance = ?, user_level = ? WHERE id = ?");
    $stmt->execute([$new_balance, $new_level, $uid]);
    echo "<div class='alert alert-success'>User updated!</div>";
}

if (isset($_GET['toggle_status'])) {
    $uid = intval($_GET['toggle_status']);
    $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
    $stmt->execute([$uid]);
    header("Location: users.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM users WHERE is_admin = 0 ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Manage Users</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Balance</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['phone']); ?></td>
                        <td>
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <input type="number" step="0.01" name="balance" class="form-control form-control-sm me-1" value="<?php echo $u['balance']; ?>" style="width: 80px;" title="Balance">
                                <input type="number" name="user_level" class="form-control form-control-sm me-1" value="<?php echo $u['user_level']; ?>" style="width: 50px;" title="Level">
                                <button type="submit" name="update_balance" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $u['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($u['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/y', strtotime($u['created_at'])); ?></td>
                        <td>
                            <a href="users.php?toggle_status=<?php echo $u['id']; ?>" class="btn btn-sm btn-warning">
                                <?php echo $u['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
