<?php
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
   header('Location: login.php');
   exit();
}

// Récupérer les informations du disque à éditer
if (isset($_GET['id'])) {
   $id = $_GET['id'];
   $result = get_chanson($id, $pdo);
} else {
   header('Location: list.php');
   exit();
}

// Vérifier que la chanson existe
if (!$result) {
   $_SESSION['error'] = "Chanson introuvable.";
   header('Location: list.php');
   exit();
}

// Traitement du formulaire après soumission
if (
   isset($_POST['titre']) && !empty($_POST['titre']) &&
   isset($_POST['duree']) && !empty($_POST['duree']) &&
   isset($_POST['url_youtube']) && !empty($_POST['url_youtube'])
) {
   // Récupérer les données du formulaire
   $id = $_GET['id'];
   $titre = $_POST['titre'];
   $duree = $_POST['duree'];
   $url_youtube = $_POST['url_youtube'];

   // Mettre à jour la chanson en base
   $res = update_chanson($pdo, $id, $titre, $duree, $url_youtube);

   if ($res) {
      $_SESSION['message'] = "Chanson modifiée avec succès !";
      header('Location: detail.php?id=' . $result['disque_id']);
      exit();
   } else {
      $_SESSION['error'] = "Erreur lors de la modification de la chanson.";
   }
}

?>


<body>
   <?php include('includes/navbar.php'); ?>

   <div class="container mt-4">
      <h1>éditer une chanson</h1>

      <?php
      // Afficher les messages d'erreur
      if (isset($_SESSION['error'])) {
         echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
         unset($_SESSION['error']);
      }
      ?>


      <!-- un peut de html : le formulaire -->
      <form action="edit_chanson.php?id=<?php echo $id; ?>" method="POST">

         <!-- Champ titre -->
         <div class="mb-3">
            <label for="titre" class="form-label">Titre de la chanson *</label>
            <input type="text"
               class="form-control"
               id="titre"
               name="titre"
               value="<?php echo htmlspecialchars($result['titre']); ?>"
               required>
         </div>

         <!-- Champ durée -->
         <div class="mb-3">
            <label for="duree" class="form-label">Durée *</label>
            <input type="text"
               class="form-control"
               id="duree"
               name="duree"
               value="<?php echo htmlspecialchars($result['duree']); ?>"
               required>
         </div>

         <!-- Champ URL YouTube -->
         <div class="mb-3">
            <label for="url_youtube" class="form-label">URL YouTube *</label>
            <input type="text"
               class="form-control"
               id="url_youtube"
               name="url_youtube"
               value="<?php echo htmlspecialchars($result['url_youtube']); ?>"
               placeholder="https://www.youtube.com/watch?v=..."
               required>
         </div>

         <!-- Boutons -->
         <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
               <i class="fas fa-save"></i> Modifier la chanson
            </button>
            <a href="detail.php?id=<?php echo $result['disque_id']; ?>" class="btn btn-secondary">
               <i class="fas fa-times"></i> Annuler
            </a>
         </div>

      </form>
   </div>

   <?php include('includes/snippets.php'); ?>
</body>

</html>