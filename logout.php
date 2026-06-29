<?php
// Initialize session with parameters matching login.php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => null,
    'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// 1. Unset all session variables
$_SESSION = array();

// 2. Erase the session cookie from the user's browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Fully destroy the server-side file tracking
session_destroy();

// 4. Send them clean back to the login page
header("Location: login.html");
exit;
?>
