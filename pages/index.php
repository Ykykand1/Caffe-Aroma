<?php
require_once '../db/db_connect.php';
include '../includes/header.php';

// 3 featured products for homepage preview
$featured = $pdo->query("SELECT * FROM products ORDER BY id LIMIT 3")->fetchAll();
?>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <span class="hero-eyebrow">Tiranë, Shqipëri — Që nga 2010</span>

        <h1>Ku çdo gotë flet<br>për <em>art</em> e <em>pasion</em></h1>

        <p class="hero-sub">
            Kafja jonë buron nga fermat më të mira të botës —<br>
            pjekur me durim, servuar me dashuri.
        </p>

        <div class="hero-cta">
            <a href="menu.php" class="btn btn-primary">Shiko Menunë</a>
            <a href="reservations.php" class="btn btn-outline">Rezervo Tavolinë</a>
        </div>
    </div>

    <div class="hero-scroll-hint">Zbrit</div>
</section>

<!-- ============================================================
     STATS
     ============================================================ -->
<section class="stats-bar">
    <div class="fade-up">
        <div class="stat-number">14+</div>
        <div class="stat-label">Vite Përvojë</div>
    </div>
    <div class="fade-up fade-up-d1">
        <div class="stat-number">60+</div>
        <div class="stat-label">Varietete Kafeje</div>
    </div>
    <div class="fade-up fade-up-d2">
        <div class="stat-number">5K+</div>
        <div class="stat-label">Klientë të Kënaqur</div>
    </div>
    <div class="fade-up fade-up-d3">
        <div class="stat-number">100%</div>
        <div class="stat-label">Kokrra Organike</div>
    </div>
</section>

<!-- ============================================================
     ABOUT
     ============================================================ -->
<section class="about-section">
    <div class="about-grid">
        <div class="about-image-wrap fade-up">
            <img
                src="https://images.unsplash.com/photo-1453614512568-c4024d13c247?w=800&q=80"
                alt="Baristja jonë duke punuar"
            >
        </div>
        <div class="about-text fade-up fade-up-d2">
            <span class="section-eyebrow">Rreth Nesh</span>
            <h2 class="section-title">Kafja si art,<br>atmoshfera si shtëpi</h2>
            <p>
                Caffè Aroma lindi nga një ëndërr e thjeshtë: të krijojmë hapësirën
                perfekte ku kafja e mirë takon njerëzit e mirë. Që nga viti 2010,
                kemi shërbyer çdo ditë me të njëjtin pasion.
            </p>
            <p>
                Çdo gotë kafe që shërbejmë kalon nëpër duart e baristave
                të trajnuar, me kokrra të zgjedhura me kujdes nga Ethiopia,
                Colombia dhe El Salvador.
            </p>
            <a href="menu.php" class="btn btn-secondary">Zbulo Menunë</a>
        </div>
    </div>
</section>

<!-- ============================================================
     MENU PREVIEW
     ============================================================ -->
<section class="menu-preview">
    <div class="menu-preview-inner">
        <div class="section-header fade-up">
            <span class="section-eyebrow">Çfarë Ofrojmë</span>
            <h2 class="section-title">Specialitetet Tona</h2>
            <p class="section-body">
                Nga espresso e fortë deri te dolçet më delikate —
                çdo gjë bëhet me dashuri çdo mëngjes.
            </p>
        </div>

        <div class="product-grid">
            <?php foreach ($featured as $i => $p): ?>
            <div class="product-card fade-up fade-up-d<?= $i + 1 ?>">
                <img
                    src="<?= htmlspecialchars($p['image_url']) ?>"
                    alt="<?= htmlspecialchars($p['name']) ?>"
                    class="product-image"
                >
                <div class="product-info">
                    <span class="product-category"><?= htmlspecialchars($p['category']) ?></span>
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <p><?= htmlspecialchars($p['description']) ?></p>
                    <div class="mt-auto">
                        <div class="product-price">€<?= number_format($p['price'], 2) ?></div>
                        <a href="menu.php" class="btn btn-secondary" style="width:100%;text-align:center;">Porosit</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center; margin-top:3rem;" class="fade-up">
            <a href="menu.php" class="btn btn-primary">Shiko të Gjithë Menunë</a>
        </div>
    </div>
</section>

<!-- ============================================================
     GALLERY
     ============================================================ -->
<section class="gallery-section">
    <div class="gallery-inner">
        <div class="section-header fade-up" style="margin-bottom:3rem;">
            <span class="section-eyebrow">Atmosfera</span>
            <h2 class="section-title light">Galeria Jonë</h2>
        </div>

        <div class="gallery-grid">
            <div class="gallery-item fade-up">
                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800&q=80" alt="Kafe me art latte">
            </div>
            <div class="gallery-item fade-up fade-up-d1">
                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600&q=80" alt="Art latte">
            </div>
            <div class="gallery-item fade-up fade-up-d2">
                <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=600&q=80" alt="Brendësia e kafes">
            </div>
            <div class="gallery-item fade-up fade-up-d3">
                <img src="https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=600&q=80" alt="Kafe me avull">
            </div>
            <div class="gallery-item fade-up">
                <img src="https://images.unsplash.com/photo-1524350876685-274059332603?w=800&q=80" alt="Barista punon">
            </div>
            <div class="gallery-item fade-up fade-up-d1">
                <img src="https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=600&q=80" alt="Kokrra kafeje">
            </div>
            <div class="gallery-item fade-up fade-up-d2">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=600&q=80" alt="Ambiente kafeje">
            </div>
            <div class="gallery-item fade-up fade-up-d3">
                <img src="https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=800&q=80" alt="Kafe dhe pasta">
            </div>
            <div class="gallery-item fade-up">
                <img src="https://images.unsplash.com/photo-1507133750040-4a8f57021571?w=600&q=80" alt="Barista derdh kafe">
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     CONTACT
     ============================================================ -->
<section class="contact-section">
    <div class="contact-inner">
        <div class="section-header fade-up">
            <span class="section-eyebrow">Na Gjeni</span>
            <h2 class="section-title">Vizitoni Caffè Aroma</h2>
            <p class="section-body">
                Jemi të hapur çdo ditë dhe presim me padurim t'ju shërbejmë
                gotën tuaj të kafes së preferuar.
            </p>
        </div>

        <div class="contact-cards">
            <div class="contact-card fade-up">
                <div class="contact-icon">📍</div>
                <h4>Adresa</h4>
                <p>Rruga Barrikadave 12<br>Tiranë, Shqipëri</p>
            </div>
            <div class="contact-card fade-up fade-up-d1">
                <div class="contact-icon">🕐</div>
                <h4>Orari</h4>
                <p>E Hënë – E Premte: 08:00–22:00<br>E Shtunë – E Diel: 09:00–23:00</p>
            </div>
            <div class="contact-card fade-up fade-up-d2">
                <div class="contact-icon">📞</div>
                <h4>Kontakti</h4>
                <p>+355 69 123 4567<br>info@caffearoma.al</p>
            </div>
        </div>

        <div style="margin-top:3rem;" class="fade-up">
            <a href="reservations.php" class="btn btn-primary">Rezervo Tani</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
