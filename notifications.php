<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

// Mark all as read
$stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Notifications</h5>
        <div class="list-group list-group-flush">
            <?php if (empty($notifications)): ?>
                <div class="text-center py-4 text-muted">No notifications found</div>
            <?php else:
                foreach ($notifications as $notif):
            ?>
                <div class="list-group-item px-0">
                    <div class="d-flex w-100 justify-content-between">
                        <small class="text-muted"><?php echo date('d M, h:i A', strtotime($notif['created_at'])); ?></small>
                    </div>
                    <p class="mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
                </div>
            <?php
                endforeach;
            endif; ?>
        </div>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
