<?php
require_once 'header.php';

$logs = $pdo->query("SELECT rl.*, u.username FROM reward_logs rl JOIN users u ON rl.user_id = u.id ORDER BY rl.created_at DESC LIMIT 100")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Reward Logs</h4>
    <button class="btn btn-sm btn-outline-secondary" onclick="exportCSV()">Export to CSV</button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="logsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logs)): ?>
                        <tr><td colspan="4" class="text-center">No reward logs found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $l): ?>
                        <tr>
                            <td><small><?php echo date('d M Y, h:i A', strtotime($l['created_at'])); ?></small></td>
                            <td><strong><?php echo htmlspecialchars($l['username']); ?></strong></td>
                            <td class="text-success">+ ৳<?php echo number_format($l['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($l['description']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportCSV() {
    let csv = [];
    let rows = document.querySelectorAll("#logsTable tr");

    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length; j++)
            row.push('"' + cols[j].innerText + '"');
        csv.push(row.join(","));
    }

    let csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "reward_logs.csv");
    document.body.appendChild(link);
    link.click();
}
</script>

<?php require_once 'footer.php'; ?>
