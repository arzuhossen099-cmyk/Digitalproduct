<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Invalid access.");
}

$tran_id = $_POST['tran_id'];
$status = $_POST['status'];

// Step 1: Verify the transaction in our database
$stmt = $pdo->prepare("SELECT * FROM deposits WHERE transaction_id = ? AND status = 'pending'");
$stmt->execute([$tran_id]);
$deposit = $stmt->fetch();

if (!$deposit) {
    die("Transaction not found or already processed.");
}

if ($status == 'VALID' || $status == 'Success') {
    try {
        $pdo->beginTransaction();

        // Update deposit status
        $stmt = $pdo->prepare("UPDATE deposits SET status = 'approved' WHERE transaction_id = ?");
        $stmt->execute([$tran_id]);

        // Add balance to user
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$deposit['amount'], $deposit['user_id']]);

        // Record transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'deposit', ?, ?)");
        $stmt->execute([$deposit['user_id'], $deposit['amount'], "Auto Deposit: " . $tran_id]);

        // Notify user
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$deposit['user_id'], "Success! Your wallet has been topped up with ৳" . $deposit['amount'] . " via auto deposit."]);

        $pdo->commit();

        // Redirect with success
        session_start();
        $_SESSION['success_msg'] = "Payment successful! Balance updated.";
        header("Location: ../transactions.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("System error processing payment.");
    }
} else {
    // Update status to rejected/failed
    $stmt = $pdo->prepare("UPDATE deposits SET status = 'rejected' WHERE transaction_id = ?");
    $stmt->execute([$tran_id]);

    session_start();
    $_SESSION['error_msg'] = "Payment failed or cancelled.";
    header("Location: ../deposit.php");
    exit();
}
?>
