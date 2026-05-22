<?php
require_once '../db/db_connect.php';
include '../includes/header.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

$products = $pdo->query("SELECT * FROM products ORDER BY category, name")->fetchAll();


$ratings_raw = $pdo->query(
    "SELECT product_id, ROUND(AVG(rating),1) AS avg, COUNT(*) AS cnt FROM reviews GROUP BY product_id"
)->fetchAll(PDO::FETCH_KEY_PAIR | PDO::FETCH_UNIQUE);


$ratings_stmt = $pdo->query(
    "SELECT product_id, ROUND(AVG(rating),1) AS avg_r, COUNT(*) AS cnt FROM reviews GROUP BY product_id"
);
$ratings = [];
foreach ($ratings_stmt as $r) {
    $ratings[$r['product_id']] = ['avg' => (float)$r['avg_r'], 'cnt' => (int)$r['cnt']];
}

$logged_in = isset($_SESSION['user_id']);
?>

<div class="page-wrapper">
    <h1 class="page-title">Menuja Jonë</h1>
    <p class="page-subtitle">Zgjidhni nga koleksioni ynë i kafeve dhe pastave artizanale.</p>

    <div class="menu-layout">

        <!-- Products column -->
        <div class="menu-products">
            <div class="menu-controls">
                <input
                    type="search"
                    id="menu-search"
                    class="search-input"
                    placeholder="Kërko produkt..."
                    autocomplete="off"
                >
            </div>

            <div class="product-grid">
                <?php foreach ($products as $p):
                    $pid = (int)$p['id'];
                    $avg = $ratings[$pid]['avg'] ?? 0;
                    $cnt = $ratings[$pid]['cnt'] ?? 0;
                    $stars = str_repeat('★', (int)round($avg)) . str_repeat('☆', 5 - (int)round($avg));
                ?>
                <div
                    class="product-card"
                    data-name="<?= htmlspecialchars($p['name']) ?>"
                    data-category="<?= htmlspecialchars($p['category']) ?>"
                >
                    <img
                        src="<?= htmlspecialchars($p['image_url']) ?>"
                        alt="<?= htmlspecialchars($p['name']) ?>"
                        class="product-image"
                    >
                    <div class="product-info">
                        <span class="product-category"><?= htmlspecialchars($p['category']) ?></span>
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <p><?= htmlspecialchars($p['description']) ?></p>

                        <!-- Stars -->
                        <div class="star-display" id="stars-<?= $pid ?>">
                            <span><?= $stars ?></span>
                            <span class="count"><?= $cnt > 0 ? "($avg · $cnt vlerësime)" : 'Pa vlerësim' ?></span>
                        </div>

                        <div class="mt-auto" style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.75rem;">
                            <button
                                class="btn add-to-cart"
                                data-id="<?= $pid ?>"
                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-price="<?= $p['price'] ?>"
                                style="flex:1;"
                            >Shto në Shportë</button>

                            <?php if ($logged_in): ?>
                            <button
                                class="btn btn-ghost btn-sm open-review"
                                data-id="<?= $pid ?>"
                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                title="Lë vlerësim"
                            >★</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cart sidebar -->
        <div class="menu-sidebar">
            <div class="card cart-sticky">
                <h3 style="font-family:'Fraunces',serif; font-weight:400; margin-bottom:1.25rem;">
                    Shporta Juaj
                </h3>
                <div id="cart-items">
                    <p class="text-mid" style="font-size:0.95rem;">Shporta është bosh.</p>
                </div>
                <hr style="border:0; border-top:1px solid #f0ebe4; margin:1.25rem 0;">
                <div style="display:flex; justify-content:space-between; font-weight:600; font-size:1.1rem; margin-bottom:1.25rem;">
                    <span>Totali:</span>
                    <span id="cart-total">€0.00</span>
                </div>
                <?php if ($logged_in): ?>
                    <button id="checkout-btn" class="btn" style="width:100%; display:none;">
                        Paguaj Tani
                    </button>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary" style="display:block; text-align:center;">
                        Hyr për të Paguar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     REVIEW MODAL
     ============================================================ -->
<div id="review-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-box">
        <button class="modal-close" id="close-review" aria-label="Mbyll">&times;</button>
        <p class="section-eyebrow" id="review-category" style="margin-bottom:0.25rem;"></p>
        <h3 class="modal-title" id="modal-title">Lë Vlerësim</h3>
        <p class="text-mid" style="font-size:0.9rem; margin-bottom:0.5rem;">
            Zgjidhni numrin e yjeve dhe lini një koment (opsionale).
        </p>

        <div class="star-picker">
            <input type="radio" name="review-rating" id="r5" value="5">
            <label for="r5" title="5 yje">★</label>
            <input type="radio" name="review-rating" id="r4" value="4">
            <label for="r4" title="4 yje">★</label>
            <input type="radio" name="review-rating" id="r3" value="3">
            <label for="r3" title="3 yje">★</label>
            <input type="radio" name="review-rating" id="r2" value="2">
            <label for="r2" title="2 yje">★</label>
            <input type="radio" name="review-rating" id="r1" value="1">
            <label for="r1" title="1 yll">★</label>
        </div>

        <div class="form-group">
            <label for="review-comment">Komenti (opcionale)</label>
            <textarea
                id="review-comment"
                rows="3"
                placeholder="Shkruani mendimin tuaj..."
            ></textarea>
        </div>

        <div style="display:flex; gap:0.75rem; align-items:center;">
            <button id="submit-review" class="btn">Dërgo Vlerësimin</button>
            <span id="review-msg" style="font-size:0.9rem;"></span>
        </div>
    </div>
</div>

<script src="../assets/js/cart.js"></script>
<script>
/* ------ Review modal ------------------------------------------------ */
(function () {
    const modal      = document.getElementById('review-modal');
    const closeBtn   = document.getElementById('close-review');
    const titleEl    = document.getElementById('modal-title');
    const catEl      = document.getElementById('review-category');
    const commentEl  = document.getElementById('review-comment');
    const submitBtn  = document.getElementById('submit-review');
    const msgEl      = document.getElementById('review-msg');
    let   currentId  = null;

    function openModal(productId, productName) {
        currentId         = productId;
        titleEl.textContent = productName;
        catEl.textContent   = 'Vlerëso produktin';
        commentEl.value     = '';
        msgEl.textContent   = '';
        document.querySelectorAll('input[name=review-rating]')
            .forEach(r => r.checked = false);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.open-review').forEach(btn => {
        btn.addEventListener('click', () =>
            openModal(btn.dataset.id, btn.dataset.name)
        );
    });
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    submitBtn.addEventListener('click', async () => {
        const rating = document.querySelector('input[name=review-rating]:checked')?.value;
        if (!rating) { msgEl.textContent = 'Ju lutemi zgjidhni një vlerësim.'; msgEl.style.color = '#c0392b'; return; }

        submitBtn.disabled = true;
        msgEl.textContent  = 'Duke dërguar...';
        msgEl.style.color  = 'var(--text-mid)';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        try {
            const res  = await fetch('../api/reviews.php', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken,
                },
                body:    JSON.stringify({
                    product_id: parseInt(currentId),
                    rating:     parseInt(rating),
                    comment:    commentEl.value.trim(),
                }),
            });
            const data = await res.json();

            if (data.success) {
                // Update stars on the card
                const starsEl = document.getElementById('stars-' + currentId);
                if (starsEl) {
                    const full   = Math.round(data.avg);
                    const stars  = '★'.repeat(full) + '☆'.repeat(5 - full);
                    starsEl.innerHTML =
                        `<span>${stars}</span><span class="count">(${data.avg} · ${data.count} vlerësime)</span>`;
                }
                msgEl.textContent = '✓ Vlerësimi u ruajt!';
                msgEl.style.color = '#1a5928';
                setTimeout(closeModal, 1400);
            } else {
                msgEl.textContent = data.error || 'Gabim i panjohur.';
                msgEl.style.color = '#c0392b';
            }
        } catch {
            msgEl.textContent = 'Gabim rrjeti. Provoni përsëri.';
            msgEl.style.color = '#c0392b';
        } finally {
            submitBtn.disabled = false;
        }
    });
})();
</script>

<?php include '../includes/footer.php'; ?>
