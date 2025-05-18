<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/check_role.php';

// Vérifie que seul le rôle 'admin' peut accéder
checkRole('admin');


// Charger tous les prisonniers avec cellule attribuée
$prisonniers = $pdo->query("
    SELECT p.id AS pid, u.nom, u.prenom, p.cellule_id, c.numero_cellule AS cellule_nom
    FROM prisonnier p
    JOIN users u ON u.id = p.utilisateur_id
    LEFT JOIN cellule c ON c.id = p.cellule_id
    ORDER BY u.nom
")->fetchAll(PDO::FETCH_ASSOC);

// Charger toutes les cellules
$cellules = $pdo->query("SELECT id, numero_cellule FROM cellule ORDER BY numero_cellule")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Surveillance Cellule</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .dashboard-container { max-width: 900px; margin: auto; padding: 20px; }
        select, button { padding: 8px 12px; margin: 5px; }
        .section { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        th { background-color: #611; color: white; }
    </style>
</head>
<body>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<div class="dashboard-container">
    <h2 style="text-align:center;">👁️ Mettre une cellule ou un prisonnier sous surveillance</h2>

    <div class="section">
        <h3>📌 Choisir un prisonnier</h3>
        <select id="prisonnier_id">
            <option value="">-- Sélectionner un prisonnier --</option>
            <?php foreach ($prisonniers as $p): ?>
                <option value="<?= $p['pid'] ?>">
                    <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?> (Cellule <?= $p['cellule_nom'] ?? 'non affectée' ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="section">
        <h3>🏢 Ou choisir une cellule</h3>
        <select id="cellule_id">
            <option value="">-- Sélectionner une cellule --</option>
            <?php foreach ($cellules as $c): ?>
                <option value="<?= $c['id'] ?>">Cellule <?= htmlspecialchars($c['numero_cellule']) ?></option>
                <?php endforeach; ?>
        </select>
    </div>

    <div id="resultat"></div>
</div>

<script>
document.getElementById('prisonnier_id').addEventListener('change', function () {
    document.getElementById('cellule_id').value = '';
    fetch("../../ajax/ajax_surveillance.php?prisonnier_id=" + this.value)
        .then(res => res.text())
        .then(html => document.getElementById('resultat').innerHTML = html);
});

document.getElementById('cellule_id').addEventListener('change', function () {
    document.getElementById('prisonnier_id').value = '';
    fetch("../../ajax/ajax_surveillance.php?cellule_id=" + this.value)
        .then(res => res.text())
        .then(html => document.getElementById('resultat').innerHTML = html);
});
</script>
</body>
</html>