<?php
// config.sample.php

// Base URL of your site (subfolder or root)
define('BASE_URL', '/fitness');

// Filesystem root of your project
define('BASE_PATH', __DIR__);

// Database credentials (fill in your own locally)
define('DB_HOST',   '127.0.0.1');
define('DB_NAME',   'FITNESS');
define('DB_USER',   'root');
define('DB_PASS',   'your_password_here');

// PDO connection (don’t edit beyond this point)
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

session_start();
