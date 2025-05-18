<?php
include "config.php";

try {
  
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();
    

    echo "<h2>Liste des utilisateurs</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nom</th><th>Email</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
