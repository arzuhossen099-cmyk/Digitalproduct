<?php
$title = "Import Leads";
$active_page = "leads";
require_once __DIR__ . '/includes/header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['lead_file'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF validation failed.");
    }

    $file = $_FILES['lead_file'];
    $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file_type === 'csv') {
        $handle = fopen($file['tmp_name'], 'r');
        $headers = fgetcsv($handle);

        $import_count = 0;
        $duplicate_count = 0;

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($headers) !== count($row)) continue;
            $data = array_combine($headers, $row);

            // Basic mapping
            $email = $data['email'] ?? null;
            if (!$email) continue;

            $stmt = $pdo->prepare("SELECT id FROM leads WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $duplicate_count++;
                continue;
            }

            $stmt = $pdo->prepare("INSERT INTO leads (first_name, last_name, full_name, email, company_name, job_title, industry, country, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $full_name = ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '');
            $stmt->execute([
                $data['first_name'] ?? null,
                $data['last_name'] ?? null,
                $full_name,
                $email,
                $data['company'] ?? null,
                $data['job_title'] ?? null,
                $data['industry'] ?? null,
                $data['country'] ?? null,
                $data['phone'] ?? null
            ]);
            $import_count++;
        }
        fclose($handle);
        $message = "Imported $import_count leads. Skipped $duplicate_count duplicates.";
        audit_log("Leads Imported", "Count: $import_count");
    } else {
        $error = "Only CSV files are currently supported for direct upload.";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-accent">Import Leads</h2>
    <a href="leads.php" class="btn btn-lp-outline btn-sm">Back to Database</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="lp-card">
            <?php if ($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            <?php if ($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="mb-4">
                    <label class="form-label text-muted small">CSV File</label>
                    <input type="file" name="lead_file" class="form-control bg-dark border-secondary text-primary" accept=".csv" required>
                </div>
                <button type="submit" class="btn btn-lp-primary w-100">Upload & Process</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
