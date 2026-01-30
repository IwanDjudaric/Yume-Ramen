<?php
session_start();
require_once '../../config/config.php';

$producten = [];
$categories = [];
$error = '';
$searchQuery = '';
$selectedCategory = '';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get search query and category filter
$searchQuery = trim($_GET['search'] ?? '');
$selectedCategory = $_GET['category'] ?? '';

// Get all categories
try {
    $sql = "SELECT * FROM categorieen ORDER BY naam";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching categories: " . $e->getMessage();
}

// Fetch products with search and category filter
try {
    $sql = "SELECT * FROM producten WHERE 1=1";
    $params = [];
    
    // Add search filter
    if (!empty($searchQuery)) {
        $sql .= " AND (naam LIKE ? OR beschrijving LIKE ?)";
        $params[] = '%' . $searchQuery . '%';
        $params[] = '%' . $searchQuery . '%';
    }
    
    // Add category filter
    if (!empty($selectedCategory) && is_numeric($selectedCategory)) {
        $sql .= " AND categorie_id = ?";
        $params[] = $selectedCategory;
    }
    
    $sql .= " ORDER BY id DESC";
    $stmt = $PDO->prepare($sql);
    $stmt->execute($params);
    $producten = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching products: " . $e->getMessage();
}
