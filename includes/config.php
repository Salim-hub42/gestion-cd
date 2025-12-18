<?php
session_start();

// Détection automatique de l'environnement
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocal = (
    strpos($host, 'localhost') !== false || 
    strpos($host, '127.0.0.1') !== false || 
    strpos($host, '.test') !== false ||
    strpos($host, '.local') !== false
);

if ($isLocal) {
   // Configuration locale (Laragon)
   $dsn = 'mysql:host=localhost;dbname=disco';
   $user = 'root';
   $pass = '';
} else {
   // Configuration serveur distant
   $dsn = 'mysql:host=localhost;dbname=sc3czkq9370_disco';
   $user = 'sc3czkq9370_salimbdd';
   $pass = 'formapedia31';
}

try {
   $pdo = new PDO($dsn, $user, $pass);
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
?>
