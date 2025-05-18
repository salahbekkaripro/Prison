<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/check_role.php';

// Vérifie que seul le rôle 'prisonnier' peut accéder
checkRole('prisonnier');

$pageTitle = "Détails de la cellule";

$cellule_id = intval($_GET['id'] ?? 0);
if (!$cellule_id) {
    echo "<p style='color:red; text-align:center;'>❌ ID de cellule invalide.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cellule WHERE id = ?");
$stmt->execute([$cellule_id]);
$cellule = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cellule) {
    echo "<p style='color:red; text-align:center;'>❌ Cellule introuvable.</p>";
    exit;
}

$current_prisonnier_id = $_SESSION['user']['prisonnier_id'] ?? -1;

$prisonniersStmt = $pdo->prepare("
    SELECT u.nom, u.prenom, p.id AS prisonnier_id, p.etat
    FROM prisonnier p
    JOIN users u ON p.utilisateur_id = u.id
    WHERE p.cellule_id = ?
");
$prisonniersStmt->execute([$cellule_id]);
$prisonniers = $prisonniersStmt->fetchAll(PDO::FETCH_ASSOC);

$feedback = '';
$show_heal_animation = false;

if (isset($_POST['manger'], $_POST['aliment_id'])) {
    $aliment_id = intval($_POST['aliment_id']);
    $stmtDel = $pdo->prepare("DELETE FROM objets_prisonniers WHERE id = ? AND prisonnier_id = ?");
    $stmtDel->execute([$aliment_id, $current_prisonnier_id]);

    if ($stmtDel->rowCount()) {
        $pdo->prepare("UPDATE prisonnier SET etat = 'sain' WHERE id = ?")->execute([$current_prisonnier_id]);
        $feedback = "✅ Vous vous sentez mieux après avoir mangé.";
        $show_heal_animation = true;
    } else {
        $feedback = "❌ Impossible de consommer cet objet.";
    }
}

$alimentairesStmt = $pdo->prepare("
    SELECT op.id, op.nom_objet
    FROM objets_prisonniers op
    JOIN objets_disponibles od ON od.id = op.objet_id
    WHERE op.prisonnier_id = ? AND od.type = 'alimentation'
");
$alimentairesStmt->execute([$current_prisonnier_id]);
$aliments = $alimentairesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php include '../../includes/head.php'; ?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .cellule-grille {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .cellule-box {
            background-color: #f5f5f5;
            border: 2px solid #444;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }
        .highlight {
            background-color: rgb(21, 58, 80) !important;
            border-color: rgb(28, 41, 31);
            color: white;
        }
        .healing-animation {
            animation: healFlash 1s ease-in-out;
        }
        @keyframes healFlash {
            0% { background-color: #d1f5d3; }
            50% { background-color: #b6f0bc; }
            100% { background-color: #d1f5d3; }
        }

        .sort-btn {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin: 5px;
        }
        .sort-btn:hover {
            background-color: #0056b3;
        }

        #overlay-evasion {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }

        .sort-btn.danger {
            background-color: darkred;
        }
        .sort-btn.danger:hover {
            background-color: crimson;
        }
    </style>
</head>
<body>
    <div class="glass-box" style="max-width: 900px; margin: 40px auto;">
        <h2 class="text-2xl">🏠 Cellule n°<?= htmlspecialchars($cellule['numero_cellule']) ?></h2>

        <ul>
            <li><strong>Capacité :</strong> <?= htmlspecialchars($cellule['capacite']) ?> prisonnier(s)</li>
            <li><strong>Surveillance :</strong> <?= $cellule['surveillance'] ? 'Oui 🔍' : 'Non ❌' ?></li>
        </ul>

        <h3 style="margin-top: 25px;">👥 Présents dans la cellule :</h3>

        <?php if ($feedback): ?>
            <div class="alert" style="margin-bottom: 20px; color: lime; font-weight: bold;"><?= $feedback ?></div>
        <?php endif; ?>

        <?php if (count($prisonniers) > 0): ?>
            <div class="cellule-grille">
                <?php foreach ($prisonniers as $p): ?>
                    <?php
                        $is_self = $p['prisonnier_id'] == $current_prisonnier_id;
                        $classes = "cellule-box" . ($is_self ? " highlight" : "");
                        if ($show_heal_animation && $is_self) {
                            $classes .= " healing-animation";
                        }
                    ?>
                    <div class="<?= $classes ?>">
                        <p style="font-weight: bold; font-size: 18px;">
                            <?= htmlspecialchars($p['prenom']) ?> <?= htmlspecialchars($p['nom']) ?>
                            <?= $is_self ? '⭐' : '' ?>
                        </p>
                        <p>🩺 État : <strong><?= htmlspecialchars($p['etat']) ?></strong></p>
                        <p>🆔 ID : <?= $p['prisonnier_id'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun prisonnier dans cette cellule.</p>
        <?php endif; ?>

        <!-- 🍽️ Formulaire manger -->
        <div style="text-align: center; margin-top: 40px;">
            <h3>🍽️ Vous avez faim ?</h3>
            <?php if (count($aliments) > 0): ?>
    <form method="POST">
        <select name="aliment_id" required>
            <?php foreach ($aliments as $a): ?>
                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nom_objet']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="manger" class="sort-btn">Manger 🍎</button>
    </form>
<?php else: ?>
    <p style="color: gray;">Vous n'avez aucun aliment.</p>
    <p>
        <a href="acheter_objet.php" class="sort-btn">🛒 Acheter un aliment</a>
    </p>
<?php endif; ?>

        </div>

        <div style="text-align:center; margin-top: 30px;">
            <a href="dashboard_prisonnier.php" class="sort-btn">⬅️ Retour au tableau de bord</a>
        </div>
    </div>

    <!-- 🔥 OVERLAY évasion automatique -->
    <div id="overlay-evasion">
        <h2>🕵️‍♂️ Êtes-vous prêt à tout risquer ?</h2>
        <p style="max-width: 600px; margin-bottom: 30px;">
            Cette décision pourrait tout changer... Vous ne pourrez peut-être pas revenir en arrière.
        </p>
        <div>
            <a href="evasion.php" class="sort-btn danger">🔥 Oui, je tente !</a>
<button onclick="fermerOverlayEvasion()" class="sort-btn">😰 Non, je reste sage</button>
        </div>
    </div>
<script>
    window.onload = function() {
        const alreadyRefused = localStorage.getItem('refused_evasion');
        if (!alreadyRefused) {
            document.getElementById('overlay-evasion').style.display = 'flex';
        }
    };

    function fermerOverlayEvasion() {
        document.getElementById('overlay-evasion').style.display = 'none';
        localStorage.setItem('refused_evasion', '1'); // Marque qu'on a refusé
    }
</script>


<?php include '../../includes/footer.php'; ?>
</body>
</html>
