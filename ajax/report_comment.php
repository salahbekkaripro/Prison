<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../views/home.php');
    exit;
}

$comment_id = (int) $_GET['id'];
$stmt = $pdo->prepare("UPDATE comments SET reported = 1 WHERE id = ?");
$stmt->execute([$comment_id]);

// Redirection douce vers la page précédente
$ref = $_SERVER['HTTP_REFERER'] ?? '../views/home.php';
header("Location: $ref");
exit;
