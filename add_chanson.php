<?php
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
   header('Location: login.php');
   exit();
}

// Récupérer les informations de l'ID du disque depuis l'URL
if (isset($_GET['id'])) {
   $id = $_GET['id'];
   $result = get_disque($id, $pdo);
} else {
   header('Location: list.php');
   exit();
}




// Traitement du formulaire après soumission
if (

   isset($_POST['titre']) && !empty($_POST['titre']) &&
   isset($_POST['duree']) && !empty($_POST['duree']) &&
   isset($_POST['disque_id']) && !empty($_POST['disque_id']) &&
   isset($_POST['url_youtube']) && !empty($_POST['url_youtube'])
) {

   // Récupérer les données du formulaire
   $id = $_GET['id'];
   $titre = $_POST['titre'];
   $duree = $_POST['duree'];
   $disque_id = $_POST['disque_id'];
   $url_youtube = $_POST['url_youtube'];



   // Ajouter la chanson en base
   $chanson = add_chanson($pdo, $titre, $duree, $disque_id, $url_youtube);


   if ($chanson) {
      $_SESSION['message'] = "Chanson ajoutée avec succès !";
      header('Location: detail.php?id=' . $disque_id);
      exit();
   } else {
      $_SESSION['error'] = "Erreur lors de l'ajout de la chanson.";
   }
}



?>


<body>
   <?php include('includes/navbar.php'); ?>

   <div class="container mt-4" style="background-color: lightsteelblue; padding: 20px; border-radius: 8px; border-radius: 45px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
      <h1>Ajouter une chanson</h1>
      <h3 class="text-muted mb-4">Album : <?php echo htmlspecialchars($result['album']); ?> -
         <?php echo htmlspecialchars($result['artiste']); ?></h3>

      <?php
      // Afficher les messages d'erreur
      if (isset($_SESSION['error'])) {
         echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
         unset($_SESSION['error']);
      }
      ?>


      <!-- un peut de html : le formulaire -->
      <form action="add_chanson.php?id=<?php echo $id; ?>" method="POST">

         <!-- Champ hidden pour l'ID du disque -->
         <input type="hidden" name="disque_id" value="<?php echo $id; ?>">

         <!-- Champ titre -->
         <div class="mb-3">
            <label for="titre" class="form-label">Titre de la chanson *</label>
            <input type="text"
               class="form-control"
               id="titre"
               name="titre"
               value=""
               required>
         </div>

         <!-- Champ duree -->
         <div class="mb-3">
            <label for="duree" class="form-label">Durée de la chanson *</label>
            <input type="text"
               class="form-control"
               id="duree"
               name="duree"
               value=""
               required>
         </div>

         <!-- Champ URL YouTube -->
         <div class="mb-3">
            <label for="url_youtube" class="form-label">Entrer une URL *</label>
            <input type="text"
               class="form-control"
               id="url_youtube"
               name="url_youtube"
               placeholder="https://www.youtube.com/watch?v=..."
               value=""
               required>
         </div>



         <!-- Boutons -->
         <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
               <i class="fas fa-save"></i> Ajouter la chanson
            </button>
            <a href="detail.php?id=<?php echo $id; ?>" class="btn btn-secondary">
               <i class="fas fa-times"></i> Annuler
            </a>
         </div>

      </form>
   </div>

   <?php include('includes/snippets.php'); ?>
</body>

</html>