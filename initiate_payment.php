<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['amount'])) {
    header("Location: deposit.php");
    exit();
}

$amount = floatval($_POST['amount']);
$user_id = $_SESSION['user_id'];
$tran_id = "TXN_" . uniqid();

// Fetch settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if (($settings['auto_deposit_status'] ?? 'disabled') != 'enabled') {
    die("Auto deposit is currently disabled.");
}

/*
 * SSLCommerz Integration Logic
 * In a real scenario, you'd use curl to post data to SSLCommerz API.
 * For this project, we'll simulate the redirect to the gateway.
 */

// Step 1: Create a pending deposit record
$stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, method, transaction_id, status) VALUES (?, ?, 'online', ?, 'pending')");
$stmt->execute([$user_id, $amount, $tran_id]);

// Step 2: Redirect to a simulated payment page or real gateway
// In production, you'd get a Gateway URL from SSLCommerz and header("Location: $url");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Payment Gateway...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <div class="card shadow-sm p-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4>Connecting to Secure Payment Gateway...</h4>
            <p class="text-muted">Transaction ID: <?php echo $tran_id; ?></p>
            <p>Amount to Pay: <strong>৳<?php echo number_format($amount, 2); ?></strong></p>

            <div class="alert alert-warning mt-4">
                <strong>Development Mode:</strong> Click below to simulate a successful payment.
                <div class="d-grid gap-2 mt-3">
                    <form action="api/callback.php" method="POST">
                        <input type="hidden" name="tran_id" value="<?php echo $tran_id; ?>">
                        <input type="hidden" name="status" value="VALID">
                        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                        <button type="submit" class="btn btn-success">Simulate Success</button>
                    </form>
                    <form action="api/callback.php" method="POST">
                        <input type="hidden" name="tran_id" value="<?php echo $tran_id; ?>">
                        <input type="hidden" name="status" value="FAILED">
                        <button type="submit" class="btn btn-danger">Simulate Failure</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
