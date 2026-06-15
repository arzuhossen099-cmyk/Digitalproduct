<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$message = '';
$error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM matches WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Match deleted successfully!';
}

if (isset($_POST['add_match'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }
    $category_id = (int)$_POST['category_id'];
    $team1_id = (int)$_POST['team1_id'];
    $team2_id = (int)$_POST['team2_id'];
    $tournament_name = $_POST['tournament_name'] ?? '';
    $match_date = $_POST['match_date'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $status = $_POST['status'] ?? 'upcoming';

    if ($category_id && $team1_id && $team2_id && $match_date) {
        $stmt = $pdo->prepare("INSERT INTO matches (category_id, team1_id, team2_id, tournament_name, match_date, venue, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $team1_id, $team2_id, $tournament_name, $match_date, $venue, $status]);
        $message = 'Match scheduled successfully!';
    } else {
        $error = 'Please fill all required fields.';
    }
}

if (isset($_POST['update_score'])) {
    $match_id = (int)$_POST['match_id'];
    $team1_score = $_POST['team1_score'];
    $team2_score = $_POST['team2_score'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE matches SET team1_score = ?, team2_score = ?, status = ? WHERE id = ?");
    $stmt->execute([$team1_score, $team2_score, $status, $match_id]);
    $message = 'Match updated successfully!';
}

$matches = $pdo->query("SELECT m.*, c.name_en as category_name, t1.name_en as team1_name, t2.name_en as team2_name
                        FROM matches m
                        JOIN categories c ON m.category_id = c.id
                        JOIN teams t1 ON m.team1_id = t1.id
                        JOIN teams t2 ON m.team2_id = t2.id
                        ORDER BY m.match_date DESC LIMIT 50")->fetchAll();

$categories = $pdo->query("SELECT id, name_en FROM categories")->fetchAll();
$teams = $pdo->query("SELECT id, name_en FROM teams")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Matches</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">Schedule New Match</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo h($cat['name_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team 1</label>
                        <select name="team1_id" class="form-select" required>
                            <?php foreach ($teams as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo h($t['name_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team 2</label>
                        <select name="team2_id" class="form-select" required>
                            <?php foreach ($teams as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo h($t['name_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tournament</label>
                        <input type="text" name="tournament_name" class="form-control" placeholder="e.g. FIFA World Cup">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Match Date & Time</label>
                        <input type="datetime-local" name="match_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Venue</label>
                        <input type="text" name="venue" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="upcoming">Upcoming</option>
                            <option value="live">Live</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <button type="submit" name="add_match" class="btn btn-primary w-100">Schedule Match</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">Recent Matches</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Match</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $m): ?>
                        <tr>
                            <td><?php echo date('d M, h:i A', strtotime($m['match_date'])); ?></td>
                            <td><?php echo h($m['team1_name']); ?> vs <?php echo h($m['team2_name']); ?></td>
                            <td>
                                <form method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="match_id" value="<?php echo $m['id']; ?>">
                                    <input type="text" name="team1_score" value="<?php echo h($m['team1_score']); ?>" class="form-control form-control-sm mx-1" style="width: 40px;">
                                    -
                                    <input type="text" name="team2_score" value="<?php echo h($m['team2_score']); ?>" class="form-control form-control-sm mx-1" style="width: 40px;">
                                    <select name="status" class="form-select form-select-sm ms-2">
                                        <option value="upcoming" <?php echo $m['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                        <option value="live" <?php echo $m['status'] == 'live' ? 'selected' : ''; ?>>Live</option>
                                        <option value="completed" <?php echo $m['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <button type="submit" name="update_score" class="btn btn-sm btn-success ms-2"><i class="fas fa-save"></i></button>
                                </form>
                            </td>
                            <td>
                                <span class="badge <?php echo $m['status'] == 'live' ? 'bg-danger' : ($m['status'] == 'completed' ? 'bg-secondary' : 'bg-primary'); ?>">
                                    <?php echo h(ucfirst($m['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
