<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/csrf.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_role    = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caffè Aroma</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300;1,9..144,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
    <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token()) ?>">
</head>
<body>

<!-- Splash Loader -->
<div id="loader">
    <span class="loader-brand">Caffè Aroma</span>
    <svg class="loader-cup" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Cup body outline -->
        <path class="cup-body" d="M10 16 L14 46 Q14 50 18 50 L38 50 Q42 50 42 46 L46 16 Z" stroke="rgba(244,237,224,0.25)" stroke-width="2"/>
        <!-- Fill (animated) -->
        <clipPath id="fillClip">
            <rect x="8" y="16" width="40" height="34"/>
        </clipPath>
        <path d="M10 16 L14 46 Q14 50 18 50 L38 50 Q42 50 42 46 L46 16 Z" fill="#D97842" clip-path="url(#fillClip)" class="cup-fill"/>
        <!-- Cup rim -->
        <rect x="8" y="13" width="40" height="5" rx="2.5" fill="rgba(244,237,224,0.2)"/>
        <!-- Steam lines -->
        <g class="cup-steam">
            <path d="M22 8 Q24 4 22 1" stroke="rgba(244,237,224,0.5)" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M28 8 Q30 4 28 1" stroke="rgba(244,237,224,0.5)" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M34 8 Q36 4 34 1" stroke="rgba(244,237,224,0.5)" stroke-width="1.5" stroke-linecap="round"/>
        </g>
    </svg>
</div>

<header>
    <a href="index.php" class="logo">☕ Caffè Aroma</a>
    <nav>
        <ul>
            <li><a href="index.php">Kryefaqja</a></li>
            <li><a href="menu.php">Menuja</a></li>
            <li><a href="world_coffees.php">Kafetë e Botës</a></li>
            <?php if ($is_logged_in): ?>
                <li><a href="reservations.php">Rezervime</a></li>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="admin_dashboard.php">Admin</a></li>
                <?php else: ?>
                    <li><a href="user_dashboard.php">Paneli</a></li>
                    <li><a href="profile.php">Profili</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Dil</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn btn-outline" style="padding:0.45rem 1.1rem;">Hyr</a></li>
                <li><a href="register.php" class="btn" style="padding:0.45rem 1.1rem;">Regjistrohu</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
