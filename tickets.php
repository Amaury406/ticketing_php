<?php
// tickets.php - Version avec prise en charge
require_once 'config.php';
requireLogin();

$pdo = connectDB();
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Requête selon le rôle
if($user_role == 'technicien' || $user_role == 'admin') {
    // Technicien et admin voient tous les tickets
    $sql = "SELECT t.*, u.prenom, u.nom, tech.prenom as tech_prenom, tech.nom as tech_nom 
            FROM tickets t 
            JOIN utilisateurs u ON t.utilisateur_id = u.id 
            LEFT JOIN utilisateurs tech ON t.technicien_id = tech.id 
            ORDER BY t.date_creation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} else {
    // Utilisateur normal ne voit que ses tickets
    $sql = "SELECT t.*, u.prenom, u.nom, tech.prenom as tech_prenom, tech.nom as tech_nom 
            FROM tickets t 
            JOIN utilisateurs u ON t.utilisateur_id = u.id 
            LEFT JOIN utilisateurs tech ON t.technicien_id = tech.id 
            WHERE t.utilisateur_id = ? 
            ORDER BY t.date_creation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
}
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🎫 Gestion des Tickets</h2>
            <a href="new-ticket.php" class="btn btn-primary">➕ Nouveau ticket</a>
        </div>
        
        <?php if(count($tickets) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Utilisateur</th>
                            <th>Catégorie</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Technicien</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tickets as $ticket): ?>
                        <tr>
                            <td>#<?php echo $ticket['id']; ?></td>
                            <td><?php echo htmlspecialchars($ticket['titre']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['prenom'] . ' ' . $ticket['nom']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['categorie']); ?></td>
                            <td>
                                <?php 
                                $couleur = match($ticket['priorite']) {
                                    'haute' => 'danger',
                                    'moyenne' => 'warning',
                                    'basse' => 'success',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?php echo $couleur; ?>">
                                    <?php echo ucfirst($ticket['priorite']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $couleur = match($ticket['statut']) {
                                    'ouvert' => 'success',
                                    'en_cours' => 'warning',
                                    'ferme' => 'secondary',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?php echo $couleur; ?>">
                                    <?php echo $ticket['statut'] == 'ferme' ? 'Fermé' : ucfirst($ticket['statut']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if($ticket['technicien_id']) {
                                    echo htmlspecialchars($ticket['tech_prenom'] . ' ' . $ticket['tech_nom']);
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['date_creation'])); ?></td>
                            <td>
                                <a href="view-ticket.php?id=<?php echo $ticket['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">Voir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <h5>Aucun ticket trouvé</h5>
                <a href="new-ticket.php" class="btn btn-primary mt-2">Créer un ticket</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>