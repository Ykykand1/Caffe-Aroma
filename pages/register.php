<?php
require_once '../db/db_connect.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Të gjitha fushat janë të detyrueshme.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = 'Emri i përdoruesit ose email-i ekziston tashmë.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt   = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed])) {
                $success = 'Regjistrimi u krye me sukses! Mund të hyni tani.';
            } else {
                $error = 'Regjistrimi dështoi. Ju lutemi provoni përsëri.';
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
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Emri i Përdoruesit</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Fjalëkalimi</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn" style="width:100%;">Regjistrohu</button>
        </form>

        <p class="text-center" style="margin-top:1.25rem; color:var(--text-mid); font-size:0.95rem;">
            Keni llogari? <a href="login.php" style="color:var(--orange);">Hyni këtu</a>.
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
