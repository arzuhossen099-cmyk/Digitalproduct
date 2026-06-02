<?php
require_once 'header.php';

if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $pdo->prepare("UPDATE package_orders SET status = 'approved' WHERE id = ? AND status = 'pending'");
    if ($stmt->execute()) {
        $stmt = $pdo->prepare("SELECT user_id, amount FROM package_orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if ($order) {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$order['user_id'], "Your package order has been approved and delivered!"]);
        }
        echo "<div class='alert alert-success'>Order approved!</div>";
    }
}

if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $stmt = $pdo->prepare("SELECT * FROM package_orders WHERE id = ? AND status = 'pending'");
    $stmt->execute([$id]);
    $order = $stmt->fetch();

    if ($order) {
        $pdo->beginTransaction();
        // Update status
        $stmt = $pdo->prepare("UPDATE package_orders SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);

        // Refund balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$order['amount'], $order['user_id']]);

        // Record refund transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, ?)");
        $stmt->execute([$order['user_id'], $order['amount'], "Package order rejected - balance refunded"]);

        // Notify user
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$order['user_id'], "Your package order was rejected. ৳" . $order['amount'] . " has been refunded to your wallet."]);

        $pdo->commit();
        echo "<div class='alert alert-danger'>Order rejected and balance refunded!</div>";
    }
}

$stmt = $pdo->query("SELECT po.*, u.username, p.name as package_name FROM package_orders po JOIN users u ON po.user_id = u.id JOIN packages p ON po.package_id = p.id ORDER BY po.created_at DESC");
$orders = $stmt->fetchAll();
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Manage Package Orders</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Package</th>
                        <th>Operator</th>
                        <th>Number</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="8" class="text-center">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><small><?php echo date('d/m/y H:i', strtotime($o['created_at'])); ?></small></td>
                            <td><?php echo htmlspecialchars($o['username']); ?></td>
                            <td><?php echo htmlspecialchars($o['package_name']); ?></td>
                            <td><?php echo htmlspecialchars($o['operator']); ?></td>
                            <td><code><?php echo htmlspecialchars($o['phone_number']); ?></code></td>
                            <td>৳<?php echo number_format($o['amount'], 2); ?></td>
                            <td>
                                <span class="badge bg-<?php
                                    echo $o['status'] == 'pending' ? 'warning' : ($o['status'] == 'approved' ? 'success' : 'danger');
                                ?>"><?php echo ucfirst($o['status']); ?></span>
                            </td>
                            <td>
                                <?php if ($o['status'] == 'pending'): ?>
                                    <a href="package_orders.php?approve=<?php echo $o['id']; ?>" class="btn btn-sm btn-success">Approve</a>
                                    <a href="package_orders.php?reject=<?php echo $o['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this order?')">Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
