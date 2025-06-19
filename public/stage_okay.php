<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../connect.php";
require_once "nav.php";
global $conn;
$level = 1;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../treatment/auth.php';
    $email = isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? htmlspecialchars($_POST["password"]) : "";
    $error = login($email, $password);
}

if (isset($_SESSION['loggedin']) && $_SESSION['role'] == "secretariat") {
    $fields = [ "nom", "prenom","date_naissance", "adresse", "telephone", "email", "level", "conventions_pdf", "paiement_ok", "sexe", "entreprise", "encadrant", "date_debut_stage", "date_fin_stage"];
    $sql = "SELECT id,nom,prenom,date_naissance,adresse,telephone,email,level,conventions_pdf,paiement_ok,sexe,entreprise,encadrant,date_debut_stage,date_fin_stage FROM students WHERE level = ? and (entreprise !='' and entreprise is not null)  and (date_debut_stage is not null) and (date_fin_stage is not null) and (conventions_pdf !='') ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $level);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr>";
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field) . "</th>";
        }
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($fields as $field) {
                if ($field === 'conventions_pdf') {
                    if (!empty($row['conventions_pdf'])) {
                        echo "<td><a href='../conventions/" . htmlspecialchars($row['conventions_pdf']) . "' target='_blank'>" . htmlspecialchars($row['conventions_pdf']) . "</a></td>";
                    } else {
                        echo "<td></td>";
                    }
                } else {
                    echo "<td>" . htmlspecialchars($row[$field] ?? '') . "</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    $stmt->close();
}
else{header('Location: ../index.php');
    exit();
}
