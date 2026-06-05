<?php
$title = "Ticket View";
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$user = get_logged_in_user();

$stmt = $pdo->prepare("SELECT t.*, u.username FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ? AND (t.user_id = ? OR ? IN ('admin', 'super_admin'))");
$stmt->execute([$id, $user['id'], $user['role']]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die("Ticket not found.");
}

if (isset($_POST['send_reply'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "CSRF failed.";
    } else {
        $msg = trim($_POST['message']);
        $stmt = $pdo->prepare("INSERT INTO ticket_messages (ticket_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$id, $user['id'], $msg]);

        // Update ticket timestamp
        $pdo->prepare("UPDATE tickets SET updated_at = NOW() WHERE id = ?")->execute([$id]);
        $success = "Reply sent!";
    }
}

$stmt = $pdo->prepare("SELECT tm.*, u.username, u.role FROM ticket_messages tm JOIN users u ON tm.user_id = u.id WHERE tm.ticket_id = ? ORDER BY tm.created_at ASC");
$stmt->execute([$id]);
$messages = $stmt->fetchAll();

if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
    require_once __DIR__ . '/admin/includes/header.php';
} else {
    require_once __DIR__ . '/includes/header_user.php';
}
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold text-accent mb-1"><?php echo h($ticket['subject']); ?></h2>
        <p class="text-muted small mb-0">Status: <span class="badge bg-secondary"><?php echo ucfirst($ticket['status']); ?></span> | Priority: <?php echo ucfirst($ticket['priority']); ?></p>
    </div>
    <a href="tickets.php" class="btn btn-lp-outline btn-sm">Back</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="messages-container mb-4">
            <?php foreach ($messages as $m):
                $is_admin_msg = in_array($m['role'], ['admin', 'super_admin']);
            ?>
                <div class="lp-card mb-3 <?php echo $is_admin_msg ? 'border-primary' : ''; ?>">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold <?php echo $is_admin_msg ? 'text-accent' : 'text-info'; ?>">
                            <?php echo h($m['username']); ?> <?php if($is_admin_msg) echo '<span class="small opacity-75">(Staff)</span>'; ?>
                        </span>
                        <span class="text-muted small"><?php echo date('M d, H:i', strtotime($m['created_at'])); ?></span>
                    </div>
                    <div class="text-secondary">
                        <?php echo nl2br(h($m['message'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="lp-card">
            <h6 class="fw-bold mb-3">Send Reply</h6>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="mb-3">
                    <textarea name="message" class="form-control bg-dark border-secondary text-primary" rows="4" placeholder="Type your message here..." required></textarea>
                </div>
                <button type="submit" name="send_reply" class="btn btn-lp-primary">Post Reply</button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="lp-card">
            <h6 class="fw-bold mb-3">Ticket Information</h6>
            <div class="small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">ID:</span>
                    <span>#<?php echo $ticket['id']; ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Created:</span>
                    <span><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Last Active:</span>
                    <span><?php echo date('M d, Y H:i', strtotime($ticket['updated_at'])); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
