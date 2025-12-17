<?php
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');
if (!isset($_SESSION['email'])) {
   header('Location: login.php');
   exit();
}

$disques = [];
$users = getUsers($pdo);




?>

<body>
   <?php
   include('includes/navbar.php');
   ?>

   <div class="container">
      <h1>liste des utilisateurs</h1>
      <table class="table">
         <thead>
            <tr>
               <th scope="col"></th>
               <th scope="col">utilisateur</th>
               <th scope="col">Date de création</th>
               <th scope="col">Action</th>


            </tr>
         </thead>
         <tbody>
            <?php
            for ($i = 0; $i < count($users); $i++) {
            ?>
               <tr>
                  <th scope="row"><?php echo ($i + 1) ?></th>
                  <td><a href="detail.php?id=<?= $users[$i]['id'] ?>"><?= $users[$i]['email'] ?></a></td>
                  <td><?= $users[$i]['created_at'] ?></td>
                  <td><a href="delete.php?id=<?php echo $users[$i]['id'] ?>&from=users"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer l\'utilisateur <?= htmlspecialchars($users[$i]['email']) ?> ?')">
                        Supprimer
                     </a>
                  </td>
                  <td><a href="edit_user.php?id=<?php echo $users[$i]['id'] ?>"
                        class="btn btn-primary btn-sm">
                        Éditer
                     </a>
                  </td>
               </tr>


            <?php
            }
            ?>

         </tbody>

      </table>
      <a href="ajout-disque.php"
         class="btn"
         style="background-color: lightgreen; color: #0e0d0dff; border-radius: 50px; border: none; text-decoration: none;">
         Ajouter un Album
      </a>
   </div>
</body>
<?php
include('includes/snippets.php');
?>