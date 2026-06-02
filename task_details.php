<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND status = 'active'");
$stmt->execute([$id]);
$task = $stmt->fetch();

if (!$task) { header("Location: earn.php"); exit(); }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['screenshot'])) {
    $target_dir = "assets/uploads/screenshots/";
    $file_ext = strtolower(pathinfo($_FILES["screenshot"]["name"], PATHINFO_EXTENSION));
    $file_name = $_SESSION['user_id'] . "_" . time() . "." . $file_ext;
    $target_file = $target_dir . $file_name;

    if (!in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
        $error = "Only JPG, JPEG & PNG files are allowed.";
    } else {
        if (move_uploaded_file($_FILES["screenshot"]["tmp_name"], $target_file)) {
            $stmt = $pdo->prepare("INSERT INTO task_submissions (user_id, task_id, screenshot) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $id, $target_file]);
            $success = "Task submitted successfully! Admin will review it.";
        } else {
            $error = "File upload failed.";
        }
    }
}
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h4><?php echo htmlspecialchars($task['title']); ?></h4>
        <div class="bg-light p-3 rounded mb-4">
            <h6>Instructions:</h6>
            <p><?php echo nl2br(htmlspecialchars($task['instructions'])); ?></p>
            <?php if($task['url']): ?>
                <a href="<?php echo $task['url']; ?>" target="_blank" class="btn btn-outline-primary mb-2">Visit Link / Social Page</a>
            <?php endif; ?>
        </div>

        <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="earn.php" class="btn btn-primary w-100">Back to Earning</a>
        <?php else: ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Upload Screenshot Proof</label>
                    <input type="file" name="screenshot" class="form-control" required accept="image/*">
                </div>
                <button type="submit" class="btn btn-success w-100">Submit for Review</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/bottom_menu.php'; require_once 'includes/footer.php'; ?>
