<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caffè Aroma</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">☕ Caffè Aroma</a>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="world_coffees.php">World Coffees</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="reservations.php">Reservations</a></li>
                    <?php if ($user_role === 'admin'): ?>
                        <li><a href="admin_dashboard.php">Admin</a></li>
                    <?php else: ?>
                        <li><a href="user_dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-secondary" style="padding: 0.4rem 1rem;">Login</a></li>
                    <li><a href="register.php" class="btn" style="padding: 0.4rem 1rem;">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
