<?php
session_start();
require_once '../config/config.php';

$errors = [];
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_gebruiker'])) {
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';
    

    if (empty($gebruikersnaam) || empty($wachtwoord)) {
        $errors[] = "Alle velden moeten ingevuld worden";
    }
    

    if (empty($errors)) {
        try {
            $sql = "SELECT * FROM gebruikers WHERE username = ?";
            $stmt = $PDO->prepare($sql);
            $stmt->execute([$gebruikersnaam]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($wachtwoord, $user['password'])) {
                $_SESSION['gebruikersnaam'] = $user['gebruikersnaam'];
                $_SESSION['user_id'] = $user['id'];
                // regular user
                header('Location: homepage/home.php');
                exit;
            } else {
                // attempt admin login (separate admin_accounts table)
                $adminSql = "SELECT * FROM admins WHERE username = ?";
                $adminStmt = $PDO->prepare($adminSql);
                $adminStmt->execute([$gebruikersnaam]);
                $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

                if ($admin && password_verify($wachtwoord, $admin['password'])) {
                    // set admin session
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['is_admin'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    header('Location: homepage/home.php');
                    exit;
                }

                $errors[] = "Ongeldige gebruikersnaam of wachtwoord";
            }
        } catch (PDOException $e) {
            $errors[] = "Database fout: " . $e->getMessage();
        }
    }
}
