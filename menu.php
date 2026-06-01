<?php
require_once 'includes/header.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Menu</h5>
        <div class="list-group list-group-flush">
            <a href="profile.php" class="list-group-item list-group-item-action py-3">
                <i class="fas fa-user me-3 text-primary"></i> Profile
            </a>
            <a href="wallet.php" class="list-group-item list-group-item-action py-3">
                <i class="fas fa-wallet me-3 text-success"></i> Wallet
            </a>
            <a href="deposit.php" class="list-group-item list-group-item-action py-3">
                <i class="fas fa-plus-circle me-3 text-info"></i> Deposit
            </a>
            <a href="withdraw.php" class="list-group-item list-group-item-action py-3">
                <i class="fas fa-minus-circle me-3 text-danger"></i> Withdraw
            </a>
            <a href="referral.php" class="list-group-item list-group-item-action py-3">
                <i class="fas fa-users me-3 text-warning"></i> Referral
            </a>
            <a href="support.php" class="list-group-item list-group-item-action py-3">
                <i class="fas fa-headset me-3 text-secondary"></i> Support
            </a>
            <a href="logout.php" class="list-group-item list-group-item-action py-3 text-danger">
                <i class="fas fa-sign-out-alt me-3"></i> Logout
            </a>
        </div>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
