<?php
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


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../messages/sent.php');
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT m.*, u.username AS receiver_name
    FROM private_messages m
    JOIN users u ON m.receiver_id = u.id
    WHERE m.id = ? AND m.sender_id = ?
");
$stmt->execute([$id, $_SESSION['user']['id']]);
$message = $stmt->fetch();

if (!$message) {
    echo "<p style='color:white; text-align:center;'>Message introuvable.</p>";
    exit;
}
?>

<style>
.container {
    max-width: 800px;
    margin: 50px auto;
}
.message-header {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,165,0,0.2);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    color: white;
    text-align: center;
}
.message-info {
    background: rgba(255,255,255,0.03);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    color: white;
}
.message-info p {
    margin: 8px 0;
}
.message-content {
    background: rgba(255,255,255,0.05);
    padding: 20px;
    border-radius: 10px;
    color: white;
    font-size: 1.1em;
    line-height: 1.5em;
}
.button {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 20px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,165,0,0.2);
    border-radius: 10px;
    color: white;
    text-decoration: none;
}
.button:hover {
    background: rgba(255,165,0,0.2);
}
</style>

<div class="container">
    <div class="message-header">
        <h2>📨 Message envoyé</h2>
    </div>

    <div class="message-info">
        <p><strong style="color:#ffaa00;">✉️ Sujet :</strong> <?= htmlspecialchars($message['subject']) ?></p>
        <p><strong style="color:#ffaa00;">👤 Destinataire :</strong> <a href="profil.php?id=<?= $message['receiver_id'] ?>" style="color:#ff5555; text-decoration:none;"> <?= htmlspecialchars($message['receiver_name']) ?> </a></p>
        <p><strong style="color:#ffaa00;">📅 Envoyé le :</strong> <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></p>
    </div>

    <div class="message-content">
        <?= nl2br(htmlspecialchars($message['content'])) ?>
    </div>

    <div style="text-align:center;">
        <a href="..messages/sent.php" class="button">🔙 Retour aux messages envoyés</a>
    </div>
</div>