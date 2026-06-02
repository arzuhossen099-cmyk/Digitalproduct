<div class="bottom-menu bg-white border-top fixed-bottom">
    <div class="container">
        <div class="row text-center py-2">
            <div class="col">
                <a href="index.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-home d-block mb-1"></i>
                    <small>Home</small>
                </a>
            </div>
            <div class="col">
                <a href="earn.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'earn.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-dollar-sign d-block mb-1"></i>
                    <small>Earn</small>
                </a>
            </div>
            <div class="col">
                <a href="leaderboard.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'leaderboard.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-trophy d-block mb-1"></i>
                    <small>Trophy</small>
                </a>
            </div>
            <div class="col">
                <a href="transactions.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-history d-block mb-1"></i>
                    <small>History</small>
                </a>
            </div>
            <div class="col">
                <a href="menu.php" class="text-decoration-none <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'text-primary' : 'text-muted'; ?>">
                    <i class="fas fa-th-large d-block mb-1"></i>
                    <small>Menu</small>
                </a>
            </div>
        </div>
    </div>
</div>
