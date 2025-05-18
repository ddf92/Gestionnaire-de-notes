<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$currentImage = $user['profile_image'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = trim($_POST['username'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $madeUpdate = false;

    $wantToChangeUsername = !empty($newUsername) && $newUsername !== $user['username'];
    $wantToChangePassword = !empty($newPassword);

    if ($wantToChangeUsername || $wantToChangePassword) {
        if (empty($currentPassword)) {
            $error = "Veuillez entrer votre mot de passe actuel pour modifier vos informations.";
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $error = "Mot de passe actuel incorrect.";
        } else {
            if ($wantToChangeUsername) {
                $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                $stmt->execute([$newUsername, $user_id]);
                $_SESSION['username'] = $newUsername;
                $_SESSION['user']['username'] = $newUsername;
                $madeUpdate = true;
            }

            if ($wantToChangePassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $user_id]);
                $madeUpdate = true;
            }
        }
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['profile_image']['tmp_name'];
        $originalName = basename($_FILES['profile_image']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($extension, $allowedExtensions)) {
            $newFileName = 'user_' . $user_id . '_' . time() . '.' . $extension;
            $uploadPath = 'uploads/' . $newFileName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                if (!empty($user['profile_image']) && file_exists('uploads/' . $user['profile_image'])) {
                    unlink('uploads/' . $user['profile_image']);
                }
                $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->execute([$newFileName, $user_id]);
                $_SESSION['user']['profile_image'] = $newFileName;
                $madeUpdate = true;
            } else {
                $error = "Erreur lors de l'enregistrement de l'image.";
            }
        } else {
            $error = "Format d'image non autorisé.";
        }
    }

    if (isset($_POST['delete_image']) && $user['profile_image']) {
        if (file_exists('uploads/' . $user['profile_image'])) {
            unlink('uploads/' . $user['profile_image']);
        }
        $stmt = $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
        $stmt->execute([$user_id]);
        $_SESSION['user']['profile_image'] = null;
        $madeUpdate = true;
    }

    if ($madeUpdate && empty($error)) {
        $success = "Mise à jour effectuée avec succès.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paramètres</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button, .delete-button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 10px;
        }
        button {
            background-color: #4caf50;
            color: white;
        }
        .delete-button {
            background-color: #f44336;
            color: white;
        }
        .msg {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #2196f3;
        }
        .profile-preview {
            text-align: center;
            margin-bottom: 10px;
        }
        .profile-preview img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Paramètres du compte</h2>

    <?php if ($success) echo "<p class='msg success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='msg error'>$error</p>"; ?>

    <div class="profile-preview">
        <?php
        $profilePath = $user['profile_image'] ? 'uploads/' . $user['profile_image'] : 'uploads/default.png';
        ?>
        <img src="<?= htmlspecialchars($profilePath) ?>" alt="Photo de profil">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Nouveau nom d'utilisateur" value="<?= htmlspecialchars($_SESSION['username']) ?>">
        <input type="password" name="current_password" placeholder="Mot de passe actuel (si nécessaire)">
        <input type="password" name="new_password" placeholder="Nouveau mot de passe">
        <label>Photo de profil :</label>
        <input type="file" name="profile_image" accept="image/*">
        <button type="submit">Mettre à jour</button>
    </form>

    <?php if ($user['profile_image']) : ?>
        <form method="POST" onsubmit="return confirm('Supprimer la photo de profil ?');">
            <input type="hidden" name="delete_image" value="1">
            <button type="submit" class="delete-button">Supprimer la photo</button>
        </form>
    <?php endif; ?>

    <a href="index.php">⬅ Retour à l'accueil</a>
</div>

</body>
</html>
