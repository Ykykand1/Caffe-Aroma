<?php
require_once '../includes/auth_check.php';
require_admin();
require_once '../db/db_connect.php';

$error   = '';
$success = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) $success = 'Produkti u fshi me sukses.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = (float)$_POST['price'];
    $category    = trim($_POST['category']);
    $image_url   = trim($_POST['image_url']);
    $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (empty($name) || !$price || empty($category)) {
        $error = 'Emri, çmimi dhe kategoria janë të detyrueshme.';
    } elseif ($id > 0) {
        $stmt = $pdo->prepare(
            "UPDATE products SET name=?, description=?, price=?, category=?, image_url=? WHERE id=?"
        );
        if ($stmt->execute([$name, $description, $price, $category, $image_url, $id]))
            $success = 'Produkti u përditësua me sukses.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)"
        );
        if ($stmt->execute([$name, $description, $price, $category, $image_url]))
            $success = 'Produkti u shtua me sukses.';
    }
}

$products    = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$edit_product = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_product = $stmt->fetch();
}

include '../includes/header.php';
?>

<div class="page-wrapper">
    <!-- Add / Edit form -->
    <div class="card" style="margin-bottom:2rem;">
        <h2 style="font-family:'Fraunces',serif; font-weight:400; margin-bottom:1.5rem;">
            <?= $edit_product ? 'Ndrysho Produktin' : 'Shto Produkt të Ri' ?>
        </h2>

        <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <form method="POST" action="admin_products.php">
            <?php if ($edit_product): ?>
                <input type="hidden" name="id" value="<?= (int)$edit_product['id'] ?>">
            <?php endif; ?>

            <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                <div class="form-group" style="flex:1; min-width:180px;">
                    <label>Emri</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>" required>
                </div>
                <div class="form-group" style="flex:1; min-width:120px;">
                    <label>Çmimi (€)</label>
                    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($edit_product['price'] ?? '') ?>" required>
                </div>
                <div class="form-group" style="flex:1; min-width:150px;">
                    <label>Kategoria</label>
                    <input type="text" name="category" value="<?= htmlspecialchars($edit_product['category'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>URL e Fotos</label>
                <input type="url" name="image_url" value="<?= htmlspecialchars($edit_product['image_url'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Përshkrimi</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea>
            </div>

            <div style="display:flex; gap:0.75rem;">
                <button type="submit" class="btn"><?= $edit_product ? 'Përditëso' : 'Shto Produktin' ?></button>
                <?php if ($edit_product): ?>
                    <a href="admin_products.php" class="btn btn-ghost">Anulo</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Product table -->
    <div class="card">
        <h2 style="font-family:'Fraunces',serif; font-weight:400; margin-bottom:1.5rem;">Lista e Produkteve</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Emri</th>
                    <th>Kategoria</th>
                    <th>Çmimi</th>
                    <th>Veprime</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td>
                        <img
                            src="<?= htmlspecialchars($p['image_url']) ?>"
                            alt=""
                            style="width:48px; height:48px; object-fit:cover; border-radius:6px;"
                        >
                    </td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['category']) ?></td>
                    <td>€<?= number_format($p['price'], 2) ?></td>
                    <td style="display:flex; gap:0.5rem;">
                        <a href="admin_products.php?edit=<?= (int)$p['id'] ?>" class="btn btn-ghost btn-sm">Ndrysho</a>
                        <a href="admin_products.php?delete=<?= (int)$p['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Jeni i sigurt që doni ta fshini këtë produkt?')">Fshi</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
