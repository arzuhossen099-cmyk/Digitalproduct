<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$industry = $_GET['industry'] ?? '';
$country = $_GET['country'] ?? '';
$job_title = $_GET['job_title'] ?? '';
$company = $_GET['company'] ?? '';
$seniority = $_GET['seniority'] ?? '';
$tech = $_GET['technology_stack'] ?? '';
$email_status = $_GET['verification_status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
$params = [];

if ($industry) { $where .= " AND industry LIKE ?"; $params[] = "%$industry%"; }
if ($country) { $where .= " AND country LIKE ?"; $params[] = "%$country%"; }
if ($job_title) { $where .= " AND job_title LIKE ?"; $params[] = "%$job_title%"; }
if ($company) { $where .= " AND company_name LIKE ?"; $params[] = "%$company%"; }
if ($seniority) { $where .= " AND seniority = ?"; $params[] = $seniority; }
if ($tech) { $where .= " AND technology_stack LIKE ?"; $params[] = "%$tech%"; }
if ($email_status) { $where .= " AND verification_status = ?"; $params[] = $email_status; }

// Reveal tracking: Check which leads are already revealed by the user
$stmt = $pdo->prepare("SELECT lead_id FROM lead_reveals WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$revealed_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->prepare("SELECT id, first_name, last_name, full_name, job_title, company_name, industry, country, verification_status, email, phone FROM leads $where ORDER BY last_updated DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$leads = $stmt->fetchAll();

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM leads $where");
$total_stmt->execute($params);
$total_leads = $total_stmt->fetchColumn();

// Add 'revealed' flag to leads
foreach ($leads as &$lead) {
    $lead['revealed'] = in_array($lead['id'], $revealed_ids);
    // Hide sensitive data if not revealed
    if (!$lead['revealed']) {
        $lead['email_masked'] = '••••••••@' . (explode('@', $lead['email'] ?? 'company.com')[1] ?? '••••.com');
        unset($lead['email']);
        unset($lead['phone']);
    }
}

json_response([
    'leads' => $leads,
    'total' => $total_leads,
    'pages' => ceil($total_leads / $limit),
    'current_page' => $page
]);
?>
