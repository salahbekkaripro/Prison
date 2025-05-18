<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']['id'])) {
    die("Non autorisé");
}

$user_id = $_SESSION['user']['id'];
$comment_id = (int)($_POST['comment_id'] ?? 0);
$type = $_POST['type'] ?? '';


if (!in_array($type, ['like', 'dislike'])) {
    die("Type invalide");
}

// Vérifie s’il existe déjà un vote
$stmt = $pdo->prepare("SELECT * FROM likes WHERE comment_id = ? AND user_id = ?");
$stmt->execute([$comment_id, $user_id]);
$existing = $stmt->fetch();

if ($existing) {
    if ($existing['type'] === $type) {
        // Même vote => suppression (toggle)
        $pdo->prepare("DELETE FROM likes WHERE id = ?")->execute([$existing['id']]);
    } else {
        // Changement de vote
        $pdo->prepare("UPDATE likes SET type = ?, created_at = NOW() WHERE id = ?")->execute([$type, $existing['id']]);
    }
} else {
    // Nouveau vote
    $pdo->prepare("INSERT INTO likes (comment_id, user_id, type) VALUES (?, ?, ?)")->execute([$comment_id, $user_id, $type]);
}

if (!empty($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../views/post.php");
}
exit;
