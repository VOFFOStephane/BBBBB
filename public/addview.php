
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ajout de Etudiant</title>
</head>
<body>
<h1>AJOUT D'UN ETUDIANT</h1>
<form action="../treatment/add.php" method="post" enctype="multipart/form-data">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" maxlength="50" required><br>

    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom" maxlength="50" required><br>

    <label for="date_naissance">Date de naissance :</label>
    <input type="date" id="date_naissance" name="date_naissance" required><br>

    <label for="adresse">Adresse :</label>
    <input type="text" id="adresse" name="adresse" maxlength="55" required><br>

    <label for="telephone">Téléphone :</label>
    <input type="tel" id="telephone" name="telephone" pattern="^\+\d{2}\s\d{8}$" required placeholder="+32 00000000"><br>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" maxlength="100" required><br>

    <label for="paiement_ok">Paiement :</label>
    <select id="paiement_ok" name="paiement_ok" required>
        <option value="1">Oui</option>
        <option value="0">Non</option>
    </select><br>

    <label for="sexe">Sexe :</label>
    <select id="sexe" name="sexe" required>
        <option value="H">Homme</option>
        <option value="F">Femme</option>
        <option value="X">Autre</option>
    </select><br>

    <label for="entreprise">Entreprise :</label>
    <input type="text" id="entreprise" name="entreprise" maxlength="100"><br>

    <label for="encadrant">Encadrant :</label>
    <input type="text" id="encadrant" name="encadrant" maxlength="100"><br>

    <label for="date_debut_stage">Date de début du stage :</label>
    <input type="date" id="date_debut_stage" name="date_debut_stage"><br>

    <label for="date_fin_stage">Date de fin du stage :</label>
    <input type="date" id="date_fin_stage" name="date_fin_stage"><br>

    <label for="conventions_pdf">Convention (PDF, max 200ko) :</label>
    <input type="file" id="conventions_pdf" name="conventions_pdf" accept=".pdf"><br>

    <button type="submit">Envoyer</button>
</form>

</body>
</html>

