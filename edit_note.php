<?php
include "header.php";


if (!$user || $role !== 'teacher') {
    http_response_code(403);
    echo "Accès refusé. Cette page est réservée aux enseignants.";
    exit();
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de la note manquant !");
}

$id = intval($_GET['id']);
$message = "";


try {
    $stmt = $pdo->prepare("SELECT * FROM grades WHERE id = ?");
    $stmt->execute([$id]);
    $note = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$note) {
        die("Note introuvable !");
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_grade = floatval($_POST["grade"]);

    if ($new_grade < 0 || $new_grade > 20) {
        $message = "La note doit être entre 0 et 20 !";
    } else {
        try {
            $updateStmt = $pdo->prepare("UPDATE grades SET grade = ? WHERE id = ?");
            $updateStmt->execute([$new_grade, $id]);
            $message = "Note mise à jour avec succès !";
            $note['grade'] = $new_grade;
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center">Modifier la Note</h2>

    <?php if ($message) : ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nouvelle Note :</label>
            <input type="number" name="grade" step="0.1" min="0" max="20"
                   value="<?= htmlspecialchars($_POST['grade'] ?? $note['grade']) ?>"
                   class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="list_notes.php" class="btn btn-secondary">Annuler</a>
    </form>

    <div class="mt-3 text-center">
        <a href="list_notes.php" class="btn btn-outline-success">← Retour à la liste des notes</a>
    </div>
</div>

<?php include "footer.php"; ?>

