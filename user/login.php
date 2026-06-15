<?php
require_once __DIR__ . '/../includes/init.php';
if (is_logged_in()) redirect('/user/profile.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        redirect('/user/profile.php');
    } else {
        $error = "Invalid credentials.";
    }
}
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="admin-card p-5 shadow-lg">
                <h2 class="text-center fw-900 mb-4 text-yellow">SIGN IN</h2>
                <?php if ($error): ?><div class="alert alert-danger small"><?php echo h($error); ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">EMAIL ADDRESS</label>
                        <input type="email" name="email" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">PASSWORD</label>
                        <input type="password" name="password" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <button type="submit" class="btn btn-yellow w-100 py-2 fw-bold">CONTINUE</button>
                </form>
                <div class="mt-4 text-center small">
                    <span class="text-muted">NEW TO PLAYPULSE?</span> <a href="register.php" class="text-yellow text-decoration-none fw-bold">CREATE ACCOUNT</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
