<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$message = '';

if (isset($_POST['add_broadcaster'])) {
    $name = $_POST['name'];
    $website = $_POST['website'];
    $stmt = $pdo->prepare("INSERT INTO broadcasters (name, website) VALUES (?, ?)");
    $stmt->execute([$name, $website]);
    $message = 'Broadcaster added!';
}

if (isset($_POST['add_ott'])) {
    $name = $_POST['name'];
    $website = $_POST['website'];
    $stmt = $pdo->prepare("INSERT INTO ott_platforms (name, website) VALUES (?, ?)");
    $stmt->execute([$name, $website]);
    $message = 'OTT Platform added!';
}

if (isset($_POST['add_guide'])) {
    $match_id = (int)$_POST['match_id'];
    $broadcaster_id = $_POST['broadcaster_id'] ? (int)$_POST['broadcaster_id'] : null;
    $ott_id = $_POST['ott_id'] ? (int)$_POST['ott_id'] : null;
    $country = $_POST['country'];
    $availability_type = $_POST['availability_type'];

    $stmt = $pdo->prepare("INSERT INTO streaming_guides (match_id, broadcaster_id, ott_id, country, availability_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$match_id, $broadcaster_id, $ott_id, $country, $availability_type]);
    $message = 'Streaming guide added!';
}

$matches = $pdo->query("SELECT m.id, t1.name_en as t1, t2.name_en as t2 FROM matches m JOIN teams t1 ON m.team1_id = t1.id JOIN teams t2 ON m.team2_id = t2.id ORDER BY m.match_date DESC")->fetchAll();
$broadcasters = $pdo->query("SELECT * FROM broadcasters")->fetchAll();
$otts = $pdo->query("SELECT * FROM ott_platforms")->fetchAll();
$guides = $pdo->query("SELECT g.*, m.tournament_name, t1.name_en as t1, t2.name_en as t2, b.name as b_name, o.name as o_name
                      FROM streaming_guides g
                      JOIN matches m ON g.match_id = m.id
                      JOIN teams t1 ON m.team1_id = t1.id
                      JOIN teams t2 ON m.team2_id = t2.id
                      LEFT JOIN broadcasters b ON g.broadcaster_id = b.id
                      LEFT JOIN ott_platforms o ON g.ott_id = o.id")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Streaming Guide</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header">Add Broadcaster</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control">
                    </div>
                    <button type="submit" name="add_broadcaster" class="btn btn-primary btn-sm">Add</button>
                </form>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header">Add OTT Platform</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control">
                    </div>
                    <button type="submit" name="add_ott" class="btn btn-primary btn-sm">Add</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header">Add Streaming Guide Entry</div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Match</label>
                        <select name="match_id" class="form-select" required>
                            <?php foreach ($matches as $m): ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo h($m['t1']); ?> vs <?php echo h($m['t2']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" placeholder="e.g. Bangladesh" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Broadcaster</label>
                        <select name="broadcaster_id" class="form-select">
                            <option value="">None</option>
                            <?php foreach ($broadcasters as $b): ?>
                                <option value="<?php echo $b['id']; ?>"><?php echo h($b['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">OTT Platform</label>
                        <select name="ott_id" class="form-select">
                            <option value="">None</option>
                            <?php foreach ($otts as $o): ?>
                                <option value="<?php echo $o['id']; ?>"><?php echo h($o['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="availability_type" class="form-select">
                            <option value="free">Free</option>
                            <option value="paid">Paid</option>
                            <option value="subscription">Subscription</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_guide" class="btn btn-success">Add Guide Entry</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">Streaming Guide Entries</div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Match</th>
                            <th>Country</th>
                            <th>Broadcaster</th>
                            <th>OTT</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guides as $g): ?>
                        <tr>
                            <td><?php echo h($g['t1']); ?> vs <?php echo h($g['t2']); ?></td>
                            <td><?php echo h($g['country']); ?></td>
                            <td><?php echo h($g['b_name']); ?></td>
                            <td><?php echo h($g['o_name']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $g['id']; ?>" class="text-danger"><i class="fas fa-trash"></i></a>
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
