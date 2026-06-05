<?php
$title = "Support Tickets";
$active_page = "tickets";
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT t.*, u.username FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.updated_at DESC");
$tickets = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">Support Requests</h2>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>User</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Last Update</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $t): ?>
            <tr>
                <td class="fw-bold"><?php echo h($t['subject']); ?></td>
                <td><?php echo h($t['username']); ?></td>
                <td><span class="badge bg-<?php echo $t['priority'] === 'urgent' ? 'danger' : 'info'; ?>"><?php echo ucfirst($t['priority']); ?></span></td>
                <td><span class="lp-badge lp-badge-<?php echo $t['status'] === 'open' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($t['status']); ?></span></td>
                <td class="small text-muted"><?php echo date('M d, H:i', strtotime($t['updated_at'])); ?></td>
                <td>
                    <a href="ticket_view.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-lp-outline">Reply</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
