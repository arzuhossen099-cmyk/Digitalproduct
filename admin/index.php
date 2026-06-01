<?php
require_once 'header.php';
?>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <h2 class="mb-0"><?php echo $total_users; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Deposits</h5>
                <h2 class="mb-0">৳<?php echo number_format($total_deposits, 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Pending Deposits</h5>
                <h2 class="mb-0"><?php echo $pending_deposits_count; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Pending Withdraws</h5>
                <h2 class="mb-0"><?php echo $pending_withdrawals_count; ?></h2>
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
