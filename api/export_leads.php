<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$lead_ids = $_POST['lead_ids'] ?? ''; // Comma separated IDs
$format = $_POST['format'] ?? 'csv';

if (empty($lead_ids)) {
    die("No leads selected for export.");
}

$ids_array = explode(',', $lead_ids);
$placeholders = implode(',', array_fill(0, count($ids_array), '?'));

// Check if these leads are revealed by the user
$stmt = $pdo->prepare("SELECT lead_id FROM lead_reveals WHERE user_id = ? AND lead_id IN ($placeholders)");
$stmt->execute(array_merge([$_SESSION['user_id']], $ids_array));
$revealed_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($revealed_ids)) {
    die("You can only export leads that you have revealed.");
}

$revealed_placeholders = implode(',', array_fill(0, count($revealed_ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM leads WHERE id IN ($revealed_placeholders)");
$stmt->execute($revealed_ids);
$leads = $stmt->fetchAll();

// Log download
$stmt = $pdo->prepare("INSERT INTO downloads (user_id, lead_ids, format) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], implode(',', $revealed_ids), $format]);

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="leads_export_' . date('Ymd') . '.csv"');

    $output = fopen('php://output', 'w');
    if (!empty($leads)) {
        fputcsv($output, array_keys($leads[0]));
        foreach ($leads as $lead) {
            fputcsv($output, $lead);
        }
    }
    fclose($output);
    exit();
} elseif ($format === 'json') {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="leads_export_' . date('Ymd') . '.json"');
    echo json_encode($leads, JSON_PRETTY_PRINT);
    exit();
}
?>
