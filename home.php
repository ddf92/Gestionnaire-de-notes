<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue sur le Gestionnaire de Notes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(120deg, #89f7fe, #66a6ff);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .welcome-box {
            background-color: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }

        .welcome-box h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #0072ff;
        }

        .welcome-box p {
            font-size: 1.2rem;
            color: #444;
        }

        .btn-connexion {
            margin-top: 30px;
            padding: 12px 30px;
            font-size: 1.1rem;
            border: none;
            border-radius: 30px;
            background-color: #0072ff;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-connexion:hover {
            background-color: #005fd3;
        }

        .illustration {
            width: 150px;
            margin-bottom: 20px;
        }

        @media (max-width: 576px) {
            .welcome-box h1 {
                font-size: 2rem;
            }
            .welcome-box p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="welcome-box">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135755.png" alt="Illustration étudiant" class="illustration">
    <h1>Bienvenue !</h1>
    <p>Bienvenue sur votre gestionnaire de notes. Connectez-vous pour accéder à votre espace personnel.</p>
    <a href="login.php" class="btn btn-connexion">Se connecter</a>
</div>

</body>
</html>
