<?php
session_start();
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');

if (isset($_GET['id'])) {
   $id = $_GET['id'];
} else {
   $id = 0;
}

// Si suppression d'utilisateur
if (isset($_GET['from']) && $_GET['from'] === 'users') {
   deleteUser($pdo, $id);
   header('Location: user_list.php');
   exit();
}

// Sinon suppression de disque
deleteSoft($pdo, $id);
header('Location: list.php');
exit();
