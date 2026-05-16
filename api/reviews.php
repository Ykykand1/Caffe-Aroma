<?php
session_start();
require_once '../db/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Duhet të jeni të kyçur.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Metodë e gabuar.']);
    exit;
}

$input      = json_decode(file_get_contents('php://input'), true);
$product_id = (int)($input['product_id'] ?? 0);
$rating     = (int)($input['rating']     ?? 0);
$comment    = trim($input['comment']     ?? '');

if ($product_id < 1 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'error' => 'Të dhëna të pavlefshme.']);
    exit;
}

// Verify product exists
$check = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$check->execute([$product_id]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Produkti nuk u gjet.']);
    exit;
}

try {
    // Upsert: one review per user per product
    $stmt = $pdo->prepare(
        "INSERT INTO reviews (user_id, product_id, rating, comment)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)"
    );
    $stmt->execute([$_SESSION['user_id'], $product_id, $rating, $comment]);

    // Return new aggregate
    $agg = $pdo->prepare(
        "SELECT ROUND(AVG(rating),1) AS avg, COUNT(*) AS cnt FROM reviews WHERE product_id = ?"
    );
    $agg->execute([$product_id]);
    $row = $agg->fetch();

    echo json_encode([
        'success' => true,
        'avg'     => (float)$row['avg'],
        'count'   => (int)$row['cnt'],
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Gabim në bazën e të dhënave.']);
}
