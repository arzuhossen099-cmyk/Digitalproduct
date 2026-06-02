<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $referral_code_used = trim($_POST['referral_code']);

    if (empty($username) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username, email or phone already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR phone = ?");
        $stmt->execute([$username, $email, $phone]);
        if ($stmt->fetch()) {
            $error = "Username, email or phone already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $new_referral_code = substr(md5(uniqid($username, true)), 0, 8);

            // Check for referrer (Level 1 and Level 2)
            $referred_by_l1 = null;
            $referred_by_l2 = null;
            if (!empty($referral_code_used)) {
                $stmt = $pdo->prepare("SELECT id, referred_by FROM users WHERE referral_code = ?");
                $stmt->execute([$referral_code_used]);
                $referrer = $stmt->fetch();
                if ($referrer) {
                    $referred_by_l1 = $referrer['id'];
                    $referred_by_l2 = $referrer['referred_by']; // Level 2 is the referrer's referrer
                }
            }

            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, phone, referral_code, referred_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $email, $phone, $new_referral_code, $referred_by_l1]);
                $user_id = $pdo->lastInsertId();

                if ($referred_by_l1) {
                    $stmt = $pdo->prepare("INSERT INTO referrals (referrer_id, referee_id) VALUES (?, ?)");
                    $stmt->execute([$referred_by_l1, $user_id]);
                }

                $pdo->commit();
                $success = "Registration successful! You can now login.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Registration failed. Please try again.";
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
    <title>Register - Telecom Rewards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Register</h3>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required placeholder="017xxxxxxxx">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Referral Code (Optional)</label>
                                <input type="text" name="referral_code" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        <div class="mt-3 text-center">
                            Already have an account? <a href="login.php">Login here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
