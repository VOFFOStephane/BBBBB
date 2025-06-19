
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
<form >
    <?php

    if(isset($_SESSION['loggedin'])&& $_SESSION['role']==='secretariat'){
        echo "<a href='view2.php'>Tout les etudiants  </a><br>";
        echo "<a href='paiement_okay.php'>  Etudiants en ordre  </a><br>";
        echo "<a href='paiement_non_okay.php'> Etudiants non en ordre </a><br>";
        echo "<a href='stage_okay.php'> Etudiants avec stage </a><br>";
        echo "<a href='stage_non_okay.php'> Etudiants sans stage </a><br>";
        echo "<a href='statistique.php'> Statistiques </a><br>";
        echo "<a href='addview.php'> Ajouter un etudiant </a><br>";
    }
    echo "<a href='../treatment/logout.php'> Deconnexion </a><br>";
    ?>

</form>
</body>
</html>
