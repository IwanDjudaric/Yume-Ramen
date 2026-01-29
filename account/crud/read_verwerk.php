<?php
require_once '../../config/config.php';

$producten = [];
$error = '';

try {
    $sql = "SELECT p.*, c.naam as categorie_naam FROM producten p LEFT JOIN categorieen c ON p.categorie_id = c.id ORDER BY p.id DESC";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();
    $producten = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Fout: " . $e->getMessage();
}
