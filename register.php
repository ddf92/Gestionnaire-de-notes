<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $role = $_POST["role"];
    $teacherCode = $_POST["teacher_code"] ?? '';

 
    if ($role === 'teacher' && $teacherCode !== 'PvR9jz6A!2bxP#4yU8fD!1kZ') {
        $error = "❌ Code professeur incorrect. Contactez l'administration.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                $error = "⚠️ Ce nom d'utilisateur est déjà pris.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $role]);

                $_SESSION["success"] = "✅ Inscription réussie. Vous pouvez vous connecter.";
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #dce35b, #45b649);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            width: 320px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: 0.3s;
        }
        input:focus, select:focus {
            border-color: #4CAF50;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
        .success {
            color: green;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Inscription</h2>

    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <?php if (isset($_SESSION['success'])) { echo "<div class='success'>" . $_SESSION['success'] . "</div>"; unset($_SESSION['success']); } ?>

    <form method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>

        <select name="role" id="role" required>
            <option value="">-- Choisissez un rôle --</option>
            <option value="student">Élève</option>
            <option value="teacher">Professeur</option>
        </select>

        <div id="teacher-code" style="display: none;">
            <input type="text" name="teacher_code" placeholder=" Code professeur ">
        </div>

        <button type="submit">S'inscrire</button>
    </form>
</div>

<script>
    const roleSelect = document.getElementById('role');
    const teacherCodeDiv = document.getElementById('teacher-code');

    roleSelect.addEventListener('change', function () {
        teacherCodeDiv.style.display = this.value === 'teacher' ? 'block' : 'none';
    });
</script>

</body>
</html>

