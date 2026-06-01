<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $method = $_POST['method'];
    $account_number = trim($_POST['account_number']);

    if ($amount < 50) {
        $error = "Minimum withdrawal is 50 BDT.";
    } elseif ($amount > $user['balance']) {
        $error = "Insufficient balance.";
    } elseif (empty($account_number)) {
        $error = "Account number is required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Deduct balance immediately or wait for approval?
            // Usually we deduct and if rejected, we refund.
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $_SESSION['user_id']]);

            $stmt = $pdo->prepare("INSERT INTO withdrawals (user_id, amount, method, account_number) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $amount, $method, $account_number]);

            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'withdrawal', ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $amount, "Withdrawal request to $method: $account_number"]);

            $pdo->commit();
            $success = "Withdrawal request submitted successfully!";
            // Update local user variable for UI
            $user['balance'] -= $amount;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to submit withdrawal request.";
        }
    }
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Withdraw Money</h5>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <p>Available Balance: <strong>৳<?php echo number_format($user['balance'], 2); ?></strong></p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Amount (BDT)</label>
                <input type="number" name="amount" class="form-control" required min="50">
            </div>
            <div class="mb-3">
                <label class="form-label">Withdraw Method</label>
                <select name="method" class="form-select" required>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Account Number</label>
                <input type="text" name="account_number" class="form-control" required placeholder="017xxxxxxxx">
            </div>
            <button type="submit" class="btn btn-danger w-100">Submit Withdrawal</button>
        </form>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
