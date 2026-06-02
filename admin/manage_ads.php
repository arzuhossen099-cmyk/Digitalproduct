<?php
require_once 'header.php';

if (isset($_POST['save_ad'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $title = $_POST['title'];
    $url = $_POST['url'];
    $reward = floatval($_POST['reward_amount']);
    $duration = intval($_POST['duration_seconds']);
    $status = $_POST['status'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE ads_tasks SET title=?, url=?, reward_amount=?, duration_seconds=?, status=? WHERE id=?");
        $stmt->execute([$title, $url, $reward, $duration, $status, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO ads_tasks (title, url, reward_amount, duration_seconds, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $url, $reward, $duration, $status]);
    }
    echo "<div class='alert alert-success'>Ad updated!</div>";
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM ads_tasks WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: manage_ads.php");
    exit();
}

$ads = $pdo->query("SELECT * FROM ads_tasks ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Manage Ads/Link Visits</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adModal" onclick="resetAdForm()">Add New Ad</button>
</div>

<div class="card shadow-sm"><div class="card-body"><div class="table-responsive">
<table class="table">
    <thead><tr><th>Title</th><th>Reward</th><th>Time</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
        <?php foreach ($ads as $a): ?>
        <tr>
            <td><?php echo htmlspecialchars($a['title']); ?></td>
            <td>৳<?php echo $a['reward_amount']; ?></td>
            <td><?php echo $a['duration_seconds']; ?>s</td>
            <td><?php echo $a['status']; ?></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick='editAd(<?php echo json_encode($a); ?>)'>Edit</button>
                <a href="manage_ads.php?delete=<?php echo $a['id']; ?>" class="btn btn-sm btn-danger">Del</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></div></div>

<div class="modal fade" id="adModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
<div class="modal-header"><h5>Ad Task</h5></div>
<div class="modal-body">
    <input type="hidden" name="id" id="ad_id">
    <div class="mb-3"><label>Title</label><input type="text" name="title" id="ad_title" class="form-control" required></div>
    <div class="mb-3"><label>URL</label><input type="url" name="url" id="ad_url" class="form-control" required></div>
    <div class="mb-3"><label>Reward (৳)</label><input type="number" step="0.01" name="reward_amount" id="ad_reward" class="form-control" required></div>
    <div class="mb-3"><label>Duration (Seconds)</label><input type="number" name="duration_seconds" id="ad_duration" class="form-control" required></div>
    <div class="mb-3"><label>Status</label><select name="status" id="ad_status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
</div>
<div class="modal-footer"><button type="submit" name="save_ad" class="btn btn-primary">Save Ad</button></div>
</form></div></div></div>

<script>
function resetAdForm() { $('#ad_id').val(''); $('#ad_title').val(''); $('#ad_url').val(''); $('#ad_reward').val(''); $('#ad_duration').val(15); }
function editAd(a) { $('#ad_id').val(a.id); $('#ad_title').val(a.title); $('#ad_url').val(a.url); $('#ad_reward').val(a.reward_amount); $('#ad_duration').val(a.duration_seconds); $('#ad_status').val(a.status); $('#adModal').modal('show'); }
</script>

<?php require_once 'footer.php'; ?>
