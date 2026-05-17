<?php
// ── CSRF ─────────────────────────────────────────────────────────────
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify(): void {
    $token   = $_POST['csrf_token'] ?? '';
    $session = $_SESSION['csrf_token'] ?? '';
    if (!hash_equals($session, $token)) {
        http_response_code(403);
        die('Kërkesë e pavlefshme (CSRF). Ju lutemi rifreskoni faqen.');
    }
    // Rotate after use
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_header_verify(): void {
    $token   = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $session = $_SESSION['csrf_token'] ?? '';
    if (!hash_equals($session, $token)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'CSRF token i pavlefshëm.']);
        exit;
    }
}

// ── Flash messages ───────────────────────────────────────────────────
function set_flash(string $message, string $type = 'success'): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}
