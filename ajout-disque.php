<?php
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
   header('Location: login.php');
   exit();
}

// Récupérer la liste des genres pour le formulaire
$genres = getGenres($pdo);

// Traitement du formulaire après soumission
if (
   isset($_POST['album']) && !empty($_POST['album']) &&
   isset($_POST['artiste']) && !empty($_POST['artiste']) &&
   isset($_POST['genre']) && !empty($_POST['genre'])
) {

   // Récupérer les données du formulaire
   $album = $_POST['album'];
   $artiste = $_POST['artiste'];
   $genre = $_POST['genre'];
   $image = null; // Pas de champ image dans le formulaire

   // Ajouter le disque en base
   $res = addDisque($pdo, $album, $artiste, $genre, $image);

   if ($res) {
      $_SESSION['message'] = "Disque ajouté avec succès !";
      header('Location: list.php');
      exit();
   } else {
      $_SESSION['error'] = "Erreur lors de l'ajout du disque.";
   }
}
?>

<body>
   <?php include('includes/navbar.php'); ?>

   <div class="container mt-4">
      <h1>Ajouter un nouveau disque</h1>

      <?php
      // Afficher les messages d'erreur
      if (isset($_SESSION['error'])) {
         echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
         unset($_SESSION['error']);
      }
      ?>


      <!-- un peut de html le formulaire -->
      <form action="ajout-disque.php" method="POST">

         <!-- Champ Album -->
         <div class="mb-3">
            <label for="album" class="form-label">Nom de l'album *</label>
            <input type="text"
               class="form-control"
               id="album"
               name="album"
               placeholder="Ex: Legend"
               required>
         </div>

         <!-- Champ Artiste -->
         <div class="mb-3">
            <label for="artiste" class="form-label">Artiste *</label>
            <input type="text"
               class="form-control"
               id="artiste"
               name="artiste"
               placeholder="Ex: Bob Marley"
               required>
         </div>

         <!-- Menu déroulant Genre -->
         <div class="mb-3">
            <label for="genre" class="form-label">Genre *</label>
            <select class="form-select" id="genre" name="genre" required>
               <option value="">Sélectionner un genre</option>
               <?php foreach ($genres as $g) { ?>
                  <option value="<?php echo $g['id'] ?>"><?= htmlspecialchars($g['genre']) ?></option>
               <?php } ?>
            </select>
         </div>

         <!-- Boutons -->
         <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
               <i class="fas fa-save"></i> Ajouter le disque
            </button>
            <a href="list.php" class="btn btn-secondary">
               <i class="fas fa-times"></i> Annuler
            </a>
         </div>

      </form>
   </div>

   <?php include('includes/snippets.php'); ?>
</body>

</html>