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
   $result = get_disque($id, $pdo);
} else {
   header('Location: list.php');
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
   $id = $_GET['id'];
   $album = $_POST['album'];
   $artiste = $_POST['artiste'];
   $genre = $_POST['genre'];


   // Ajouter le disque en base
   $res = edit_Disque($pdo, $id, $album, $artiste, $genre);

   if ($res) {
      $_SESSION['message'] = "Disque éditer avec succès !";
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
      <h1>éditer un disque</h1>

      <?php
      // Afficher les messages d'erreur
      if (isset($_SESSION['error'])) {
         echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
         unset($_SESSION['error']);
      }
      ?>


      <!-- un peut de html : le formulaire -->
      <form action="edit_album.php?id=<?= $id ?>" method="POST">

         <!-- Champ Album -->
         <div class="mb-3">
            <label for="album" class="form-label">Nom de l'album *</label>
            <input type="text"
               class="form-control"
               id="album"
               name="album"
               value="<?php echo $result['album']; ?>"
               required>
         </div>

         <!-- Champ Artiste -->
         <div class="mb-3">
            <label for="artiste" class="form-label">Artiste *</label>
            <input type="text"
               class="form-control"
               id="artiste"
               name="artiste"
               value="<?php echo $result['artiste']; ?>"
               required>
         </div>

         <!-- Menu déroulant Genre -->
         <div class="mb-3">
            <label for="genre" class="form-label">Genre *</label>
            <select class="form-select" id="genre" name="genre" required>

               <?php foreach ($genres as $g) { ?>
                  <option value="<?php echo $g['id'] ?>"
                     <?php if ($g['id'] == $result['genre']) {
                        echo 'selected'; // Sélectionne le genre actuel du disque
                     } ?>><?= htmlspecialchars($g['genre']) ?></option>
               <?php } ?>
            </select>
         </div>

         <!-- Boutons -->
         <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
               <i class="fas fa-save"></i> éditer le disque
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