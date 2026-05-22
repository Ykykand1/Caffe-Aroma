<?php
require_once '../includes/auth_check.php';
require_login();
require_once '../db/db_connect.php';
require_once '../includes/csrf.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $date    = $_POST['date']   ?? '';
    $time    = $_POST['time']   ?? '';
    $guests  = (int)($_POST['guests'] ?? 0);
    $phone   = trim($_POST['phone']   ?? '');

    $phone_regex = '/^(\+355|0)[0-9]{8,9}$/';

    if (empty($date) || empty($time) || $guests < 1) {
        $error = 'Ju lutemi plotësoni të gjitha fushat e detyrueshme.';
    } elseif ($phone !== '' && !preg_match($phone_regex, $phone)) {
        $error = 'Numri i telefonit duhet të jetë në formatin +355XXXXXXXXX ose 0XXXXXXXXX.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO reservations (user_id, reservation_date, reservation_time, guests, phone)
             VALUES (?, ?, ?, ?, ?)"
        );
        if ($stmt->execute([$_SESSION['user_id'], $date, $time, $guests, $phone ?: null])) {
            set_flash('Rezervimi u krye me sukses! Ju presim me padurim.');
            header('Location: reservations.php');
            exit;
        } else {
            $error = 'Rezervimi dështoi. Provoni përsëri.';
        }
    }
}

include '../includes/header.php';
?>

<div class="page-wrapper">
    <div class="card" style="max-width:520px; margin:0 auto;">
        <h1 class="page-title text-center" style="font-size:2rem;">Rezervo Tavolinë</h1>
        <p class="text-center" style="color:var(--text-mid); margin-bottom:1.75rem;">
            Rezervoni vendin tuaj në Caffè Aroma për një eksperiencë të paharrueshme.
        </p>

        <?php if ($error): ?>
        <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?=json_encode($error)?>,'error'));</script>
        <?php endif; ?>

        <form method="POST" action="reservations.php">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Data</label>
                <input type="date" name="date"
                       value="<?= htmlspecialchars($_POST['date'] ?? '') ?>"
                       required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Ora</label>
                <input type="time" name="time"
                       value="<?= htmlspecialchars($_POST['time'] ?? '') ?>"
                       required min="08:00" max="22:00">
            </div>
            <div class="form-group">
                <label>Numri i Personave</label>
                <input type="number" name="guests" min="1" max="20" required
                       value="<?= (int)($_POST['guests'] ?? 2) ?>">
            </div>
            <div class="form-group">
                <label>Numri i Telefonit <span style="color:var(--text-mid); font-weight:400;">(opsionale)</span></label>
                <input type="tel" name="phone"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                       placeholder="+355 6X XXX XXXX"
                       pattern="(\+355|0)[0-9]{8,9}"
                       autocomplete="tel">
            </div>
            <button type="submit" class="btn" style="width:100%;">Konfirmo Rezervimin</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
