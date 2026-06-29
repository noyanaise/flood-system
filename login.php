<?php
// Security headers and session configuration
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data: https:;");

// Secure session configuration
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// Automatic Routing Guard
if (isset($_SESSION['user_role'])) {
    header("Location: index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Dynamic Environment Variables for Railway Cloud / Local XAMPP fallback
    $host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
    $db   = getenv('MYSQLDATABASE') ?: 'railway'; 
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: 'KKnlRsdVlmoSIGLSsKzsFKvCgPmxdYrx';
    $port = getenv('MYSQLPORT') ?: '3306';
    
    $dsn  = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        header("Location: login.html?error=empty");
        exit;
    }

    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userRow && password_verify($password, $userRow['password_hash'])) {
            session_regenerate_id(true); 

            // OTP REMOVED: Instantly elevate user to active authenticated session
            $_SESSION['user_id']   = $userRow['id'];
            $_SESSION['username']  = $userRow['username'];
            $_SESSION['user_role'] = $userRow['role'];

            // Route directly to main system control matrix
            header("Location: index.html");
            exit;
        } else {
            header("Location: login.html?error=failed");
            exit;
        }

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: login.html");
    exit;
}
?>
