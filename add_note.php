<?php
include "header.php";

// Vérifie que l'utilisateur est bien un enseignant
if ($role !== 'teacher') {
    http_response_code(403);
    echo "Accès refusé. Cette page est réservée aux enseignants.";
    include "footer.php";
    exit();
}

// Récupère les étudiants et les matières
$students = $pdo->query("SELECT id, username FROM users WHERE role = 'student'")->fetchAll();
$subjects = $pdo->query("SELECT id, name FROM subjects")->fetchAll();

$message = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = intval($_POST["user_id"]);
    $subject_id = intval($_POST["subject_id"]);
    $grade = floatval($_POST["grade"]);

    if ($grade < 0 || $grade > 20) {
        $message = "❌ La note doit être entre 0 et 20.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO grades (user_id, subject_id, grade) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $subject_id, $grade])) {
            $message = "✅ Note ajoutée avec succès !";
        } else {
            $message = "❌ Erreur lors de l'ajout de la note.";
        }
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Ajouter une Note</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" class="border rounded p-4 shadow-sm bg-light">
        <div class="mb-3">
            <label class="form-label">Étudiant :</label>
            <select name="user_id" class="form-select" required>
                <option value="">-- Choisir un étudiant --</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Matière :</label>
            <select name="subject_id" class="form-select" required>
                <option value="">-- Choisir une matière --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Note :</label>
            <input type="number" name="grade" step="0.1" min="0" max="20" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="list_notes.php" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>

<?php include "footer.php"; ?>
