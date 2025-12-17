# Guide complet - Gestion des utilisateurs

**Date de création** : 17 décembre 2025

---

## 📋 Analyse de l'existant

Vous avez déjà :
- ✅ Une table `utilisateurs` avec email et password
- ✅ Un système de connexion (login.php)
- ✅ Un système de déconnexion (logout.php)
- ✅ Une fonction d'ajout d'utilisateur (user-add.php)
- ✅ Hachage des mots de passe avec `password_hash()`
- ✅ Vérification avec `password_verify()`

---

## 🎯 Ce qu'il manque pour une gestion complète

### 1. **Liste des utilisateurs** (user-list.php)

**Objectif** : Afficher tous les utilisateurs dans un tableau

**Fonction à ajouter dans function-pdo.php** :
```php
function getUsers($pdo) {
    $sql = 'SELECT id, email, created_at FROM utilisateurs ORDER BY created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

**Structure de la page user-list.php** :
```php
<?php
include('includes/header.php');
include('includes/function-pdo.php');
include('includes/auth-check.php'); // Protection de la page

$users = getUsers($pdo);
?>

<body>
<?php include('includes/navbar.php'); ?>

<div class="container">
    <h1>Gestion des utilisateurs</h1>
    
    <a href="user-add.php" class="btn btn-success mb-3">Ajouter un utilisateur</a>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td>
                    <a href="user-edit.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Éditer</a>
                    <a href="user-delete.php?id=<?= $user['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                       Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('includes/snippets.php'); ?>
</body>
</html>
```

---

### 2. **Édition d'utilisateur** (user-edit.php)

**Objectif** : Modifier l'email et/ou le mot de passe d'un utilisateur

**Fonctions à ajouter dans function-pdo.php** :
```php
function getUserById($pdo, $id) {
    $sql = 'SELECT id, email FROM utilisateurs WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUser($pdo, $id, $email, $password = null) {
    if ($password) {
        // Si nouveau mot de passe fourni
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
```

**Structure de la page user-edit.php** :
```php
<?php
include('includes/header.php');
include('includes/function-pdo.php');
include('includes/auth-check.php'); // Protection de la page

// Récupérer l'ID de l'utilisateur
$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID utilisateur manquant";
    header('Location: user-list.php');
    exit();
}

// Traitement du formulaire
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;
    
    // Validation du mot de passe si fourni
    if ($password && $password !== $confirm_password) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas";
    } else {
        $res = updateUser($pdo, $id, $email, $password);
        
        if ($res) {
            $_SESSION['message'] = "Utilisateur modifié avec succès";
            header('Location: user-list.php');
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification";
        }
    }
}

// Récupérer les données de l'utilisateur
$user = getUserById($pdo, $id);

if (!$user) {
    $_SESSION['error'] = "Utilisateur introuvable";
    header('Location: user-list.php');
    exit();
}
?>

<body>
<?php include('includes/navbar.php'); ?>

<div class="container">
    <h1>Éditer l'utilisateur</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <form action="user-edit.php?id=<?= $id ?>" method="POST">
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        
        <div class="form-group">
            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
        </div>
        
        <br>
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="user-list.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include('includes/snippets.php'); ?>
</body>
</html>
```

---

### 3. **Suppression d'utilisateur** (user-delete.php)

**Objectif** : Supprimer un utilisateur de la base

**Fonction à ajouter dans function-pdo.php** :
```php
function deleteUser($pdo, $id) {
    // Empêcher la suppression de son propre compte
    $currentUserEmail = $_SESSION['email'];
    $sql = 'SELECT email FROM utilisateurs WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && $user['email'] === $currentUserEmail) {
        return false; // Ne pas supprimer son propre compte
    }
    
    $sql = 'DELETE FROM utilisateurs WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return true;
}
```

**Structure de la page user-delete.php** :
```php
<?php
include('includes/header.php');
include('includes/function-pdo.php');
include('includes/auth-check.php'); // Protection de la page

// Récupérer l'ID
$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID utilisateur manquant";
    header('Location: user-list.php');
    exit();
}

// Supprimer l'utilisateur
$res = deleteUser($pdo, $id);

if ($res) {
    $_SESSION['message'] = "Utilisateur supprimé avec succès";
} else {
    $_SESSION['error'] = "Impossible de supprimer cet utilisateur (peut-être votre propre compte)";
}

header('Location: user-list.php');
exit();
```

---

### 4. **Protection des pages** (Middleware)

**Objectif** : Empêcher l'accès aux pages sans authentification

**Créer le fichier includes/auth-check.php** :
```php
<?php
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page";
    header('Location: login.php');
    exit();
}
?>
```

**Utilisation** : Inclure en haut de chaque page à protéger
```php
include('includes/auth-check.php');
```

---

### 5. **Système de rôles** (OPTIONNEL mais recommandé)

**Objectif** : Différencier administrateur et utilisateur simple

**Modification de la base de données** :
```sql
ALTER TABLE utilisateurs ADD COLUMN role ENUM('admin', 'user') DEFAULT 'user';
```

**Fonction de vérification dans function-pdo.php** :
```php
function isAdmin($pdo, $email) {
    $sql = 'SELECT role FROM utilisateurs WHERE email = :email';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user && $user['role'] === 'admin';
}
```

**Créer le fichier includes/admin-check.php** :
```php
<?php
include('includes/auth-check.php'); // Vérifier d'abord la connexion

// Vérifier si l'utilisateur est administrateur
if (!isAdmin($pdo, $_SESSION['email'])) {
    $_SESSION['error'] = "Accès non autorisé. Droits administrateur requis.";
    header('Location: accueil.php');
    exit();
}
?>
```

**Utilisation** : Inclure au début des pages d'administration
```php
include('includes/admin-check.php');
```

---

### 6. **Fonctionnalités avancées** (BONUS)

#### a) **Récupération de mot de passe**

**Étapes** :
1. Page avec formulaire email
2. Génération d'un token unique (avec `bin2hex(random_bytes(32))`)
3. Stockage du token en base avec expiration
4. Envoi par email (avec PHPMailer)
5. Page de réinitialisation avec vérification du token

**Table supplémentaire** :
```sql
CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255),
    token VARCHAR(255),
    expires_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### b) **Double authentification (2FA)**

**Étapes** :
1. Lors de la connexion, générer un code à 6 chiffres
2. Stocker le code en session
3. Envoyer par email
4. Page de vérification du code
5. Connexion finale si code correct

#### c) **Historique des connexions**

**Table supplémentaire** :
```sql
CREATE TABLE login_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);
```

**Fonction à ajouter** :
```php
function logLogin($pdo, $userId) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $sql = 'INSERT INTO login_history (user_id, ip_address, user_agent) 
            VALUES (:user_id, :ip, :user_agent)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id' => $userId,
        'ip' => $ip,
        'user_agent' => $userAgent
    ]);
}
```

#### d) **Pagination pour la liste**

**Fonction améliorée** :
```php
function getUsers($pdo, $limit = 10, $offset = 0) {
    $sql = 'SELECT id, email, created_at 
            FROM utilisateurs 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countUsers($pdo) {
    $sql = 'SELECT COUNT(*) as total FROM utilisateurs';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}
```

---

## 📁 Structure de fichiers recommandée

```
gestion-cd/
├── user-list.php        ← À CRÉER (Liste des utilisateurs)
├── user-add.php         ✅ Existant (Ajout)
├── user-edit.php        ← À CRÉER (Édition)
├── user-delete.php      ← À CRÉER (Suppression)
├── login.php            ✅ Existant (Connexion)
├── logout.php           ✅ Existant (Déconnexion)
└── includes/
    ├── auth-check.php   ← À CRÉER (Vérification de connexion)
    ├── admin-check.php  ← À CRÉER (Vérification admin - optionnel)
    └── function-pdo.php ✅ Existant (Ajouter les nouvelles fonctions)
```

---

## 🔐 Recommandations de sécurité

### Déjà implémenté ✅
- ✅ Requêtes préparées (protection SQL Injection)
- ✅ `password_hash()` et `password_verify()`
- ✅ Sessions pour l'authentification

### À ajouter 🔒
1. **Régénération de session** : Après connexion
   ```php
   session_regenerate_id(true);
   ```

2. **CSRF Protection** : Ajouter des tokens dans les formulaires
   ```php
   // Génération
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
   
   // Dans le formulaire
   <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
   
   // Vérification
   if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
       die('Token CSRF invalide');
   }
   ```

3. **XSS Protection** : Toujours utiliser `htmlspecialchars()`
   ```php
   echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
   ```

4. **Validation d'email**
   ```php
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       $_SESSION['error'] = "Email invalide";
   }
   ```

5. **Limitation des tentatives de connexion**
   - Compter les échecs en session
   - Bloquer après 5 tentatives
   - Ajouter un délai ou CAPTCHA

6. **HTTPS** : Toujours utiliser HTTPS en production
   ```php
   // Forcer HTTPS
   if ($_SERVER['HTTPS'] !== 'on') {
       header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
       exit();
   }
   ```

---

## 🎨 Interface utilisateur - Menu de navigation

**Modifier includes/navbar.php** pour ajouter :

```php
<?php if (isset($_SESSION['email'])): ?>
    <li class="nav-item">
        <a class="nav-link" href="list.php">Mes disques</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="user-list.php">Gestion utilisateurs</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="logout.php">Déconnexion (<?= htmlspecialchars($_SESSION['email']) ?>)</a>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="login.php">Connexion</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="user-add.php">Inscription</a>
    </li>
<?php endif; ?>
```

---

## 🚀 Ordre d'implémentation recommandé

### Phase 1 : Fonctionnalités de base
1. ✅ **Créer includes/auth-check.php**
2. ✅ **Ajouter fonction getUsers()** dans function-pdo.php
3. ✅ **Créer user-list.php** (liste avec boutons éditer/supprimer)
4. ✅ **Ajouter fonction deleteUser()** dans function-pdo.php
5. ✅ **Créer user-delete.php** (suppression)
6. ✅ **Ajouter fonctions getUserById() et updateUser()** dans function-pdo.php
7. ✅ **Créer user-edit.php** (édition)
8. ✅ **Protéger toutes les pages** avec auth-check.php

### Phase 2 : Améliorations
9. **Ajouter système de rôles** (admin/user)
10. **Créer admin-check.php** pour protéger pages admin
11. **Améliorer messages de succès/erreur**
12. **Ajouter validation JavaScript** côté client

### Phase 3 : Fonctionnalités avancées (optionnel)
13. **Récupération de mot de passe**
14. **Double authentification (2FA)**
15. **Historique des connexions**
16. **Pagination**
17. **Recherche/filtrage des utilisateurs**

---

## 📝 Checklist de validation

Avant de considérer la gestion des utilisateurs comme complète :

- [ ] Un utilisateur peut se connecter
- [ ] Un utilisateur peut se déconnecter
- [ ] Un admin peut voir la liste des utilisateurs
- [ ] Un admin peut ajouter un utilisateur
- [ ] Un admin peut modifier un utilisateur (email + mot de passe)
- [ ] Un admin peut supprimer un utilisateur (sauf lui-même)
- [ ] Les pages sont protégées (redirection si non connecté)
- [ ] Les mots de passe sont hachés
- [ ] Messages de succès/erreur s'affichent correctement
- [ ] Protection XSS (htmlspecialchars sur les affichages)
- [ ] Protection CSRF (tokens dans les formulaires)
- [ ] Validation des emails
- [ ] Impossible de supprimer son propre compte

---

## 🆘 Problèmes courants et solutions

### Problème 1 : "Headers already sent"
**Cause** : Affichage avant `header()`  
**Solution** : Vérifier qu'il n'y a pas d'espace ou d'echo avant les redirections

### Problème 2 : Session non persistante
**Cause** : `session_start()` manquant  
**Solution** : Vérifier que header.php contient bien `session_start()`

### Problème 3 : Mot de passe ne fonctionne pas
**Cause** : Comparaison directe au lieu de `password_verify()`  
**Solution** : Toujours utiliser `password_verify($password, $hash)`

### Problème 4 : Suppression de son propre compte
**Cause** : Pas de vérification dans deleteUser()  
**Solution** : Comparer l'email de la session avec celui de l'utilisateur à supprimer

### Problème 5 : Table utilisateurs n'existe pas
**Cause** : Base de données pas à jour  
**Solution** : 
```sql
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 📚 Ressources utiles

- **Documentation PHP** : https://www.php.net/manual/fr/
- **PDO** : https://www.php.net/manual/fr/book.pdo.php
- **Password hashing** : https://www.php.net/manual/fr/function.password-hash.php
- **Bootstrap** : https://getbootstrap.com/docs/5.3/
- **Sécurité PHP** : https://www.php.net/manual/fr/security.php

---

## ✅ Conclusion

Cette gestion d'utilisateurs vous permettra de :
- ✅ Authentifier les utilisateurs
- ✅ Gérer les comptes (CRUD complet)
- ✅ Sécuriser votre application
- ✅ Différencier les rôles (admin/user)
- ✅ Suivre les connexions

**Temps estimé d'implémentation** :
- Phase 1 (base) : 2-3 heures
- Phase 2 (améliorations) : 1-2 heures
- Phase 3 (avancé) : 3-5 heures

Bon courage ! 🚀
