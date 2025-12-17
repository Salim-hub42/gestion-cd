<?php

function isValid($email, $password, $pdo)
{
   $sql = 'SELECT email,password FROM utilisateurs WHERE email = :email';
   $stmt = $pdo->prepare($sql);
   $stmt->bindParam(':email', $email);
   $stmt->execute();
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   if (count($result) > 0) {
      // Utilise password_verify() pour comparer le mot de passe hashé
      if (password_verify($password, $result[0]['password'])) {
         return true;
      } else {
         return false;
      }
   }
   return false;
}


function getDisques($pdo)
{
   $sql = 'SELECT D.id, D.album, D.artiste, G.genre FROM disques D JOIN genre G ON D.genre = G.id ;';
   $stmt = $pdo->prepare($sql);
   $stmt->execute();
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   return $result;
}

function getDisqueById($pdo, $id)
{
   $sql = 'SELECT D.id, D.album, D.artiste, D.image, G.genre 
           FROM disques D 
           JOIN genre G ON D.genre = G.id 
           WHERE D.id = :id';
   $stmt = $pdo->prepare($sql);
   $stmt->execute(['id' => $id]); // le id est lié ici car WHERE D.id = :id 
   $result = $stmt->fetch(PDO::FETCH_ASSOC);

   return $result;
}

function deleteSoft($pdo, $id)
{
   if ($id > 0) {

      $sql = 'DELETE FROM disques WHERE id = :id';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['id' => $id]); /*$stmt->bindParam(':id', $id);*/

      // Ensuite supprimer le disque
      $sql2 = 'DELETE FROM disques WHERE id = :id';
      $stmt2 = $pdo->prepare($sql2);
      $stmt2->execute(['id' => $id]);

      // sinon utiliser une suppression en cascade dans la base de données avec ON DELETE CASCADE

      return true;
   }
   return false;
}

function delete_chanson($pdo, $id)
{
   if ($id > 0) { // Vérifier que l'ID est existe et valide

      $sql = 'DELETE FROM chanson WHERE id = :id';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['id' => $id]); /*$stmt->bindParam(':id', $id);*/
      return true;
   }
   return false;
}

function addUser($pdo, $email, $password)
{
   $sql = 'INSERT INTO utilisateurs (email, password) VALUES (:email, :password)';
   $stmt = $pdo->prepare($sql);
   $stmt->execute([
      'email' => $email,
      'password' => $password
   ]);
   return true;
}

function addDisque($pdo, $album, $artiste, $genre, $image = null)
{
   $sql = 'INSERT INTO disques (album, artiste, genre, image) VALUES (:album, :artiste, :genre, :image)';
   $stmt = $pdo->prepare($sql);
   $stmt->execute([
      'album' => $album,
      'artiste' => $artiste,
      'genre' => $genre,
      'image' => $image
   ]);
   return true;
}

function getGenres($pdo)
{
   $sql = 'SELECT id, genre FROM genre ORDER BY genre ASC'; // Ajout d'un ordre alphabétique pour une meilleure UX (ASC = Ascendant)
   $stmt = $pdo->prepare($sql);
   $stmt->execute();
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   return $result;
}

function getChansonsByDisqueId($pdo, $disqueId)
{
   $sql = 'SELECT * FROM chanson WHERE disque_id = :disque_id';
   $stmt = $pdo->prepare($sql);
   $stmt->execute(['disque_id' => $disqueId]);
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   return $result;
}


// Fonction pour récupérer les informations d'un disque
function get_disque($id, $pdo)
{
   echo "Album trouvé"; // Ligne de débogage
   $sql = 'SELECT album,artiste,genre FROM disques WHERE id = :id';
   $stmt = $pdo->prepare($sql);
   $stmt->execute(['id' => $id]);
   $result = $stmt->fetch(PDO::FETCH_ASSOC);

   return $result;
}

// Fonction pour éditer un disque
function edit_Disque($pdo, $id, $album, $artiste, $genre)
{
   $sql = 'UPDATE disques SET album = :album, artiste = :artiste, genre = :genre WHERE id = :id';
   $stmt = $pdo->prepare($sql);
   $stmt->execute([
      'id' => $id,
      'album' => $album,
      'artiste' => $artiste,
      'genre' => $genre
   ]);
   return true;
}

function add_chanson($pdo, $titre, $duree, $disque_id, $url_youtube)
{
   $sql = 'INSERT INTO chanson (titre, duree, disque_id, url_youtube) VALUES (:titre, :duree, :disque_id, :url_youtube)';
   $stmt = $pdo->prepare($sql);
   $stmt->execute([
      'titre' => $titre,
      'duree' => $duree,
      'disque_id' => $disque_id,
      'url_youtube' => $url_youtube
   ]);
   return true;
}

// Fonction pour récupérer les informations d'une chanson
function get_chanson($id, $pdo)
{
   $sql = 'SELECT * FROM chanson WHERE id = :id';
   $stmt = $pdo->prepare($sql);
   $stmt->execute(['id' => $id]);
   $result = $stmt->fetch(PDO::FETCH_ASSOC);

   return $result;
}

// Fonction pour éditer une chanson
function update_chanson($pdo, $id, $titre, $duree, $url_youtube)
{
   $sql = 'UPDATE chanson SET titre = :titre, duree = :duree, url_youtube = :url_youtube WHERE id = :id';
   $stmt = $pdo->prepare($sql);
   $stmt->execute([
      'id' => $id,
      'titre' => $titre,
      'duree' => $duree,
      'url_youtube' => $url_youtube
   ]);
   return true;
}

function getUsers($pdo)
{
   $sql = 'SELECT id, email, created_at FROM utilisateurs ORDER BY created_at DESC';
   $stmt = $pdo->prepare($sql);
   $stmt->execute();
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteUser($pdo, $id)
{
   if ($id > 0) {
      $sql = 'DELETE FROM utilisateurs WHERE id = :id';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['id' => $id]);
      return true;
   }
   return false;
}

function getUserById($pdo, $id)
{
   $sql = 'SELECT id, email FROM utilisateurs WHERE id = :id';
   $stmt = $pdo->prepare($sql);
   $stmt->execute(['id' => $id]);
   return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUser($pdo, $id, $email, $password = null)
{
   if ($password) {
      // Si nouveau mot de passe fourni, le hacher
      $hashed = password_hash($password, PASSWORD_BCRYPT);
      $sql = 'UPDATE utilisateurs SET email = :email, password = :password WHERE id = :id';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['id' => $id, 'email' => $email, 'password' => $hashed]);
   } else {
      // Sinon, modifier uniquement l'email
      $sql = 'UPDATE utilisateurs SET email = :email WHERE id = :id';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['id' => $id, 'email' => $email]);
   }
   return true;
}

function add_user($pdo, $email, $password)
{
   $sql = 'INSERT INTO utilisateurs (email, password) VALUES (:email, :password)';
   $stmt = $pdo->prepare($sql);
   $stmt->execute([
      'email' => $email,
      'password' => $password
   ]);
   return true;
}

function edit_user($pdo, $id, $email, $password)
{
   $sql = 'UPDATE utilisateurs SET email = :email, password = :password WHERE id = :id';
   $stmt = $pdo->prepare($sql);
   $stmt->execute([
      'id' => $id,
      'email' => $email,
      'password' => $password
   ]);
   return true;
}
