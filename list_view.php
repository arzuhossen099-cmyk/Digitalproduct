<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$user = get_logged_in_user();

$stmt = $pdo->prepare("SELECT sl.*, (SELECT COUNT(*) FROM saved_list_leads sll WHERE sll.list_id = sl.id) as count FROM saved_lists sl WHERE sl.id = ? AND sl.user_id = ?");
$stmt->execute([$id, $user['id']]);
$list = $stmt->fetch();

if (!$list) {
    die("List not found.");
}

$stmt = $pdo->prepare("SELECT l.* FROM leads l JOIN saved_list_leads sll ON l.id = sll.lead_id WHERE sll.list_id = ?");
$stmt->execute([$id]);
$leads = $stmt->fetchAll();

$title = "List: " . $list['name'];
require_once __DIR__ . '/includes/header_user.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="saved_lists.php" class="btn btn-lp-outline btn-sm mb-3"><i class="fas fa-arrow-left me-1"></i> Back</a>
        <h2 class="fw-bold text-accent mb-0"><?php echo h($list['name']); ?></h2>
        <p class="text-muted small mb-0"><?php echo h($list['description']); ?></p>
    </div>
    <div class="btn-group">
        <button class="btn btn-lp-primary"><i class="fas fa-download me-2"></i> Export List</button>
    </div>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Lead Name</th>
                <th>Job Title</th>
                <th>Company</th>
                <th>Location</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leads as $lead): ?>
            <tr>
                <td class="fw-bold"><?php echo h($lead['full_name']); ?></td>
                <td><?php echo h($lead['job_title']); ?></td>
                <td class="text-accent"><?php echo h($lead['company_name']); ?></td>
                <td><?php echo h($lead['country']); ?></td>
                <td><?php echo h($lead['email'] ?: '••••••••@••••.com'); ?></td>
                <td><?php echo h($lead['phone'] ?: '••••••••'); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($leads)): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted">No leads in this list yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
