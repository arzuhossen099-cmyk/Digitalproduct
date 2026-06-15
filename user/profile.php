<?php
require_once __DIR__ . '/../includes/init.php';
if (!is_logged_in()) redirect('/user/login.php');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="admin-card p-4 mb-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x text-yellow"></i>
                </div>
                <h4 class="fw-bold mb-0"><?php echo h($user['username']); ?></h4>
                <p class="text-muted small"><?php echo h($user['email']); ?></p>
                <hr class="border-secondary">
                <a href="logout.php" class="btn btn-outline-danger btn-sm w-100">LOGOUT</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="admin-card p-4 mb-4">
                <h5 class="fw-bold mb-4 text-uppercase border-bottom border-secondary pb-3">Account Settings</h5>
                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">USERNAME</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" value="<?php echo h($user['username']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">FULL NAME</label>
                            <input type="text" name="full_name" class="form-control bg-dark border-secondary text-white" value="<?php echo h($user['full_name']); ?>">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-yellow">UPDATE PROFILE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
