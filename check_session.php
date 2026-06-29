<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => null, // Allows browser to handle local vs cloud routing smoothly
    'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

header('Content-Type: application/json');

// Check both session slots to accommodate standard users
$resolved_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;

if ($resolved_role && isset($_SESSION['username'])) {
    echo json_encode([
        'logged_in' => true,
        'username'  => $_SESSION['username'],
        'role'      => strtolower(trim($resolved_role)) // Ensures auth-guard.js reads "user" or "admin" safely
    ]);
} else {
    echo json_encode([
        'logged_in' => false
    ]);
}
exit;
?>
