<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['package_id'])) {
    $package_id = intval($_POST['package_id']);

    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->execute([$package_id]);
    $package = $stmt->fetch();

    if (!$package) {
        $error = "Invalid package selected.";
    } elseif ($user['balance'] < $package['price']) {
        $error = "Insufficient balance.";
    } else {
        try {
            $pdo->beginTransaction();

            // Deduct balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$package['price'], $_SESSION['user_id']]);

            // Record transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'package_purchase', ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $package['price'], "Purchased package: " . $package['name']]);

            // Send notification
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], "You have successfully purchased " . $package['name']]);

            $pdo->commit();
            $success = "Package purchased successfully!";
            $user['balance'] -= $package['price'];
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Purchase failed.";
        }
    }
}

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
                    <form method="POST">
                        <input type="hidden" name="package_id" value="<?php echo $pkg['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('Buy this package?')">Buy Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
        endforeach;
    endif; ?>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
