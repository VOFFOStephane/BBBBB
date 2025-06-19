<?php
session_start();
session_unset();
session_destroy();
if (session_status() === PHP_SESSION_NONE) {
    echo "Déconnexion réussie !";
    } else {
        echo "Erreur : la session est toujours active.";
    }
header("Location: ../index.php");
exit();

