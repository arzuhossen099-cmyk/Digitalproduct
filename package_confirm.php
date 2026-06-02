<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_POST['confirm_purchase'])) {
    header("Location: packages.php");
    exit();
}

$package_id = intval($_POST['package_id']);
$operator = trim($_POST['operator']);
$phone_number = trim($_POST['phone_number']);

$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
$stmt->execute([$package_id]);
$package = $stmt->fetch();

if (!$package) {
    header("Location: packages.php");
    exit();
}

if (isset($_POST['confirm_purchase'])) {
    if ($user['balance'] < $package['price']) {
        $error = "Insufficient balance. Please deposit money first.";
    } else {
        try {
            $pdo->beginTransaction();

            // Deduct balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$package['price'], $_SESSION['user_id']]);

            // Record order
            $stmt = $pdo->prepare("INSERT INTO package_orders (user_id, package_id, phone_number, operator, amount, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $package_id, $phone_number, $operator, $package['price']]);

            // Record transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'package_purchase', ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $package['price'], "Order: " . $package['name'] . " for " . $phone_number . " (" . $operator . ")"]);

            // Notify user
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], "Your order for " . $package['name'] . " (৳" . $package['price'] . ") is pending approval."]);

            $pdo->commit();
            $success = "Purchase request submitted! Admin will approve it soon.";
            $user['balance'] -= $package['price'];
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Order failed. Please try again.";
        }
    }
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Confirm Purchase</h5>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <a href="packages.php" class="btn btn-secondary w-100">Go Back</a>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="index.php" class="btn btn-primary w-100">Back to Home</a>
            <a href="transactions.php" class="btn btn-outline-primary w-100 mt-2">View History</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Package</th>
                        <td><?php echo htmlspecialchars($package['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Operator</th>
                        <td><?php echo htmlspecialchars($operator); ?></td>
                    </tr>
                    <tr>
                        <th>Mobile Number</th>
                        <td><?php echo htmlspecialchars($phone_number); ?></td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td>৳<?php echo number_format($package['price'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Validity</th>
                        <td><?php echo htmlspecialchars($package['validity']); ?></td>
                    </tr>
                </table>
            </div>

            <form method="POST">
                <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
                <input type="hidden" name="operator" value="<?php echo $operator; ?>">
                <input type="hidden" name="phone_number" value="<?php echo $phone_number; ?>">
                <button type="submit" name="confirm_purchase" class="btn btn-primary w-100 py-3 mb-2" onclick="return confirm('Confirm this purchase?')">
                    <i class="fas fa-check-circle me-2"></i> Pay & Confirm
                </button>
                <a href="packages.php" class="btn btn-outline-secondary w-100">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
