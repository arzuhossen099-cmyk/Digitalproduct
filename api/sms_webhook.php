<?php
require_once __DIR__ . '/../config/db.php';

// Security check: Match token sent by SMS Forwarder app
// Expected header: X-Webhook-Token or as a POST parameter
$headers = getallheaders();
$sent_token = $headers['X-Webhook-Token'] ?? ($_POST['token'] ?? '');

$stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'sms_webhook_token'");
$correct_token = $stmt->fetchColumn();

if ($sent_token !== $correct_token) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$sender = $_POST['from'] ?? ''; // Sender number of the SMS
$body = $_POST['message'] ?? ''; // Content of the SMS

if (empty($body)) {
    die(json_encode(['status' => 'error', 'message' => 'Empty message']));
}

$parsed_method = '';
$parsed_trx_id = '';
$parsed_amount = 0.00;
$parsed_sender = '';

// bKash Regex Pattern
// Example: You have received Tk 500.00 from 017xxxxxxxx. Ref: . Fee Tk 0.00. Balance Tk 10500.00. TrxID 8N7X6Y5Z4W at 10/10/2023 10:10
if (stripos($body, 'bkash') !== false || stripos($body, 'received') !== false) {
    $parsed_method = 'bkash';
    // Extract Amount
    if (preg_match('/(?:Tk|Amount)\s*([\d,.]+)/i', $body, $matches)) {
        $parsed_amount = floatval(str_replace(',', '', $matches[1]));
    }
    // Extract TrxID
    if (preg_match('/TrxID\s*([A-Z0-9]+)/i', $body, $matches)) {
        $parsed_trx_id = strtoupper($matches[1]);
    }
    // Extract Sender Number
    if (preg_match('/from\s*(01[3-9]\d{8})/i', $body, $matches)) {
        $parsed_sender = $matches[1];
    }
}

// Nagad Regex Pattern
// Example: Cash In Received Amount: Tk 500.00 From: 017xxxxxxxx Time: 10:10AM Date: 10/10/2023 TxnID: 7M6N5B4V3C2X1
if (stripos($body, 'nagad') !== false || stripos($body, 'TxnID') !== false) {
    $parsed_method = 'nagad';
    // Extract Amount
    if (preg_match('/Amount:\s*Tk\s*([\d,.]+)/i', $body, $matches)) {
        $parsed_amount = floatval(str_replace(',', '', $matches[1]));
    }
    // Extract TrxID
    if (preg_match('/TxnID:\s*([A-Z0-9]+)/i', $body, $matches)) {
        $parsed_trx_id = strtoupper($matches[1]);
    }
    // Extract Sender Number
    if (preg_match('/From:\s*(01[3-9]\d{8})/i', $body, $matches)) {
        $parsed_sender = $matches[1];
    }
}

if (!empty($parsed_trx_id)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO sms_logs (sender, body, parsed_method, parsed_trx_id, parsed_amount, parsed_sender) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$sender, $body, $parsed_method, $parsed_trx_id, $parsed_amount, $parsed_sender]);
        echo json_encode(['status' => 'success', 'trx_id' => $parsed_trx_id]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['status' => 'duplicate', 'message' => 'TrxID already exists']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    }
} else {
    echo json_encode(['status' => 'ignored', 'message' => 'Could not parse TrxID']);
}
?>
