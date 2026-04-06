<?php
require_once 'config.php';

// Traitement du formulaire
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = connectDB();
    
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    // Validation simple
    $errors = [];
    
    if(empty($nom)) $errors[] = "Le nom est requis";
    if(empty($email)) $errors[] = "L'email est requis";
    if(strlen($password) < 6) $errors[] = "Le mot de passe doit faire 6 caractères minimum";
    if($password != $confirm) $errors[] = "Les mots de passe ne correspondent pas";
    
    // Vérifier si email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if($stmt->rowCount() > 0) {
        $errors[] = "Cet email est déjà utilisé";
    }
    
    // Si pas d'erreurs, créer l'utilisateur
    if(empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $hashedPassword]);
        
        $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Créer un compte</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($errors)): ?>
                            <?php foreach($errors as $error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary">Se connecter</a>
                            </div>
                        <?php else: ?>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe *</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmer le mot de passe *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="login.php">Déjà un compte ? Se connecter</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>