<?php
require_once '../includes/auth_check.php';
require_login();
require_once '../db/db_connect.php';

$user_id = $_SESSION['user_id'];
$stmt    = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$info_success = $info_error = $pass_success = $pass_error = '';

// ── Update username / email ──────────────────────────────────────────
if (isset($_POST['update_info'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);

    if (empty($username) || empty($email)) {
        $info_error = 'Të gjitha fushat janë të detyrueshme.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $info_error = 'Adresa e email-it është e pavlefshme.';
    } else {
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?"
        );
        $check->execute([$username, $email, $user_id]);
        if ($check->rowCount() > 0) {
            $info_error = 'Ky emër përdoruesi ose email ekziston tashmë.';
        } else {
            $upd = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            if ($upd->execute([$username, $email, $user_id])) {
                $_SESSION['username'] = $username;
                $user['username']     = $username;
                $user['email']        = $email;
                $info_success         = 'Informacioni u përditësua me sukses.';
            }
        }
    }
}

// ── Change password ─────────────────────────────────────────────────
if (isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $pass_error = 'Fjalëkalimi aktual është i gabuar.';
    } elseif (strlen($new) < 6) {
        $pass_error = 'Fjalëkalimi i ri duhet të ketë të paktën 6 karaktere.';
    } elseif ($new !== $confirm) {
        $pass_error = 'Fjalëkalimet e reja nuk përputhen.';
    } else {
        $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($upd->execute([password_hash($new, PASSWORD_BCRYPT), $user_id])) {
            $pass_success = 'Fjalëkalimi u ndryshua me sukses.';
        }
    }
}

include '../includes/header.php';
?>

<div class="page-wrapper">
    <h1 class="page-title">Profili Im</h1>
    <p class="page-subtitle">Ndryshoni të dhënat e llogarisë suaj.</p>

    <div style="display:flex; gap:2.5rem; flex-wrap:wrap; max-width:900px;">

        <!-- ── Profile info ── -->
        <div class="card" style="flex:1; min-width:280px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:400; font-size:1.4rem; margin-bottom:1.5rem;">
                Informacioni i Llogarisë
            </h2>

            <?php if ($info_error):   ?><div class="alert alert-error"><?= htmlspecialchars($info_error) ?></div><?php endif; ?>
            <?php if ($info_success): ?><div class="alert alert-success"><?= htmlspecialchars($info_success) ?></div><?php endif; ?>

            <form method="POST" action="profile.php">
                <div class="form-group">
                    <label for="username">Emri i Përdoruesit</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($user['username']) ?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($user['email']) ?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <label>Roli</label>
                    <input
                        type="text"
                        value="<?= $user['role'] === 'admin' ? 'Administrator' : 'Përdorues' ?>"
                        disabled
                        style="background:#f8f5f2; color:var(--text-mid);"
                    >
                </div>
                <div class="form-group">
                    <label>Anëtar që nga</label>
                    <input
                        type="text"
                        value="<?= date('d M Y', strtotime($user['created_at'])) ?>"
                        disabled
                        style="background:#f8f5f2; color:var(--text-mid);"
                    >
                </div>
                <button type="submit" name="update_info" class="btn">Ruaj Ndryshimet</button>
            </form>
        </div>

        <!-- ── Password change ── -->
        <div class="card" style="flex:1; min-width:280px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:400; font-size:1.4rem; margin-bottom:1.5rem;">
                Ndrysho Fjalëkalimin
            </h2>

            <?php if ($pass_error):   ?><div class="alert alert-error"><?= htmlspecialchars($pass_error) ?></div><?php endif; ?>
            <?php if ($pass_success): ?><div class="alert alert-success"><?= htmlspecialchars($pass_success) ?></div><?php endif; ?>

            <form method="POST" action="profile.php">
                <div class="form-group">
                    <label for="current_password">Fjalëkalimi Aktual</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        required
                        autocomplete="current-password"
                    >
                </div>
                <div class="form-group">
                    <label for="new_password">Fjalëkalimi i Ri</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        required
                        minlength="6"
                        autocomplete="new-password"
                    >
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmo Fjalëkalimin e Ri</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        required
                        autocomplete="new-password"
                    >
                </div>
                <button type="submit" name="update_password" class="btn btn-secondary">
                    Ndrysho Fjalëkalimin
                </button>
            </form>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
