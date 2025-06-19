<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../connect.php";
function login($email, $password) {
global $conn;
$stmt = $conn->prepare("SELECT password FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && hash('sha256', $password) === $user['password']) {
$_SESSION['email'] = $email;
$roles = [
'direction@ecole.be' => 'direction',
'secretariat@ecole.be' => 'secretariat',
];
$_SESSION['role'] = $roles[$email] ?? 'etudiant';
$_SESSION['loggedin'] = true;
header('Location: public/view2.php');
exit();
} else {
return "Nom ou mot de passe incorrect";
}
}
