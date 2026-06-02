<?php
require_once 'header.php';

if (isset($_POST['update_status'])) {
    $sub_id = intval($_POST['sub_id']);
    $status = $_POST['status'];
    $note = $_POST['admin_note'];

    $stmt = $pdo->prepare("SELECT ts.*, t.reward_amount, t.title FROM task_submissions ts JOIN tasks t ON ts.task_id = t.id WHERE ts.id = ? AND ts.status = 'pending'");
    $stmt->execute([$sub_id]);
    $sub = $stmt->fetch();

    if ($sub) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE task_submissions SET status=?, admin_note=? WHERE id=?");
        $stmt->execute([$status, $note, $sub_id]);

        if ($status == 'approved') {
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$sub['reward_amount'], $sub['user_id']]);

            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, ?)");
            $stmt->execute([$sub['user_id'], $sub['reward_amount'], "Task Approved: " . $sub['title']]);

            require_once '../includes/leaderboard_helper.php';
            updateLeaderboard($pdo, $sub['user_id'], $sub['reward_amount']);

            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$sub['user_id'], "Your task submission for '" . $sub['title'] . "' was approved! ৳" . $sub['reward_amount'] . " added."]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$sub['user_id'], "Your task submission for '" . $sub['title'] . "' was rejected. Reason: $note"]);
        }
        $pdo->commit();
        echo "<div class='alert alert-success'>Submission updated!</div>";
    }
}

$subs = $pdo->query("SELECT ts.*, u.username, t.title, t.reward_amount FROM task_submissions ts JOIN users u ON ts.user_id = u.id JOIN tasks t ON ts.task_id = t.id ORDER BY ts.created_at DESC")->fetchAll();
?>

<h4>Task Submissions</h4>
<div class="card shadow-sm"><div class="card-body"><div class="table-responsive">
<table class="table table-hover">
    <thead><tr><th>Date</th><th>User</th><th>Task</th><th>Screenshot</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
        <?php foreach ($subs as $s): ?>
        <tr>
            <td><small><?php echo date('d/m/y H:i', strtotime($s['created_at'])); ?></small></td>
            <td><?php echo htmlspecialchars($s['username']); ?></td>
            <td><?php echo htmlspecialchars($s['title']); ?><br><small>৳<?php echo $s['reward_amount']; ?></small></td>
            <td><a href="../<?php echo $s['screenshot']; ?>" target="_blank" class="btn btn-sm btn-outline-info">View</a></td>
            <td><span class="badge bg-<?php echo $s['status']=='pending'?'warning':($s['status']=='approved'?'success':'danger'); ?>"><?php echo $s['status']; ?></span></td>
            <td>
                <?php if($s['status'] == 'pending'): ?>
                <form method="POST" class="d-flex">
                    <input type="hidden" name="sub_id" value="<?php echo $s['id']; ?>">
                    <input type="text" name="admin_note" class="form-control form-control-sm me-1" placeholder="Note">
                    <button type="submit" name="update_status" value="approved" class="btn btn-sm btn-success me-1">Approve</button>
                    <button type="submit" name="update_status" value="rejected" class="btn btn-sm btn-danger">Reject</button>
                </form>
                <?php else: echo $s['admin_note']; endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></div></div>

<?php require_once 'footer.php'; ?>
