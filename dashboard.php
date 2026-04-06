<?php
require_once 'config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Bienvenue <?php echo $_SESSION['user_prenom']; ?> !</h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>🎫</h3>
                        <a href="tickets.php" class="btn btn-primary">Mes tickets</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>➕</h3>
                        <a href="new-ticket.php" class="btn btn-success">Nouveau ticket</a>
                    </div>
                </div>
            </div>
            <?php if($_SESSION['user_role'] == 'admin'): ?>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>👑</h3>
                        <a href="admin.php" class="btn btn-danger">Admin</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>