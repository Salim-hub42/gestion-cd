<?php
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');

if (isset($_GET['id'])) { // Vérifier que l'ID de la chanson est présent dans l'URL
   $id = $_GET['id'];

   // Récupérer les infos de la chanson pour connaître le disque_id
   $chanson = get_chanson($id, $pdo);

   if ($chanson) { // Vérifier que la chanson existe
      $disque_id = $chanson['disque_id']; //

      // Supprimer la chanson
      $result = delete_chanson($pdo, $id);

      if ($result) {
         $_SESSION['message'] = "Chanson supprimée avec succès !";
      } else {
         $_SESSION['error'] = "Erreur lors de la suppression de la chanson.";
      }

      // Rediriger vers le détail du disque
      header('Location: detail.php?id=' . $disque_id);
      exit();
   } else {
      $_SESSION['error'] = "Chanson introuvable.";
      header('Location: list.php');
      exit();
   }
} else {
   $_SESSION['error'] = "ID de chanson manquant.";
   header('Location: list.php');
   exit();
}
