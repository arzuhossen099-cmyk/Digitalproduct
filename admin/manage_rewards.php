<?php
require_once 'header.php';

// Handle Add/Edit Reward
if (isset($_POST['save_reward'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $amount = floatval($_POST['amount']);
    $percentage = floatval($_POST['percentage']);
    $bonus_amount = floatval($_POST['bonus_amount']);
    $type = $_POST['type'];
    $frequency = $_POST['frequency'];
    $min_deposit = floatval($_POST['min_deposit']);
    $min_plan_price = floatval($_POST['min_plan_price']);
    $status = $_POST['status'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE rewards SET name=?, description=?, amount=?, percentage=?, bonus_amount=?, type=?, frequency=?, min_deposit=?, min_plan_price=?, status=? WHERE id=?");
        $stmt->execute([$name, $description, $amount, $percentage, $bonus_amount, $type, $frequency, $min_deposit, $min_plan_price, $status, $id]);
        $msg = "Reward updated successfully!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO rewards (name, description, amount, percentage, bonus_amount, type, frequency, min_deposit, min_plan_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $amount, $percentage, $bonus_amount, $type, $frequency, $min_deposit, $min_plan_price, $status]);
        $msg = "Reward created successfully!";
    }
    echo "<div class='alert alert-success'>$msg</div>";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM rewards WHERE id=?");
    $stmt->execute([$id]);
    header("Location: manage_rewards.php");
    exit();
}

$rewards = $pdo->query("SELECT * FROM rewards ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Reward Management</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rewardModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i> Add Reward Plan
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount/Bonus</th>
                        <th>Min. Req.</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rewards as $r): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($r['name']); ?></strong><br>
                            <small class="text-muted"><?php echo ucfirst($r['frequency']); ?></small>
                        </td>
                        <td><span class="badge bg-info"><?php echo ucfirst($r['type']); ?></span></td>
                        <td>
                            ৳<?php echo number_format($r['amount'], 2); ?>
                            <?php if($r['bonus_amount'] > 0) echo "+ ৳" . number_format($r['bonus_amount'], 2); ?>
                        </td>
                        <td>
                            <small>
                                Dep: ৳<?php echo $r['min_deposit']; ?><br>
                                Plan: ৳<?php echo $r['min_plan_price']; ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $r['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($r['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editReward(<?php echo json_encode($r); ?>)'>Edit</button>
                            <a href="manage_rewards.php?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this reward?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reward Modal -->
<div class="modal fade" id="rewardModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Reward Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="reward_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reward Title</label>
                            <input type="text" name="name" id="reward_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reward Type</label>
                            <select name="type" id="reward_type" class="form-select" required>
                                <option value="daily">Daily Check-in</option>
                                <option value="ad">Ad Reward</option>
                                <option value="referral">Referral Bonus</option>
                                <option value="achievement">Achievement</option>
                                <option value="vip">VIP Reward</option>
                                <option value="manual">Manual/Special</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="reward_desc" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Amount (৳)</label>
                            <input type="number" step="0.01" name="amount" id="reward_amount" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Percentage (%)</label>
                            <input type="number" step="0.01" name="percentage" id="reward_perc" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bonus Amount (৳)</label>
                            <input type="number" step="0.01" name="bonus_amount" id="reward_bonus" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frequency</label>
                            <select name="frequency" id="reward_freq" class="form-select">
                                <option value="one-time">One-time</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="reward_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <h6 class="mt-3">Eligibility Requirements</h6>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min. Total Deposit (৳)</label>
                            <input type="number" step="0.01" name="min_deposit" id="reward_min_dep" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min. Active Plan Price (৳)</label>
                            <input type="number" step="0.01" name="min_plan_price" id="reward_min_plan" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="save_reward" class="btn btn-primary">Save Reward</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    $('#modalTitle').text('Add Reward Plan');
    $('#reward_id').val('');
    $('#reward_name').val('');
    $('#reward_desc').val('');
    $('#reward_amount').val(0);
    $('#reward_perc').val(0);
    $('#reward_bonus').val(0);
    $('#reward_type').val('daily');
    $('#reward_freq').val('one-time');
    $('#reward_min_dep').val(0);
    $('#reward_min_plan').val(0);
    $('#reward_status').val('active');
}

function editReward(reward) {
    $('#modalTitle').text('Edit Reward Plan');
    $('#reward_id').val(reward.id);
    $('#reward_name').val(reward.name);
    $('#reward_desc').val(reward.description);
    $('#reward_amount').val(reward.amount);
    $('#reward_perc').val(reward.percentage);
    $('#reward_bonus').val(reward.bonus_amount);
    $('#reward_type').val(reward.type);
    $('#reward_freq').val(reward.frequency);
    $('#reward_min_dep').val(reward.min_deposit);
    $('#reward_min_plan').val(reward.min_plan_price);
    $('#reward_status').val(reward.status);
    $('#rewardModal').modal('show');
}
</script>

<?php require_once 'footer.php'; ?>
