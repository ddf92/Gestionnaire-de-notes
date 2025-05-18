<?php
session_start();
include "config.php";

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupère l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Vérifie le rôle
if (!$user || $user['role'] !== 'teacher') {
    http_response_code(403);
    echo "Accès refusé. Cette page est réservée aux enseignants.";
    exit();
}

// Vérifie que l'ID est bien passé
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list_notes.php");
    exit();
}

$id = intval($_GET['id']);

// Vérifie si la note existe avant suppression (optionnel mais propre)
$checkStmt = $pdo->prepare("SELECT * FROM grades WHERE id = ?");
$checkStmt->execute([$id]);
$note = $checkStmt->fetch();

if (!$note) {
    echo "Erreur : note introuvable.";
    exit();
}

// Supprime la note
try {
    $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: list_notes.php");
    exit();
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}