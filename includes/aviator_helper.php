<?php
function getAviatorSettings($pdo) {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM aviator_settings");
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function isAviatorActive($pdo) {
    $stmt = $pdo->prepare("SELECT setting_value FROM aviator_settings WHERE setting_key = 'game_status'");
    $stmt->execute();
    return $stmt->fetchColumn() === 'active';
}

function generateCrashPoint($settings) {
    $rand = mt_rand(1, 100);
    $point = 0;

    // House Edge check (percentage chance to crash at 1.00x)
    $house_edge = intval($settings['house_edge'] ?? 5);
    if (mt_rand(1, 100) <= $house_edge) return 1.00;

    if ($rand <= 50) $point = 1.1 + (mt_rand(0, 190) / 100); // 1.10 - 3.00
    elseif ($rand <= 80) $point = 3.01 + (mt_rand(0, 299) / 100); // 3.01 - 6.00
    else $point = 6.01 + (mt_rand(0, 899) / 100); // 6.01 - 15.00

    return round($point, 2);
}

function getAviatorStats($pdo) {
    $stats = [];
    $stats['total_bets'] = $pdo->query("SELECT SUM(bet_amount) FROM aviator_history")->fetchColumn() ?: 0;
    $stats['total_wins'] = $pdo->query("SELECT SUM(win_amount) FROM aviator_history WHERE result = 'win'")->fetchColumn() ?: 0;
    $stats['today_bets'] = $pdo->query("SELECT SUM(bet_amount) FROM aviator_history WHERE DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;
    $stats['today_wins'] = $pdo->query("SELECT SUM(win_amount) FROM aviator_history WHERE result = 'win' AND DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;
    return $stats;
}
?>
