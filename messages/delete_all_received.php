<<?php
// Démarrage de session
session_start();

// Inclusion des fonctions (doit être avant l'appel require_user_login)
require_once '../includes/functions.php';

// Appel pour vérifier que l'utilisateur est connecté
require_user_login();

// Inclusion de la connexion base de données (parfois utilisée dans functions)
require_once '../includes/db.php';

// Inclusion du head (meta, css, etc)
require_once '../includes/head.php';

// Inclusion du header HTML (top barre, logo, etc)
include('../includes/header.php');

// Inclusion de la navbar principale
require_once '../includes/navbar.php';
;

// Suppression de TOUS les messages reçus par l'utilisateur connecté
$stmt = $pdo->prepare("DELETE FROM private_messages WHERE receiver_id = ?");
$stmt->execute([$_SESSION['user']['id']]);

// Redirection après suppression
header('Location: inbox.php');
exit;
