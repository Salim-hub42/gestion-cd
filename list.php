<?php
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');
if (!isset($_SESSION['email'])) {
   header('Location: login.php');
   exit();
}

$disques = [];
$disques = getDisques($pdo);




?>

<body>
   <?php
   include('includes/navbar.php');
   ?>

   <div class="container">
      <h1>liste de l'album</h1>
      <table class="table">
         <thead>
            <tr>
               <th scope="col"></th>
               <th scope="col">Album</th>
               <th scope="col">Artiste</th>
               <th scope="col">Genre</th>
               <th scope="col">Action</th>
               <th scope="col">Editer</th>

            </tr>
         </thead>
         <tbody>
            <?php
            for ($i = 0; $i < count($disques); $i++) {
            ?>
               <tr>
                  <th scope="row"><?php echo ($i + 1) ?></th>
                  <td><a href="detail.php?id=<?= $disques[$i]['id'] ?>"><?= $disques[$i]['album'] ?></a></td>
                  <td><?= $disques[$i]['artiste'] ?></td>
                  <td><?= $disques[$i]['genre'] ?></td>
                  <td><a href="delete.php?id=<?php echo $disques[$i]['id'] ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer l\'album <?= htmlspecialchars($disques[$i]['album']) ?> ?')">
                        Supprimer
                     </a>
                  </td>
                  <td><a href="edit_album.php?id=<?php echo $disques[$i]['id'] ?>"
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