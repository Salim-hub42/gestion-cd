<?php
error_reporting(E_ALL); // Activer le rapport d'erreurs
ini_set('display_errors', 1);

include('includes/config.php');
include('includes/function-pdo.php');

// Si l'utilisateur est déjà connecté, rediriger vers list.php
if (isset($_SESSION['email'])) {
   header('Location: list.php');
   exit();
}

if (count($_POST) > 0) {
   if (isValid($_POST['email'], $_POST['password'], $pdo)) {
      $_SESSION['email'] = $_POST['email'];
      header('Location: list.php');
      exit();
   } else {
      $_SESSION['error'] = 'Email ou mot de passe incorrect';
      header('Location: login.php');
      exit();
   }
}

include('includes/header.php');
?>

<body>
   <?php
   include('includes/navbar.php');
   ?>

   <div class="container">
      <h1>Page de connexion</h1>
      <?php
      // Afficher le message de succès si présent
      if (isset($_SESSION['message'])) {
         echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
         echo htmlspecialchars($_SESSION['message']);
         echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
         echo '</div>';
         unset($_SESSION['message']); // Supprimer le message après affichage
      }

      // Afficher le message d'erreur si présent
      if (isset($_SESSION['error'])) {
         echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
         echo htmlspecialchars($_SESSION['error']);
         echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
         echo '</div>';
         unset($_SESSION['error']); // Supprimer le message après affichage
      }
      ?>

      <form action="login.php" method="POST">
         <div class="form-group ">
            <label for="exempleInputEmail1" class="form-label">Email</label>
            <input type="email" class="form-control" id="exempleInputEmail1" name="email" required>
         </div>
         <div class="form-group">
            <label for="exempleInputPassword1" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="exempleInputPassword1" name="password" required>
         </div>
         <br>
         <button type="submit" class="btn btn-primary">Se connecter</button>
      </form>
   </div>
   <?php
   include('includes/snippets.php');
   ?>
</body>

</html>