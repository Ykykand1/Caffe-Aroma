<?php
require_once '../includes/auth_check.php';
require_login();
require_once '../db/db_connect.php';

$user_id = $_SESSION['user_id'];

$res_stmt = $pdo->prepare(
    "SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC"
);
$res_stmt->execute([$user_id]);
$reservations = $res_stmt->fetchAll();

$order_stmt = $pdo->prepare(
    "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
);
$order_stmt->execute([$user_id]);
$orders = $order_stmt->fetchAll();

// Pre-load all order items for this user's orders (for the detail modal)
$order_ids = array_column($orders, 'id');
$items_map = [];
if ($order_ids) {
    $ph        = implode(',', array_fill(0, count($order_ids), '?'));
    $item_stmt = $pdo->prepare(
        "SELECT oi.order_id, oi.quantity, oi.price, p.name AS product_name
         FROM order_items oi
         JOIN products p ON oi.product_id = p.id
         WHERE oi.order_id IN ($ph)"
    );
    $item_stmt->execute($order_ids);
    foreach ($item_stmt as $row) {
        $items_map[$row['order_id']][] = $row;
    }
}

$status_labels = [
    'pending'   => ['label' => 'Në Pritje',  'badge' => 'badge-pending'],
    'confirmed' => ['label' => 'Konfirmuar', 'badge' => 'badge-confirmed'],
    'cancelled' => ['label' => 'Anuluar',    'badge' => 'badge-cancelled'],
    'completed' => ['label' => 'Përfunduar', 'badge' => 'badge-completed'],
];

include '../includes/header.php';
?>

<div class="page-wrapper">
    <h1 class="page-title">
        Mirë se vini, <?= htmlspecialchars($_SESSION['username']) ?>!
    </h1>
    <p class="page-subtitle">
        Menaxhoni rezervimet dhe porositë tuaja.
        <a href="profile.php" style="color:var(--orange); margin-left:0.5rem;">Ndrysho Profilin →</a>
    </p>

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
                        <?php foreach ($reservations as $r):
                            $s = $status_labels[$r['status']] ?? $status_labels['pending'];
                        ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($r['reservation_date'])) ?></td>
                            <td><?= substr($r['reservation_time'], 0, 5) ?></td>
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
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o):
                            $s = $status_labels[$o['status']] ?? $status_labels['pending'];
                        ?>
                        <tr>
                            <td>#<?= (int)$o['id'] ?></td>
                            <td>€<?= number_format($o['total_amount'], 2) ?></td>
                            <td><span class="badge <?= $s['badge'] ?>"><?= $s['label'] ?></span></td>
                            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                            <td>
                                <button
                                    class="btn btn-ghost btn-sm view-order"
                                    data-id="<?= (int)$o['id'] ?>"
                                >Detajet</button>
                            </td>
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

<!-- Order detail modal -->
<div id="order-modal" class="modal-overlay" role="dialog" aria-modal="true">
    <div class="modal-box" style="max-width:480px;">
        <button class="modal-close" id="close-order">&times;</button>
        <h3 class="modal-title" id="order-modal-title">Detajet e Porosisë</h3>

        <table id="order-items-table" style="margin-top:1.25rem; font-size:0.9rem;">
            <thead>
                <tr>
                    <th>Produkti</th>
                    <th>Sasia</th>
                    <th>Çmimi</th>
                    <th>Subtotali</th>
                </tr>
            </thead>
            <tbody id="order-items-body"></tbody>
        </table>

        <div id="order-total-row" style="display:flex; justify-content:space-between; font-weight:600; font-size:1.05rem; margin-top:1rem; padding-top:0.75rem; border-top:1px solid #f0ebe4;">
            <span>Totali</span>
            <span id="order-total-val"></span>
        </div>
    </div>
</div>

<script>
(function () {
    // Order items keyed by order ID, injected from PHP
    const itemsMap = <?= json_encode($items_map) ?>;

    const modal    = document.getElementById('order-modal');
    const closeBtn = document.getElementById('close-order');
    const titleEl  = document.getElementById('order-modal-title');
    const tbody    = document.getElementById('order-items-body');
    const totalEl  = document.getElementById('order-total-val');

    function openOrder(id) {
        const items = itemsMap[id] || [];
        titleEl.textContent = 'Porosia #' + id;
        tbody.innerHTML = '';
        let total = 0;

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-mid);">Asnjë artikull</td></tr>';
        } else {
            items.forEach(it => {
                const sub = it.price * it.quantity;
                total += sub;
                const tr = document.createElement('tr');
                tr.innerHTML =
                    `<td>${it.product_name}</td>` +
                    `<td>${it.quantity}</td>` +
                    `<td>€${parseFloat(it.price).toFixed(2)}</td>` +
                    `<td>€${sub.toFixed(2)}</td>`;
                tbody.appendChild(tr);
            });
        }

        totalEl.textContent = '€' + total.toFixed(2);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.view-order').forEach(btn => {
        btn.addEventListener('click', () => openOrder(btn.dataset.id));
    });
    closeBtn.addEventListener('click', close);
    modal.addEventListener('click', e => { if (e.target === modal) close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
})();
</script>

<?php include '../includes/footer.php'; ?>
