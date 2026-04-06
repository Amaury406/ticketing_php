<?php
require_once 'config.php';
requireLogin();

if($_SESSION['user_role'] != 'admin') {
    header("Location: dashboard.php");
    exit;
}

$pdo = connectDB();
$users = $pdo->query("SELECT COUNT(*) as nb FROM utilisateurs")->fetch()['nb'];
$tickets = $pdo->query("SELECT COUNT(*) as nb FROM tickets")->fetch()['nb'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <h1>👑 Panel Administrateur</h1>
        
        <div class="row mt-4">
            <!-- Carte Utilisateurs -->
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h3><?php echo $users; ?></h3>
                        <p>Utilisateurs</p>
                        <a href="admin-users.php" class="btn btn-light">Gérer</a>
                    </div>
                </div>
            </div>
            
            <!-- Carte Tickets -->
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h3><?php echo $tickets; ?></h3>
                        <p>Tickets</p>
                        <a href="tickets.php" class="btn btn-light">Voir</a>
                    </div>
                </div>
            </div>
            
            <!-- Carte Gestion utilisateurs (ajout rapide) -->
            <div class="col-md-4">
                <div class="card text-white bg-secondary">
                    <div class="card-body text-center">
                        <h5>👥 Gestion</h5>
                        <p>Ajouter des utilisateurs</p>
                        <a href="admin-users.php" class="btn btn-light">Ajouter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>