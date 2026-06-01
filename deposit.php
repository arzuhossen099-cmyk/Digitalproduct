<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $method = $_POST['method'];
    $transaction_id = trim($_POST['transaction_id']);

    if ($amount < 10) {
        $error = "Minimum deposit is 10 BDT.";
    } elseif (empty($transaction_id)) {
        $error = "Transaction ID is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, method, transaction_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $amount, $method, $transaction_id]);
            $success = "Deposit request submitted successfully! It will be approved soon.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Transaction ID already used.";
            } else {
                $error = "Failed to submit deposit request.";
            }
        }
    }
}
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Deposit Money</h5>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="alert alert-info">
            <p class="mb-1">Send money to any of these numbers:</p>
            <ul class="mb-0">
                <li>bKash: <strong><?php echo $settings['bkash_number']; ?></strong></li>
                <li>Nagad: <strong><?php echo $settings['nagad_number']; ?></strong></li>
                <li>Rocket: <strong><?php echo $settings['rocket_number']; ?></strong></li>
            </ul>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Amount (BDT)</label>
                <input type="number" name="amount" class="form-control" required min="10">
            </div>
            <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select name="method" class="form-select" required>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="rocket">Rocket</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Transaction ID</label>
                <input type="text" name="transaction_id" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Deposit</button>
        </form>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
