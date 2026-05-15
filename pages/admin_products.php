<?php
require_once '../includes/auth_check.php';
require_admin();
require_once '../db/db_connect.php';

$error = '';
$success = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "Product deleted successfully.";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $image_url = trim($_POST['image_url']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (empty($name) || empty($price) || empty($category)) {
        $error = "Name, price, and category are required.";
    } else {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category=?, image_url=? WHERE id=?");
            if ($stmt->execute([$name, $description, $price, $category, $image_url, $id])) {
                $success = "Product updated successfully.";
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $description, $price, $category, $image_url])) {
                $success = "Product added successfully.";
            }
        }
    }
}

// Fetch products
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

$edit_product = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_product = $stmt->fetch();
}

include '../includes/header.php';
?>

<div class="card" style="margin-bottom: 2rem;">
    <h2><?= $edit_product ? 'Edit Product' : 'Add New Product' ?></h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" action="admin_products.php">
        <?php if ($edit_product): ?>
            <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
        <?php endif; ?>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>" required>
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label>Price</label>
                <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($edit_product['price'] ?? '') ?>" required>
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label>Category</label>
                <input type="text" name="category" value="<?= htmlspecialchars($edit_product['category'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Image URL</label>
            <input type="url" name="image_url" value="<?= htmlspecialchars($edit_product['image_url'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea>
        </div>
        
        <button type="submit" class="btn"><?= $edit_product ? 'Update Product' : 'Add Product' ?></button>
        <?php if ($edit_product): ?>
            <a href="admin_products.php" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="card table-container">
    <h2>Product List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><img src="<?= htmlspecialchars($p['image_url']) ?>" alt="Product" style="width:50px; height:50px; object-fit:cover; border-radius:4px;"></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['category']) ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td>
                        <a href="admin_products.php?edit=<?= $p['id'] ?>" class="btn" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Edit</a>
                        <a href="admin_products.php?delete=<?= $p['id'] ?>" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem; border-color: #dc3545; color: #dc3545;" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
