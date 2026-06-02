<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$user_id = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? '';

require_once __DIR__ . '/../includes/aviator_helper.php';
$aviator_settings = getAviatorSettings($pdo);

// Helper to get user balance
function getBalance($pdo, $uid) {
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$uid]);
    return $stmt->fetchColumn();
}

switch ($action) {
    case 'bet':
        $amount = floatval($_POST['amount']);
        $balance = getBalance($pdo, $user_id);

        if (!isAviatorActive($pdo)) {
            echo json_encode(['status' => 'error', 'message' => 'Game is inactive']);
        } elseif ($amount < $aviator_settings['min_bet'] || $amount > $aviator_settings['max_bet']) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid bet amount']);
        } elseif ($amount > $balance) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient balance']);
        } else {
            $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")->execute([$amount, $user_id]);
            echo json_encode(['status' => 'success', 'new_balance' => $balance - $amount, 'crash_point' => generateCrashPoint($aviator_settings)]);
        }
        break;

    case 'cashout':
        $multiplier = floatval($_POST['multiplier']);
        $crash_point = floatval($_POST['crash_point']);
        $bet_amount = floatval($_POST['bet_amount']);
        $win_amount = round($bet_amount * $multiplier, 2);

        if ($multiplier >= $crash_point) {
            echo json_encode(['status' => 'error', 'message' => 'Game already crashed']);
            exit;
        }

        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")->execute([$win_amount, $user_id]);
            $new_balance = getBalance($pdo, $user_id);
            $pdo->prepare("INSERT INTO aviator_history (user_id, bet_amount, multiplier, win_amount, crash_point, result) VALUES (?, ?, ?, ?, ?, 'win')")
                ->execute([$user_id, $bet_amount, $multiplier, $win_amount, $crash_point]);

            $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'game', ?, ?)")
                ->execute([$user_id, $win_amount, "Aviator Win @ {$multiplier}x"]);

            $pdo->commit();
            echo json_encode(['status' => 'success', 'win_amount' => $win_amount, 'new_balance' => number_format($new_balance, 2)]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
        break;

    case 'crash':
        $bet_amount = floatval($_POST['bet_amount']);
        $crash_point = floatval($_POST['crash_point']);

        $pdo->prepare("INSERT INTO aviator_history (user_id, bet_amount, multiplier, win_amount, crash_point, result) VALUES (?, ?, ?, ?, ?, 'loss')")
            ->execute([$user_id, $bet_amount, 0, 0, $crash_point]);

        echo json_encode(['status' => 'success']);
        break;

    case 'history':
        $stmt = $pdo->prepare("SELECT crash_point FROM aviator_history ORDER BY created_at DESC LIMIT 10");
        $stmt->execute();
        echo json_encode($stmt->fetchAll());
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
