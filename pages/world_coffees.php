<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/header.php';
?>

<div class="card text-center" style="margin-bottom: 2rem;">
    <h2>World Coffees Explorer</h2>
    <p>Discover popular hot and iced coffees from around the world using the Sample APIs Coffee database.</p>
    
    <div style="margin-top: 1rem;">
        <button id="btn-hot" class="btn">Hot Coffees</button>
        <button id="btn-iced" class="btn btn-secondary">Iced Coffees</button>
    </div>
</div>

<div id="coffee-loading" class="text-center" style="display: none; padding: 2rem;">
    <p>Loading coffees...</p>
</div>

<div id="coffee-grid" class="product-grid">
    <!-- Coffees will be dynamically loaded here -->
</div>

<script src="../assets/js/world-coffees.js"></script>

<?php include '../includes/footer.php'; ?>
