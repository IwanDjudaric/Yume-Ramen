<?php
require_once '../../config/config.php';

$product = null;
$error = '';
$success = '';
$product_id = $_GET['id'] ?? '';

if (empty($product_id)) {
    $error = "Product ID ontbreekt";
} else {
    try {
        $sql = "SELECT * FROM producten WHERE id = ?";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            $error = "Product niet gevonden";
        }
    } catch (PDOException $e) {
        $error = "Fout: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product) {
    $naam = trim($_POST['naam'] ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $prijs = trim($_POST['prijs'] ?? '');
    $categorie_id = trim($_POST['categorie_id'] ?? '');

    if (empty($naam) || empty($beschrijving) || empty($prijs) || empty($categorie_id)) {
        $error = "Alle velden zijn verplicht";
    } else {
        try {
            $sql = "UPDATE producten SET naam = ?, beschrijving = ?, prijs = ?, categorie_id = ? WHERE id = ?";
            $stmt = $PDO->prepare($sql);
            $stmt->execute([$naam, $beschrijving, $prijs, $categorie_id, $product_id]);
            $success = "Product succesvol bijgewerkt";
            
            // Refresh product data
            $sql = "SELECT * FROM producten WHERE id = ?";
            $stmt = $PDO->prepare($sql);
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
        } catch (PDOException $e) {
            $error = "Fout: " . $e->getMessage();
        }
    }
}

// Get categories
$categories = [];
try {
    $sql = "SELECT * FROM categorieen";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Fout bij ophalen categorieën: " . $e->getMessage();
}
