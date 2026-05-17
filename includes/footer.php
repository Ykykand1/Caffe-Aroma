</main>

<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="index.php" class="logo">☕ Caffè Aroma</a>
            <p>Kafja jonë është fryt i dashuri<br>dhe traditës — që nga viti 2010.</p>
        </div>
        <div class="footer-col">
            <h5>Navigim</h5>
            <ul>
                <li><a href="index.php">Kryefaqja</a></li>
                <li><a href="menu.php">Menuja</a></li>
                <li><a href="world_coffees.php">Kafetë e Botës</a></li>
                <li><a href="reservations.php">Rezervime</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h5>Na Gjeni</h5>
            <ul>
                <li><a href="#">Rruga Barrikadave 12, Tiranë</a></li>
                <li><a href="#">+355 69 123 4567</a></li>
                <li><a href="#">info@caffearoma.al</a></li>
                <li><a href="#">E Hënë – E Diel, 08:00–22:00</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?php echo date('Y'); ?> <strong>Caffè Aroma</strong>. Të gjitha të drejtat e rezervuara.</span>
        <span>Ndërtuar me ❤ në Tiranë</span>
    </div>
</footer>

<script src="../assets/js/animations.js"></script>
<?php if (!empty($_SESSION['flash'])):
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
?>
<script>
document.addEventListener('DOMContentLoaded', () =>
    showToast(<?= json_encode($flash['message']) ?>, <?= json_encode($flash['type']) ?>)
);
</script>
<?php endif; ?>
</body>
</html>
