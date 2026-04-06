<?php
// admin-users.php - Gestion des utilisateurs
require_once 'config.php';
requireLogin();

// Vérifier que l'utilisateur est admin
if($_SESSION['user_role'] != 'admin') {
    header("Location: dashboard.php");
    exit;
}

$pdo = connectDB();
$message = '';
$error = '';

// AJOUTER UN UTILISATEUR
if(isset($_POST['add_user'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = 'admin123'; // Mot de passe par défaut
    
    // Vérifier si email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->rowCount() > 0) {
        $error = "Cet email existe déjà !";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $hash, $role]);
        
        $message = "✅ Utilisateur ajouté avec succès ! (Mot de passe : admin123)";
    }
}

// MODIFIER LE RÔLE
if(isset($_GET['changer_role'])) {
    $user_id = $_GET['id'];
    $nouveau_role = $_GET['role'];
    
    // Ne pas modifier son propre rôle
    if($user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
        $stmt->execute([$nouveau_role, $user_id]);
        $message = "✅ Rôle modifié avec succès !";
    } else {
        $error = "❌ Vous ne pouvez pas modifier votre propre rôle !";
    }
}

// SUPPRIMER UN UTILISATEUR
if(isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Ne pas supprimer son propre compte
    if($user_id != $_SESSION['user_id']) {
        // Supprimer d'abord ses tickets
        $stmt = $pdo->prepare("DELETE FROM tickets WHERE utilisateur_id = ?");
        $stmt->execute([$user_id]);
        
        // Puis supprimer l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$user_id]);
        
        $message = "✅ Utilisateur supprimé avec succès !";
    } else {
        $error = "❌ Vous ne pouvez pas supprimer votre propre compte !";
    }
}

// Récupérer tous les utilisateurs
$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY date_creation DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin.php">Panel Admin</a></li>
                <li class="breadcrumb-item active">Gestion des utilisateurs</li>
            </ol>
        </nav>
        
        <h2>👥 Gestion des utilisateurs</h2>
        
        <!-- Messages -->
        <?php if($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire d'ajout -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">➕ Ajouter un utilisateur</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="nom" class="form-control" placeholder="Nom" required>
                    </div>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-select" required>
                            <option value="utilisateur">👤 Utilisateur</option>
                            <option value="technicien">🔧 Technicien</option>
                            <option value="admin">👑 Admin</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="add_user" class="btn btn-success w-100">
                            ➕
                        </button>
                    </div>
                </form>
                <small class="text-muted mt-2 d-block">
                    📝 Le mot de passe par défaut est : <strong>admin123</strong>
                </small>
            </div>
        </div>
        
        <!-- Liste des utilisateurs -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">📋 Liste des utilisateurs (<?php echo count($users); ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nom complet</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Date inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                                    <?php if($user['id'] == $_SESSION['user_id']): ?>
                                        <span class="badge bg-info ms-1">Vous</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        switch($user['role']) {
                                            case 'admin': echo 'bg-danger'; break;
                                            case 'technicien': echo 'bg-warning'; break;
                                            default: echo 'bg-info';
                                        }
                                        ?>">
                                        <?php 
                                        if($user['role'] == 'admin') echo '👑 Admin';
                                        elseif($user['role'] == 'technicien') echo '🔧 Technicien';
                                        else echo '👤 Utilisateur';
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['date_creation'])); ?></td>
                                <td>
                                    <!-- Changer rôle -->
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary dropdown-toggle" 
                                                data-bs-toggle="dropdown">
                                            Rôle
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="?changer_role&id=<?php echo $user['id']; ?>&role=utilisateur">
                                                    👤 Utilisateur
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="?changer_role&id=<?php echo $user['id']; ?>&role=technicien">
                                                    🔧 Technicien
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="?changer_role&id=<?php echo $user['id']; ?>&role=admin">
                                                    👑 Admin
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <!-- Supprimer (sauf soi-même) -->
                                    <?php if($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Supprimer cet utilisateur ?')">
                                            🗑️
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled>🗑️</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="admin.php" class="btn btn-secondary">← Retour au panel admin</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>