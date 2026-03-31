<?php
// config/database.php

$host = '127.0.0.1';
$db   = 'smart_scheme';
$user = 'root';
$pass = ''; // Default XAMPP password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If database doesn't exist yet, we can catch it or simply let it fail.
    // In a real production app we'd hide the exact message.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
