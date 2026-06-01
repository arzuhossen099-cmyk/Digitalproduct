<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';
?>

<div class="row g-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <img src="https://via.placeholder.com/400x200?text=Ludo+Game" class="card-img-top" alt="Ludo">
            <div class="card-body">
                <h5 class="card-title">Ludo King</h5>
                <p class="card-text text-muted">Play Ludo and win up to ৳50 reward.</p>
                <button class="btn btn-primary w-100" onclick="alert('Game starting soon!')">Play Now</button>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <img src="https://via.placeholder.com/400x200?text=Spin+Wheel" class="card-img-top" alt="Spin">
            <div class="card-body">
                <h5 class="card-title">Lucky Spin</h5>
                <p class="card-text text-muted">Spin the wheel and test your luck.</p>
                <button class="btn btn-danger w-100" onclick="alert('Coming soon!')">Spin Now</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
