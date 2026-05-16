<?php
require_once '../db/db_connect.php';
include '../includes/header.php';

$products = $pdo->query("SELECT * FROM products ORDER BY category, name")->fetchAll();
?>

<div class="page-wrapper">
    <h1 class="page-title">Menuja Jonë</h1>
    <p class="page-subtitle">Zgjidhni nga koleksioni ynë i kafeve dhe pastave artizanale.</p>

    <div class="menu-layout">
        <!-- Products column -->
        <div class="menu-products">
            <div class="menu-controls">
                <input
                    type="search"
                    id="menu-search"
                    class="search-input"
                    placeholder="Kërko produkt..."
                    autocomplete="off"
                >
            </div>

            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                <div
                    class="product-card"
                    data-name="<?= htmlspecialchars($p['name']) ?>"
                    data-category="<?= htmlspecialchars($p['category']) ?>"
                >
                    <img
                        src="<?= htmlspecialchars($p['image_url']) ?>"
                        alt="<?= htmlspecialchars($p['name']) ?>"
                        class="product-image"
                    >
                    <div class="product-info">
                        <span class="product-category"><?= htmlspecialchars($p['category']) ?></span>
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <p><?= htmlspecialchars($p['description']) ?></p>
                        <div class="mt-auto">
                            <div class="product-price">€<?= number_format($p['price'], 2) ?></div>
                            <button
                                class="btn add-to-cart"
                                data-id="<?= $p['id'] ?>"
                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-price="<?= $p['price'] ?>"
                                style="width:100%;"
                            >Shto në Shportë</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cart sidebar -->
        <div class="menu-sidebar">
            <div class="card cart-sticky">
                <h3 style="font-family:'Fraunces',serif; font-weight:400; margin-bottom:1.25rem;">
                    Shporta Juaj
                </h3>
                <div id="cart-items">
                    <p class="text-mid" style="font-size:0.95rem;">Shporta është bosh.</p>
                </div>
                <hr style="border:0; border-top:1px solid #f0ebe4; margin:1.25rem 0;">
                <div style="display:flex; justify-content:space-between; font-weight:600; font-size:1.1rem; margin-bottom:1.25rem;">
                    <span>Totali:</span>
                    <span id="cart-total">€0.00</span>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button id="checkout-btn" class="btn" style="width:100%; display:none;">
                        Paguaj Tani
                    </button>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary" style="display:block; text-align:center;">
                        Hyr për të Paguar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/cart.js"></script>
<?php include '../includes/footer.php'; ?>
