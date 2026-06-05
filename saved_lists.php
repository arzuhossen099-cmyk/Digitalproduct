<?php
$title = "My Saved Lists";
$active_page = "lists";
require_once __DIR__ . '/includes/header_user.php';

$user = get_logged_in_user();
$stmt = $pdo->prepare("SELECT sl.*, (SELECT COUNT(*) FROM saved_list_leads sll WHERE sll.list_id = sl.id) as lead_count FROM saved_lists sl WHERE sl.user_id = ? ORDER BY sl.created_at DESC");
$stmt->execute([$user['id']]);
$lists = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Lead Lists</h2>
    <button class="btn btn-lp-primary" data-bs-toggle="modal" data-bs-target="#listModal"><i class="fas fa-plus me-2"></i> Create New List</button>
</div>

<div class="row g-4">
    <?php foreach ($lists as $list): ?>
    <div class="col-md-4">
        <div class="lp-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="fw-bold mb-0"><?php echo h($list['name']); ?></h5>
                <span class="lp-badge lp-badge-info"><?php echo $list['lead_count']; ?> Leads</span>
            </div>
            <p class="text-muted small mb-4"><?php echo h($list['description'] ?: 'No description provided.'); ?></p>
            <div class="mt-auto d-flex gap-2">
                <a href="list_view.php?id=<?php echo $list['id']; ?>" class="btn btn-lp-outline btn-sm flex-grow-1">View Leads</a>
                <button class="btn btn-lp-outline btn-sm text-danger border-danger border-opacity-25"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($lists)): ?>
        <div class="col-12">
            <div class="lp-card text-center py-5">
                <i class="fas fa-list-ul fa-3x text-muted mb-3"></i>
                <p class="text-muted">You haven't created any lead lists yet.</p>
                <button class="btn btn-lp-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#listModal">Create My First List</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="listModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-card border-secondary">
            <form method="POST">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Create New List</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">List Name</label>
                        <input type="text" name="list_name" class="lp-input w-100" placeholder="e.g. Q4 Outreach" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="lp-input w-100" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-lp-primary">Create List</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
