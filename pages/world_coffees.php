<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../includes/header.php';
?>

<div class="page-wrapper">
    <div class="wc-header">
        <span class="section-eyebrow">Eksploro</span>
        <h1 class="page-title">Kafetë e Botës</h1>
        <p>Zbuloni kafetë e nxehta dhe të ftohta nga e gjithë bota, nëpërmjet bazës së të dhënave Coffee API.</p>
        <div class="wc-controls">
            <button id="btn-hot" class="btn">Kafe të Nxehta</button>
            <button id="btn-iced" class="btn btn-secondary">Kafe të Ftohta</button>
        </div>
    </div>

    <div id="coffee-loading" class="text-center" style="display:none; padding:3rem;">
        <p class="text-mid">Duke ngarkuar kafetë...</p>
    </div>

    <div id="coffee-grid" class="product-grid"></div>
</div>

<script src="../assets/js/world-coffees.js"></script>
<?php include '../includes/footer.php'; ?>
