<?php
// config.php - VERSION CORRIGÉE
session_start();

// Connexion base de données
function connectDB() {
    try {
        $pdo = new PDO(
            'mysql:host=localhost;port=3307;dbname=ticketing_system;charset=utf8',
            'root',
            '',  // Mot de passe VIDE
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch(PDOException $e) {
        die("Erreur base de données: " . $e->getMessage());
    }
}
// Ajoute ces fonctions dans config.php

// Vérifier si technicien ou admin
function isTechnicien() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'technicien' || $_SESSION['user_role'] == 'admin');
}
// Vérifier si connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Rediriger si non connecté
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Vérifier si admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

// Vérifier si technicien
function isTechnician() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'technicien' || $_SESSION['user_role'] == 'admin');
}
?>