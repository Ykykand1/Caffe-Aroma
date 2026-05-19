<?php
require_once '../db/db_connect.php';
include '../includes/header.php';

// Ensure session is started and determine login state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

$products = $pdo->query("SELECT * FROM products ORDER BY category, name")->fetchAll();
?>

<div style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <div style="flex: 3; min-width: 300px;">
        <h2>Our Menu</h2>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-image">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <p style="font-size: 0.9rem; color: #666;"><?= htmlspecialchars($p['description']) ?></p>
                        <div class="mt-auto">
                            <p class="product-price">$<?= number_format($p['price'], 2) ?></p>
                            <button class="btn add-to-cart" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-price="<?= $p['price'] ?>" style="width: 100%;">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div style="flex: 1; min-width: 300px;">
        <div class="card" style="position: sticky; top: 100px;">
            <h3>Your Cart</h3>
            <div id="cart-items">
                <p>Your cart is empty.</p>
            </div>
            <hr style="border: 0; border-top: 1px solid #ddd; margin: 1rem 0;">
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; margin-bottom: 1rem;">
                <span>Total:</span>
                <span id="cart-total">$0.00</span>
            </div>
            <?php if ($is_logged_in): ?>
                <button id="checkout-btn" class="btn" style="width: 100%; display: none;">Checkout</button>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary" style="display: block; text-align: center;">Login to Checkout</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../assets/js/cart.js"></script>

<?php include '../includes/footer.php'; ?>
