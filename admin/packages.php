<?php
require_once 'header.php';

if (isset($_POST['add_package'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $commission = $_POST['commission'];
    $comm_type = $_POST['commission_type'];
    $comm1 = $_POST['comm_level1'];
    $comm2 = $_POST['comm_level2'];
    $comm3 = $_POST['comm_level3'];
    $validity = $_POST['validity'];
    $details = $_POST['details'];

    $stmt = $pdo->prepare("INSERT INTO packages (name, type, price, commission, commission_type, comm_level1, comm_level2, comm_level3, validity, details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $type, $price, $commission, $comm_type, $comm1, $comm2, $comm3, $validity, $details]);
    echo "<div class='alert alert-success'>Package added!</div>";
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: packages.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM packages ORDER BY type, price ASC");
$packages = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Add New Package</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. 1GB Internet">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="internet">Internet</option>
                            <option value="sms">SMS</option>
                            <option value="talktime">Talktime</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Base Price (BDT)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Extra Commission/Charge (BDT)</label>
                        <input type="number" step="0.01" name="commission" class="form-control" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Commission Type</label>
                        <select name="commission_type" class="form-select">
                            <option value="fixed">Fixed Amount</option>
                            <option value="percent">Percentage (%)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label class="form-label small">Lvl 1 Comm</label>
                            <input type="number" step="0.01" name="comm_level1" class="form-control" value="0">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label small">Lvl 2 Comm</label>
                            <input type="number" step="0.01" name="comm_level2" class="form-control" value="0">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label small">Lvl 3 Comm</label>
                            <input type="number" step="0.01" name="comm_level3" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Validity</label>
                        <input type="text" name="validity" class="form-control" required placeholder="e.g. 7 Days">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea name="details" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" name="add_package" class="btn btn-primary w-100">Add Package</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Existing Packages</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Validity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packages as $pkg): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo ucfirst($pkg['type']); ?></span></td>
                                <td><?php echo htmlspecialchars($pkg['name']); ?></td>
                                <td>৳<?php echo number_format($pkg['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($pkg['validity']); ?></td>
                                <td>
                                    <a href="packages.php?delete=<?php echo $pkg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this package?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
