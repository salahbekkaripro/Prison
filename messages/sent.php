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


require_once '../includes/navbar.php';

$stmt = $pdo->prepare("
    SELECT m.*, u.username AS receiver_name
    FROM private_messages m
    JOIN users u ON m.receiver_id = u.id
    WHERE m.sender_id = ?
    ORDER BY m.created_at DESC
");
$stmt->execute([$_SESSION['user']['id']]);
$messages = $stmt->fetchAll();
?>

<style>
.container {
    max-width: 900px;
    margin: 50px auto;
}
.sent-header {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,165,0,0.2);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    color: white;
    text-align: center;
}
.actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 20px;
}
.actions a {
    padding: 10px 20px;
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
    color: white;
    border: 1px solid rgba(255,165,0,0.2);
    text-decoration: none;
}
.actions a:hover {
    background: rgba(255,165,0,0.2);
}
.message-table {
    width: 100%;
    color: white;
    border-collapse: collapse;
}
.message-table th, .message-table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.message-table thead {
    background: rgba(255,100,0,0.1);
}
</style>

<div class="container">
    <div class="sent-header">
        <h2>📤 Messages envoyés</h2>
    </div>

    <div class="actions">
        <a href="new_message.php">✉️ Nouveau message</a>
        <a href="inbox.php">📥 Boîte de réception</a>
    </div>

    <?php if (empty($messages)): ?>
        <p style="color:white; text-align:center;">Aucun message envoyé.</p>
    <?php else: ?>
        <table class="message-table">
            <thead>
                <tr>
                    <th>📨 Sujet</th>
                    <th>👤 Destinataire</th>
                    <th>📅 Envoyé le</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td style="text-align:left;">
                            <a href="view_sent.php?id=<?= $msg['id'] ?>" style="color:#ffaa55;">
                                <?= htmlspecialchars($msg['subject']) ?>
                            </a>
                        </td>
                        <td>
                            <a href="/forum-prison/profil.php?id=<?= $msg['receiver_id'] ?>" style="color:#ff5555;">
                                <?= htmlspecialchars($msg['receiver_name']) ?>
                            </a>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
