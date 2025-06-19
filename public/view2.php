<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "nav.php";
require_once "../connect.php";

global $conn;
$level = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../treatment/auth.php';
    $email = isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? htmlspecialchars($_POST["password"]) : "";
    $error = login($email, $password);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit();
}


$baseFields = [ "nom", "prenom","date_naissance", "adresse", "telephone", "email", "level", "conventions_pdf", "paiement_ok", "sexe", "entreprise", "encadrant", "date_debut_stage", "date_fin_stage"];
if ($_SESSION['role'] === 'secretariat') {
    $fields = $baseFields;
} elseif ($_SESSION['role'] === 'direction') {
    $fields = ["nom", "encadrant" ];
} else { // étudiant
    $fields = $baseFields;
}


if ($_SESSION['role'] === 'direction' ) {
    $sql = "SELECT nom,encadrant,conventions_pdf,entreprise,date_debut_stage,date_fin_stage FROM students WHERE level = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $level);
}elseif ( $_SESSION['role'] === 'secretariat') {
    $sql = "SELECT id,nom,prenom,date_naissance,adresse,telephone,email,level,conventions_pdf,paiement_ok,sexe,entreprise,encadrant,date_debut_stage,date_fin_stage FROM students WHERE level = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $level);
}else
 {
    $sql = "SELECT nom,prenom,date_naissance,adresse,telephone,email,level,conventions_pdf,paiement_ok,sexe,entreprise,encadrant,date_debut_stage,date_fin_stage FROM students WHERE email = ? AND level = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $_SESSION['email'], $level);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='2'><tr>";


    foreach ($fields as $field) {
        echo "<th>" . htmlspecialchars($field) . "</th>";
    }
    if ($_SESSION['role'] === 'direction') {
        echo "<th>statut_du_stage</th>";
    }
    if ($_SESSION['role'] === 'secretariat') {
        echo "<th>statut_du_stage</th>";
        echo "<th>Actions</th>";
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
                if ($_SESSION['role'] === 'secretariat' && $field === "nom") {
                    echo "<td>
                   <form method='post' action='viewmore.php'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                        <button type='submit' style='border:none; background:none; color:blue; text-decoration:underline; cursor:pointer; font-size:inherit;'>" . htmlspecialchars($row['nom']) . "</button>
                   </form>
               </td>";
                }elseif ($_SESSION['role'] !== 'secretariat' && $field === "nom") {
                    echo "<td>".htmlspecialchars($row['nom'])."</td>";
                }
                elseif ($field !== "nom") {
                    echo "<td>" . htmlspecialchars($row[$field] ?? '') . "</td>";
                }
            }
        }


        if ($_SESSION['role'] === 'direction' || $_SESSION['role'] === 'secretariat') {

            $statut = "";

            if (empty($row['entreprise'])) {
                $statut = "Aucun stage";
            } elseif (
                !empty($row['date_debut_stage']) &&
                !empty($row['date_fin_stage']) &&
                !empty($row['conventions_pdf'])
            ) {
                $statut = "Validé";
            } else {
                $statut = "En attente";
            }


            echo "<td>" . htmlspecialchars($statut) . "</td>";
        }
        if ($_SESSION['role'] === 'secretariat') {
            echo "<td>
                    <form method='post' action='updateview.php'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>
                        <button type='submit'>Modifier</button>
                    </form>
                    <form method='post' action='../delete.php' onsubmit=\"return confirm('Voulez-vous vraiment supprimer l’étudiant : " . addslashes($row["nom"]) . " (" . addslashes($row["email"]) . ") ?');\">
                        <input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>
                        <button type='submit'>Delete</button>
                    </form>
                </td>";

        }
        echo "</tr>";

    }

    echo "</table>";
} else {
    echo "Aucune donnée trouvée.";
}

$stmt->close();

?>
