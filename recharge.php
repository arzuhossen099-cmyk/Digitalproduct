<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = trim($_POST['phone']);
    $amount = floatval($_POST['amount']);
    $operator = $_POST['operator'];
    $type = $_POST['type'];

    if ($amount < 10) {
        $error = "Minimum recharge amount is 10 BDT.";
    } elseif ($amount > $user['balance']) {
        $error = "Insufficient balance.";
    } elseif (empty($phone)) {
        $error = "Phone number is required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Deduct balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $_SESSION['user_id']]);

            // Record transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'recharge', ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $amount, "Mobile recharge to $phone ($operator)"]);

            // Send notification
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], "Your recharge request for $phone (৳$amount) is successful!"]);

            $pdo->commit();
            $success = "Recharge successful!";
            $user['balance'] -= $amount;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Recharge failed. Please try again.";
        }
    }
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Mobile Recharge</h5>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" required placeholder="01xxxxxxxxx">
            </div>
            <div class="mb-3">
                <label class="form-label">Operator</label>
                <select name="operator" class="form-select" required>
                    <option value="grameenphone">Grameenphone</option>
                    <option value="robi">Robi</option>
                    <option value="airtel">Airtel</option>
                    <option value="banglalink">Banglalink</option>
                    <option value="teletalk">Teletalk</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Recharge Type</label>
                <select name="type" class="form-select" required>
                    <option value="prepaid">Prepaid</option>
                    <option value="postpaid">Postpaid</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Amount (BDT)</label>
                <input type="number" name="amount" class="form-control" required min="10">
            </div>
            <button type="submit" class="btn btn-primary w-100">Recharge Now</button>
        </form>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
