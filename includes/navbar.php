<nav class="navbar navbar-expand-lg navbar-light bg-light">
   <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
   </button>
   <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">


         <?php if (isset($_SESSION['email'])) { ?>
            <!-- Liens pour utilisateur connecté -->
            <li class="nav-item">
               <a class="nav-link" href="list.php">My Discs</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" href="logout.php">Logout</a>
            </li>
         <?php } else { ?>
            <!-- Liens pour utilisateur non connecté -->
            <li class="nav-item active">
               <a class="nav-link" href="accueil.php">Home</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" href="login.php">Login</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" href="user-add.php">Inscription</a>
            </li>
         <?php } ?>
      </ul>
   </div>
</nav>