<?php
// take-ticket.php - Prendre en charge un ticket
require_once 'config.php';
requireLogin();

// Seul technicien ou admin peut prendre en charge
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

// Vérifier que le ticket existe et est ouvert
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND statut = 'ouvert'");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if(!$ticket) {
    header("Location: tickets.php?error=Ticket non trouvé ou déjà pris en charge");
    exit;
}

// Mettre à jour le ticket
$stmt = $pdo->prepare("UPDATE tickets SET technicien_id = ?, statut = 'en_cours' WHERE id = ?");
$stmt->execute([$_SESSION['user_id'], $ticket_id]);

header("Location: view-ticket.php?id=" . $ticket_id . "&success=Vous avez pris en charge ce ticket");
exit;
?>