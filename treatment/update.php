<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connect.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])){

    function verifierFormatDate($date) {
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    $errors = array();
    $id = intval($_POST['id']);
    $nom = isset($_POST['nom']) ? ucwords(strtolower(htmlspecialchars(trim($_POST['nom'])))) : '';
    $prenom = isset($_POST['prenom']) ? ucwords(strtolower(htmlspecialchars(trim($_POST['prenom'])))) : '';
    $adresse = isset($_POST['adresse'])?htmlspecialchars(trim($_POST['adresse'])):'';
    $telephone = isset($_POST['telephone'])?htmlspecialchars(trim($_POST['telephone'])):'';
    $email = isset($_POST['email'])?htmlspecialchars(trim($_POST['email'])):'';
    $paiement_ok = isset($_POST['paiement_ok'])?htmlspecialchars(trim($_POST['paiement_ok'])):'';
    $sexe = isset($_POST['sexe'])?htmlspecialchars(trim($_POST['sexe'])):'';
    $encadrant = isset($_POST['encadrant']) ? ucwords(strtolower(trim($_POST['encadrant']))) : '';
    $entreprise = isset($_POST['entreprise']) ? ucwords(strtolower(trim($_POST['entreprise']))) : '';
    $date_debut_stage = (isset($_POST['date_debut_stage']) && $_POST['date_debut_stage'] !== "") ? htmlspecialchars(trim($_POST['date_debut_stage'])):null;
    $date_fin_stage = $_POST['date_fin_stage']?htmlspecialchars($_POST['date_fin_stage']): null;
    global $conn;

    $stmt = $conn->prepare("SELECT conventions_pdf FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($ancien_pdf);
    $stmt->fetch();
    $stmt->close();

    $uploadDir = "../conventions/";
    $conventions_pdf = $ancien_pdf;

    if (isset($_FILES['conventions_pdf']) && $_FILES['conventions_pdf']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['conventions_pdf']['tmp_name'];
        $fileName = $_FILES['conventions_pdf']['name'];
        $fileType = $_FILES['conventions_pdf']['type'];
        $fileSize = $_FILES['conventions_pdf']['size'];

        if ($fileType !== "application/pdf") {
            $errors[] = "Seuls les fichiers PDF sont admis.";
        } elseif ($fileSize > 512000) {
            $errors[] = "Fichier trop volumineux (512 mo max).";
        } else {
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileNameBase = pathinfo($fileName, PATHINFO_FILENAME);
            $count = 1;
            $fileNameNew = $fileNameBase . "-" . $count . "." . $fileExt;
            while (file_exists($uploadDir . $fileNameNew)) {
                $count++;
                $fileNameNew = $fileNameBase . "_" . $count . "." . $fileExt;
            }

            if (move_uploaded_file($fileTmpPath, $uploadDir . $fileNameNew)) {
                $conventions_pdf = $fileNameNew;
            } else {
                $errors[] = "Erreur lors de l'upload.";
            }
        }
    }

    if ($date_debut_stage && !verifierFormatDate($date_debut_stage)) {
        $errors[] = "Format de la date de début du stage invalide (attendu : YYYY-MM-DD).";
    }
    if(!empty($entreprise)) {
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
    }else{
        $errors[]="vous ne pouvez pas mettre les dates de debut et de fin sans lieu de stage";
    }
    if (!preg_match("/^\+\d{2}\s\d{8}$/", $telephone)) {
        $errors[] = "Format de téléphone invalide. Exemple: +32 00000000";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }
    if (!in_array($sexe, ['H', 'F', 'X'])) {
        $errors[] = "Valeur du sexe invalide.";
    }
    if (!in_array($paiement_ok, ['0', '1'])) {
        $errors[] = "Valeur du paiement invalide.";
    }
    $longueurs_max = [
        "nom" => 50, "prenom" => 50, "adresse" => 55,
        "telephone" => 20, "email" => 100, "entreprise" => 100, "encadrant" => 100
    ];
    foreach ($longueurs_max as $champ => $max) {
        if (strlen($$champ) > $max) {
            $errors[] = ucfirst($champ) . " ne doit pas dépasser $max caractères.";
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        $sql = "UPDATE students SET nom=?, prenom=?, adresse=?, telephone=?,email=?,conventions_pdf=? ,paiement_ok=?, sexe=?, entreprise=?, encadrant=?, date_debut_stage=?, date_fin_stage=? where id=? ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssi", $nom, $prenom,  $adresse, $telephone, $email, $conventions_pdf,$paiement_ok, $sexe, $entreprise, $encadrant, $date_debut_stage, $date_fin_stage,$id);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Utilisateur mise à jour avec succès.</p>";
            echo "<br><a href='../public/view2.php'>retour</a>";
        } else {
            echo "<p style='color: red;'>Erreur lors de la mise à jour.</p>";
        }
        $stmt->close();
    }
    $conn->close();


}