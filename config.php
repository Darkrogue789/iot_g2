<?php
// config.php

// Database credentials
$host = 'localhost';
$dbname = 'iot';
$user = 'postgres';
$pass = 'root';

try {
    // Establish the PDO connection
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    die("Connection failed: " . $e->getMessage());
}
?>
