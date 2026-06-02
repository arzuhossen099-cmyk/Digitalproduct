<?php
require_once 'header.php';

if (isset($_POST['save_task'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $title = $_POST['title'];
    $instructions = $_POST['instructions'];
    $url = $_POST['url'];
    $reward = floatval($_POST['reward_amount']);
    $status = $_POST['status'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE tasks SET title=?, instructions=?, url=?, reward_amount=?, status=? WHERE id=?");
        $stmt->execute([$title, $instructions, $url, $reward, $status, $id]);
        echo "<div class='alert alert-success'>Task updated!</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, instructions, url, reward_amount, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $instructions, $url, $reward, $status]);
        echo "<div class='alert alert-success'>Task created!</div>";
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id=?");
    $stmt->execute([$id]);
    header("Location: manage_tasks.php");
    exit();
}

$tasks = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Manage Social Tasks</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="resetTaskForm()">Add New Task</button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Reward</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $t): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($t['title']); ?></td>
                        <td>৳<?php echo number_format($t['reward_amount'], 2); ?></td>
                        <td><span class="badge bg-<?php echo $t['status'] == 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($t['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editTask(<?php echo json_encode($t); ?>)'>Edit</button>
                            <a href="manage_tasks.php?delete=<?php echo $t['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header"><h5>Social Task</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="task_id">
                    <div class="mb-3"><label>Title</label><input type="text" name="title" id="task_title" class="form-control" required></div>
                    <div class="mb-3"><label>Instructions</label><textarea name="instructions" id="task_instructions" class="form-control" rows="3"></textarea></div>
                    <div class="mb-3"><label>Target URL</label><input type="url" name="url" id="task_url" class="form-control"></div>
                    <div class="mb-3"><label>Reward (৳)</label><input type="number" step="0.01" name="reward_amount" id="task_reward" class="form-control" required></div>
                    <div class="mb-3"><label>Status</label><select name="status" id="task_status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
                <div class="modal-footer"><button type="submit" name="save_task" class="btn btn-primary">Save Task</button></div>
            </form>
        </div>
    </div>
</div>

<script>
function resetTaskForm() { $('#task_id').val(''); $('#task_title').val(''); $('#task_instructions').val(''); $('#task_url').val(''); $('#task_reward').val(''); }
function editTask(t) { $('#task_id').val(t.id); $('#task_title').val(t.title); $('#task_instructions').val(t.instructions); $('#task_url').val(t.url); $('#task_reward').val(t.reward_amount); $('#task_status').val(t.status); $('#taskModal').modal('show'); }
</script>

<?php require_once 'footer.php'; ?>
