<?php
include('includes/config.php');
include('includes/header.php');
include('includes/function-pdo.php');



$id = isset($_GET['id']) ? $_GET['id'] : 0; // Récupère l'ID du disque depuis l'URL, ou 0 si non défini
echo "<p style='background:lightblue; padding:10px;'>Salut voici ta selection ! </p>";



$disque = getDisqueById($pdo, $id);

if (!$disque) { // Si le disque n'existe pas, rediriger vers la liste
   header('Location: list.php');
   exit();
}

$chansons = getChansonsByDisqueId($pdo, $disque['id']);





if (!isset($_SESSION['email'])) {
   header('Location: login.php');
   exit();
}

?>

<body>
   <?php include('includes/navbar.php'); ?>

   <div class="container mt-5">
      <h1><?= htmlspecialchars($disque['album']) ?></h1>

      <?php if ($disque) { ?>
         <div class="row">
            <!-- Première card -->
            <div class="col-md-6 mb-4">
               <div class="card h-100">
                  <img src="assets/<?= htmlspecialchars($disque['image']) ?>"
                     class="card-img-top"
                     style="width: 100%; object-fit: cover;"
                     alt="<?= htmlspecialchars($disque['album']) ?>">
                  <div class="card-body">
                     <p class="card-text">
                        <strong>Artiste :</strong> <?= htmlspecialchars($disque['artiste']) ?><br>
                        <strong>Genre :</strong> <?= htmlspecialchars($disque['genre']) ?><br>
                        <strong>Choix n° :</strong> <?= $disque['id'] ?>
                     </p>
                     <a href="list.php" class="btn btn-primary">Retour à la liste</a>
                  </div>
               </div>
            </div>

            <!-- Deuxième card - Liste des chansons -->
            <div class="col-md-6 mb-4">
               <div class="card h-100">
                  <div class="card-body">
                     <h3 class="card-title">Liste des chansons</h3>

                     <?php if (count($chansons) > 0) { ?>
                        <ul class="list-group list-group-flush">
                           <?php foreach ($chansons as $chanson) { ?>
                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                 <a href="<?= htmlspecialchars($chanson['url_youtube']) ?>" target="_blank" rel="noopener noreferrer"> <!-- YouTube ne saura pas d'où vient le visiteur. -->
                                    <span><?= htmlspecialchars($chanson['titre']) ?></span> <!-- Échappe les titres pour éviter les failles XSS. -->
                                 </a>
                                 <div>
                                    <span class="badge bg-primary rounded-pill"><?= $chanson['duree'] ?></span>
                                    <a href="edit_chanson.php?id=<?= $chanson['id'] ?>" class="btn btn-sm btn-primary ms-2">
                                       <i class="fas fa-edit"> Editer</i>
                                    </a>
                                    <a href="delete_chanson.php?id=<?= $chanson['id'] ?>" class="btn btn-sm btn-danger ms-2">
                                       <i class="fas fa-trash"> Supprimer</i>
                                    </a>
                                 </div>
                              </li>
                           <?php } ?>
                        </ul>
                     <?php } else { ?>
                        <p class="text-muted">Aucune chanson enregistrée pour cet album.</p>
                     <?php } ?>

                     <div class="mt-3">
                        <a href="add_chanson.php?id=<?= $disque['id'] ?>" class="btn btn-success">Ajouter une chanson</a>
                     </div>
                  </div>
                  <img src="assets\dbz.jpg" alt="YouTube Logo" style="width: 130px; position: absolute; bottom: 10px; right: 150px; opacity: 1.8;">
                  <i class="fas fa-music" style="font-size: 150px; position: absolute; bottom: 10px; right: 10px; opacity: 0.7; color: #a554dbff;"></i>
               </div>
            </div>
         </div>
      <?php } else { ?>
         <div class="alert alert-danger">Disque introuvable</div>
         <a href="list.php" class="btn btn-primary">Retour à la liste</a>
      <?php } ?>
   </div>

</body>
<?php
include('includes/snippets.php');
?>