<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$commentId = $_POST['comment_id'] ?? null;
$content = trim($_POST['content'] ?? '');

if (!$commentId || !$content) {
    echo json_encode(['success' => false, 'error' => 'Champs manquants']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE comments SET content = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$content, $commentId]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
