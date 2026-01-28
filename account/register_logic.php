<?php
global $PDO;
session_start();
require_once '../config.php';

$errors = [];
$success = '';
$formData = [
    'gebruikersnaam' => '',
    'email' => ''
];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_user'])) {
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';
    $wachtwoordrepeat = $_POST['wachtwoordrepeat'] ?? '';


    $formData['gebruikersnaam'] = $gebruikersnaam;
    $formData['email'] = $email;
    
    // Validation
    if (empty($gebruikersnaam) || empty($email) || empty($wachtwoord) || empty($wachtwoordrepeat)) {
        $errors[] = "Alle velden moeten ingevuld worden";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Het email adres is ongeldig";
    }
    
    if (strlen($wachtwoord) < 6) {
        $errors[] = "Het wachtwoord moet minstens 6 karakters lang zijn";
    }
    
    if ($wachtwoord !== $wachtwoordrepeat) {
        $errors[] = "De wachtwoorden komen niet overeen";
    }

    if (empty($errors)) {
        try {
            $sql = "SELECT * FROM gebruikers WHERE username = ? OR email = ?";
            $stmt = $PDO->prepare($sql);
            $stmt->execute([$gebruikersnaam, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Gebruikersnaam of email bestaat al";
            }
        } catch (PDOException $e) {
            $errors[] = "Database fout: " . $e->getMessage();
        }
    }
    
    // Insert new user if no error
    if (empty($errors)) {
        try {
            $passwordHash = password_hash($wachtwoord, PASSWORD_DEFAULT);
            $sql = "INSERT INTO gebruikers (username, password, email) VALUES (?, ?, ?)";
            $stmt = $PDO->prepare($sql);
            $stmt->execute([$gebruikersnaam, $passwordHash, $email]);
            
            $success = "Account succesvol aangemaakt! Je kunt nu inloggen.";
            
            // Clear form data on success
            $formData = ['gebruikersnaam' => '', 'email' => ''];
            
        } catch (PDOException $e) {
            $errors[] = "Database fout: " . $e->getMessage();
        }
    }
}