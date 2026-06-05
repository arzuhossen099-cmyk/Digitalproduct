<?php
require_once 'includes/auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        // Check if email or username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $error = 'Email or Username already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $referral_code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, referral_code) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $username, $email, $hashed_password, $referral_code])) {
                $user_id = $pdo->lastInsertId();

                // Assign Free Plan by default
                $free_plan_stmt = $pdo->prepare("SELECT id FROM plans WHERE name = 'Free Plan' LIMIT 1");
                $free_plan_stmt->execute();
                $free_plan = $free_plan_stmt->fetch();

                if ($free_plan) {
                    $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_id, expires_at) VALUES (?, ?, NULL)");
                    $stmt->execute([$user_id, $free_plan['id']]);
                }

                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = 'user';
                $_SESSION['username'] = $username;

                lp_log("New user registered: " . $email);
                redirect('index.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LeadPress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem 0; }
        .login-card { background-color: #1e293b; border: 1px solid #334155; border-radius: 1rem; width: 100%; max-width: 450px; padding: 2rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
        .form-control { background-color: #0f172a; border-color: #334155; color: #f8fafc; }
        .form-control:focus { background-color: #0f172a; color: #f8fafc; border-color: #38bdf8; box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.25); }
        .btn-primary { background-color: #0284c7; border: none; }
        .btn-primary:hover { background-color: #0369a1; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-info">LeadPress</h2>
            <p class="text-muted">Create your free account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="johndoe" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="name@company.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Create Account</button>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted small">Already have an account? <a href="login.php" class="text-info text-decoration-none">Sign In</a></p>
        </div>
    </div>
</body>
</html>
