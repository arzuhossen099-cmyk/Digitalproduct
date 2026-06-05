<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Invalid request method'], 405);
}

$lead_id = (int)($_POST['lead_id'] ?? 0);
$type = $_POST['type'] ?? 'full'; // email, phone, full

if (!$lead_id) {
    json_response(['error' => 'Lead ID is required'], 400);
}

$user = get_logged_in_user();

// Check if already revealed
$check_stmt = $pdo->prepare("SELECT id FROM lead_reveals WHERE user_id = ? AND lead_id = ?");
$check_stmt->execute([$user['id'], $lead_id]);
if ($check_stmt->fetch()) {
    json_response(['error' => 'Lead already revealed'], 400);
}

// Get cost from settings
$cost_key = "reveal_credit_$type";
$cost = (int)get_setting($cost_key, 1);

if ($user['credits'] < $cost) {
    json_response(['error' => 'Insufficient credits'], 403);
}

try {
    $pdo->beginTransaction();

    // Deduct credits
    $deduct_stmt = $pdo->prepare("UPDATE users SET credits = credits - ? WHERE id = ?");
    $deduct_stmt->execute([$cost, $user['id']]);

    // Log reveal
    $reveal_stmt = $pdo->prepare("INSERT INTO lead_reveals (user_id, lead_id, credits_spent, revealed_fields) VALUES (?, ?, ?, ?)");
    $reveal_stmt->execute([$user['id'], $lead_id, $cost, json_encode([$type])]);

    // Fetch lead details
    $lead_stmt = $pdo->prepare("SELECT email, phone, mobile, linkedin_url FROM leads WHERE id = ?");
    $lead_stmt->execute([$lead_id]);
    $lead_data = $lead_stmt->fetch();

    $pdo->commit();

    json_response([
        'success' => true,
        'message' => 'Lead revealed successfully',
        'credits_remaining' => $user['credits'] - $cost,
        'lead' => $lead_data
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    lp_log("Reveal Error: " . $e->getMessage(), 'ERROR');
    json_response(['error' => 'An error occurred during reveal'], 500);
}
?>
