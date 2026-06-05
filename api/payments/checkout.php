<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/subscription_manager.php';
require_login();

$plan_id = (int)($_POST['plan_id'] ?? 0);
$method = $_POST['method'] ?? 'stripe';

$stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    json_response(['error' => 'Invalid plan'], 400);
}

// Logic for different gateways
if ($method === 'stripe') {
    // Mock Stripe checkout session creation
    $transaction_id = 'STRIPE_' . bin2hex(random_bytes(8));

    // In a real implementation, we would return the Stripe session URL
    // For this prototype, we simulate a successful payment callback

    $stmt = $pdo->prepare("INSERT INTO payments (user_id, amount, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, 'completed')");
    $stmt->execute([$_SESSION['user_id'], $plan['price'], 'Stripe', $transaction_id]);

    $subManager = new SubscriptionManager($pdo);
    if ($subManager->activatePlan($_SESSION['user_id'], $plan_id)) {
        json_response(['success' => true, 'message' => 'Payment successful and plan activated!']);
    } else {
        json_response(['error' => 'Payment processed but plan activation failed'], 500);
    }
} elseif ($method === 'sslcommerz') {
    // Similar logic for SSLCommerz (bKash/Nagad/Rocket)
    $transaction_id = 'SSLC_' . bin2hex(random_bytes(8));
    $stmt = $pdo->prepare("INSERT INTO payments (user_id, amount, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, 'completed')");
    $stmt->execute([$_SESSION['user_id'], $plan['price'], 'SSLCommerz', $transaction_id]);

    $subManager = new SubscriptionManager($pdo);
    $subManager->activatePlan($_SESSION['user_id'], $plan_id);
    json_response(['success' => true, 'message' => 'Payment successful!']);
}
?>
