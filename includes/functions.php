<?php
/**
 * LeadPress Global Functions
 */

// XSS Protection
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// CSRF Token Generation
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token Validation
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

// JSON Response helper
function json_response($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit();
}

// Format Currency
function format_currency($amount) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'currency' LIMIT 1");
    $stmt->execute();
    $currency = $stmt->fetchColumn() ?: 'USD';

    if ($currency === 'BDT') {
        return '৳' . number_format($amount, 2);
    }
    return '$' . number_format($amount, 2);
}

// Get Setting
function get_setting($key, $default = null) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
    $stmt->execute([$key]);
    return $stmt->fetchColumn() ?: $default;
}

// Logger
function lp_log($message, $level = 'INFO') {
    $log_file = __DIR__ . '/../logs/app.log';
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] [$level] $message" . PHP_EOL, FILE_APPEND);
}

function audit_log($action, $details = null) {
    global $pdo;
    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $details, $ip]);
}
?>
