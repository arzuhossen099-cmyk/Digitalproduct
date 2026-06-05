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

    if ($file_type === 'zip') {
        $error = "ZIP processing logic initialized. Placeholder for ZIP extraction and bulk CSV import.";
        // Logic to extract ZIP and loop through CSV files would go here
    } elseif ($file_type !== 'csv') {
        $error = "Unsupported file format. Please upload CSV or ZIP.";
    } else {
        $handle = fopen($file['tmp_name'], 'r');
        $headers = fgetcsv($handle);

        // Column mapping logic (Simplified for this step)
        // Expected headers: first_name, last_name, email, company, job_title, industry, country, phone, linkedin

        $row_count = 0;
        $import_count = 0;
        $duplicate_count = 0;

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($headers) !== count($row)) {
                $error = "Malformed CSV row detected. Skipping row.";
                continue;
            }
            $data = array_combine($headers, $row);

            // Duplicate detection by email
            $stmt = $pdo->prepare("SELECT id FROM leads WHERE email = ?");
            $stmt->execute([$data['email'] ?? '']);
            if ($stmt->fetch()) {
                $duplicate_count++;
                continue;
            }

            $stmt = $pdo->prepare("INSERT INTO leads (first_name, last_name, full_name, email, company_name, job_title, industry, country, phone, linkedin_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $full_name = ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '');
            $stmt->execute([
                $data['first_name'] ?? null,
                $data['last_name'] ?? null,
                $full_name,
                $data['email'] ?? null,
                $data['company'] ?? null,
                $data['job_title'] ?? null,
                $data['industry'] ?? null,
                $data['country'] ?? null,
                $data['phone'] ?? null,
                $data['linkedin'] ?? null
            ]);
            $import_count++;
            $row_count++;
        }
        fclose($handle);
        $message = "Import completed! Imported: $import_count, Duplicates skipped: $duplicate_count";
        lp_log("Leads imported: $import_count from " . $file['name']);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Import Leads</h2>
    <a href="leads.php" class="btn btn-outline-info"><i class="fas fa-arrow-left me-2"></i> Back to Database</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="stat-card">
            <h5 class="fw-bold mb-4">Upload CSV File</h5>
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="mb-3">
                    <label class="form-label">Choose File (CSV or ZIP)</label>
                    <input type="file" name="lead_file" class="form-control" accept=".csv,.zip" required>
                    <div class="form-text text-muted">Headers should include: first_name, last_name, email, company, job_title, industry, country, phone, linkedin</div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i> Start Import</button>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <h5 class="fw-bold mb-4">Import Guidelines</h5>
            <ul class="text-muted">
                <li>Ensure the file is in UTF-8 CSV format.</li>
                <li>The first row must contain column headers.</li>
                <li>Duplicate detection is performed using the <strong>Email</strong> field.</li>
                <li>Large files may take a few minutes to process.</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
