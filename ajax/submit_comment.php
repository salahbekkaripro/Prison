<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user']['id'])) {
    die("Non autorisé.");
}

$user_id = $_SESSION['user']['id'];
$post_id = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');
$parent_id = $_POST['parent_id'] ?? null;
$tag = trim($_POST['tag'] ?? '');

if (!$post_id || !$content) {
    die("Champs requis manquants.");
}

// Gestion de l'upload
$attachment = null;
if (!empty($_FILES['attachment']['name'])) {
    $allowedTypes = [
        'image/jpeg', 'image/png', 'image/gif',
        'application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    $fileType = mime_content_type($_FILES['attachment']['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        die("Type de fichier non autorisé.");
    }

    $uploadDir = __DIR__ . '/uploads/comments/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
        $attachment = $filename;
    } else {
        die("Erreur lors de l'upload.");
    }
}

// Insertion du commentaire
$stmt = $pdo->prepare("INSERT INTO comments (
    post_id, parent_id, user_id, content, attachment, tag, created_at, reported, validated_by_admin
) VALUES (?, ?, ?, ?, ?, ?, NOW(), 0, 0)");
$stmt->execute([
    $post_id,
    $parent_id ?: null,
    $user_id,
    $content,
    $attachment,
    $tag ?: null
]);
$comment_id = $pdo->lastInsertId();


// Si c'est une réponse à un commentaire, notifier l'auteur du commentaire parent
if (!empty($parent_id)) {
    // Récupère l'auteur du commentaire parent
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->execute([$parent_id]);
    $parent = $stmt->fetch();

    if ($parent && $parent['user_id'] != $user_id) {
        // Insertion notification AVEC $comment_id correct
        $notif = $pdo->prepare("INSERT INTO notifications (recipient_id, sender_id, comment_id, post_id) VALUES (?, ?, ?, ?)");
        $notif->execute([$parent['user_id'], $user_id, $comment_id, $post_id]);
    }
}


header("Location: post.php?id=" . $post_id);
exit;
