<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../connect.php";
global $conn;

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT id, nom, prenom, adresse, telephone, email, conventions_pdf, paiement_ok, sexe, entreprise, encadrant, date_debut_stage, date_fin_stage FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        ?>
        <!doctype html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mettre à Jour Étudiant</title>
        </head>
        <body>
        <h1>MISE À JOUR D'UN ÉTUDIANT</h1>

        <form action="../treatment/update.php" method="post" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" maxlength="50" value="<?= htmlspecialchars($row['nom']); ?>"><br>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" maxlength="50" value="<?= htmlspecialchars($row['prenom']); ?>"><br>

            <label for="adresse">Adresse :</label>
            <input type="text" id="adresse" name="adresse" maxlength="55" value="<?= htmlspecialchars($row['adresse']); ?>"><br>

            <label for="telephone">Téléphone :</label>
            <input type="tel" id="telephone" name="telephone" pattern="^\+\d{2}\s\d{8}$" placeholder="+32 00000000" value="<?= htmlspecialchars($row['telephone']); ?>"><br>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" maxlength="100" value="<?= htmlspecialchars($row['email']); ?>"><br>

            <label for="paiement_ok">Paiement :</label>
            <select id="paiement_ok" name="paiement_ok" required>
                <option value="1" <?= $row['paiement_ok'] === "1" ? "selected" : ""; ?>>Oui</option>
                <option value="0" <?= $row['paiement_ok'] === "0" ? "selected" : ""; ?>>Non</option>
            </select><br>

            <label for="sexe">Sexe :</label>
            <select id="sexe" name="sexe" required>
                <option value="H" <?= $row['sexe'] === 'H' ? "selected" : ""; ?>>Homme</option>
                <option value="F" <?= $row['sexe'] === 'F' ? "selected" : ""; ?>>Femme</option>
                <option value="X" <?= strtoupper($row['sexe']) === 'X' ? "selected" : ""; ?>>Autre</option>
            </select><br>

            <label for="entreprise">Entreprise :</label>
            <input type="text" id="entreprise" name="entreprise" maxlength="100" value="<?= htmlspecialchars($row['entreprise']); ?>"><br>

            <label for="encadrant">Encadrant :</label>
            <input type="text" id="encadrant" name="encadrant" maxlength="100" value="<?= htmlspecialchars($row['encadrant']); ?>"><br>

            <label for="date_debut_stage">Date de début du stage :</label>
            <input type="date" id="date_debut_stage" name="date_debut_stage"
                   value="<?= !empty($row['date_debut_stage']) ? htmlspecialchars($row['date_debut_stage']) : ''; ?>"><br>

            <label for="date_fin_stage">Date de fin du stage :</label>
            <input type="date" id="date_fin_stage" name="date_fin_stage"
                   value="<?= !empty($row['date_fin_stage']) ? htmlspecialchars($row['date_fin_stage']) : ''; ?>"><br>

            <label for="conventions_pdf">Convention (PDF, max 200ko) :</label>
            <input type="file" id="conventions_pdf" name="conventions_pdf" accept=".pdf"><br>
            <?php if (!empty($row['conventions_pdf'])): ?>
                <p>Fichier actuel : <?= htmlspecialchars($row['conventions_pdf']); ?></p>
            <?php endif; ?>

            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']); ?>">
            <input type="submit" value="Modifier">
        </form>

        </body>
        </html>
        <?php
    } else {
        echo "Erreur : aucun étudiant trouvé avec cet identifiant.";
    }
} else {
    echo "Erreur : requête invalide ou identifiant manquant.";
}
?>
