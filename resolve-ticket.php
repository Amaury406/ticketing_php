<?php
// resolve-ticket.php - Marquer un ticket comme résolu/fermé
require_once 'config.php';
requireLogin();

// Seul technicien ou admin peut résoudre
if($_SESSION['user_role'] != 'technicien' && $_SESSION['user_role'] != 'admin') {
    header("Location: tickets.php");
    exit;
}

if(!isset($_GET['id'])) {
    header("Location: tickets.php");
    exit;
}

$ticket_id = $_GET['id'];
$pdo = connectDB();

// Vérifier que le ticket existe et est en cours
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND statut = 'en_cours'");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if(!$ticket) {
    header("Location: tickets.php?error=Ticket non trouvé ou non pris en charge");
    exit;
}

// Mettre à jour le ticket
$stmt = $pdo->prepare("UPDATE tickets SET statut = 'ferme', date_modification = NOW() WHERE id = ?");
$stmt->execute([$ticket_id]);

header("Location: view-ticket.php?id=" . $ticket_id . "&success=Ticket marqué comme résolu");
exit;
?>