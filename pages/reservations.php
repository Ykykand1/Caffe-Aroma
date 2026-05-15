<?php
require_once '../includes/auth_check.php';
require_login();
require_once '../db/db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = (int)$_POST['guests'];

    if (empty($date) || empty($time) || $guests < 1) {
        $error = "Please fill in all valid details.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, reservation_date, reservation_time, guests) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $date, $time, $guests])) {
            $success = "Table reserved successfully! We look forward to seeing you.";
        } else {
            $error = "Failed to reserve table. Please try again.";
        }
    }
}

include '../includes/header.php';
?>

<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h2 class="text-center">Book a Table</h2>
    <p class="text-center">Reserve your spot at Caffè Aroma for a perfect coffee experience.</p>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="reservations.php" style="margin-top: 1.5rem;">
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" required min="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-group">
            <label>Time</label>
            <input type="time" name="time" required>
        </div>
        <div class="form-group">
            <label>Number of Guests</label>
            <input type="number" name="guests" min="1" max="20" required value="2">
        </div>
        <button type="submit" class="btn" style="width: 100%;">Confirm Reservation</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
