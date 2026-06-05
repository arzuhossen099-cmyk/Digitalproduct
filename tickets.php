<?php
$title = "Support Tickets";
require_once __DIR__ . '/includes/header_user.php';

$user = get_logged_in_user();

if (isset($_POST['open_ticket'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }
    $subject = trim($_POST['subject']);
    $priority = $_POST['priority'];
    $message = trim($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO tickets (user_id, subject, priority, status) VALUES (?, ?, ?, 'open')");
    $stmt->execute([$user['id'], $subject, $priority]);
    $ticket_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO ticket_messages (ticket_id, user_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$ticket_id, $user['id'], $message]);
    $success = "Ticket opened successfully!";
}

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY updated_at DESC");
$stmt->execute([$user['id']]);
$tickets = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">My Support Tickets</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ticketModal"><i class="fas fa-plus me-2"></i> Open New Ticket</button>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-dark table-hover border-secondary">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?php echo h($ticket['subject']); ?></td>
                    <td><span class="badge bg-<?php echo $ticket['priority'] === 'urgent' ? 'danger' : ($ticket['priority'] === 'high' ? 'warning' : 'info'); ?>"><?php echo ucfirst($ticket['priority']); ?></span></td>
                    <td><span class="badge bg-<?php echo $ticket['status'] === 'open' ? 'success' : 'secondary'; ?>"><?php echo str_replace('_', ' ', ucfirst($ticket['status'])); ?></span></td>
                    <td><?php echo date('M d, Y H:i', strtotime($ticket['updated_at'])); ?></td>
                    <td>
                        <a href="ticket_view.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i> View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($tickets)): ?>
                    <tr><td colspan="5" class="text-center text-muted">No support tickets found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <form method="POST">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Open Support Ticket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" name="open_ticket" class="btn btn-primary">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
