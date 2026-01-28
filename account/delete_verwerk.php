<?php
require_once '../config/config.php';

$product_id = $_GET['id'] ?? '';
$error = '';
$success = false;

if (empty($product_id)) {
    $error = "Product ID ontbreekt";
} else {
    try {
        // Check if product exists
        $sql = "SELECT * FROM producten WHERE id = ?";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            $error = "Product niet gevonden";
        } else {
            // Delete product
            $sql = "DELETE FROM producten WHERE id = ?";
            $stmt = $PDO->prepare($sql);
            $stmt->execute([$product_id]);
            $success = true;
        }
    } catch (PDOException $e) {
        $error = "Fout: " . $e->getMessage();
    }
}

if ($success) {
    header('Location: read.php?success=deleted');
    exit;
}
