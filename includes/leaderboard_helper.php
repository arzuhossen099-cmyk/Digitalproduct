<?php
function updateLeaderboard($pdo, $user_id, $amount) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("INSERT INTO daily_leaderboard (user_id, total_earned, date)
                           VALUES (?, ?, ?)
                           ON DUPLICATE KEY UPDATE total_earned = total_earned + VALUES(total_earned)");
    $stmt->execute([$user_id, $amount, $today]);
}
?>
