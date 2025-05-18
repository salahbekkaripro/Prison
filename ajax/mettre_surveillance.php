<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/check_role.php';

// Vérifie que seul le rôle 'admin' peut accéder
checkRole('admin');
// Vérifier que l'utilisateur est connecté et autorisé
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'chef'])) {
    echo "Accès interdit.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prisonnier_id'])) {
    $prisonnier_id = intval($_POST['prisonnier_id']);

    // Optionnel : créer une table 'surveillance' si elle n'existe pas
    // INSERT dans la table de surveillance (id, prisonnier_id, admin_id, date_debut, date_fin)
    $stmt = $pdo->prepare("INSERT INTO surveillance (prisonnier_id, admin_id, date_debut, date_fin)
                          VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 2 HOUR))");
    $stmt->execute([
        $prisonnier_id,
        $_SESSION['user']['id']
    ]);

    // Rediriger avec confirmation
    header("Location: views/admin/surveillance_cellule.php?success=1");
    exit;
} else {
    echo "Données invalides.";
    exit;
}
?>
