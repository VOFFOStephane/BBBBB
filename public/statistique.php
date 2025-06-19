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
    $sql = "SELECT 
    COUNT(*) AS total_etudiants,
    SUM(CASE 
        WHEN (entreprise != '') 
        AND entreprise IS NOT NULL         
        AND date_debut_stage IS NOT NULL     
        AND date_fin_stage IS NOT NULL 
        AND conventions_pdf !='' THEN 1 
        ELSE 0 
    END) AS etudiants_en_stage,
    SUM(CASE 
        WHEN (entreprise !='')
        AND (date_debut_stage IS NULL OR date_fin_stage IS NULL OR conventions_pdf ='') THEN 1 
        ELSE 0 
    END) AS etudiants_en_attente,
    SUM(CASE 
        WHEN entreprise ='' THEN 1 
        ELSE 0 
    END) AS etudiants_sans_stage
FROM students WHERE level = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $level);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        echo "Nombre total d'etudiants : " . $row["total_etudiants"] . "<br>";
        echo "Nombre d etudiant en stage : " . $row["etudiants_en_stage"] . "<br>";
        echo "Nombre d'etudiants en attente : " . $row["etudiants_en_attente"] . "<br>";
        echo "Nombre d'etudiants sans stage : " . $row["etudiants_sans_stage"] . "<br>";
    }

    $stmt->close();

}