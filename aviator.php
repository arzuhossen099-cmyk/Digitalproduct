<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';

// Fetch Aviator Settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM aviator_settings");
$aviator_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

if (($aviator_settings['game_status'] ?? 'inactive') != 'active') {
    die("<div class='alert alert-danger mt-5'>Aviator game is currently under maintenance. Please check back later.</div>");
}
?>

<div id="aviator-game" class="game-container mt-2">
    <!-- Top Stats / History -->
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <div id="crash-history" class="d-flex gap-2 overflow-auto">
            <!-- Last 10 crash points will be loaded here -->
        </div>
        <div class="how-to-play text-muted small" style="cursor:pointer;" onclick="alert('Bet before takeoff. Cash out before the plane flies away!')">
            <i class="fas fa-info-circle"></i>
        </div>
    </div>

    <!-- Main Game Area -->
    <div class="game-board position-relative rounded-4 mb-3 overflow-hidden shadow-lg" style="height: 300px; background: radial-gradient(circle, #1a2a4a 0%, #0a1128 100%);">
        <!-- Multiplier -->
        <div id="multiplier-display" class="position-absolute w-100 text-center" style="top: 30%; z-index: 10;">
            <h1 class="display-1 fw-bold mb-0 text-white" id="main-multiplier">1.00x</h1>
        </div>

        <!-- Airplane Animation -->
        <div id="airplane-container" class="position-absolute" style="bottom: 20px; left: 10%; transition: all 0.05s linear;">
            <img src="https://i.ibb.co/L9gV6M7/plane.png" id="airplane" width="80" style="transform: rotate(-10deg);">
        </div>

        <!-- Crash Message -->
        <div id="crash-overlay" class="position-absolute w-100 h-100 d-none align-items-center justify-content-center" style="top:0; left:0; background: rgba(0,0,0,0.4); z-index: 20;">
            <div class="text-center">
                <h2 class="text-danger fw-bold display-4 shake">FELL DOWN!</h2>
                <p class="text-white h5" id="final-crash-point">0.00x</p>
            </div>
        </div>

        <!-- Canvas for the trail (optional refinement) -->
        <canvas id="trail-canvas" width="800" height="300" class="position-absolute top-0 start-0"></canvas>
    </div>

    <!-- Betting Controls -->
    <div class="betting-panel card border-0 p-3 shadow-sm bg-dark">
        <div class="row align-items-center g-2">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-lg border border-secondary rounded-3 overflow-hidden bg-dark">
                    <button class="btn btn-outline-secondary border-0" onclick="adjustBet(-10)">-</button>
                    <input type="number" id="bet-amount" class="form-control bg-dark text-white text-center border-0" value="<?php echo $aviator_settings['default_bet'] ?? 10; ?>" min="<?php echo $aviator_settings['min_bet'] ?? 10; ?>" max="<?php echo $aviator_settings['max_bet'] ?? 1000; ?>">
                    <button class="btn btn-outline-secondary border-0" onclick="adjustBet(10)">+</button>
                </div>
            </div>
            <div class="col-12 col-md-7">
                <button id="bet-btn" class="btn btn-success btn-lg w-100 fw-bold py-3 shadow" onclick="placeBet()">PLACE BET</button>
                <button id="cashout-btn" class="btn btn-warning btn-lg w-100 fw-bold py-3 shadow d-none" onclick="cashOut()">CASH OUT <span id="potential-win">৳0.00</span></button>
            </div>
        </div>

        <!-- Quick Bet Buttons -->
        <div class="d-flex justify-content-between mt-3 gap-1 overflow-auto pb-1">
            <?php foreach([50, 100, 200, 500, 1000] as $amount): ?>
                <button class="btn btn-sm btn-outline-secondary flex-fill" onclick="setBet(<?php echo $amount; ?>)"><?php echo $amount; ?></button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.game-container { max-width: 800px; margin: 0 auto; }
#main-multiplier { text-shadow: 0 0 20px rgba(255,255,255,0.3); }
.shake { animation: shake 0.5s; }
@keyframes shake {
  0% { transform: translate(1px, 1px) rotate(0deg); }
  10% { transform: translate(-1px, -2px) rotate(-1deg); }
  20% { transform: translate(-3px, 0px) rotate(1deg); }
  30% { transform: translate(3px, 2px) rotate(0deg); }
  40% { transform: translate(1px, -1px) rotate(1deg); }
  50% { transform: translate(-1px, 2px) rotate(-1deg); }
  60% { transform: translate(-3px, 1px) rotate(0deg); }
  70% { transform: translate(3px, 1px) rotate(-1deg); }
  80% { transform: translate(-1px, -1px) rotate(1deg); }
  90% { transform: translate(1px, 2px) rotate(0deg); }
  100% { transform: translate(1px, -2px) rotate(-1deg); }
}
.crash-point-badge { border-radius: 20px; padding: 2px 10px; font-size: 0.8rem; font-weight: bold; }
.crash-high { color: #00ff00; background: rgba(0,255,0,0.1); border: 1px solid #00ff00; }
.crash-mid { color: #ffff00; background: rgba(255,255,0,0.1); border: 1px solid #ffff00; }
.crash-low { color: #ff0000; background: rgba(255,0,0,0.1); border: 1px solid #ff0000; }
</style>

<script>
let isPlaying = false;
let currentBet = 0;
let currentMultiplier = 1.00;
let crashPoint = 0;
let gameInterval;
let planePos = { x: 10, y: 20 };
const minBet = <?php echo $aviator_settings['min_bet'] ?? 10; ?>;
const maxBet = <?php echo $aviator_settings['max_bet'] ?? 1000; ?>;

$(document).ready(function() {
    loadHistory();
    // Handle Spacebar
    $(document).keydown(function(e) {
        if (e.keyCode == 32) { // Space
            e.preventDefault();
            if (isPlaying) cashOut();
            else placeBet();
        }
    });
    // Handle Double Click
    $('#aviator-game').dblclick(function() {
        if (isPlaying) cashOut();
    });
});

function adjustBet(amount) {
    let val = parseInt($('#bet-amount').val()) + amount;
    if (val < minBet) val = minBet;
    if (val > maxBet) val = maxBet;
    $('#bet-amount').val(val);
}

function setBet(amount) {
    $('#bet-amount').val(amount);
}

function placeBet() {
    if (isPlaying) return;
    const amount = parseFloat($('#bet-amount').val());
    if (isNaN(amount) || amount < minBet || amount > maxBet) {
        alert('Invalid bet amount');
        return;
    }

    $.ajax({
        url: 'api/aviator.php',
        type: 'POST',
        data: { action: 'bet', amount: amount },
        dataType: 'json',
        success: function(res) {
            if (res.status == 'success') {
                currentBet = amount;
                crashPoint = res.crash_point; // Received securely from server
                startGame();
            } else {
                alert(res.message);
            }
        }
    });
}

function startGame() {
    isPlaying = true;
    currentMultiplier = 1.00;
    planePos = { x: 10, y: 20 };

    $('#bet-btn').addClass('d-none');
    $('#cashout-btn').removeClass('d-none');
    $('#crash-overlay').addClass('d-none');
    $('#airplane-container').css({ left: '10%', bottom: '20px' });

    gameInterval = setInterval(updateGame, 80);
}

function updateGame() {
    currentMultiplier += 0.02;
    currentMultiplier = parseFloat(currentMultiplier.toFixed(2));

    $('#main-multiplier').text(currentMultiplier.toFixed(2) + 'x');
    $('#potential-win').text('৳' + (currentBet * currentMultiplier).toFixed(2));

    // Update color based on multiplier
    if (currentMultiplier >= 5.00) $('#main-multiplier').css('color', '#ff0000');
    else if (currentMultiplier >= 2.00) $('#main-multiplier').css('color', '#ffff00');
    else $('#main-multiplier').css('color', '#ffffff');

    // Update Plane Position
    planePos.x += 0.5;
    planePos.y += 0.3;
    $('#airplane-container').css({
        left: (10 + planePos.x) + '%',
        bottom: (20 + planePos.y) + 'px'
    });

    if (currentMultiplier >= crashPoint) {
        crashGame();
    }
}

function cashOut() {
    if (!isPlaying) return;
    clearInterval(gameInterval);

    $.ajax({
        url: 'api/aviator.php',
        type: 'POST',
        data: { action: 'cashout', multiplier: currentMultiplier, crash_point: crashPoint, bet_amount: currentBet },
        dataType: 'json',
        success: function(res) {
            if (res.status == 'success') {
                isPlaying = false;
                $('#bet-btn').removeClass('d-none');
                $('#cashout-btn').addClass('d-none');
                $('#main-multiplier').css('color', '#00ff00'); // Turn green on win

                // Update balance in header
                $('.fa-wallet').parent().html(`<i class="fas fa-wallet me-1 text-success"></i> ৳${res.new_balance}`);

                alert('Success! You won ৳' + res.win_amount);
                loadHistory();
            }
        }
    });
}

function crashGame() {
    clearInterval(gameInterval);
    isPlaying = false;

    $('#crash-overlay').removeClass('d-none');
    $('#final-crash-point').text(crashPoint.toFixed(2) + 'x');
    $('#cashout-btn').addClass('d-none');
    $('#bet-btn').removeClass('d-none');

    $.ajax({
        url: 'api/aviator.php',
        type: 'POST',
        data: { action: 'crash', bet_amount: currentBet, crash_point: crashPoint },
        dataType: 'json',
        success: function() {
            loadHistory();
        }
    });
}

function loadHistory() {
    $.get('api/aviator.php?action=history', function(data) {
        let html = '';
        data.forEach(item => {
            let cls = 'crash-low';
            if (item.crash_point >= 5.00) cls = 'crash-high';
            else if (item.crash_point >= 2.00) cls = 'crash-mid';
            html += `<span class="crash-point-badge ${cls}">${parseFloat(item.crash_point).toFixed(2)}x</span>`;
        });
        $('#crash-history').html(html);
    }, 'json');
}
</script>

<?php require_once 'includes/bottom_menu.php'; require_once 'includes/footer.php'; ?>
