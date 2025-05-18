<?php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/check_role.php';

// Vérifie que seul le rôle 'prisonnier' peut accéder
checkRole('prisonnier');
require_user_login();

if ($_SESSION['user']['role'] !== 'prisonnier') {
    echo "⛔ Accès interdit.";
    exit;
}

$user_id = $_SESSION['user']['id'];

// Vérifie que le prisonnier possède les deux objets
$stmt = $pdo->prepare("
    SELECT nom_objet FROM objets_prisonniers op
    JOIN prisonnier p ON op.prisonnier_id = p.id
    WHERE p.utilisateur_id = ?
");
$stmt->execute([$user_id]);
$objets = $stmt->fetchAll(PDO::FETCH_COLUMN);

$hasKey = in_array('Clé artisanale', $objets);
$hasPlan = in_array('Plan de la prison', $objets);

$pageTitle = "Tentative d’évasion";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .dashboard-container { padding: 20px; max-width: 800px; margin: auto; text-align: center; }
        .btn { padding: 10px 20px; font-weight: bold; border-radius: 6px; border: none; cursor: pointer; margin: 5px; }
        .btn.danger { background-color: #dc3545; color: white; }
        .target { width: 50px; height: 50px; background-color: red; border-radius: 50%; position: absolute; cursor: pointer; }
        #gameZone { position: relative; height: 300px; border: 2px dashed #999; margin-top: 20px; }

        #victory-overlay, #fail-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            font-size: 2rem;
            padding: 30px;
            animation: fadeIn 0.8s ease-in-out;
        }

        #victory-overlay h1 {
            font-size: 3rem;
            color: #58ff99;
            animation: pulse 1s infinite;
        }

        @keyframes fadeIn {
            from { opacity: 0; } to { opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="dashboard-container">
    <h2>🚪 Tentative d’évasion</h2>

    <?php if ($hasKey && $hasPlan): ?>
        <p>Tu as une <strong>Clé artisanale</strong> et un <strong>Plan de la prison</strong>... Tu peux tenter ta chance.</p>
        <p>Clique rapidement sur la cible rouge quand elle apparaît ! Tu n’as qu’une seule chance !</p>
        <button class="btn danger" onclick="demarrerEvasion()">Tenter l’évasion</button>

        <div id="gameZone"></div>
    <?php else: ?>
        <p>❌ Tu as besoin de <strong>Clé artisanale</strong> <em>et</em> <strong>Plan de la prison</strong> pour tenter une évasion.</p>
    <?php endif; ?>
</div>

<!-- Overlays -->
<div id="victory-overlay">
    <div>
        <h1>🎉 TU ES LIBRE !</h1>
        <p>Tu as réussi à t’échapper de la prison... Bonne route.</p>
    </div>
</div>

<div id="fail-overlay">
    <div>
        <h1>🚨 RATÉ...</h1>
        <p>Tu as échoué. Les gdmins t'ont rattrapé.</p>
    </div>
</div>

<script>
function demarrerEvasion() {
    const zone = document.getElementById('gameZone');
    zone.innerHTML = '';
    setTimeout(() => {
        const cible = document.createElement('div');
        cible.classList.add('target');
        cible.style.top = Math.random() * 250 + "px";
        cible.style.left = Math.random() * 700 + "px";

        // Si on clique à temps
        cible.onclick = () => {
            document.getElementById('victory-overlay').style.display = 'flex';
            fetch('../../ajax/supprimer_evasion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            });
            setTimeout(() => window.location.href = '../index.php', 3000);
        };

        zone.appendChild(cible);

        // Si le joueur rate
        setTimeout(() => {
            if (document.body.contains(cible)) {
                zone.removeChild(cible);
                document.getElementById('fail-overlay').style.display = 'flex';

                fetch('../../ajax/echec_evasion.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                });

                setTimeout(() => window.location.href = 'cachot.php', 3000);
            }
        }, 1000);
    }, 1500);
}

 // délai avant apparition

</script>

</body>
</html>
