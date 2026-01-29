<?php
session_start();
require_once '../../config/config.php';

// Get latest products from database
$producten = [];
try {
    $sql = "SELECT * FROM producten ORDER BY id DESC LIMIT 6";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();
    $producten = $stmt->fetchAll();
} catch (PDOException $e) {
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
