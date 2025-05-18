<?php
session_start();
require_once '../includes/functions.php';
require_user_login(); // protège l'accès à la page
require_once '../includes/db.php';
require_once 'includes/head.php';
include '../includes/header.php';
require_once '../includes/navbar.php';
?>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Soumettre un nouveau sujet</title>
</head>
<div id="page-transition"></div>
<div id="app" class="form-input" style="max-width: 800px; margin: auto; margin-top: 60px;">

    <h2 style="color:white;">Soumettre un nouveau sujet</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $title = htmlspecialchars($_POST["title"]);
        $content = htmlspecialchars($_POST["content"]);
        $author = $_SESSION["user"]["username"]; // ✅ ici est la vraie source du pseudo

        $stmt = $pdo->prepare("INSERT INTO posts (title, content, author, is_approved, created_at)
                               VALUES (?, ?, ?, 0, NOW())");
        $stmt->execute([$title, $content, $author]);

        echo "<p class='btn-neon' style='text-align:center;'>✅ Sujet soumis pour validation. Il sera visible une fois accepté par un admin.</p>";
    }
    ?>

    <form method="post" style="display:flex; flex-direction:column; gap:15px;">
        <input type="text" name="title" placeholder="Titre du sujet" required>
        <textarea name="content" placeholder="Contenu..." rows="6" required></textarea>
        <button type="submit" style="width: 100%; padding: 12px; border-radius: 10px; border: none; background: rgba(255,255,255,0.05); color: white; font-size: 1em;">Soumettre</button>
        </form>
</div>
