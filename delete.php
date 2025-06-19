<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "connect.php";
global $conn;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
$id= $_POST['id'];
$sql = "SELECT * FROM students WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<p style='color:red;'>Aucun étudiant de niveau 0 trouvé.</p>");
}
$stmt2 = $conn->prepare("DELETE FROM students WHERE id=?");
$stmt2->bind_param("i", $id);
if ($stmt2->execute()) {
    echo "<p style='color:green;'>Étudiants de niveau 0 supprimés avec succès.</p>";
} else {
    echo "<p style='color:red;'>Erreur lors de la suppression : " . htmlspecialchars($stmt2->error) . "</p>";
}

echo "<a href='public/view2.php'><button class='btn'>Retour</button></a>";

// Fermeture des connexions
$stmt2->close();
$stmt->close();
$conn->close();
} else {
    echo "<p style='color:red;'>Requête invalide.</p>";
}
?>