<?php
session_start();
require_once '../db/db_connect.php';
require_once '../includes/csrf.php';

header('Content-Type: application/json');

csrf_header_verify();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['cart']) || empty($input['cart'])) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty.']);
    exit;
}

$cart = $input['cart'];
$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // Fetch real prices from DB — never trust client-sent prices
    $product_ids = array_map(fn($item) => (int)$item['id'], $cart);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $price_stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $price_stmt->execute($product_ids);
    $db_prices = $price_stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => price]

    $total_amount = 0;
    foreach ($cart as $item) {
        $id = (int)$item['id'];
        if (!isset($db_prices[$id])) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'Produkt i pavlefshëm në shportë.']);
            exit;
        }
        $total_amount += $db_prices[$id] * (int)$item['quantity'];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$user_id, $total_amount]);
    $order_id = $pdo->lastInsertId();

    $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $item) {
        $id = (int)$item['id'];
        $item_stmt->execute([$order_id, $id, (int)$item['quantity'], $db_prices[$id]]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Gabim në bazën e të dhënave: ' . $e->getMessage()]);
}
