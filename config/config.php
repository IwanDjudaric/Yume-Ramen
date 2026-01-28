<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$servername =  'localhost';
$username =   'Yume_ramen_102381';
$password =   'Yume_ramen_102381';
$dbname = '102381_CRUD';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $PDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $options);
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
