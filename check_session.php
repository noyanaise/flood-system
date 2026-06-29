<?php
// Secure session configuration MUST match your login file parameters perfectly!
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => null,
    'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

header('Content-Type: application/json');

// Checks if session contains valid access keys matching active logging rules
if (isset($_SESSION['user_role'])) {
    // Synchronize both session keys so that auth-guard.js gets the role parameter
    $_SESSION['role'] = $_SESSION['user_role']; 

    echo json_encode([
        'logged_in' => true,
        'username'  => $_SESSION['username'],
        'role'      => $_SESSION['user_role']
    ]);
} else {
    echo json_encode([
        'logged_in' => false
    ]);
}
exit;
?>
