<?php
require_once __DIR__ . '/../config/db.php';

function distributeLeaderboardRewards($pdo) {
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    // Check if already distributed
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM daily_leaderboard WHERE date = ? AND reward_given = 1");
    $stmt->execute([$yesterday]);
    if ($stmt->fetchColumn() > 0) return "Already distributed.";

    // Get top 10
    $stmt = $pdo->prepare("SELECT * FROM daily_leaderboard WHERE date = ? ORDER BY total_earned DESC LIMIT 10");
    $stmt->execute([$yesterday]);
    $top_earners = $stmt->fetchAll();

    $rewards = [50, 40, 30, 20, 10, 5, 5, 5, 5, 5]; // Reward amounts for ranks 1-10

    foreach ($top_earners as $index => $lb) {
        $reward = $rewards[$index] ?? 0;
        if ($reward > 0) {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")->execute([$reward, $lb['user_id']]);
            $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'reward', ?, ?)")
                ->execute([$lb['user_id'], $reward, "Leaderboard Reward - Rank " . ($index + 1)]);
            $pdo->prepare("UPDATE daily_leaderboard SET reward_given = 1 WHERE id = ?")->execute([$lb['id']]);
            $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)")
                ->execute([$lb['user_id'], "Congratulations! You ranked #" . ($index + 1) . " in yesterday's leaderboard and earned ৳" . $reward]);
            $pdo->commit();
        }
    }
    return "Rewards distributed for $yesterday.";
}
?>
