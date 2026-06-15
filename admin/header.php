<?php
if (!is_admin_logged_in()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PLAYPULSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-bg: #111A2E;
            --main-bg: #0B1220;
            --card-bg: #16213A;
            --accent: #FFC107;
        }
        body { background-color: var(--main-bg); color: #fff; font-family: 'Inter', sans-serif; }
        .sidebar { min-height: 100vh; background: var(--sidebar-bg); border-right: 1px solid rgba(255,255,255,0.1); }
        .sidebar .nav-link { color: #adb5bd; padding: 12px 20px; font-weight: 500; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: var(--accent); background: rgba(255,193,7,0.1); }
        .admin-card { background: var(--card-bg); border: none; border-radius: 12px; }
        .stat-value { font-size: 2rem; font-weight: 800; }
        .stat-label { color: #adb5bd; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
            <div class="position-sticky pt-3">
                <div class="px-4 mb-4">
                    <h5 class="fw-bold text-white"><i class="fas fa-play-circle text-danger me-2"></i> PLAYPULSE</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-th-large me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="articles.php"><i class="fas fa-newspaper me-2"></i> News</a></li>
                    <li class="nav-item"><a class="nav-link" href="matches.php"><i class="fas fa-satellite-dish me-2"></i> Live Scores</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-video me-2"></i> Videos</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-list me-2"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="settings.php"><i class="fas fa-cog me-2"></i> Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-users me-2"></i> Users</a></li>
                    <hr class="border-secondary mx-3">
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
