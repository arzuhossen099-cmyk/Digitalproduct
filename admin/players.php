<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$message = '';
$error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Player deleted successfully!';
}

if (isset($_POST['add_player'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }
    $team_id = (int)$_POST['team_id'];
    $name_en = $_POST['name_en'] ?? '';
    $name_bn = $_POST['name_bn'] ?? '';

    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'player_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/' . $photo);
    }

    if ($name_en && $name_bn) {
        $stmt = $pdo->prepare("INSERT INTO players (team_id, name_en, name_bn, photo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$team_id, $name_en, $name_bn, $photo]);
        $message = 'Player added successfully!';
    }
}

$players = $pdo->query("SELECT p.*, t.name_en as team_name FROM players p LEFT JOIN teams t ON p.team_id = t.id ORDER BY p.name_en ASC")->fetchAll();
$teams = $pdo->query("SELECT id, name_en FROM teams")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Players</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">Add New Player</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Team</label>
                        <select name="team_id" class="form-select">
                            <option value="">No Team</option>
                            <?php foreach ($teams as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo h($t['name_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Player Name (EN)</label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Player Name (BN)</label>
                        <input type="text" name="name_bn" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                    <button type="submit" name="add_player" class="btn btn-primary">Add Player</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">Players List</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name (EN)</th>
                            <th>Team</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $p): ?>
                        <tr>
                            <td><img src="../uploads/<?php echo h($p['photo']); ?>" width="30"></td>
                            <td><?php echo h($p['name_en']); ?></td>
                            <td><?php echo h($p['team_name'] ?: 'N/A'); ?></td>
                            <td>
                                <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
