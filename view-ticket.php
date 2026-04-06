<?php
// view-ticket.php - Visualisation d'un ticket
require_once 'config.php';
requireLogin();

if(!isset($_GET['id'])) {
    header("Location: tickets.php");
    exit;
}

$pdo = connectDB();
$ticket_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Récupérer le ticket selon le rôle
if($user_role == 'technicien' || $user_role == 'admin') {
    // Technicien et admin voient tous les tickets
    $sql = "SELECT t.*, u.prenom, u.nom, u.email, tech.prenom as tech_prenom, tech.nom as tech_nom 
            FROM tickets t 
            JOIN utilisateurs u ON t.utilisateur_id = u.id 
            LEFT JOIN utilisateurs tech ON t.technicien_id = tech.id 
            WHERE t.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticket_id]);
} else {
    // Utilisateur normal ne voit que ses tickets
    $sql = "SELECT t.*, u.prenom, u.nom, u.email, tech.prenom as tech_prenom, tech.nom as tech_nom 
            FROM tickets t 
            JOIN utilisateurs u ON t.utilisateur_id = u.id 
            LEFT JOIN utilisateurs tech ON t.technicien_id = tech.id 
            WHERE t.id = ? AND t.utilisateur_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticket_id, $user_id]);
}

$ticket = $stmt->fetch();

if(!$ticket) {
    header("Location: tickets.php?error=Ticket non trouvé ou accès non autorisé");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?php echo $ticket['id']; ?> - Ticketing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            height: 100%;
        }
        .description-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            min-height: 200px;
        }
        .badge-statut {
            font-size: 1rem;
            padding: 8px 15px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <!-- Fil d'Ariane -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="tickets.php">Tickets</a></li>
                <li class="breadcrumb-item active">Ticket #<?php echo $ticket['id']; ?></li>
            </ol>
        </nav>
        
        <!-- En-tête du ticket -->
        <div class="ticket-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">🎫 <?php echo htmlspecialchars($ticket['titre']); ?></h2>
                    <p class="mb-0 mt-2">
                        Créé par <strong><?php echo htmlspecialchars($ticket['prenom'] . ' ' . $ticket['nom']); ?></strong>
                        le <?php echo date('d/m/Y à H:i', strtotime($ticket['date_creation'])); ?>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark badge-statut">
                        📋 Ticket #<?php echo $ticket['id']; ?>
                    </span>
                    <br>
                    <span class="badge 
                        <?php 
                        switch($ticket['statut']) {
                            case 'ouvert': echo 'bg-success'; break;
                            case 'en_cours': echo 'bg-warning'; break;
                            case 'ferme': echo 'bg-secondary'; break;
                            default: echo 'bg-secondary';
                        }
                        ?> badge-statut mt-2">
                        <?php 
                        if($ticket['statut'] == 'ouvert') echo '🟢 Ouvert';
                        elseif($ticket['statut'] == 'en_cours') echo '🟡 En cours';
                        else echo '🔒 Fermé';
                        ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <!-- Colonne gauche : Description -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">📝 Description du problème</h5>
                    </div>
                    <div class="card-body">
                        <div class="description-box">
                            <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Colonne droite : Informations -->
            <div class="col-md-4">
                <div class="info-card shadow-sm">
                    <h5 class="mb-3">📋 Informations</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">ID :</th>
                            <td><strong>#<?php echo $ticket['id']; ?></strong></td>
                        </tr>
                        <tr>
                            <th>Catégorie :</th>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo htmlspecialchars($ticket['categorie']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Priorité :</th>
                            <td>
                                <?php 
                                $couleur_priorite = [
                                    'basse' => 'success',
                                    'moyenne' => 'warning',
                                    'haute' => 'danger'
                                ];
                                $couleur = $couleur_priorite[$ticket['priorite']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $couleur; ?>">
                                    <?php echo ucfirst($ticket['priorite']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Statut :</th>
                            <td>
                                <span class="badge 
                                    <?php 
                                    switch($ticket['statut']) {
                                        case 'ouvert': echo 'bg-success'; break;
                                        case 'en_cours': echo 'bg-warning'; break;
                                        case 'ferme': echo 'bg-secondary'; break;
                                        default: echo 'bg-secondary';
                                    }
                                    ?>">
                                    <?php 
                                    if($ticket['statut'] == 'ouvert') echo 'Ouvert';
                                    elseif($ticket['statut'] == 'en_cours') echo 'En cours';
                                    else echo 'Fermé';
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Créé le :</th>
                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['date_creation'])); ?></td>
                        </tr>
                        <tr>
                            <th>Modifié le :</th>
                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['date_modification'])); ?></td>
                        </tr>
                        <?php if($ticket['technicien_id']): ?>
                        <tr>
                            <th>Technicien :</th>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo htmlspecialchars($ticket['tech_prenom'] . ' ' . $ticket['tech_nom']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Boutons d'action -->
        <div class="mt-4 mb-5">
            <a href="tickets.php" class="btn btn-outline-secondary">← Retour à la liste</a>
            
            <?php if(($user_role == 'technicien' || $user_role == 'admin') && $ticket['statut'] == 'ouvert'): ?>
                <a href="take-ticket.php?id=<?php echo $ticket['id']; ?>" 
                   class="btn btn-warning"
                   onclick="return confirm('Prendre ce ticket en charge ?')">
                    ✋ Prendre en charge
                </a>
            <?php endif; ?>
            
            <?php if(($user_role == 'technicien' || $user_role == 'admin') && $ticket['statut'] == 'en_cours'): ?>
                <a href="resolve-ticket.php?id=<?php echo $ticket['id']; ?>" 
                   class="btn btn-success"
                   onclick="return confirm('Marquer ce ticket comme résolu ?')">
                    ✅ Résoudre
                </a>
            <?php endif; ?>
            
            <?php if($user_role == 'admin' && $ticket['statut'] != 'ferme'): ?>
                <button class="btn btn-danger" 
                        onclick="alert('Fonctionnalité : Supprimer le ticket (à venir)')">
                    🗑️ Supprimer
                </button>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>