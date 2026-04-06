<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">🎫 Ticketing</a>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tickets.php">Mes tickets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="new-ticket.php">Nouveau</a>
                </li>
                <?php if($_SESSION['user_role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">Admin</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <span class="navbar-text"><?php echo $_SESSION['user_prenom']; ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Déconnexion</a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</nav>