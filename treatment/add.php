<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST);
    $errors = array();
    function verifierFormatDate($date) {
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }


    $nom = isset($_POST['nom']) ? ucwords(strtolower(htmlspecialchars(trim($_POST['nom'])))) : '';
    $prenom = isset($_POST['prenom']) ? ucwords(strtolower(htmlspecialchars(trim($_POST['prenom'])))) : '';
    $date_naissance = isset($_POST['date_naissance'])?htmlspecialchars(trim($_POST['date_naissance'])):null;
    $adresse = isset($_POST['adresse'])?htmlspecialchars(trim($_POST['adresse'])):'';
    $telephone = isset($_POST['telephone'])?htmlspecialchars(trim($_POST['telephone'])):'';
    $email = isset($_POST['email'])?htmlspecialchars(trim($_POST['email'])):'';
    $paiement_ok = isset($_POST['paiement_ok'])?htmlspecialchars(trim($_POST['paiement_ok'])):'';
    $sexe = isset($_POST['sexe'])?htmlspecialchars(trim($_POST['sexe'])):'';
    $encadrant = isset($_POST['encadrant']) ? ucwords(strtolower(trim($_POST['encadrant']))) : '';
    $entreprise = isset($_POST['entreprise']) ? ucwords(strtolower(trim($_POST['entreprise']))) : '';
    $date_debut_stage = (isset($_POST['date_debut_stage']) && $_POST['date_debut_stage'] !== "") ? htmlspecialchars(trim($_POST['date_debut_stage'])):null;
    $date_fin_stage = $_POST['date_fin_stage']?htmlspecialchars($_POST['date_fin_stage']): null;
    $password = hash('sha256', 'eafcE');
    $level = 1;
    global $conn;

    $uploadDir = "../conventions/";
    $fileNameNew = "";
    if (isset($_FILES['conventions_pdf']) && $_FILES['conventions_pdf']['error'] == 0) {
        $fileTmpPath = $_FILES['conventions_pdf']['tmp_name'];
        $fileName = $_FILES['conventions_pdf']['name'];
        $fileType = $_FILES['conventions_pdf']['type'];
        $fileSize = $_FILES['conventions_pdf']['size'];

        if($fileType!=="application/pdf"){
            $errors[]= "seul les pdf sont admis";
            exit();
        }
        elseif ($fileSize > 200000) {
            $errors[] = "fichier trop gros, maximum 200ko";
            exit();
        }else{
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileNameBase = pathinfo($fileName, PATHINFO_FILENAME);
            $count = 1;
            $fileNameNew = $fileNameBase . "-" . $count . "." . $fileExt;
            while (file_exists($uploadDir . $fileNameNew)) {
                $count++;
                $fileNameNew = $fileNameBase . "_" . $count . "." . $fileExt;
            }
            if (move_uploaded_file($fileTmpPath, $uploadDir . $fileNameNew)) {
                echo "okay";
            }else{
                $errors[]= "erreur lors de l'upload";

            }
        }
    }

    if ($date_naissance && !verifierFormatDate($date_naissance)) {
        $errors[] = "Format de la date de naissance invalide (attendu : YYYY-MM-DD).";
    }
    if ($date_debut_stage && !verifierFormatDate($date_debut_stage)) {
        $errors[] = "Format de la date de début du stage invalide (attendu : YYYY-MM-DD).";
    }
    if ($date_fin_stage && !verifierFormatDate($date_fin_stage)) {
            $errors[] = "Format de la date de fin du stage invalide (attendu : YYYY-MM-DD).";
    }
    if (!is_null($date_debut_stage) && is_null($date_fin_stage)) {
            $errors[] = "Impossible de valider une date de debut sans date de fin.";
        }
    if (is_null($date_debut_stage) && !is_null($date_fin_stage)) {
        $errors[] = "Impossible de valider une date de fin sans date de début.";
    }
    if (!is_null($date_debut_stage) && !is_null($date_fin_stage)) {
        $debut = new DateTime($date_debut_stage);
        $fin = new DateTime($date_fin_stage);

        if ($fin < $debut) {
            $errors[] = "La date de fin du stage doit être postérieure à la date de début.";
        }
    }
    var_dump($date_fin_stage,$date_naissance,$date_debut_stage);


    $date_naissance_obj = new DateTime($date_naissance);
    $date_actuelle = new DateTime();
    $age = $date_naissance_obj->diff($date_actuelle)->y;
    if ($age < 18) {
        $errors[] = "L'utilisateur doit avoir au moins 18 ans.";
    }

    if (!preg_match("/^\+\d{2}\s\d{8}$/", $telephone)) {
        $errors[] = "Format de téléphone invalide. Exemple: +32 00000000";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    $sql="SELECT COUNT(*) FROM students WHERE email= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
        $errors[] = "Cet email est déjà enregistré.";
    }
    if (!in_array($sexe, ['H', 'F', 'X'])) {
        $errors[] = "Valeur du sexe invalide.";
    }
    if (!in_array($paiement_ok, ['0', '1'])) {
        $errors[] = "Valeur du paiement invalide.";
    }
    //verfi longeurs
    $longueurs_max = [
        "nom" => 50, "prenom" => 50, "adresse" => 55,
        "telephone" => 20, "email" => 100, "entreprise" => 100, "encadrant" => 100
    ];
    foreach ($longueurs_max as $champ => $max) {
        if (strlen($$champ) > $max) {
            $errors[] = ucfirst($champ) . " ne doit pas dépasser $max caractères.";
        }
    }
    $requiredFields = ['nom', 'prenom', 'date_naissance', 'adresse', 'telephone', 'email'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || ($_POST[$field] === "")) {
            $errors[] = "Le champ $field est requis.";
        }
    }


    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        $sql = "INSERT INTO students (nom, prenom, date_naissance, adresse, telephone, email, password, level,conventions_pdf ,paiement_ok, sexe, entreprise, encadrant, date_debut_stage, date_fin_stage) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssisssssss", $nom, $prenom, $date_naissance, $adresse, $telephone, $email, $password, $level,$fileNameNew,$paiement_ok, $sexe, $entreprise, $encadrant, $date_debut_stage, $date_fin_stage);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Utilisateur ajouté avec succès.</p>";
            echo "<a href='../public/view2.php'>retour</a>";
        } else {
            echo "<p style='color: red;'>Erreur lors de l'insertion.</p>";
        }
        $stmt->close();
    }

    }