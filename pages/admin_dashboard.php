<?php
require_once '../includes/auth_check.php';
require_admin();
require_once '../db/db_connect.php';

// Fetch quick stats
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$reservation_count = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();

include '../includes/header.php';
?>

<div class="card">
    <h2>Admin Dashboard</h2>
    <p>Welcome, Administrator <?= htmlspecialchars($_SESSION['username']) ?>.</p>

    <div style="display: flex; gap: 1rem; margin: 2rem 0; flex-wrap: wrap;">
        <div class="card" style="flex: 1; text-align: center; min-width: 200px;">
            <h3>Users</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--accent-color);"><?= $user_count ?></p>
        </div>
        <div class="card" style="flex: 1; text-align: center; min-width: 200px;">
            <h3>Products</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--accent-color);"><?= $product_count ?></p>
            <a href="admin_products.php" class="btn" style="margin-top: 1rem; padding: 0.5rem 1rem;">Manage Products</a>
        </div>
        <div class="card" style="flex: 1; text-align: center; min-width: 200px;">
            <h3>Orders</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--accent-color);"><?= $order_count ?></p>
        </div>
        <div class="card" style="flex: 1; text-align: center; min-width: 200px;">
            <h3>Reservations</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--accent-color);"><?= $reservation_count ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
