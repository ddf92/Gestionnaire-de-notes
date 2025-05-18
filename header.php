<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";


$user = null;
$role = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $role = $user['role'] ?? null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Gestion des Notes</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <li class="nav-item">
          <a class="nav-link" href="list_notes.php">Liste des notes</a>
        </li>

        <?php if ($role === 'teacher'): ?>
        <li class="nav-item">
          <a class="nav-link" href="add_note.php">Ajouter une note</a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link" href="logout.php">Déconnexion</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
