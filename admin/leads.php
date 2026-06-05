<?php
$title = "Lead Database";
$active_page = "leads";
require_once __DIR__ . '/includes/header.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT * FROM leads ORDER BY id DESC LIMIT $limit OFFSET $offset");
$stmt->execute();
$leads = $stmt->fetchAll();

$total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">Lead Database</h2>
    <div class="btn-group">
        <a href="import_leads.php" class="btn btn-lp-primary"><i class="fas fa-file-import me-2"></i> Import CSV</a>
    </div>
</div>

<div class="lp-table-container">
    <table class="lp-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Job Title</th>
                <th>Company</th>
                <th>Industry</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leads as $lead): ?>
            <tr>
                <td class="fw-bold"><?php echo h($lead['full_name']); ?></td>
                <td><?php echo h($lead['job_title']); ?></td>
                <td class="text-accent"><?php echo h($lead['company_name']); ?></td>
                <td><?php echo h($lead['industry']); ?></td>
                <td><?php echo h($lead['country']); ?></td>
                <td>
                    <span class="lp-badge lp-badge-<?php echo $lead['verification_status'] === 'verified' ? 'success' : 'info'; ?>">
                        <?php echo ucfirst($lead['verification_status']); ?>
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-lp-outline px-2 py-0"><i class="fas fa-edit small"></i></button>
                        <button class="btn btn-sm btn-lp-outline px-2 py-0 text-danger"><i class="fas fa-trash small"></i></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
