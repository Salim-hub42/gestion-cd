<?php

include('includes/config.php');
include('includes/function-pdo.php');

// Vérifier si le formulaire a été soumis et double vérification des champs avec required dans le HTML
if (isset($_POST['email']) && isset($_POST['password'])) {

   // Récupérer les données
   $email = $_POST['email'];
   $password = $_POST['password'];

   // Hassher le mot de passe
   $crypt_password = password_hash($password, PASSWORD_BCRYPT);

   // Ajouter l'utilisateur en base de données requete sql
   $res = addUser($pdo, $email, $crypt_password);

   // Si réussi, rediriger vers login
   if ($res) {
      $_SESSION['message'] = "Utilisateur créé avec succès. Vous pouvez vous connecter.";
      header('Location: login.php');
      exit();
   } else {
      $_SESSION['error'] = "Erreur lors de la création de l'utilisateur.";
   }
}

include('includes/header.php');
?>



<body>
   <?php
   include('includes/navbar.php');
   ?>

   <div class="container">
      <h1>Création utilisateur</h1>
      <form action="user-add.php" method="POST">
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