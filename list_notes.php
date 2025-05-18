<?php
session_start();
include "config.php";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$role = $user['role'];


$students = $role === 'teacher' 
    ? $pdo->query("SELECT id, username FROM users WHERE role = 'student'")->fetchAll() 
    : [];


$subjects = $pdo->query("SELECT id, name FROM subjects")->fetchAll();


$filterStudent = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;
$filterSubject = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : null;


$sql = "
    SELECT g.id, u.username AS student, s.name AS subject, g.grade
    FROM grades g
    JOIN users u ON g.user_id = u.id
    JOIN subjects s ON g.subject_id = s.id
    WHERE 1=1
";

$params = [];

if ($role === 'student') {
    $sql .= " AND g.user_id = ?";
    $params[] = $user['id'];

    if ($filterSubject) {
        $sql .= " AND g.subject_id = ?";
        $params[] = $filterSubject;
    }

} else {
    if ($filterStudent) {
        $sql .= " AND g.user_id = ?";
        $params[] = $filterStudent;
    }
    if ($filterSubject) {
        $sql .= " AND g.subject_id = ?";
        $params[] = $filterSubject;
    }
}

$sql .= " ORDER BY u.username, s.name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$notes = $stmt->fetchAll();

// Moyenne
$total = 0;
$count = count($notes);
foreach ($notes as $note) {
    $total += $note['grade'];
}
$average = $count > 0 ? round($total / $count, 2) : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "header.php"; ?>

<div class="container mt-4">
    <h2 class="mb-4">Liste des notes</h2>

    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-primary">← Retour au menu principal</a>
    </div>

    <!-- Formulaire de filtre -->
    <form method="get" class="row g-3 mb-4">
        <?php if ($role === 'teacher'): ?>
            <div class="col-md-4">
                <label for="student_id" class="form-label">Étudiant :</label>
                <select name="student_id" id="student_id" class="form-select">
                    <option value="">-- Tous --</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['id'] ?>" <?= ($filterStudent == $student['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($student['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="col-md-4">
            <label for="subject_id" class="form-label">Matière :</label>
            <select name="subject_id" id="subject_id" class="form-select">
                <option value="">-- Toutes --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id'] ?>" <?= ($filterSubject == $subject['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($subject['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4 align-self-end">
            <input type="submit" value="Filtrer" class="btn btn-success">
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th>Étudiant</th>
                <th>Matière</th>
                <th>Note</th>
                <?php if ($role === 'teacher'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($notes)): ?>
                <tr>
                    <td colspan="<?= $role === 'teacher' ? 4 : 3 ?>" class="text-center">Aucune note trouvée.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($notes as $note): ?>
                    <tr>
                        <td><?= htmlspecialchars($note['student']) ?></td>
                        <td><?= htmlspecialchars($note['subject']) ?></td>
                        <td><?= htmlspecialchars($note['grade']) ?></td>
                        <?php if ($role === 'teacher'): ?>
                            <td>
                                <a href="edit_note.php?id=<?= $note['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="delete_note.php?id=<?= $note['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')">Supprimer</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($average !== null): ?>
        <div class="alert alert-secondary text-center fw-bold">
            <?php if ($role === 'teacher' && $filterStudent): ?>
                Moyenne de l'élève 
                <strong><?= htmlspecialchars($pdo->query("SELECT username FROM users WHERE id = $filterStudent")->fetchColumn()) ?></strong> :
                <?= $average ?>
            <?php else: ?>
                Moyenne <?= $role === 'student' ? 'de vos notes' : 'des élèves' ?> : <?= $average ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include "footer.php"; ?>
</body>
</html>

