<?php
require_once '../db/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Të gjitha fushat janë të detyrueshme.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            header('Location: ' . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
            exit;
        } else {
            $error = 'Emri i përdoruesit ose fjalëkalimi është i gabuar.';
        }
    }
}

include '../includes/header.php';
?>

<div class="page-wrapper">
    <div class="auth-container card">
        <h2 class="text-center">Hyr në Llogari</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Emri i Përdoruesit</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Fjalëkalimi</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn" style="width:100%;">Hyr</button>
        </form>

        <p class="text-center" style="margin-top:1.25rem; color:var(--text-mid); font-size:0.95rem;">
            Nuk keni llogari? <a href="register.php" style="color:var(--orange);">Regjistrohu këtu</a>.
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
