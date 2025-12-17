<?php
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
   header('Location: login.php');
   exit();
}

// Récupérer les informations de l'utilisateur à éditer
if (isset($_GET['id'])) {
   $id = $_GET['id'];
   $result = getUserById($pdo, $id);

   if (!$result) {
      $_SESSION['error'] = "Utilisateur introuvable.";
      header('Location: user_list.php');
      exit();
   }
} else {
   header('Location: user_list.php');
   exit();
}



// Traitement du formulaire après soumission
if (isset($_POST['email']) && !empty($_POST['email'])) {

   // Récupérer les données du formulaire
   $id = $_GET['id'];
   $email = $_POST['email'];
   $password = !empty($_POST['password']) ? $_POST['password'] : null;

   // Mettre à jour l'utilisateur
   $res = updateUser($pdo, $id, $email, $password);

   if ($res) {
      $_SESSION['message'] = "Utilisateur édité avec succès !";
      header('Location: user_list.php');
      exit();
   } else {
      $_SESSION['error'] = "Erreur lors de l'édition de l'utilisateur.";
   }
}


?>


<body>
   <?php include('includes/navbar.php'); ?>

   <div class="container mt-4">
      <h1>éditer un utilisateur</h1>

      <?php
      // Afficher les messages d'erreur
      if (isset($_SESSION['error'])) {
         echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
         unset($_SESSION['error']);
      }
      ?>


      <!-- un peut de html : le formulaire -->
      <form action="edit_user.php?id=<?= $id ?>" method="POST">

         <!-- Champ utilisateur -->
         <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email"
               class="form-control"
               id="email"
               name="email"
               value="<?php echo htmlspecialchars($result['email']); ?>"
               required>
         </div>

         <!-- Champ Mot de passe -->
         <div class="mb-3">
            <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password"
               class="form-control"
               id="password"
               name="password"
               placeholder="Laisser vide pour conserver l'ancien mot de passe">
         </div>



         <!-- Boutons -->
         <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
               <i class="fas fa-save"></i> éditer l'utilisateur
            </button>
            <a href="user_list.php" class="btn btn-secondary">
               <i class="fas fa-times"></i> Annuler
            </a>
         </div>

      </form>
   </div>

   <?php include('includes/snippets.php'); ?>
</body>

</html>