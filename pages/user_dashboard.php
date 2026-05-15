<?php
require_once '../includes/auth_check.php';
require_login();
require_once '../db/db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch reservations
$res_stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC");
$res_stmt->execute([$user_id]);
$reservations = $res_stmt->fetchAll();

// Fetch orders
$order_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$order_stmt->execute([$user_id]);
$orders = $order_stmt->fetchAll();

include '../includes/header.php';
?>

<div class="card">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
    <p>This is your dashboard where you can view your orders and reservations.</p>

    <div style="display: flex; gap: 2rem; margin-top: 2rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <h3>Your Reservations</h3>
            <?php if (count($reservations) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                            <tr>
                                <td><?= htmlspecialchars($res['reservation_date']) ?></td>
                                <td><?= htmlspecialchars($res['reservation_time']) ?></td>
                                <td><?= htmlspecialchars($res['guests']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($res['status'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no reservations.</p>
                <a href="reservations.php" class="btn">Book a Table</a>
            <?php endif; ?>
        </div>

        <div style="flex: 1; min-width: 300px;">
            <h3>Your Orders</h3>
            <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($order['id']) ?></td>
                                <td>$<?= htmlspecialchars(number_format($order['total_amount'], 2)) ?></td>
                                <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
                                <td><?= htmlspecialchars(date('M d, Y', strtotime($order['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no past orders.</p>
                <a href="menu.php" class="btn">View Menu</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
