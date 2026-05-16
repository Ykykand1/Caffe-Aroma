<?php
require_once '../includes/auth_check.php';
require_admin();
require_once '../db/db_connect.php';

$user_count        = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$product_count     = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$order_count       = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$reservation_count = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();

include '../includes/header.php';
?>

<div class="page-wrapper">
    <h1 class="page-title">Paneli i Adminit</h1>
    <p class="page-subtitle">Mirë se vini, <?= htmlspecialchars($_SESSION['username']) ?>.</p>

    <div class="stat-cards">
        <div class="stat-card">
            <h3>Përdorues</h3>
            <div class="big-num"><?= $user_count ?></div>
        </div>
        <div class="stat-card">
            <h3>Produkte</h3>
            <div class="big-num"><?= $product_count ?></div>
            <a href="admin_products.php" class="btn btn-secondary btn-sm" style="margin-top:1rem;">Menaxho</a>
        </div>
        <div class="stat-card">
            <h3>Porosi</h3>
            <div class="big-num"><?= $order_count ?></div>
        </div>
        <div class="stat-card">
            <h3>Rezervime</h3>
            <div class="big-num"><?= $reservation_count ?></div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
