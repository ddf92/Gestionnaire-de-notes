<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$user = $_SESSION['user'];
$username = $user['username'];
$role = $user['role']; // Récupérer le rôle de l'utilisateur

$profileImage = $user['profile_image'] ?? null;
$profilePath = $profileImage ? 'uploads/' . $profileImage : 'uploads/default.png';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px 0;
            width: 100%;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
        }
        header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .user-info {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 0.95em;
            color: #fff;
        }
        .user-info a {
            color: #fff;
            text-decoration: underline;
            margin-left: 10px;
            transition: color 0.3s ease;
        }
        .user-info a:hover {
            color: #cceeff;
            text-decoration: none;
        }
        .container {
            width: 90%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 100px; /* plus grand pour éviter le header + lien utilisateur */
        }
        .welcome h2 {
            font-size: 2em;
            color: #4CAF50;
        }
        ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        ul li {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        ul li a {
            display: block;
            width: 80%;
            max-width: 300px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            font-size: 1.2em;
            border-radius: 5px;
            transition: background-color 0.3s;
            text-align: center;
        }
        ul li a:hover {
            background-color: #45a049;
        }
        footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            background-color: #4CAF50;
            color: white;
            font-size: 1em;
            width: 100%;
        }
        .user-info {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 0.95em;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-pic {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

    </style>
</head>
<body>

<header>
    <h1>Bienvenue à la Gestion des Notes</h1>
    <div class="user-info">
    <img src="<?= htmlspecialchars($profilePath) ?>" alt="Profil" class="profile-pic">
    <?php echo htmlspecialchars($username); ?> |
    <a href="settings.php">Paramètres</a>
</div>

</header>

<div class="container">
    <div class="welcome">
        <h2>Bonjour, <?php echo htmlspecialchars($username); ?> !</h2>
        <p>Bienvenue sur la plateforme de gestion des notes. Que souhaitez-vous faire aujourd'hui ?</p>
    </div>

    <ul>
        <li><a href="list_notes.php">Voir les notes</a></li>

        <?php if ($role == 'teacher') : ?>
            <li><a href="add_note.php">Ajouter une note</a></li>
        <?php endif; ?>

        <li><a href="logout.php">Déconnexion</a></li>
    </ul>
</div>

<footer>
    <p>© 2025 Gestion des Notes | Tous droits réservés</p>
</footer>

</body>
</html>

