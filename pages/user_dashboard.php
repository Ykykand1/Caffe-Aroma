<?php
require_once '../includes/auth_check.php';
require_login();
require_once '../db/db_connect.php';

$user_id = $_SESSION['user_id'];

$res_stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC");
$res_stmt->execute([$user_id]);
$reservations = $res_stmt->fetchAll();

$order_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$order_stmt->execute([$user_id]);
$orders = $order_stmt->fetchAll();

$status_labels = [
    'pending'   => ['label' => 'Në Pritje',  'badge' => 'badge-pending'],
    'confirmed' => ['label' => 'Konfirmuar', 'badge' => 'badge-confirmed'],
    'cancelled' => ['label' => 'Anuluar',    'badge' => 'badge-cancelled'],
    'completed' => ['label' => 'Përfunduar', 'badge' => 'badge-completed'],
];

include '../includes/header.php';
?>

<div class="page-wrapper">
    <h1 class="page-title">Mirë se vini, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <p class="page-subtitle">Këtu mund të shikoni rezervimet dhe porositë tuaja.</p>

    <div style="display:flex; gap:2.5rem; flex-wrap:wrap;">

        <!-- Reservations -->
        <div style="flex:1; min-width:300px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:400; font-size:1.5rem; margin-bottom:1.25rem;">
                Rezervimet
            </h2>
            <?php if ($reservations): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Ora</th>
                            <th>Persona</th>
                            <th>Statusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $r): ?>
                        <?php $s = $status_labels[$r['status']] ?? ['label' => $r['status'], 'badge' => 'badge-pending']; ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d M Y', strtotime($r['reservation_date']))) ?></td>
                            <td><?= htmlspecialchars(substr($r['reservation_time'], 0, 5)) ?></td>
                            <td><?= (int)$r['guests'] ?></td>
                            <td><span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-mid" style="margin-bottom:1rem;">Nuk keni rezervime.</p>
                <a href="reservations.php" class="btn btn-secondary">Rezervo Tani</a>
            <?php endif; ?>
        </div>

        <!-- Orders -->
        <div style="flex:1; min-width:300px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:400; font-size:1.5rem; margin-bottom:1.25rem;">
                Porositë
            </h2>
            <?php if ($orders): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Totali</th>
                            <th>Statusi</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <?php $s = $status_labels[$o['status']] ?? ['label' => $o['status'], 'badge' => 'badge-pending']; ?>
                        <tr>
                            <td>#<?= (int)$o['id'] ?></td>
                            <td>€<?= number_format($o['total_amount'], 2) ?></td>
                            <td><span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span></td>
                            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-mid" style="margin-bottom:1rem;">Nuk keni porosi.</p>
                <a href="menu.php" class="btn btn-secondary">Shiko Menunë</a>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
