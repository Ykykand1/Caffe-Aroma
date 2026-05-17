<?php
require_once '../includes/auth_check.php';
require_login();
require_once '../db/db_connect.php';
require_once '../includes/csrf.php';

$user_id  = $_SESSION['user_id'];
$stmt     = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$info_error = $pass_error = '';
$phone_regex = '/^(\+355|0)[0-9]{8,9}$/';

// ── Update info ──────────────────────────────────────────────────────
if (isset($_POST['update_info'])) {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');

    if (empty($username) || empty($email)) {
        $info_error = 'Emri dhe email-i janë të detyrueshme.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $info_error = 'Adresa e email-it është e pavlefshme.';
    } elseif ($phone !== '' && !preg_match($phone_regex, $phone)) {
        $info_error = 'Numri i telefonit duhet të jetë +355XXXXXXXXX ose 0XXXXXXXXX.';
    } else {
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?"
        );
        $check->execute([$username, $email, $user_id]);
        if ($check->rowCount() > 0) {
            $info_error = 'Ky emër ose email ekziston tashmë.';
        } else {
            $upd = $pdo->prepare(
                "UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?"
            );
            if ($upd->execute([$username, $email, $phone ?: null, $user_id])) {
                $_SESSION['username'] = $username;
                $user['username']     = $username;
                $user['email']        = $email;
                $user['phone']        = $phone;
                set_flash('Informacioni u përditësua me sukses.');
                header('Location: profile.php');
                exit;
            }
        }
    }
}

// ── Change password ──────────────────────────────────────────────────
if (isset($_POST['update_password'])) {
    csrf_verify();

    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!password_verify($current, $user['password'])) {
        $pass_error = 'Fjalëkalimi aktual është i gabuar.';
    } elseif (strlen($new) < 6) {
        $pass_error = 'Fjalëkalimi i ri duhet të ketë të paktën 6 karaktere.';
    } elseif ($new !== $confirm) {
        $pass_error = 'Fjalëkalimet e reja nuk përputhen.';
    } else {
        $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($upd->execute([password_hash($new, PASSWORD_BCRYPT), $user_id])) {
            set_flash('Fjalëkalimi u ndryshua me sukses.');
            header('Location: profile.php');
            exit;
        }
    }
}

include '../includes/header.php';
?>

<div class="page-wrapper">
    <h1 class="page-title">Profili Im</h1>
    <p class="page-subtitle">Ndryshoni të dhënat e llogarisë suaj.</p>

    <?php if ($info_error): ?>
    <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?=json_encode($info_error)?>,'error'));</script>
    <?php endif; ?>
    <?php if ($pass_error): ?>
    <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?=json_encode($pass_error)?>,'error'));</script>
    <?php endif; ?>

    <div style="display:flex; gap:2.5rem; flex-wrap:wrap; max-width:900px;">

        <!-- Profile info -->
        <div class="card" style="flex:1; min-width:280px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:400; font-size:1.4rem; margin-bottom:1.5rem;">
                Informacioni i Llogarisë
            </h2>
            <form method="POST" action="profile.php">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="username">Emri i Përdoruesit</label>
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Numri i Telefonit</label>
                    <input type="tel" id="phone" name="phone"
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                           placeholder="+355 6X XXX XXXX"
                           pattern="(\+355|0)[0-9]{8,9}"
                           autocomplete="tel">
                </div>
                <div class="form-group">
                    <label>Roli</label>
                    <input type="text"
                           value="<?= $user['role'] === 'admin' ? 'Administrator' : 'Përdorues' ?>"
                           disabled style="background:#f8f5f2; color:var(--text-mid);">
                </div>
                <div class="form-group">
                    <label>Anëtar që nga</label>
                    <input type="text"
                           value="<?= date('d M Y', strtotime($user['created_at'])) ?>"
                           disabled style="background:#f8f5f2; color:var(--text-mid);">
                </div>
                <button type="submit" name="update_info" class="btn">Ruaj Ndryshimet</button>
            </form>
        </div>

        <!-- Password change -->
        <div class="card" style="flex:1; min-width:280px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:400; font-size:1.4rem; margin-bottom:1.5rem;">
                Ndrysho Fjalëkalimin
            </h2>
            <form method="POST" action="profile.php">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="current_password">Fjalëkalimi Aktual</label>
                    <input type="password" id="current_password" name="current_password"
                           required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label for="new_password">Fjalëkalimi i Ri</label>
                    <input type="password" id="new_password" name="new_password"
                           required minlength="6" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmo Fjalëkalimin e Ri</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           required autocomplete="new-password">
                </div>
                <button type="submit" name="update_password" class="btn btn-secondary">
                    Ndrysho Fjalëkalimin
                </button>
            </form>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
