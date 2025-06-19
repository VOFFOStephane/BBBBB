<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "connect.php";
global $conn;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    include __DIR__ . '/auth.php';
    $email = isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? htmlspecialchars($_POST["password"]) : "";
    $error = login($email, $password);
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}
if ($_SESSION['role'] === 'direction' || $_SESSION['role'] === 'secretariat') {
    $sql = "SELECT * FROM students WHERE level = ?";
    $stmt = $conn->prepare($sql);
    $level = 1;
    $stmt->bind_param("i", $level);
    $stmt->execute();
    $result = $stmt->get_result();
    var_dump($result);
    if ($result->num_rows > 0) {
        echo "<table border='1' cellspacing='0' cellpadding='10'>";
        echo "<tr>";
        echo "<th>id</th>";
        echo "<th>nom</th>";
        echo "<th>prenom</th>";
        echo "<th>date_naissance</th>";
        echo "<th>adresse</th>";
        echo "<th>telephone</th>";
        echo "<th>email</th>";
        echo "<th>level</th>";
        echo "<th>convention_pdf</th>";
        echo "<th>paiement_ok</th>";
        echo "<th>sexe</th>";
        echo "<th>entreprise</th>";
        echo "<th>encadrant</th>";
        echo "<th>date_debut_stage</th>";
        echo "<th>date_fin_stage</th>";
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['nom'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['prenom'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['date_naissance'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['adresse'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['telephone'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['email'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['level'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['conventions_pdf'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['paiement_ok'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['sexe'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['entreprise'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['encadrant'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['date_debut_stage'] ?? '—') . "</td>";
            echo "<td>" . htmlspecialchars($row['date_fin_stage'] ?? '—') . "</td>";

        }

    }
    $stmt->close();

} else {
    $sql = "SELECT * FROM students WHERE email = ? and  level = ?";
    $stmt = $conn->prepare($sql);
    $level = 1;
    $stmt->bind_param("si", $_SESSION['email'], $level);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        echo "<table border='1' cellspacing='0' cellpadding='10'>";
        echo "<tr>";
        echo "<th>id</th>";
        echo "<th>Nom</th>";
        echo "<th>Prenom</th>";
        echo "<th>date_naissance</th>";
        echo "<th>adresse</th>";
        echo "<th>telephone</th>";
        echo "<th>email</th>";
        echo "<th>level</th>";
        echo "<th>convention_pdf</th>";
        echo "<th>paiement_ok</th>";
        echo "<th>sexe</th>";
        echo "<th>entreprise</th>";
        echo "<th>encadrant</th>";
        echo "<th>date_debut_stage</th>";
        echo "<th>date_fin_stage</th>";
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td> ". htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row["nom"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["prenom"]) . " </td>";
            echo "<td> " . htmlspecialchars($row["date_naissance"]) . " </td>";
            echo "<td>" . htmlspecialchars($row["adresse"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["telephone"]) . "</td>";
            echo "<td> " . htmlspecialchars($row["email"]) . " </td>";
            echo "<td> " . htmlspecialchars($row["level"]) . " </td>";
            echo "<td> " . htmlspecialchars($row["conventions_pdf"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["paiement_ok"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["sexe"]) . "</td>";
            echo "<td> " . htmlspecialchars($row["entreprise"]) . " </td>";
            echo "<td> " . htmlspecialchars($row["encadrant"]) . " </td>";
            echo "<td> " . htmlspecialchars($row["date_debut_stage"]) . "</td>";
            echo "<td> " . htmlspecialchars($row["date_fin_stage"]) . "</td>";
        }

    }
    $stmt->close();
}
echo "<a href='../treatment/logout.php'>Deconnexion</a>";
var_dump($_SESSION);
