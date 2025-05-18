<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'prisonnier') {
    header('Location: ../login.php');
    exit;
}

// 🔍 Récupère la date de fin de sanction "mise_au_trou"
$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT s.fin_sanction
    FROM sanction s
    JOIN prisonnier p ON s.prisonnier_id = p.id
    WHERE p.utilisateur_id = ? 
      AND s.type_sanction = 'mise_au_trou'
    ORDER BY s.date_sanction DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$row = $stmt->fetch();

if (!$row || !$row['fin_sanction']) {
    echo "<p style='color:white;'>⛔ Aucune sanction active.</p>";
    exit;
}

$fin = $row['fin_sanction'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>⛓️ Vous êtes au cachot</title>
    <style>
        body {
            background-color: #000;
            color: #ff4c4c;
            font-family: monospace;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        h1 { font-size: 3rem; margin-bottom: 20px; }
        p { font-size: 1.5rem; }
        #countdown { font-size: 2rem; color: #fff; margin-top: 20px; }
    </style>
</head>
<body>

    <h1>⛔ Vous êtes au cachot</h1>
    <p>Vous avez été sanctionné par l'administration.</p>
    <p>Temps restant avant libération :</p>
    <div id="countdown">Chargement...</div>

    <script>
        const finSanction = new Date("<?= $fin ?>").getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const diff = finSanction - now;

            if (diff <= 0) {
                document.getElementById("countdown").innerText = "✅ Vous serez libéré dans quelques instants...";
                setTimeout(() => location.reload(), 5000);
                return;
            }

            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById("countdown").innerText = 
                `${String(h).padStart(2, '0')}h ${String(m).padStart(2, '0')}m ${String(s).padStart(2, '0')}s`;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>

</body>
</html>
