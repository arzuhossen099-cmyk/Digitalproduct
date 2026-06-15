<?php
require_once __DIR__ . '/init.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'PLAYPULSE - Every Game. Every Moment.'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
            <i class="fas fa-play-circle text-danger me-2 fa-lg"></i>
            <span class="text-white">PLAY</span><span class="text-danger">PULSE</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars text-white"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="/index.php"><?php echo $lang['home']; ?></a></li>
                <li class="nav-item"><a class="nav-link" href="/match-center/index.php"><?php echo $lang['live_scores']; ?></a></li>
                <li class="nav-item"><a class="nav-link" href="/news/index.php"><?php echo $lang['news']; ?></a></li>
                <li class="nav-item"><a class="nav-link" href="/where-to-watch/index.php"><?php echo $lang['streaming_guide']; ?></a></li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <?php echo strtoupper($lang_code); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="?lang=en">EN</a></li>
                        <li><a class="dropdown-item" href="?lang=bn">বাংলা</a></li>
                    </ul>
                </div>
                <?php if (is_logged_in()): ?>
                    <a href="/user/profile.php" class="btn btn-yellow btn-sm"><i class="fas fa-user me-1"></i></a>
                <?php else: ?>
                    <a href="/user/login.php" class="btn btn-yellow btn-sm"><i class="fas fa-play me-1"></i> LIVE</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
