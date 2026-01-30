<?php
require_once '../../config/config.php';

$error = '';
$success = '';
$naam = '';
$beschrijving = '';
$prijs = '';
$categorie_id = '';
$categories = [];

// Get categories
try {
    $sql = "SELECT * FROM categorieen";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Fout bij ophalen categorieën: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $prijs = trim($_POST['prijs'] ?? '');
    $categorie_id = trim($_POST['categorie_id'] ?? '');

    if (empty($naam) || empty($beschrijving) || empty($prijs) || empty($categorie_id)) {
        $error = "Alle velden zijn verplicht";
    } elseif (is_numeric($naam)) {
        $error = "Product name cannot be only numbers";
    } elseif (strlen($naam) < 3) {
        $error = "Product name must be at least 3 characters long";
    } elseif (strlen($beschrijving) < 10) {
        $error = "Description must be at least 10 characters long";
    } elseif (!is_numeric($prijs) || $prijs <= 0) {
        $error = "Price must be a positive number";
    } else {
        try {
            $sql = "INSERT INTO producten (naam, beschrijving, prijs, categorie_id) VALUES (?, ?, ?, ?)";
            $stmt = $PDO->prepare($sql);
            $stmt->execute([$naam, $beschrijving, $prijs, $categorie_id]);
            $success = "Product succesvol toegevoegd";
            
            // Clear form
            $naam = '';
            $beschrijving = '';
            $prijs = '';
            $categorie_id = '';
        } catch (PDOException $e) {
            $error = "Fout: " . $e->getMessage();
        }
    }
}
