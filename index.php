<?php
session_start();
require_once "connect.php";
global $conn ;
if($_SERVER["REQUEST_METHOD"] == "POST"){
    include __DIR__ . "/treatment/auth.php";
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    $error = login($email,$password);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
<h1>Connexion</h1>
<form action="index.php" method="post" >

    <label for="email">Nom d'utilisateur</label><br>
    <input type="text" id="email" name="email" required><br>
    <label for="password"> Mot de passe :</label><br>
    <input type="password" id="password" name="password" required><br>
    <input type="submit" value="se connecter">
    <?php
    if(!empty($error)):?>
        <P><?php echo $error; ?></P>
    <?php endif; ?>
</form>
</body>
</html>
