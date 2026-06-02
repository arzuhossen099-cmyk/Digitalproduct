<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

$type = $_GET['type'] ?? 'internet';
$stmt = $pdo->prepare("SELECT * FROM packages WHERE type = ? ORDER BY price ASC");
$stmt->execute([$type]);
$packages = $stmt->fetchAll();
?>

<ul class="nav nav-pills nav-justified mb-4 shadow-sm bg-white rounded p-1">
    <li class="nav-item">
        <a class="nav-link <?php echo $type == 'internet' ? 'active' : ''; ?>" href="packages.php?type=internet">Internet</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $type == 'sms' ? 'active' : ''; ?>" href="packages.php?type=sms">SMS</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $type == 'talktime' ? 'active' : ''; ?>" href="packages.php?type=talktime">Talktime</a>
    </li>
</ul>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="row g-3">
    <?php if (empty($packages)): ?>
        <div class="col-12 text-center text-muted py-5">No packages available in this category.</div>
    <?php else:
        foreach ($packages as $pkg):
    ?>
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($pkg['name']); ?></h5>
                    <span class="h5 mb-0 text-primary">৳<?php echo number_format($pkg['price'], 2); ?></span>
                </div>
                <p class="card-text text-muted mb-1"><?php echo htmlspecialchars($pkg['details']); ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="fas fa-clock me-1"></i> Validity: <?php echo $pkg['validity']; ?></small>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick='openPurchaseModal(<?php echo json_encode($pkg); ?>)'>Buy Now</button>
                </div>
            </div>
        </div>
    </div>
    <?php
        endforeach;
    endif; ?>
</div>

<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="package_confirm.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Purchase Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="package_id" id="modal_package_id">
                    <div class="mb-3">
                        <label class="form-label">Selected Package</label>
                        <input type="text" id="modal_package_name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="text" id="modal_package_price" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Operator</label>
                        <select name="operator" class="form-select" required>
                            <option value="">Select Operator</option>
                            <option value="Grameenphone">Grameenphone</option>
                            <option value="Robi">Robi</option>
                            <option value="Banglalink">Banglalink</option>
                            <option value="Airtel">Airtel</option>
                            <option value="Teletalk">Teletalk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="phone_number" class="form-control" placeholder="01xxxxxxxxx" required pattern="01[3-9][0-9]{8}">
                        <small class="text-muted">Enter a valid 11-digit mobile number.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPurchaseModal(pkg) {
    $('#modal_package_id').val(pkg.id);
    $('#modal_package_name').val(pkg.name);
    $('#modal_package_price').val('৳' + pkg.price);
    $('#purchaseModal').modal('show');
}
</script>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
