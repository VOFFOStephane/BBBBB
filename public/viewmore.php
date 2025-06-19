<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../connect.php";
require_once "nav.php";
global $conn;
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
if(isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "SELECT nom,prenom,date_naissance,adresse,telephone,email,level,conventions_pdf,paiement_ok,sexe,entreprise,encadrant,date_debut_stage,date_fin_stage FROM students WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<div>";
        echo "<p>nom: ".$row['nom']."</p>";
        echo "<p>prenom: ".$row['prenom']."</p>";
        echo "<p>date_naissance: ".$row['date_naissance']."</p>";
        echo "<p>adresse: ".$row['adresse']."</p>";
        echo "<p>telephone: ".$row['telephone']."</p>";
        echo "<p>email: ".$row['email']."</p>";
        echo "<p>level: ".$row['level']."</p>";
        echo "<p> convention_pdf:  <a href='../conventions/" . htmlspecialchars($row['conventions_pdf']) . "' target='_blank'>" . htmlspecialchars($row['conventions_pdf']) . "</a></p>";
        echo "<p>paiement_ok:".$row['paiement_ok']."</p>";
        echo "<p>sexe:".$row['sexe']."</p>";
        echo "<p>entreprise:".$row['entreprise']."</p>";
        echo "<p>encadrant:".$row['encadrant']."</p>";
        echo "<p>date_debut_stage:".$row['date_debut_stage']."</p>";
        echo "<p>date_fin_stage:".$row['date_fin_stage']."</p>";
        echo"</div>";
    }
}else{echo "<p>pas d'id dispo</p>";}
