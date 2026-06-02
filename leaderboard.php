<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT lb.*, u.username FROM daily_leaderboard lb JOIN users u ON lb.user_id = u.id WHERE lb.date = ? ORDER BY lb.total_earned DESC LIMIT 10");
$stmt->execute([$today]);
$top_earners = $stmt->fetchAll();
?>

<div class="card shadow-sm mb-4 bg-dark text-white border-0">
    <div class="card-body text-center">
        <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
        <h4 class="card-title">Daily Leaderboard</h4>
        <p class="mb-0">Top 10 earners of the day get special rewards!</p>
        <small class="text-muted">Last updated: <?php echo date('h:i A'); ?></small>
    </div>
</div>

<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Rank</th>
                        <th>User</th>
                        <th class="text-end pe-4">Earned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($top_earners)): ?>
                        <tr><td colspan="3" class="text-center py-5 text-muted">No data for today yet</td></tr>
                    <?php else:
                        foreach ($top_earners as $index => $lb):
                    ?>
                        <tr>
                            <td class="ps-4">
                                <?php if ($index == 0): ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-crown"></i> 1</span>
                                <?php elseif ($index == 1): ?>
                                    <span class="badge bg-secondary">2</span>
                                <?php elseif ($index == 2): ?>
                                    <span class="badge bg-danger">3</span>
                                <?php else: ?>
                                    <span class="ms-1"><?php echo $index + 1; ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($lb['username']); ?></strong></td>
                            <td class="text-end pe-4 text-success fw-bold">৳<?php echo number_format($lb['total_earned'], 2); ?></td>
                        </tr>
                    <?php
                        endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="alert alert-info mt-4 small">
    <i class="fas fa-info-circle me-1"></i> Rewards are distributed automatically at midnight based on final rankings.
</div>

<?php require_once 'includes/bottom_menu.php'; require_once 'includes/footer.php'; ?>
