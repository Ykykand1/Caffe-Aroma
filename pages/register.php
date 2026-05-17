<?php
require_once '../db/db_connect.php';
require_once '../includes/csrf.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $password = $_POST['password']      ?? '';

    $phone_regex = '/^(\+355|0)[0-9]{8,9}$/';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Emri, email-i dhe fjalëkalimi janë të detyrueshme.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresa e email-it është e pavlefshme.';
    } elseif ($phone !== '' && !preg_match($phone_regex, $phone)) {
        $error = 'Numri i telefonit duhet të jetë në formatin +355XXXXXXXXX ose 0XXXXXXXXX.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->rowCount() > 0) {
            $error = 'Emri i përdoruesit ose email-i ekziston tashmë.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt   = $pdo->prepare(
                "INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)"
            );
            if ($stmt->execute([$username, $email, $phone ?: null, $hashed])) {
                set_flash('Regjistrimi u krye me sukses! Mund të hyni tani.');
                header('Location: login.php');
                exit;
            } else {
                $error = 'Regjistrimi dështoi. Provoni përsëri.';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="page-wrapper">
    <div class="auth-container card">
        <h2 class="text-center">Krijo Llogari</h2>

        <?php if ($error): ?>
        <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?=json_encode($error)?>,'error'));</script>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Emri i Përdoruesit</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="phone">Numri i Telefonit <span style="color:var(--text-mid); font-weight:400;">(opcionale)</span></label>
                <input type="tel" id="phone" name="phone"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                       placeholder="+355 6X XXX XXXX"
                       pattern="(\+355|0)[0-9]{8,9}"
                       autocomplete="tel">
            </div>
            <div class="form-group">
                <label for="password">Fjalëkalimi</label>
                <input type="password" id="password" name="password"
                       required minlength="6" autocomplete="new-password">
            </div>
            <button type="submit" class="btn" style="width:100%;">Regjistrohu</button>
        </form>

        <p class="text-center" style="margin-top:1.25rem; color:var(--text-mid); font-size:0.95rem;">
            Keni llogari? <a href="login.php" style="color:var(--orange);">Hyni këtu</a>.
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
