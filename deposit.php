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
            $pdo->beginTransaction();

            // Check for matching SMS record for auto-approval
            $transaction_id = strtoupper($transaction_id);
            $stmt = $pdo->prepare("SELECT * FROM sms_logs WHERE parsed_trx_id = ? AND parsed_amount = ? AND status = 'unmatched'");
            $stmt->execute([$transaction_id, $amount]);
            $sms_match = $stmt->fetch();

            if ($sms_match) {
                // Auto-approve
                $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, method, transaction_id, status) VALUES (?, ?, ?, ?, 'approved')");
                $stmt->execute([$_SESSION['user_id'], $amount, $method, $transaction_id]);

                $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$amount, $_SESSION['user_id']]);

                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'deposit', ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $amount, "Auto-Deposit (SMS Match): " . $transaction_id]);

                $stmt = $pdo->prepare("UPDATE sms_logs SET status = 'matched' WHERE id = ?");
                $stmt->execute([$sms_match['id']]);

                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], "Success! Your deposit of ৳$amount has been automatically approved."]);

                $success = "Deposit automatically approved and balance updated!";
                $user['balance'] += $amount;
            } else {
                // Submit for manual approval
                $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, method, transaction_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $amount, $method, $transaction_id]);
                $success = "Deposit request submitted successfully! It will be approved soon.";
            }

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
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

        <ul class="nav nav-tabs mb-3" id="depositTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#autoDeposit">Auto Deposit</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#manualDeposit">Manual</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Auto Deposit -->
            <div class="tab-pane fade show active" id="autoDeposit">
                <?php if (($settings['auto_deposit_status'] ?? 'disabled') == 'enabled'): ?>
                <form action="initiate_payment.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Amount (BDT)</label>
                        <input type="number" name="amount" class="form-control" required min="10" placeholder="Min. 10 BDT">
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2">
                        <i class="fas fa-bolt me-1"></i> Pay Now (Instant)
                    </button>
                    <div class="text-center mt-2">
                        <small class="text-muted">Supports bKash, Nagad, Cards, etc.</small>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-secondary py-4 text-center">
                        Auto deposit is currently disabled. Please use manual method.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Manual Deposit -->
            <div class="tab-pane fade" id="manualDeposit">
                <div class="alert alert-info py-2 small">
                    Send money to our numbers and submit the TxID.
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
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
