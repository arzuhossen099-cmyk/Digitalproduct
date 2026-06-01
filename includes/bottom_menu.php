<div class="bottom-menu bg-white border-top fixed-bottom">
    <div class="container">
        <div class="row text-center py-2">
            <div class="col">
                <a href="index.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-home d-block"></i>
                    <small>Home</small>
                </a>
            </div>
            <div class="col">
                <a href="recharge.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'recharge.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-mobile-alt d-block"></i>
                    <small>Recharge</small>
                </a>
            </div>
            <div class="col">
                <a href="rewards.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'rewards.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-gift d-block"></i>
                    <small>Rewards</small>
                </a>
            </div>
            <div class="col">
                <a href="transactions.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-history d-block"></i>
                    <small>History</small>
                </a>
            </div>
            <div class="col">
                <a href="menu.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-bars d-block"></i>
                    <small>Menu</small>
                </a>
            </div>
        </div>
    </div>
</div>
