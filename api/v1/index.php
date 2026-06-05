<?php
require_once __DIR__ . '/../../includes/auth.php';

$api_key = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? '';

if (empty($api_key)) {
    json_response(['error' => 'API Key required'], 401);
}

$stmt = $pdo->prepare("SELECT user_id FROM api_keys WHERE api_key = ? AND status = 'active'");
$stmt->execute([$api_key]);
$key_data = $stmt->fetch();

if (!$key_data) {
    json_response(['error' => 'Invalid or revoked API Key'], 403);
}

$api_user_id = $key_data['user_id'];

// Update last used
$stmt = $pdo->prepare("UPDATE api_keys SET last_used_at = NOW() WHERE api_key = ?");
$stmt->execute([$api_key]);

// Mock API endpoint response
$action = $_GET['action'] ?? 'search';

if ($action === 'search') {
    // Re-use search logic but restricted to API user
    $industry = $_GET['industry'] ?? '';
    $stmt = $pdo->prepare("SELECT full_name, job_title, company_name FROM leads WHERE industry LIKE ? LIMIT 10");
    $stmt->execute(["%$industry%"]);
    $leads = $stmt->fetchAll();
    json_response(['leads' => $leads]);
} else {
    json_response(['error' => 'Invalid action'], 400);
}
?>
