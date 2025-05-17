<?php
/*
 |----------------------------------------------------------
 |  config.php
 |----------------------------------------------------------
 |  • Starts the PHP session (login handling)
 |  • Creates a global PDO object ($pdo) for DB queries
 |  • NEVER commit real passwords to Git – add config.php
 |    to .gitignore.  Instead commit a config.sample.php.
 */

session_start();

$DB_HOST = '127.0.0.1';
$DB_NAME = 'FITNESS';
$DB_USER = 'root';
$DB_PASS = '';          // <-- your local MySQL/XAMPP password

$dsn  = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$opts = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $opts);
} catch (PDOException $e) {
    // In production you’d log to a file and show a pretty error page
    die('Database connection failed: ' . $e->getMessage());
}
