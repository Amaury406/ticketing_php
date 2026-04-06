<?php
// new-ticket.php - VERSION SIMPLIFIÉE
require_once 'config.php';
requireLogin();

$pdo = connectDB();

// Catégories fixes (pas besoin de table séparée)
$categories = [
    'matériel' => 'Problèmes matériel (ordinateur, imprimante, écran...)',
    'logiciel' => 'Problèmes logiciels (Windows, Office, applications...)',
    'réseau' => 'Problèmes réseau (WiFi, internet, connexion...)',
    'autre' => 'Autres problèmes'
];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = trim($_POST['titre']);
    $categorie = $_POST['categorie'];
    $priorite = $_POST['priorite'];
    $description = trim($_POST['description']);
    
    if(empty($titre) || empty($description)) {
        $error = "Le titre et la description sont obligatoires";
    } else {
        $sql = "INSERT INTO tickets (titre, description, utilisateur_id, categorie, priorite) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titre, $description, $_SESSION['user_id'], $categorie, $priorite]);
        
        $success = "✅ Ticket créé avec succès !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>➕ Créer un nouveau ticket</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="tickets.php" class="btn btn-primary">Voir mes tickets</a>
            <a href="new-ticket.php" class="btn btn-outline-primary">Créer un autre ticket</a>
        <?php else: ?>
        
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre du problème *</label>
                <input type="text" class="form-control" id="titre" name="titre" 
                       placeholder="Ex: Mon ordinateur ne démarre plus" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="categorie" class="form-label">Catégorie *</label>
                    <select class="form-select" id="categorie" name="categorie" required>
                        <option value="">Choisir une catégorie</option>
                        <?php foreach($categories as $key => $label): ?>
                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="priorite" class="form-label">Priorité *</label>
                    <select class="form-select" id="priorite" name="priorite" required>
                        <option value="basse">🟢 Basse</option>
                        <option value="moyenne" selected>🟡 Moyenne</option>
                        <option value="haute">🟠 Haute</option>
                        <option value="critique">🔴 Critique</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description détaillée *</label>
                <textarea class="form-control" id="description" name="description" 
                          rows="6" placeholder="Décrivez votre problème en détail..." required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Créer le ticket</button>
            <a href="tickets.php" class="btn btn-secondary">Annuler</a>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>