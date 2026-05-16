<?php
require_once '../includes/auth_check.php';
require_login();
require_once '../db/db_connect.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date   = $_POST['date'];
    $time   = $_POST['time'];
    $guests = (int)$_POST['guests'];

    if (empty($date) || empty($time) || $guests < 1) {
        $error = 'Ju lutemi plotësoni të gjitha fushat saktë.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO reservations (user_id, reservation_date, reservation_time, guests) VALUES (?, ?, ?, ?)"
        );
        if ($stmt->execute([$_SESSION['user_id'], $date, $time, $guests])) {
            $success = 'Rezervimi u krye me sukses! Ju presim me padurim.';
        } else {
            $error = 'Rezervimi dështoi. Ju lutemi provoni përsëri.';
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
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="reservations.php">
            <div class="form-group">
                <label>Data</label>
                <input type="date" name="date" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Ora</label>
                <input type="time" name="time" required min="08:00" max="22:00">
            </div>
            <div class="form-group">
                <label>Numri i Personave</label>
                <input type="number" name="guests" min="1" max="20" required value="2">
            </div>
            <button type="submit" class="btn" style="width:100%;">Konfirmo Rezervimin</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
