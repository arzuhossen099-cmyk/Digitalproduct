<?php
require_once 'header.php';

if (isset($_POST['distribute_lb'])) {
    require_once '../includes/reward_distributor.php';
    $msg = distributeLeaderboardRewards($pdo);
    echo "<div class='alert alert-info'>$msg</div>";
}

$total_withdrawals = $pdo->query("SELECT SUM(amount) FROM withdrawals WHERE status = 'approved'")->fetchColumn() ?? 0;
$total_earning_tasks = $pdo->query("SELECT SUM(amount) FROM reward_logs")->fetchColumn() ?? 0;
$active_users_today = $pdo->query("SELECT COUNT(DISTINCT id) FROM users WHERE status = 'active'")->fetchColumn(); // Simplified
?>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small">Total Users</h6>
                <h3 class="mb-0"><?php echo $total_users; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small">Net Deposits</h6>
                <h3 class="mb-0">৳<?php echo number_format($total_deposits, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-info border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small">User Earnings</h6>
                <h3 class="mb-0">৳<?php echo number_format($total_earning_tasks, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small">Total Payouts</h6>
                <h3 class="mb-0">৳<?php echo number_format($total_withdrawals, 2); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Pending Requests Overview</h5>
            </div>
            <div class="card-body">
                <div class="row text-center g-4">
                    <div class="col-4">
                        <a href="deposits.php" class="text-decoration-none">
                            <h2 class="text-warning"><?php echo $pending_deposits_count; ?></h2>
                            <small class="text-muted">Deposits</small>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="withdrawals.php" class="text-decoration-none">
                            <h2 class="text-danger"><?php echo $pending_withdrawals_count; ?></h2>
                            <small class="text-muted">Withdrawals</small>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="task_submissions.php" class="text-decoration-none">
                            <h2 class="text-info"><?php echo $pending_tasks_count; ?></h2>
                            <small class="text-muted">Tasks</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">System Status</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>Active Users</span>
                        <span class="badge bg-success rounded-pill"><?php echo $active_users_today; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>Pending Orders</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $pending_orders_count; ?></span>
                    </li>
                </ul>
                <form method="POST" class="mt-3">
                    <button type="submit" name="distribute_lb" class="btn btn-sm btn-outline-warning w-100">Distribute Leaderboard Rewards</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <h4>Recent Activity</h4>
    <div class="table-responsive">
        <table class="table table-hover bg-white shadow-sm rounded">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent = $pdo->query("
                    (SELECT created_at, user_id, 'Deposit' as type, amount, status FROM deposits)
                    UNION
                    (SELECT created_at, user_id, 'Withdraw' as type, amount, status FROM withdrawals)
                    ORDER BY created_at DESC LIMIT 10
                ")->fetchAll();

                foreach ($recent as $item):
                    $u = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                    $u->execute([$item['user_id']]);
                    $uname = $u->fetchColumn();
                ?>
                <tr>
                    <td><?php echo date('d M, h:i A', strtotime($item['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($uname); ?></td>
                    <td><?php echo $item['type']; ?></td>
                    <td>৳<?php echo number_format($item['amount'], 2); ?></td>
                    <td>
                        <span class="badge bg-<?php
                            echo $item['status'] == 'pending' ? 'warning' : ($item['status'] == 'approved' ? 'success' : 'danger');
                        ?>"><?php echo ucfirst($item['status']); ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'footer.php';
?>
