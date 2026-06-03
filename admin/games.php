<?php
require_once 'header.php';

// Handle Add/Edit Game
if (isset($_POST['save_game'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = $_POST['name'];
    $icon = $_POST['icon_class'];
    $status = $_POST['status'];
    $link = $_POST['link'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE games SET name=?, icon_class=?, status=?, link=? WHERE id=?");
        $stmt->execute([$name, $icon, $status, $link, $id]);
        echo "<div class='alert alert-success'>Game updated successfully!</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO games (name, icon_class, status, link) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $icon, $status, $link]);
        echo "<div class='alert alert-success'>Game added successfully!</div>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM games WHERE id=?");
    $stmt->execute([$id]);
    header("Location: games.php");
    exit();
}

$games = $pdo->query("SELECT * FROM games ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Dynamic Game Management</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#gameModal" onclick="resetGameForm()">
        <i class="fas fa-plus me-2"></i> Add New Game
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Link</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $g): ?>
                    <tr>
                        <td><i class="<?php echo htmlspecialchars($g['icon_class'] ?? ''); ?> fa-2x"></i></td>
                        <td><strong><?php echo htmlspecialchars($g['name'] ?? ''); ?></strong></td>
                        <td><small><?php echo htmlspecialchars($g['link'] ?? ''); ?></small></td>
                        <td>
                            <span class="badge bg-<?php echo $g['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($g['status'] ?? ''); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editGame(<?php echo json_encode($g); ?>)'>Edit</button>
                            <a href="games.php?delete=<?php echo $g['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this game?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Game Modal -->
<div class="modal fade" id="gameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="gameModalLabel">Add Game</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="game_id">
                    <div class="mb-3">
                        <label class="form-label">Game Name</label>
                        <input type="text" name="name" id="game_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Class (FontAwesome)</label>
                        <input type="text" name="icon_class" id="game_icon" class="form-control" value="fas fa-gamepad" required>
                        <small class="text-muted">e.g., fas fa-gamepad, fas fa-rocket</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Game Link (URL)</label>
                        <input type="text" name="link" id="game_link" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="game_status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="save_game" class="btn btn-primary">Save Game</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetGameForm() {
    $('#gameModalLabel').text('Add Game');
    $('#game_id').val('');
    $('#game_name').val('');
    $('#game_icon').val('fas fa-gamepad');
    $('#game_link').val('');
    $('#game_status').val('active');
}

function editGame(g) {
    $('#gameModalLabel').text('Edit Game');
    $('#game_id').val(g.id);
    $('#game_name').val(g.name);
    $('#game_icon').val(g.icon_class);
    $('#game_link').val(g.link);
    $('#game_status').val(g.status);
    $('#gameModal').modal('show');
}
</script>

<?php require_once 'footer.php'; ?>
