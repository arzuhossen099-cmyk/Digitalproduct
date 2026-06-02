<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$tab = $_GET['tab'] ?? 'tasks';
?>

<ul class="nav nav-pills nav-justified mb-4 shadow-sm bg-white rounded p-1">
    <li class="nav-item"><a class="nav-link <?php echo $tab == 'tasks' ? 'active' : ''; ?>" href="earn.php?tab=tasks">Social Tasks</a></li>
    <li class="nav-item"><a class="nav-link <?php echo $tab == 'ads' ? 'active' : ''; ?>" href="earn.php?tab=ads">Ads Earning</a></li>
    <li class="nav-item"><a class="nav-link <?php echo $tab == 'history' ? 'active' : ''; ?>" href="earn.php?tab=history">My Earning</a></li>
</ul>

<?php if ($tab == 'tasks'): ?>
    <div class="row g-3">
        <?php
        $tasks = $pdo->query("SELECT * FROM tasks WHERE status = 'active' ORDER BY created_at DESC")->fetchAll();
        foreach ($tasks as $t):
            // Check if already submitted
            $stmt = $pdo->prepare("SELECT status FROM task_submissions WHERE user_id = ? AND task_id = ?");
            $stmt->execute([$_SESSION['user_id'], $t['id']]);
            $sub_status = $stmt->fetchColumn();
        ?>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1"><?php echo htmlspecialchars($t['title']); ?></h6>
                        <span class="text-success fw-bold">৳<?php echo number_format($t['reward_amount'], 2); ?></span>
                    </div>
                    <?php if ($sub_status): ?>
                        <span class="badge bg-<?php echo $sub_status=='pending'?'warning':($sub_status=='approved'?'success':'danger'); ?>"><?php echo ucfirst($sub_status); ?></span>
                    <?php else: ?>
                        <a href="task_details.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-primary">Start Task</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php elseif ($tab == 'ads'): ?>
    <div class="row g-3">
        <?php
        $ads = $pdo->query("SELECT * FROM ads_tasks WHERE status = 'active' ORDER BY created_at DESC")->fetchAll();
        foreach ($ads as $a):
        ?>
        <div class="col-6">
            <a href="watch_ad.php?id=<?php echo $a['id']; ?>" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center p-3">
                    <i class="fas fa-play-circle fa-2x text-primary mb-2"></i>
                    <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($a['title']); ?></h6>
                    <small class="text-success">৳<?php echo $a['reward_amount']; ?></small>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

<?php elseif ($tab == 'history'): ?>
    <div class="list-group shadow-sm">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'reward' ORDER BY created_at DESC LIMIT 20");
        $stmt->execute([$_SESSION['user_id']]);
        $txs = $stmt->fetchAll();
        foreach ($txs as $tx):
        ?>
        <div class="list-group-item">
            <div class="d-flex justify-content-between">
                <span><?php echo htmlspecialchars($tx['description']); ?></span>
                <span class="text-success">+ ৳<?php echo $tx['amount']; ?></span>
            </div>
            <small class="text-muted"><?php echo date('d M, h:i A', strtotime($tx['created_at'])); ?></small>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/bottom_menu.php'; require_once 'includes/footer.php'; ?>
