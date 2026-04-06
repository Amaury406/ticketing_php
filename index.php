<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Système de Ticketing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand">🎫 Ticketing System</span>
        </div>
    </nav>
    
    <div class="container text-center mt-5">
        <h1>Bienvenue sur le Système de Ticketing</h1>
        <p class="lead">Gérez vos demandes de support simplement</p>
        
        <div class="mt-5">
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php" class="btn btn-primary btn-lg">Tableau de bord</a>
                <a href="logout.php" class="btn btn-outline-danger btn-lg">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-lg me-3">Connexion</a>
                <a href="register.php" class="btn btn-outline-primary btn-lg">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>