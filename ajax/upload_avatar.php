<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

require_user_login();

$userId = $_SESSION['user']['id'];

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/avatars/';
    $serverPath = $_SERVER['DOCUMENT_ROOT'] . '/forum-prison/' . $uploadDir;

    if (!is_dir($serverPath)) {
        if (!mkdir($serverPath, 0777, true)) {
            die("Erreur : impossible de créer le dossier uploads/avatars");
        }
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
    finfo_close($finfo);

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!array_key_exists($mime, $allowed)) {
        die("Type de fichier non autorisé.");
    }

    $extension = $allowed[$mime];
    $filename = time() . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
    $targetPath = $serverPath . $filename;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->execute([$filename, $userId]);
        $_SESSION['user']['avatar'] = $filename;
    } else {
        die("Erreur lors du déplacement du fichier.");
    }
}

header('Location: ../views/profil.php');
exit;
