<?php
require_once '../includes/auth_check.php';
require_admin();
require_once '../db/db_connect.php';

// ── Status update handlers ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_reservation'])) {
        $id     = (int)$_POST['reservation_id'];
        $status = $_POST['status'];
        if (in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
            $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?")->execute([$status, $id]);
        }
    }
    if (isset($_POST['update_order'])) {
        $id     = (int)$_POST['order_id'];
        $status = $_POST['status'];
        if (in_array($status, ['pending', 'completed', 'cancelled'], true)) {
            $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $id]);
        }
    }
    header('Location: admin_dashboard.php?tab=' . ($_POST['tab'] ?? 'overview'));
    exit;
}

// ── Stats ────────────────────────────────────────────────────────────
$user_count        = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$product_count     = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$order_count       = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$reservation_count = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();

// ── Reservations (with user info) ────────────────────────────────────
$reservations = $pdo->query(
    "SELECT r.*, u.username
     FROM reservations r
     JOIN users u ON r.user_id = u.id
     ORDER BY r.reservation_date DESC, r.reservation_time DESC"
)->fetchAll();

// ── Orders (with user info) ──────────────────────────────────────────
$orders = $pdo->query(
    "SELECT o.*, u.username
     FROM orders o
     JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC"
)->fetchAll();

$active_tab = $_GET['tab'] ?? 'overview';

$status_labels = [
    'pending'   => ['label' => 'Në Pritje',  'badge' => 'badge-pending'],
    'confirmed' => ['label' => 'Konfirmuar', 'badge' => 'badge-confirmed'],
    'cancelled' => ['label' => 'Anuluar',    'badge' => 'badge-cancelled'],
    'completed' => ['label' => 'Përfunduar', 'badge' => 'badge-completed'],
];

include '../includes/header.php';
?>

<div class="page-wrapper">
    <h1 class="page-title">Paneli i Adminit</h1>
    <p class="page-subtitle">Mirë se vini, <?= htmlspecialchars($_SESSION['username']) ?>.</p>

    <!-- Tab navigation -->
    <div style="display:flex; gap:0.5rem; margin-bottom:2.5rem; border-bottom:2px solid var(--cream-dark); padding-bottom:0;">
        <?php foreach (['overview' => 'Përmbledhje', 'reservations' => 'Rezervime', 'orders' => 'Porosi'] as $tab => $label): ?>
        <a
            href="admin_dashboard.php?tab=<?= $tab ?>"
            style="
                padding:0.65rem 1.25rem;
                font-size:0.9rem;
                font-weight:500;
                text-decoration:none;
                border-bottom:2px solid <?= $active_tab === $tab ? 'var(--orange)' : 'transparent' ?>;
                margin-bottom:-2px;
                color:<?= $active_tab === $tab ? 'var(--orange)' : 'var(--text-mid)' ?>;
                transition:color 0.2s;
            "
        ><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <!-- ── OVERVIEW ── -->
    <?php if ($active_tab === 'overview'): ?>
    <div class="stat-cards">
        <div class="stat-card"><h3>Përdorues</h3><div class="big-num"><?= $user_count ?></div></div>
        <div class="stat-card">
            <h3>Produkte</h3>
            <div class="big-num"><?= $product_count ?></div>
            <a href="admin_products.php" class="btn btn-secondary btn-sm" style="margin-top:1rem;">Menaxho</a>
        </div>
        <div class="stat-card">
            <h3>Porosi</h3>
            <div class="big-num"><?= $order_count ?></div>
            <a href="admin_dashboard.php?tab=orders" class="btn btn-ghost btn-sm" style="margin-top:1rem;">Shiko</a>
        </div>
        <div class="stat-card">
            <h3>Rezervime</h3>
            <div class="big-num"><?= $reservation_count ?></div>
            <a href="admin_dashboard.php?tab=reservations" class="btn btn-ghost btn-sm" style="margin-top:1rem;">Shiko</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── RESERVATIONS ── -->
    <?php if ($active_tab === 'reservations'): ?>
    <div class="card">
        <h2 style="font-family:'Fraunces',serif; font-weight:400; margin-bottom:1.5rem;">
            Të Gjitha Rezervimet
        </h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Përdoruesi</th>
                    <th>Data</th>
                    <th>Ora</th>
                    <th>Persona</th>
                    <th>Statusi</th>
                    <th>Veprim</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $r):
                    $s = $status_labels[$r['status']] ?? $status_labels['pending'];
                ?>
                <tr>
                    <td>#<?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td><?= date('d M Y', strtotime($r['reservation_date'])) ?></td>
                    <td><?= substr($r['reservation_time'], 0, 5) ?></td>
                    <td><?= (int)$r['guests'] ?></td>
                    <td><span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span></td>
                    <td>
                        <form method="POST" style="display:inline-flex; gap:0.4rem; align-items:center;">
                            <input type="hidden" name="reservation_id" value="<?= (int)$r['id'] ?>">
                            <input type="hidden" name="tab" value="reservations">
                            <select name="status" style="padding:0.3rem 0.5rem; border:1.5px solid #ddd; border-radius:6px; font-size:0.85rem;">
                                <option value="pending"   <?= $r['status']==='pending'   ? 'selected':'' ?>>Në Pritje</option>
                                <option value="confirmed" <?= $r['status']==='confirmed' ? 'selected':'' ?>>Konfirmuar</option>
                                <option value="cancelled" <?= $r['status']==='cancelled' ? 'selected':'' ?>>Anuluar</option>
                            </select>
                            <button type="submit" name="update_reservation" class="btn btn-ghost btn-sm">Ruaj</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- ── ORDERS ── -->
    <?php if ($active_tab === 'orders'): ?>
    <div class="card">
        <h2 style="font-family:'Fraunces',serif; font-weight:400; margin-bottom:1.5rem;">
            Të Gjitha Porositë
        </h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Përdoruesi</th>
                    <th>Totali</th>
                    <th>Data</th>
                    <th>Statusi</th>
                    <th>Veprim</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o):
                    $s = $status_labels[$o['status']] ?? $status_labels['pending'];
                ?>
                <tr>
                    <td>#<?= (int)$o['id'] ?></td>
                    <td><?= htmlspecialchars($o['username']) ?></td>
                    <td>€<?= number_format($o['total_amount'], 2) ?></td>
                    <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                    <td><span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span></td>
                    <td>
                        <form method="POST" style="display:inline-flex; gap:0.4rem; align-items:center;">
                            <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                            <input type="hidden" name="tab" value="orders">
                            <select name="status" style="padding:0.3rem 0.5rem; border:1.5px solid #ddd; border-radius:6px; font-size:0.85rem;">
                                <option value="pending"   <?= $o['status']==='pending'   ? 'selected':'' ?>>Në Pritje</option>
                                <option value="completed" <?= $o['status']==='completed' ? 'selected':'' ?>>Përfunduar</option>
                                <option value="cancelled" <?= $o['status']==='cancelled' ? 'selected':'' ?>>Anuluar</option>
                            </select>
                            <button type="submit" name="update_order" class="btn btn-ghost btn-sm">Ruaj</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>
