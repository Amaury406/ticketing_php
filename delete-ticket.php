<?php
// delete-ticket.php - Supprimer un ticket
require_once 'config.php';
requireLogin();

// Vérifier que l'utilisateur est admin
if($_SESSION['user_role'] != 'admin') {
    header("Location: tickets.php");
    exit;
}

if(!isset($_GET['id'])) {
    header("Location: tickets.php");
    exit;
}

$ticket_id = $_GET['id'];
$pdo = connectDB();

// Supprimer le ticket
$stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
$stmt->execute([$ticket_id]);

header("Location: tickets.php?success=Ticket supprimé avec succès");
exit;
?>