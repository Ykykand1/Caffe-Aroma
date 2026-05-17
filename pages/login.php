<?php
require_once '../db/db_connect.php';
require_once '../includes/csrf.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$error    = '';
$blocked  = false;
$ip       = $_SERVER['REMOTE_ADDR'];
$window   = 15 * 60; // 15 minutes in seconds
$max_att  = 5;

// ── Check existing rate-limit record ────────────────────────────────
$att_stmt = $pdo->prepare("SELECT * FROM login_attempts WHERE ip_address = ?");
$att_stmt->execute([$ip]);
$att_row = $att_stmt->fetch();

if ($att_row) {
    $elapsed = time() - strtotime($att_row['last_attempt']);
    if ($elapsed > $window) {
        // Window expired — clear the record
        $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?")->execute([$ip]);
        $att_row = null;
    } elseif ($att_row['attempts'] >= $max_att) {
        $remaining = ceil(($window - $elapsed) / 60);
        $error     = "Shumë tentativa dështuese. Provoni përsëri pas $remaining minut.";
        $blocked   = true;
    }
}

// ── Handle POST ──────────────────────────────────────────────────────
if (!$blocked && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Të gjitha fushat janë të detyrueshme.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Successful login — clear attempts
            $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?")->execute([$ip]);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            set_flash('Mirë se vini, ' . $user['username'] . '!');
            header('Location: ' . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
            exit;
        } else {
            // Failed — record attempt
            $new_count = ($att_row['attempts'] ?? 0) + 1;
            $pdo->prepare(
                "INSERT INTO login_attempts (ip_address, attempts)
                 VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE attempts = ?, last_attempt = CURRENT_TIMESTAMP"
            )->execute([$ip, $new_count, $new_count]);

            $left  = max(0, $max_att - $new_count);
            $error = $left > 0
                ? "Emri ose fjalëkalimi është i gabuar. ($new_count/$max_att tentativa)"
                : "Shumë tentativa dështuese. Llogaria u bllokua për 15 minuta.";
        }
    }
}

include '../includes/header.php';
?>

<div class="page-wrapper">
    <div class="auth-container card">
        <h2 class="text-center">Hyr në Llogari</h2>

        <?php if ($error): ?>
        <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?=json_encode($error)?>,'error'));</script>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Emri i Përdoruesit</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required autocomplete="username" <?= $blocked ? 'disabled' : '' ?>>
            </div>
            <div class="form-group">
                <label for="password">Fjalëkalimi</label>
                <input type="password" id="password" name="password"
                       required autocomplete="current-password" <?= $blocked ? 'disabled' : '' ?>>
            </div>
            <button type="submit" class="btn" style="width:100%;" <?= $blocked ? 'disabled' : '' ?>>
                Hyr
            </button>
        </form>

        <p class="text-center" style="margin-top:1.25rem; color:var(--text-mid); font-size:0.95rem;">
            Nuk keni llogari? <a href="register.php" style="color:var(--orange);">Regjistrohu këtu</a>.
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
